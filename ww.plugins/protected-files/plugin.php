<?php
$plugin=array(
	'name'=>'protected files',
	'description'=>'Protect files by requiring either a login or an email address',
	'admin'=>array(
		'menu'=>array(
			'top'=>'site options'
		)
	),
	'frontend'=>array(
		'file_hook'=>'protectedFiles_check'
	),
	'version'=>5
);
function protectedFiles_log($fname,$success,$email='',$pf_id){
	$i=$_SERVER['REMOTE_ADDR'];
	if(!isset($_SESSION['session_md5']))$_SESSION['session_md5']=md5($i.$_SERVER['REQUEST_TIME']);
	$m=$_SESSION['session_md5'];
	$f=addslashes($fname);
	$e=addslashes($email);
	dbQuery("delete from protected_files_log where session_md5='$m' and file='$f'");
	dbQuery("insert into protected_files_log set ip='$i',file='$f',last_access=now(),success=$success,email='$e',session_md5='$m',pf_id=$pf_id");
}
function protectedFiles_check($vars){
	global $PAGEDATA;
	$fname=$vars['requested_file'];
	$protected_files=cache_load('protected_files','all');
	if(!$protected_files){
		$protected_files=dbAll('select * from protected_files');
		cache_save('protected_files','all',$protected_files);
	}
	foreach($protected_files as $pr){
		if(strpos($fname,$pr['directory'].'/')===0){
			$email='';
			if(isset($_SESSION['protected_files_email']) && $_SESSION['protected_files_email'])$email=$_SESSION['protected_files_email'];
			else if(isset($_SESSION['userdata']['email']) && $_SESSION['userdata']['email'])$email=$_SESSION['userdata']['email'];
			else if(isset($_REQUEST['email']) && filter_var($_REQUEST['email'],FILTER_VALIDATE_EMAIL))$email=$_REQUEST['email'];
			if($email){
				require_once SCRIPTBASE.'ww.incs/common.php';
				$_SESSION['protected_files_email']=$email;
				if(!isset($_SESSION['protected_files_stage2'])){
					$_SESSION['protected_files_stage2']=1;
					$PAGEDATA=Page::getInstance(0);
					$PAGEDATA->title='File Download';
#					$template=template_load($PAGEDATA);
#					ob_start();
#					show_page($template,'<p>Your download should begin in two seconds. If it doesn\'t, please <a href="'.htmlspecialchars($_SERVER['REQUEST_URI']).'">click here</a></p><script>setTimeout(function(){document.location="'.$_SERVER['REQUEST_URI'].'";},2000);</script><p><a href="'.$_SESSION['referer'].'">Click here</a> to return to the referring page.</p>',$PAGEDATA);
					echo '<p>Your download should begin in two seconds. If it doesn\'t, please <a href="'.htmlspecialchars($_SERVER['REQUEST_URI']).'">click here</a></p><script>setTimeout(function(){document.location="'.$_SERVER['REQUEST_URI'].'";},2000);</script><p><a href="'.$_SESSION['referer'].'">Click here</a> to return to the referring page.</p>';
#					ob_show_and_log('page');
					exit;
				}
				else{
					webmeMail($pr['recipient_email'], $pr['recipient_email'], '['.$_SERVER['HTTP_HOST'].'] protected file downloaded', 'protected file "'.addslashes($fname).'" was downloaded by "'.addslashes($email).'"'); 
					protectedFiles_log($fname,1,$email,$pr['id']);
					unset($_SESSION['referer']);
					exit;
				}
			}
			else{
				unset($_SESSION['protected_files_stage2']);
				if(!isset($_SESSION['referer']))$_SESSION['referer']=$_SERVER['HTTP_REFERER'];
				protectedFiles_log($fname,0,'',$pr['id']);
				$PAGEDATA=Page::getInstance(0);
				$PAGEDATA->title='File Download';
#				require SCRIPTBASE . 'common/templates.php';
#				$template=template_load($PAGEDATA);
#				ob_start();
#				show_page($template,$pr['message'].'<form method="post" action="/f'.htmlspecialchars($fname).'"><input name="email" /><input type="submit" /></form>',$PAGEDATA);
				echo $pr['message'].'<form method="post" action="/f'.htmlspecialchars($fname).'"><input name="email" /><input type="submit" /></form>';
#				ob_show_and_log('page');
				exit;
			}
		}
	}
}
