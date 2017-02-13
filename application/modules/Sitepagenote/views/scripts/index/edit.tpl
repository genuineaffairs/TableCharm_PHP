<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $include_file = 1;?>

<?php 
  include APPLICATION_PATH . '/application/modules/Sitepagenote/views/scripts/pageNoteHeader.tpl';
?>
<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnoteedit', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)): ?>
  <div class="layout_right" id="communityad_noteedit">
    <?php echo $this->content()->renderWidget("sitepage.page-ads", array('limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnoteedit', 3), 'tab' => 'noteedit', 'communityadid' => 'communityad_noteedit', 'isajax' => 0)); ?>
  </div>
<?php endif; ?>
<div class="layout_middle">
	<div class="sitepagenote_form">
	  <form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" class="global_form" id="form-upload-sitepagenote">
	    <div>
	      <div>
	        <h3>
	          <?php echo $this->translate($this->form->getTitle()) ?>        
	        </h3>
	        <?php echo $this->translate($this->form->getDescription()) ?>
	        <div class="form-elements">
	          <?php echo $this->form->title; ?>
	          <?php echo $this->form->tags; ?>
	          <?php echo $this->form->body; ?>
	          <?php echo $this->form->draft; ?>
	          <?php echo $this->form->search; ?>
	          <div class="form-wrapper">
	            <div class="form-label">&nbsp;</div>
	            <div class="form-element">
	              <?php echo $this->form->execute->render(); ?>
	              <?php echo $this->translate('or');?> <?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Cancel')) ?>
	            </div>
	          </div>			
	        </div>
	      </div>
	    </div>
	  </form>
	</div>
</div>	
