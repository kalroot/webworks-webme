<?php
$html='<tr><td colspan="6"><div class="tabs">';
// { main
$html.='<div class="tabPage"><h2>Header</h2><p>This will appear above the login/registration form</p>';
$html.=ckeditor('body',$page['body'],false);
$html.='</div>';
// }
// { options
$html.='<div class="tabPage"><h2>Options</h2><table style="width:100%">';
// { visibility, user groups
$html.='<tr><th>'.__('Visibility').'</th><td>'.wInput('page_vars[userlogin_visibility]','select',array(
	'3'=>__('Login and Register forms'),
	'1'=>__('Login form'),
	'2'=>__('Register form')
),$page_vars['userlogin_visibility']).'</td>';

$html.='<th rowspan="3">Add New Users To</th><td rowspan="3">';
$groups=array();
$grs=dbAll('select id,name from groups');
$gms=array();
$gms='{}';
if(isset($page_vars['userlogin_groups']))$gms=$page_vars['userlogin_groups'];
$gms=json_decode($gms);
foreach($grs as $g){
	$groups[$g['id']]=$g['name'];
}
foreach($groups as $k=>$g){
	$html.='<input type="checkbox" name="page_vars[userlogin_groups]['.$k.']"';
	if(isset($gms->$k))$html.=' checked="checked"';
	$html.=' />'.htmlspecialchars($g).'<br />';
}
$html.='</td></tr>';
// }
// { registration type
$html.='<tr><th>'.__('Registration type:').'</th><td><select name="page_vars[userlogin_registration_type]"><option>Moderated</option>';
$html.='<option';
if(isset($page_vars['userlogin_registration_type']) && $page_vars['userlogin_registration_type']=='Email-verified')$html.=' selected="selected"';
$html.='>Email-verified</option>';
$html.='</select></td></tr>';
// }
// { redirect on login
$html.='<tr><th>'.__('redirect on login:').'</th><td>';
$html.='<select id="page_vars_userlogin_redirect_to" name="page_vars[userlogin_redirect_to]">';
if($page_vars['userlogin_redirect_to']){
	$parent=Page::getInstance($page_vars['userlogin_redirect_to']);
	$html.='<option value="'.$parent->id.'">'.htmlspecialchars($parent->name).'</option>';
}
else{
	$page_vars['userlogin_redirect_to']=0;
	$html.='<option value="0"> -- '.__('none').' -- </option>';
}
$html.='</select></td></tr>';
// }
$html.='</table></div>';
// }
// { messages
$html.='<div class="tabPage"><h2>Messages</h2><div class="tabs">';
// { Login header
$html.='<div class="tabPage"><h2>Login</h2><p>This message appears above the login form.</p>';
if(!isset($page_vars['userlogin_message_login']))$page_vars['userlogin_message_login']='<p>Please log in using your email address and password. If you don\'t already have a user account, please use the Register tab (see above) to register.</p>';
$html.=ckeditor('page_vars[userlogin_message_login]',$page_vars['userlogin_message_login'],false);
$html.='</div>';
// }
// { Reminder header
$html.='<div class="tabPage"><h2>Reminder</h2><p>This message appears above the password reminder form.</p>';
if(!isset($page_vars['userlogin_message_reminder']))$page_vars['userlogin_message_reminder']='<p>If you have forgotten your password, please enter your email address here to have a new verification email sent out to you.</p>';
$html.=ckeditor('page_vars[userlogin_message_reminder]',$page_vars['userlogin_message_reminder'],false);
$html.='</div>';
// }
// { Register header
$html.='<div class="tabPage"><h2>Registration</h2><p>This message appears above the user registration form.</p>';
if(!isset($page_vars['userlogin_message_registration']))$page_vars['userlogin_message_registration']='<p>Please enter your name and email address. After submitting, please check your email account for your account verification link.</p>';
$html.=ckeditor('page_vars[userlogin_message_registration]',$page_vars['userlogin_message_registration'],false);
$html.='</div>';
// }
$html.='</div></div>';
// }
// { terms and conditions
$html.='<div class="tabPage"><h2>Terms and Conditions</h2><p>Leave blank if no terms and conditions agreement is needed</p>';
$html.=ckeditor('page_vars[userlogin_terms_and_conditions]',$page_vars['userlogin_terms_and_conditions'],false);
$html.='</div>';
// }
// { addition privacy fields
$html.= '<div class="tabPage"><h2>Extra User Data</h2><p>These are fields that you can ask your subscribers to fill-in for your info.</p>';
$html.= '<table id="privacyfieldsTable" width="100%"><tr><th width="30%">Name</th><th width="30%">Type</th><th width="10%">Required</th><th id="extrasColumn"><a href="javascript:privacyfieldsAddRow()">add field</a></th></tr></table>';
$html.='<ul id="privacy_fields" style="list-style:none">';
if(!isset($page_vars['privacy_extra_fields']))$page_vars['privacy_extra_fields']='[]';
$rs=json_decode($page_vars['privacy_extra_fields']);
$i=0;
$arr=array('email'=>__('email'),'input box'=>__('input box'),'textarea'=>__('textarea'),'date'=>__('date'),
'checkbox'=>__('checkbox'),'selectbox'=>__('selectbox'),'hidden'=>__('hidden message'),'ccdate'=>__('credit card expiry date'));
foreach($rs as $r){
	if(!isset($r->name))continue;
	if(!isset($r->type))$r->type='input box';
	if(!isset($r->is_required))$r->is_required=false;
	if(!isset($r->extra))$r->extra='';
	$html.= '<li><table width="100%"><tr><td width="30%">'.wInput('page_vars[privacy_extra_fields]['.$i.'][name]','',htmlspecialchars($r->name)).'</td><td width="30%">'
	.wInput('page_vars[privacy_extra_fields]['.$i.'][type]','select',$arr,$r->type).'</td><td width="10%">'
	.wInput('page_vars[privacy_extra_fields]['.($i).'][is_required]','checkbox',$r->is_required).'</td><td>';
	switch($r->type){
		case 'selectbox':case 'hidden':{
			$html.= wInput('page_vars[privacy_extra_fields]['.($i++).'][extra]','textarea',$r->extra,'small');
			break;
		}
		default:{
			$html.= wInput('page_vars[privacy_extra_fields]['.($i++).'][extra]','hidden',$r->extra);
		}
	}
	$html.= '</td></tr></table></li>';
}
$html.='</ul>';
$html.='<script type="text/javascript">var privacyfieldElements='.$i.';</script>';
$html.='<script type="text/javascript" src="/ww.plugins/privacy/j/admin.fields.js"></script>';
$html.='</div>';
// }
$html.='</div><script>var page_vars_userlogin_redirect_to='.$page_vars['userlogin_redirect_to'].';
$(document).ready(function(){
	$("#page_vars_userlogin_redirect_to").remoteselectoptions({
		url:"/ww.admin/pages/get_parents.php"
	});
});</script>';
$html.='</td></tr>';