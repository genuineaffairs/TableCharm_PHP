<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: reportdiscription.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php echo '<div class="admin_report_des"><div><p>' . $this->ad_cancel_dis . '</p><button onclick="javascript:parent.Smoothbox.close()">'. $this->translate('Close'). '</button></div></div>'; ?>
<style type="text/css">
.admin_report_des 
{
	background-color: #E9F4FA;
	overflow: hidden;
	padding: 10px;
	margin:10px;
}
.admin_report_des > div {
	background: none repeat scroll 0 0 #FFFFFF;
	border: 1px solid #D7E8F1;
	overflow: hidden;
	padding: 10px;
	width:500px;
}
button{
	clear:both;
	float:left;
	margin-top:10px;
}
</style>