<?php
/*
	Webme Mailing List Plugin v0.2
	File: upgrade.php
	Developer: Conor Mac Aoidh <http://macaoidh.name>
	Report Bugs: <conor@macaoidh.name>
*/

require SCRIPTBASE.'ww.incs/db.php';
if($version==0){
	dbQuery('create table if not exists mailing_list(id int auto_increment not null primary key,email text,name text,status text,hash text)default charset=utf8');
	dbQuery('create table if not exists mailing_list_options(id int auto_increment not null primary key,name text,value text)default charset=utf8');
	$from='noreply@webme.eu';
	$subject='Mailing List SUbscription';
	$body='Hi, \n
		You or someone using your email address has applied to join our mailing list. \n
		To approve this subscription please click on the link below: \n
		%link% \n
		Thanks, \n
		The Team';
	dbQuery('insert into mailing_list_options values("","from","'.$from.'")');
	dbQuery('insert into mailing_list_options values("","subject","'.$subject.'")');
	dbQuery('insert into mailing_list_options values("","body","'.$body.'")');
	dbQuery('insert into mailing_list_options values("","dis_pend","1")');
	dbQuery('insert into mailing_list_options values("","dis_sub","1")');
	dbQuery('insert into mailing_list_options values("","col_name","0")');
	dbQuery('insert into mailing_list_options values("","use_bcc","1")');
	dbQuery('insert into mailing_list_options values("","email","noreply@webme.eu")');
	$version='0.1';
}
if($version=='0.1'){
	dbQuery('insert into mailing_list_options values("","use_js","1")');
	dbQuery('insert into mailing_list_options values("","inp_em","Join Our Mailing List")');
	dbQuery('insert into mailing_list_options values("","inp_nm","Enter Your Name Here")');
	dbQuery('insert into mailing_list_options values("","inp_sub","Join")');
	$version='0.2';
}

$DBVARS[$pname.'|version']=$version;
config_rewrite();
?>