<?php
require_once '../common/jslibs.php';
require_once '../ww.incs/basics.php';

header('Cache-Control: max-age=2592000, public');
header('Expires-Active: On');
header('Expires: Fri, 1 Jan 2500 01:01:01 GMT');
header('Pragma:');
header('Content-type: text/javascript; charset=utf-8');

$name=md5_of_dir('./');
if(!is_dir(USERBASE . '/ww.cache/j'))mkdir(USERBASE . '/ww.cache/j');
else{
	$js=file_get_contents('mootools.v1.11.js');
	$js.=file_get_contents('jquery-ui/js/jquery-1.4.2.min.js');
	$js.=file_get_contents('jquery-ui/js/jquery-ui-1.8.1.custom.min.js');
	$js.=file_get_contents('json.js');
	$js.=file_get_contents('js.js');
	$js.=file_get_contents('tabs.js');
	$js.=file_get_contents('addrow.js');
	$js.=file_get_contents('formhide.js');
	$js.=file_get_contents('getels.js');
	$js.=file_get_contents('getmouseat.js');
	$js.=file_get_contents('getoffset.js');
	
	{ # browser-specific functions
		if(!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))$_SERVER['HTTP_ACCEPT_LANGUAGE']='';
		require_once('Net/UserAgent/Detect.php');
		$browser=new Net_UserAgent_Detect();
		$os=$browser->getOSString();
		$isIE       =$browser->isIE();
		if($isIE){
			$js.=file_get_contents('getwindowscrollat.ie.js');
			$js.=file_get_contents('getwindowsize.ie.js');
			$js.=file_get_contents('newel.ie.js');
			$js.=file_get_contents('xmlhttprequest.ie.js');
		}
		else{
			$js.=file_get_contents('getwindowscrollat.js');
			$js.=file_get_contents('getwindowsize.js');
			$js.=file_get_contents('newel.js');
		}
	}
	
	if(is_admin()){
		$js.=file_get_contents('../ww.admin/j/common.js');
		$js.=file_get_contents('notice.js');
	}
	
/* get rid of browser-dependent code above before uncommenting this
  if(isset($_REQUEST['minify'])){
    require '../common/jsmin-1.1.1.php';
    $js=JSMin::minify($js);
    file_put_contents('../ww.cache/j/'.$name,$js);
    delete_old_md5s('../ww.cache/j/');
    exit;
  }
  else{
    $js.="setTimeout(function(){var a=document.createElement('img');a.src='/j/js.php?minify=1';a.style.display='none';document.body.appendChild(a);},5000);";
  }
*/

	echo $js;
}
