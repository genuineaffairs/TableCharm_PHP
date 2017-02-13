<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: process.tpl  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div>
  <h2> <?php echo $this->translate("Processing Payment")?></h2>
	<div>
	  <center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/process.gif" /></center>
	</div>
	<div id="LoadingImage" style="text-align:center;margin-top:15px;font-size:17px;">  
	  <?php echo $this->translate("Processing Request. Please wait .....")?>
	</div>
</div>
<script type="text/javascript">
  window.addEvent('load', function(){

    var url = '<?php echo $this->transactionUrl ?>';
    var data = <?php echo Zend_Json::encode($this->transactionData) ?>;
    var request = new Request.Post({
      url : url,
      data : data
    });
    request.send();
  });
</script>