<?php
function admin_can_create_top_pages(){
	return has_page_permissions(1024);
}
function config_rewrite(){
	global $DBVARS;
	$tmparr=$DBVARS;
	$tmparr2=array();
	$tmparr['plugins']=join(',',$DBVARS['plugins']);
	foreach($tmparr as $name=>$val)$tmparr2[]='\''.addslashes($name).'\'=>\''.addslashes($val).'\'';
	$config="<?php\n\$DBVARS=array(\n	".join(",\n	",$tmparr2)."\n);";
/*	$config='<'."?php
\$DBVARS=array(
	'username'     => '".addslashes($DBVARS['username'])."',
	'password'     => '".addslashes($DBVARS['password'])."',
	'hostname'     => '".addslashes($DBVARS['hostname'])."',
	'db_name'      => '".addslashes($DBVARS['db_name'])."',

	'theme'        => '".addslashes($DBVARS['theme'])."',
	'theme_dir'    => '".addslashes($DBVARS['theme_dir'])."',
	'theme_variant'=> '".addslashes($DBVARS['theme_variant'])."',

	'site_title'   => '".addslashes($DBVARS['site_title'])."',
	'site_subtitle'=> '".addslashes($DBVARS['site_subtitle'])."',
	'version'      => ".((int)$DBVARS['version']).",
	'userbase'     => '".addslashes($DBVARS['userbase'])."',
	'plugins'      => '".join(',',$DBVARS['plugins'])."'
);";*/
	file_put_contents(CONFIG_FILE,$config);
}
function is_admin(){
	return (isset($_SESSION['userdata']) && isset($_SESSION['userdata']['groups']['administrators']));
}
function is_logged_in(){
	return isset($_SESSION['userdata']);
}
function get_userid(){
	return $_SESSION['userdata']['id'];
}
function has_page_permissions($val){
	return true;
}
function has_access_permissions($val){
	return true;
}
if(isset($DBVARS['userbase']))define('USERBASE', $DBVARS['userbase']);
else define('USERBASE', $_SERVER['DOCUMENT_ROOT']);