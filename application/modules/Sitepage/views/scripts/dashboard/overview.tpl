<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: overview.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>

<div class="layout_middle">
  <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>

  <div class="sitepage_edit_content">
    <div class="sitepage_edit_header">
      <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->sitepage->page_id, $this->sitepage->owner_id, $this->sitepage->getSlug()),$this->translate('VIEW_PAGE')) ?>
      <h3><?php echo $this->translate('Dashboard: ') . $this->sitepage->title; ?></h3>
    </div>
    <div id="show_tab_content">
      <?php if (!empty($this->success)): ?>
        <ul class="form-notices" >
          <li>
            <?php echo $this->translate($this->success); ?>
          </li>
        </ul>
      <?php endif; ?>
      <div class="sitepage_overview_editor">
      	<?php echo $this->form->render($this); ?>
      </div>	
    </div>
  </div>
</div>
<script type="text/javascript">
  window.addEvent('domready', function () {
		 
    if ($('body-label')) {
      var catdiv1 = $('body-label');
      var catarea1 = catdiv1.parentNode;
      catarea1.removeChild(catdiv1);
    }
    if ($('save-label')) {
      var catdiv2 = $('save-label');  	
      var catarea2 = catdiv2.parentNode;
      catarea2.removeChild(catdiv2);
    }
  });
		
</script>