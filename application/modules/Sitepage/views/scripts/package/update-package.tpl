<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: updatepackage.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $sitecouponEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecoupon'); ?>
<?php if (!empty($sitecouponEnabled)):?>
	<?php include_once APPLICATION_PATH . '/application/modules/Sitecoupon/views/scripts/getcode.tpl'; ?>
<?php endif;?>

<?php if (empty($this->is_ajax)) : ?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>

 <?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
  <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
  <div class="sitepage_edit_content">
    <div class="sitepage_edit_header">
	   <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->sitepage->page_id, $this->sitepage->owner_id, $this->sitepage->getSlug()),$this->translate('VIEW_PAGE')) ?>
	    <h3><?php echo $this->translate('Dashboard: ').$this->sitepage->title; ?></h3>
    </div>

<div id="show_tab_content">
<?php endif; ?> 

    <div class="sitepage_package_page">
      
      <ul class="sitepage_package_list">        
        <li>
         
          <div class="sitepage_package_list_title">
            <div class="sitepage_create_link">
              <?php if (Engine_Api::_()->sitepage()->canShowPaymentLink($this->sitepage->page_id)): ?>
                <a href='javascript:void(0);' onclick="submitSession(<?php echo $this->sitepage->page_id ?>)"><?php echo $this->translate('Make Payment'); ?></a>
                <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), 'sitepage_session_payment', true) ?>">
                <input type="hidden" name="page_id_session" id="page_id_session" />
                </form>
              <?php endif; ?>
              <?php if (Engine_Api::_()->sitepage()->canShowRenewLink($this->sitepage->page_id)): ?>
                <a href='javascript:void(0);' onclick="submitSession(<?php echo $this->sitepage->page_id ?>)"><?php echo $this->translate('Renew'); ?></a>
                <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), 'sitepage_session_payment', true) ?>">
                <input type="hidden" name="page_id_session" id="page_id_session" />
                </form>
              <?php endif; ?>
            </div>
            <h3><?php echo $this->translate("Current Package: " ).$this->translate(ucfirst($this->package->title)); ?></h3>
          </div>
          <?php $item=$this->package;?>
          <?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/package/_packageInfo.tpl'; ?>
        </li>
      </ul>
    </div>
    <div class='sitepage_package_page mtop15'>
      <?php if (count($this->paginator)): ?>
        
        <ul class="sitepage_package_list">
          <li>
						<h3><?php echo $this->translate('Other available Packages') ?></h3>
					
						
					
          <span>  <?php echo $this->translate('If you want to change the package for your page, please select one package from the following list of available packages.'); ?></span>
          <?php //start coupon plugin work. ?>
						<?php if (!empty($this->modules_enabled) && in_array("sitepage_package", $this->modules_enabled)) : ?>
						<div style="margin-top:10px;"><a href="javascript:void(0);" class=" buttonlink item_icon_coupon" onclick="javascript:preview('<?php echo '500' ?>', '<?php echo '500' ?>', '<?php echo 'sitepage_package' ?>');"><?php echo $this->translate('Discount Coupons') ?></a></div>
						<?php endif; ?>
          </li>
          <li>
          <div class="tip">
            <span>
              <?php echo $this->translate("Note: Once you change package for your page, all the settings of the page will be applied according to the new package, including apps available, features available, price, etc.");?>
            </span>
          </div>
          </li>
         
          <?php foreach ($this->paginator as $item): ?>
            <li>
              <div class="sitepage_package_list_title">
                <div class="sitepage_create_link">
                <?php
                 echo $this->htmlLink(
                array('route' => 'sitepage_packages', 'action' =>'update-confirmation', "page_id"=> $this->page_id, "package_id"=> $item->package_id),
                $this->translate('Change Package'), array('onclick' => 'owner(this);return false' ,'title' => $this->translate('Change Package')));
                 ?>
                </div>
                <h3>             
                  <a href='<?php echo $this->url(array("action"=>"detail" ,'id' => $item->package_id), 'sitepage_packages', true) ?>' onclick="owner(this);return false;" title="<?php echo $this->translate(ucfirst($item->title)) ?>"><?php echo $this->translate(ucfirst($item->title)); ?></a>
                </h3>                 
              </div>
              <?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/package/_packageInfo.tpl'; ?>
            </li>
          <?php endforeach; ?>
          <br />
          <div>
          <?php echo $this->paginationControl($this->paginator); ?>
          </div>
        </ul>
       
      <?php else: ?>
        <div class="tip">
          <span>
          <?php echo $this->translate("There are no other packages yet.") ?>
          </span>
        </div>
      <?php endif; ?>
      </div>
    <?php if (empty($this->is_ajax)) : ?>		
	</div>
</div>
<?php endif; ?>
<script type="text/javascript">
    
  function submitSession(id){
    document.getElementById("page_id_session").value=id;
    document.getElementById("setSession_form").submit();
  }

</script>
<script type="text/javascript" >
  function owner(thisobj) {
    var Obj_Url = thisobj.href;
    Smoothbox.open(Obj_Url);
  }
</script>