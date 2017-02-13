<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Sitepagenote/views/scripts/pageNoteHeader.tpl';
?>
<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnotedelete', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)): ?>
  <div class="layout_right" id="communityad_notedelete">
    <?php echo $this->content()->renderWidget("sitepage.page-ads", array('limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnotedelete', 3), 'tab' => 'notedelete', 'communityadid' => 'communityad_notedelete', 'isajax' => 0)); ?>
  </div>
<?php endif; ?>
<div class="layout_middle">
    <div class='global_form'>
    <form method="post" class="global_form">
      <div>
        <div>
          <h3><?php echo $this->translate('Delete Page Note ?'); ?></h3>
          <p>
            <?php echo $this->translate('Are you sure that you want to delete the page note titled "%1$s" last modified %2$s? It will not be recoverable after being deleted.', $this->sitepagenote->title, $this->timestamp($this->sitepagenote->modified_date)) ?>
          </p>
          <br />
          <p>
            <input type="hidden" name="confirm" value="true"/>
            <button type='submit' ><?php echo $this->translate('Delete'); ?></button>
           <?php echo $this->translate('or'); ?> <?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>$this->tab_selected_id)),$this->translate('cancel')) ?>
          </p>
        </div>
      </div>
    </form>
  </div>
</div>	