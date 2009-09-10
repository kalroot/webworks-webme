<?php
if(!$edit && isset($replytoid) && $replytoid)$c.= wInput('replytoid','hidden',$replytoid);
$c.= '<div class="tabs">';
// { header
$c.='<div class="tabPage"><h2>Header</h2><p>Text to be shown above the form</p>';
$c.=ckeditor('body',$page['body']);
$c.='</div>';
// }
// { main details
$c.= '<div class="tabPage"><h2>Main Details</h2><table>';
// { send as email, recipient
if(!isset($vars['forms_send_as_email']))$vars['forms_send_as_email']=1;
if(!isset($vars['forms_recipient']))$vars['forms_recipient']=$_SESSION['userdata']['email'];
$c.= '<tr><th>'.__('Send as Email').'</th><td>'.wInput('page_vars[forms_send_as_email]','select',array('1'=>'Yes','0'=>'No'),$vars['forms_send_as_email']).'</td>';
$c.= '<th>'.__('Recipient').'</th><td>'.wInput('page_vars[forms_recipient]','',htmlspecialchars($vars['forms_recipient'])).'</td></tr>';
// }
// { captcha, reply-to
if(!isset($vars['forms_captcha_required']))$vars['forms_captcha_required']=1;
$c.= '<tr><th>Captcha Required</th><td>'.wInput('page_vars[forms_captcha_required]','select',array('1'=>'Yes','0'=>'No'),$vars['forms_captcha_required']).'</td>';
$c.= '<th>Reply-To</th><td>'.wInput('page_vars[forms_replyto]','',htmlspecialchars(@$vars['forms_replyto'])).'</td></tr>';
// }
$c.= '</table></div>';
// }
// { form fields
$c.= '<div class="tabPage"><h2>Form Fields</h2>';
$c.= '<table id="formfieldsTable" width="100%"><tr><th width="30%">Name</th><th width="30%">Type</th><th width="10%">Required</th><th id="extrasColumn"><a href="javascript:formfieldsAddRow()">add field</a></th></tr></table>';
$c.='<ul id="form_fields" style="list-style:none">';
$q2=dbAll('select * from forms_fields where formsId="'.$id.'" order by id');
$i=0;
$arr=array('email'=>__('email'),'input box'=>__('input box'),'textarea'=>__('textarea'),'date'=>__('date'),
'checkbox'=>__('checkbox'),'selectbox'=>__('selectbox'),'hidden'=>__('hidden message'),'ccdate'=>__('credit card expiry date'));
foreach($q2 as $r2){
$c.= '<li><table width="100%"><tr><td width="30%">'.wInput('formfieldElementsName['.$i.']','',htmlspecialchars($r2['name'])).'</td><td width="30%">'
.wInput('formfieldElementsType['.$i.']','select',$arr,$r2['type']).'</td><td width="10%">'
.wInput('formfieldElementsIsRequired['.($i).']','checkbox',$r2['isrequired']).'</td><td>';
switch($r2['type']){
case 'selectbox':case 'hidden':{
$c.= wInput('formfieldElementsExtra['.($i++).']','textarea',$r2['extra'],'small');
break;
}
default:{
$c.= wInput('formfieldElementsExtra['.($i++).']','hidden',$r2['extra']);
}
}
$c.= '</td></tr></table></li>';
}
$c.= '</ul></div>';
// }
// { success message
$c.= '<div class="tabPage"><h2>Success Message</h2>';
$c.= '<p>What should be displayed on-screen when the message is sent.</p>';
$c.= ckeditor('page_vars[forms_successmsg]',@$vars['forms_successmsg']);
$c.= '</div>';
// }
// { template
$c.= '<div class="tabPage"><h2>Template</h2>';
$c.= '<p>Leave blank to have an auto-generated template displayed.</p>';
$c.= ckeditor('page_vars[forms_template]',@$vars['forms_template']);
$c.= '</div>';
// }
$c.= '</div>';
$c.= '<script type="text/javascript">var formfieldElements='.$i.';</script>';
$c.='<script type="text/javascript" src="/ww.plugins/forms/j/admin.fields.js"></script>';
