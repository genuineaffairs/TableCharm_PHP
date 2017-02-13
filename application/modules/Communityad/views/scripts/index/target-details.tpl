<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: target-details.tpl  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if (!empty($this->targetDetails)): ?>
  <div class="global_form" style="margin:15px 0 0 15px;">
    <div>
      <div>
        <h2><?php echo $this->translate("Targeting Details") ?></h2>
        <div class="form-elements" style="width:550px;">
        <?php foreach ($this->targetDetails as $key => $value): ?>
          <div class="form-wrapper">
            <div class="form-label" style="width:100px;padding-top:0px;">  
              <label><?php echo $this->translate($value['label'] . ":") ?></label>
            </div>  
            <div class="form-element" style="max-width:400px;padding-top:0px;"><?php echo $this->translate($value['value']) ?> </div>
          </div>
        <?php endforeach; ?>
        </div>
        <div class="form-wrapper">
          <div class="form-label" style="width:100px;">  
           &nbsp;
          </div>  
	        <div class="form-element">
	          <button onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('Close'); ?></button>
	         </div>
	       </div>  
      </div>
    </div>
  </div>  
<?php endif; ?>
<?php if (@$this->closeSmoothbox): ?>
	<script type="text/javascript">
	  TB_close();
	</script>
<?php endif; ?>