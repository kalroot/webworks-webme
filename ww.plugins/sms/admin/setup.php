<?php
if(!is_admin())exit;

if(isset($_REQUEST['account']) && $_REQUEST['account']=='new'){
	echo '<table id="sms_account_setup">'
		.'<tr><th>Admin\'s email address</th><td><input id="sms_email" /></td></tr>'
		.'<tr><th>Preferred Password</th><td><input id="sms_password" /></td></tr>'
		.'<tr><th>repeat Password</th><td><input id="sms_password2" /></td></tr>'
		.'<tr><td colspan="2" id="sms_messages">Please fill in the above form.</td></tr>'
		.'</table><script src="/ww.plugins/sms/admin/setup.js"></script>';
}
