<?
include_once($_SERVER['DOCUMENT_ROOT'].'/.private/config.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/common/webme_specific.php');
if(!session_id()){
	if(isset($_GET['cms_session']))session_id($_GET['cms_session']);
	session_start();
}
if(($_SERVER['PHP_SELF']!='/j/fckeditor-2.6.3/editor/plugins/kfm/get.php') && (!isset($kfm_api_auth_override)||!$kfm_api_auth_override) && !is_admin()){
	echo 'access denied!';
	exit;
}
