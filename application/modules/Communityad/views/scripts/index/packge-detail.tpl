
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: package-detail.tpl  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
  $this->headLink()
  	->prependStylesheet($this->layout()->staticBaseUrl .'application/modules/Communityad/externals/styles/style_communityad.css');
?>
<?php foreach ($this->package as $item): ?>
	<div class="cmad_package_page" style="margin:10px 10px 0;">
		<ul class="cmad_package_list">
			<li style="width:650px;">	
				<div class="cmad_package_list_title">
					<?php if($this->can_create):?>
	        	<div class="cmad_hr_link">
	        		<a href="javascript:void(0);" onclick="createAD()"><?php echo $this->translate("COMMUNITYAD_PACKAGE_CREATE_BUTTON_".strtoupper($item['type'])); ?> &raquo;</a>
	        	</div>
        	<?php endif;?>
					<h3><?php echo $this->translate('Package Details'); ?>: <?php echo $item['title']; ?></h3>
				</div>	
				<div class="cmad_package_stat">
					<span>
						<b><?php echo $this->translate("Price"). ": "; ?> </b>
						<?php if(!$item->isFree()):?>
							<?php echo $this->locale()->toCurrency($item['price'], Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')); ?>
		        <?php else:?>
      				<?php echo $this->translate('FREE'); ?>
		        <?php endif;?>
					</span>
					<span>
						<b><?php echo $this->translate("Quantity"). ": "; ?> </b>
        		 <?php  switch ($item["price_model"]):
              case "Pay/view":
                 if($item["model_detail"] != -1):echo $this->translate(array('%s View', '%s Views', $item["model_detail"]), $this->locale()->toNumber($item["model_detail"])); else: echo $this->translate('UNLIMITED Views'); endif ;
                 break;
              case "Pay/click":
                 if($item["model_detail"] != -1):echo $this->translate(array('%s Click', '%s Clicks', $item["model_detail"]), $this->locale()->toNumber($item["model_detail"])); else: echo $this->translate('UNLIMITED Clicks'); endif ;
                 break;
              case "Pay/period":
                 if($item["model_detail"] != -1):echo $this->translate(array('%s Day', '%s Days', $item["model_detail"]), $this->locale()->toNumber($item["model_detail"])); else: echo $this->translate('UNLIMITED Days'); endif ;
              break;
            endswitch;?>
					</span>
          <?php if($item['type']=='default'):?>
					<span>
						<b><?php echo $this->translate("Featured"). ": "; ?> </b>
						<?php
	            if ($item['featured'] == 1)
	              echo $this->translate("Yes");
	            else
	              echo $this->translate("No");
						?>
					</span>
					<span>
						<b><?php echo $this->translate("Sponsored"). ": "; ?> </b>
						<?php
	            if ($item['sponsored'] == 1)
	              echo $this->translate("Yes");
	            else
	              echo $this->translate("No");
						?>
					</span>
          <?php endif; ?>
					<span>
						<b><?php echo $this->translate("Targeting"). ": "; ?> </b>
						<?php
	            if ($item['network'] == 1)
	              echo $this->translate("Yes");
	            else
	              echo $this->translate("No");
						?>
					</span>
					<span style="clear:both;margin-right:10px;">
						<b><?php echo $this->translate("You can advertise"). ": "; ?> </b>
           	<?php
              $canAdvertise=explode(",",$item['urloption']);
              if(in_array("website",$canAdvertise)){
               $canAdvertise[array_search("website",$canAdvertise)]= $this->translate('Custom Ad');
              }
              foreach ($canAdvertise as $key=>$value):
                if( strstr($value, "sitereview") ){
                    $isReviewPluginEnabled = Engine_Api::_()->getDbtable('modules', 'communityad')->getModuleInfo("sitereview");
                    if( !empty($isReviewPluginEnabled) ){
                        $sitereviewExplode = explode("_", $value);
                        $getAdsMod = Engine_Api::_()->getItem("communityad_module", $sitereviewExplode[1]);
                        $modTemTitle = strtolower($getAdsMod->module_title);
                        $modTemTitle = ucfirst($modTemTitle);
                        $canAdvertise[$key] = $modTemTitle;
                    }else {
                        unset($canAdvertise[$key]);
                    }
                }else {
                  if( $value != 'Custom Ad' ) {
                    $getInfo = Engine_Api::_()->getDbtable('modules', 'communityad')->getModuleInfo( $value );
                    if( !empty($getInfo) ) {
                      $canAdvertise[$key] = $this->translate($getInfo['module_title']);
                    }else {
                        unset($canAdvertise[$key]);
                    }
                  }else {
                    $canAdvertise[$key] = ucfirst($value);
                  }
                }
            endforeach;
              $canAdStr=implode(", ",$canAdvertise);

              echo $canAdStr;
           	?>
					</span>
				</div>
				<div class="cmad_list_details">
					<?php echo  $item['desc']; ?>
				</div>
				<div style="clear:both;height:20px;"></div>
				<button onclick='javascript:parent.Smoothbox.close()' style="float:right;"><?php echo $this->translate('Close'); ?></button>
			</li>						
		</ul>
  </div>
<?php endforeach;?>
<style type="text/css">
.cmad_package_stat span
{
	margin-right:30px;
}
</style>
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>
 <script type="text/javascript">

  function createAD()
{

	<?php $create_ad_url = $this->url(array('id' => $item['package_id']), 'communityad_create', true); ?>

  var url='<?php echo $create_ad_url ?>';

  parent.window.location.href=url;
  parent.Smoothbox.close();
}

  </script>