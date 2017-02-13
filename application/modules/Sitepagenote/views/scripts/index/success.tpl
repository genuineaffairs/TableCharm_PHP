<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: success.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
  include APPLICATION_PATH . '/application/modules/Sitepagenote/views/scripts/pageNoteHeader.tpl';
?>
<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnotesuccess', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)): ?>
  <div class="layout_right" id="communityad_notesuccess">
    <?php echo $this->content()->renderWidget("sitepage.page-ads", array('limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnotesuccess', 3), 'tab' => 'notesuccess', 'communityadid' => 'communityad_notesuccess', 'isajax' => 0)); ?>
  </div>
<?php endif; ?>
<div class="layout_middle">
  <div class='global_form'>
    <form method="post" class="global_form">
      <div>
        <div>
          <h3><?php echo $this->translate('Add Photos'); ?></h3>
          <p>
            <?php if ($this->sitepagenote->draft == 0) : ?>
              <?php echo $this->translate('The note for your Page has been successfully published. Would you like to add some photos to it?'); ?>
            <?php else : ?>
              <?php echo $this->translate('The note for your Page has been successfully drafted. Would you like to add some photos to it?'); ?>
            <?php endif; ?>
          </p>
          <br />
          <p>
            <input type="hidden" name="confirm" value="true"/>
            <button type='submit'><?php echo $this->translate('Add Photos'); ?></button>
            <?php echo $this->translate('or'); ?>
            <?php //echo $this->htmlLink(array('route' => 'sitepage_entry_view', 'page_url' => Engine_Api::_()->sitepage()->getPageUrl($this->sitepage->page_id), 'tab' => $this->tab_selected_id), $this->translate('Continue to Page')) ?>
            <?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Continue to Page')) ?>
          </p>
        </div>
      </div>
    </form>
  </div>
</div>	