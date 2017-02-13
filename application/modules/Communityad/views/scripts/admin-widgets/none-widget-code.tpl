
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: none-widget-code.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="cmadd_code_popup">
  <div>

    <?php echo $this->translate('Please copy the following code and paste it at the desired location on a non-widgetized page to display advertisements over there.'); ?>
    <div class="tip" style="margin-top: 10px; "><span style="margin: 0px;">
      <?php echo $this->translate("Note: Please be sure that you are pasting this code at the right place. After pasting this code in your template page, if the page layout becomes disorganized, then contact your theme developer to assist you."); ?>
      </span> </div>
    <div id="tab1">
			<div class="cmadd_codetop">
				 <?php echo $this->translate('Code'); ?>
			</div>
	    <div class="cmadd_codemain">
	      <?php echo $this->none_widget_code_div; ?>
	    </div>
	  </div>
    <div class="cmadd_code_popup_btm">
      <a onclick="javascript:parent.Smoothbox.close()" href="javascript:void(0);"><?php echo $this->translate('Close'); ?></a>
    </div>
    
  </div>
</div>
<style type="text/css">
  .cmadd_code_popup
  {
    font-family: arial,verdana,tahoma,sans-serif;
    font-size:11px;
    background-color: #E9F4FA;
    overflow: hidden;
    padding: 10px;
    margin:10px 10px 0;
  }
  .cmadd_code_popup > div
  {
    background:#FFFFFF;
    border: 1px solid #D7E8F1;
    overflow: hidden;
    padding: 10px;
  }
  .cmadd_code_popup *
  {
    font-family: arial,verdana,tahoma,sans-serif;
  }
  .cmadd_code_popup_tbs
  {
  	clear:both;
  	margin-top:10px;
  	float:left;
  	width:98%;
  	position: relative;
  }
  .cmadd_code_popup_tbs a
  {
  	padding:3px 7px;
  	background:#E6F6FF;
  	border:1px solid #8EB7CD;
  	float:left;
  	margin-right:5px;
  	margin-bottom:-1px;
  	font-weight:bold;
  	outline:none;
  }
  .cmadd_code_popup_tbs a:hover
  {
  	text-decoration:none;
  }
  .cmadd_code_popup_tbs a.active
  {
	  background:#B0D5E8;
	  border-bottom-color:#B0D5E8;
	  color:#000;
  }
  .cmadd_codetop{
    background-color: #B0D5E8;
    color: #000000;
    font-weight: bold;
    margin: 10px auto 0;
    padding: 3px;
    width: 98%;
    border:1px solid #8EB7CD;
    border-bottom:none;
    float:left;
    clear:both;
  }
  .cmadd_codemain{
    background-color: #FAFCFE;
    border: 1px solid #8EB7CD;
    color: #465584;
    font-family: Courier,Courier New,Verdana,Arial;
    margin: 0 auto;
    padding:10px 3px;
    width: 98%;
    float:left;
    clear:both;
  }
  .cmadd_code_popup_btm
  {
    padding:5px 0;
    width:98%;
    margin-top:10px;
    float:left;
  }
  .cmadd_code_popup_btm a
  {
    padding:2px 5px;
    background-color: #B0D5E8;
    color: #000000;
    margin-right:5px;
    border:1px solid #5D889F;
  }
  .cmadd_code_popup_btm a:hover
  {
    text-decoration:none;
    background-color: #82AFC6;
  }
</style>
<script type="text/javascript">
	function displaydiv (no)
	{
		if(no==1){
			$("tab1").style.display = 'none';
			$("tab1_button").set('class','');
			$("tab2").style.display =  'block';
			$("tab2_button").set('class','active');

		}else{
				document.getElementById("tab1").style.display = 'block';

			$("tab2").style.display ='none';
			$("tab2_button").set('class','');
				$("tab1_button").set('class','active');
		}
	}
  </script>