<?php
function show_messaging_notifier($vars){
	if(!is_array($vars) && isset($vars->id) && $vars->id){
		$data=dbOne('select data from messaging_notifier where id='.$vars->id,'data');
		if($data)return parse_messaging_notifier(json_decode($data),$vars);
	}
	return '<!-- this Messaging Notifier is not yet defined. -->';
}
function parse_messaging_notifier($data,$vars){
	$altogether=array();
	foreach($data as $r){
		$md5=md5($r->url);
		$f=cache_load('messaging-notifier',$md5);
		if($f===false || (file_exists(USERBASE.'ww.cache/messaging-notifier/'.$md5) && filectime(USERBASE.'ww.cache/messaging-notifier/'.$md5)+$r->refresh*60 < time())){
			switch($r->type){
				case 'email': // {
					$f=messaging_notifier_get_email($r);
					break;
				// }
				case 'phpBB3': // {
					$f=messaging_notifier_get_phpbb3($r);
					break;
				// }
				case 'RSS': // {
					$f=messaging_notifier_get_rss($r);
					break;
				// }
				case 'Twitter': // {
					$f=messaging_notifier_get_twitter($r);
					break;
				// }
			}
		}
		$altogether=array_merge($altogether,$f);
	}
	$html='<div id="messaging-notifier-'.$vars->id.'"'.$height.'><ul class="messaging-notifier">';
	$i=0;
	$ordered=array();
	foreach($altogether as $r){
		$ordered[$r['unixtime']]=$r;
	}
	krsort($ordered);
	foreach($ordered as $r){
		if(++$i > 10)continue;
		$description='';
		if($vars->characters_shown){
			$description=preg_replace('/<[^>]*>/','',$r['description']);
			if(strlen($description)>$vars->characters_shown)$description=substr($description,0,$vars->characters_shown).'...';
		}
		$target=$vars->load_in_other_tab?' target="_blank"':'';
		$title=$vars->hide_story_title?'':'<strong>'.htmlspecialchars($r['title']).'</strong><br />';
		$html.='<li class="messaging-notifier-'.$r['type'].'"><a'.$target.' href="'.$r['link'].'">'.$title.$description.'</a><br /><i>'.date('Y M jS H:i',$r['unixtime']).'</i></li>';
	}
	$html.='</ul></div><style type="text/css">@import "/ww.plugins/messaging-notifier/c/styles.css";</style>';
	if(isset($vars->scrolling) && $vars->scrolling){
		$n_items=isset($vars->stories_to_show) && is_numeric($vars->stories_to_show)?$vars->stories_to_show:2;
		if(isset($vars->scrolling) && $vars->scrolling)$html.='<script src="/ww.plugins/messaging-notifier/j/jquery.vticker.js"></script><script>$(function(){
			$("#messaging-notifier-'.$vars->id.'").vTicker({
				speed: 4000,
				pause: 5000,
				showItems: '.$n_items.',
				animation: "",
				mousePause: true
			});
		});</script><style>@import "/ww.plugins/messaging-notifier/c/scroller.css";</style>';
	}
	$height=$vars->height_in_px?' style="height:'.((int)$vars->height_in_px).'px"':'';
	return $html;
}
function messaging_notifier_get_rss($r){
	$f=file_get_contents($r->url);
	$dom=DOMDocument::loadXML($f);
	$items=$dom->getElementsByTagName('item');
	$arr=array();
	foreach($items as $item){
		$i=array();
		$i['type']='RSS';
		$title=$item->getElementsByTagName('title');
		$i['title']=$title->item(0)->nodeValue;
		$link=$item->getElementsByTagName('link');
		$i['link']=$link->item(0)->nodeValue;
		$description=$item->getElementsByTagName('description');
		$i['description']=$description->item(0)->nodeValue;
		$unixtime=$item->getElementsByTagName('pubDate');
		$i['unixtime']=strtotime($unixtime->item(0)->nodeValue);
		$arr[]=$i;
	}
	cache_save('messaging-notifier',md5($r->url),$arr);
	return $arr;
}
function messaging_notifier_get_twitter($r){
	$f=file_get_contents($r->url);
	$dom=DOMDocument::loadXML($f);
	$items=$dom->getElementsByTagName('item');
	$arr=array();
	foreach($items as $item){
		$i=array();
		$i['type']='Twitter';
		$title=$item->getElementsByTagName('title');
		$i['title']=$title->item(0)->nodeValue;
		$link=$item->getElementsByTagName('link');
		$i['link']=$link->item(0)->nodeValue;
		$unixtime=$item->getElementsByTagName('pubDate');
		$i['unixtime']=strtotime($unixtime->item(0)->nodeValue);
		$arr[]=$i;
	}
	cache_save('messaging-notifier',md5($r->url),$arr);
	return $arr;
}
function messaging_notifier_get_phpbb3($r){
	$f=file_get_contents($r->url);
	$urlbase=preg_replace('#/[^/]*$#','/',$r->url);
	$dom=@DOMDocument::loadHTML($f);
	$lists=$dom->getElementsByTagName('ul');
	$arr=array();
	foreach($lists as $list){
		$class=$list->getAttribute('class');
		if($class!='topiclist topics')continue;
		$items=$list->getElementsByTagName('li');
		foreach($items as $item){
			$i=array();
			$i['type']='phpBB3';
			$str=$item->getElementsByTagName('dt');
			$tmp_doc=new DOMDocument();
			$tmp_doc->appendChild($tmp_doc->importNode($str->item(0),true));
			$str=preg_replace('/[ 	]+/',' ',str_replace(array("\n","\r"),' ',$tmp_doc->saveHTML()));
			$i['title']=
				preg_replace('#^.*<a href="./memb[^>]*>([^<]*)<.*#','\1',$str)
				.' wrote a post in: '
				.preg_replace('#^<dt[^>]*> <a href=[^>]*>([^<]*)<.*#','\1',$str);
			$i['link']=$urlbase.preg_replace('#^<dt[^>]*> <a href="([^"]*)".*#','\1',$str);
			if(strpos($i['link'],'&amp;sid=')!==false){ // strip session id
				$i['link']=preg_replace('/&amp;sid=.*/','',$i['link']);
			}
			$i['unixtime']=strtotime(preg_replace('#.*raquo; (.*) </dt>#','\1',$str));
			$arr[]=$i;
		}
	}
	cache_save('messaging-notifier',md5($r->url),$arr);
	return $arr;
}
function messaging_notifier_get_email($r){
	$bs=explode('|',$r->url);
	$username=$bs[0];
	$password=$bs[1];
	$hostname=$bs[2];
	$link_url=isset($bs[3])?$bs[3]:'';
	$mbox=imap_open('{'.$hostname.':143/novalidate-cert}INBOX',$username,$password);
	$emails=imap_search($mbox,'ALL');
	$arr=array();
	if($emails && is_array($emails))foreach($emails as $email_number){
		$overview=imap_fetch_overview($mbox,$email_number,0);
		$subject=$overview[0]->subject;
		$from=trim(preg_replace('/<[^>]*>/','',$overview[0]->from));
		$arr[]=array(
			'type'  => 'email',
			'title' => $from.' wrote an email: '.$subject,
			'link' => $link_url,
			'unixtime'=>strtotime($overview[0]->date)
		);
		imap_delete($mbox,$email_number);
	}
	imap_expunge($mbox);
	imap_close($mbox);
	$md5=md5($r->url);
	$c=cache_load('messaging-notifier',$md5);
	if($c===false)$c=array();
	$arr=array_merge($arr,$c);
	krsort($arr);
	$arr=array_slice($arr,0,10);
	cache_save('messaging-notifier',$md5,$arr);
	return $arr;
}