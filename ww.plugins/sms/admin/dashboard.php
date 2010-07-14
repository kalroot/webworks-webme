<?php
if(!is_admin())exit;
require SCRIPTBASE.'ww.plugins/sms/admin/libs.php';

if(!isset($DBVARS['sms_email']) || !$DBVARS['sms_email'] || !$DBVARS['sms_password']){
	echo '<em>You have not set up your textr.mobi account yet. Please <a href="/ww.admin/plugin.php?_plugin=sms&amp;_page=setup&amp;account=new">click here</a> to do so.</em>';
	return;
}

echo '<table style="width:100%">'
	.'<tr><th>Credits</th><td>'.SMS_getCreditBalance().'</td>'
	.'<th rowspan="2">Purchase credits</th><td rowspan="2"><select id="sms_purchase_amt"><option value="0">--</option><option>200</option></select></td>'
	.'<td id="sms_paypal_button_holder" rowspan="2"></td></tr>'
	.'<tr><th>Price per credit<br /><span style="font-size:8px">1 credit = 1 message</span></th><td>&euro;'.SMS_getCreditPrice().'</td></tr>'
	.'</table><script src="/ww.plugins/sms/admin/dashboard.js"></script>';
