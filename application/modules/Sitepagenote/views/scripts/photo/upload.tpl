<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upload.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/css.php?request=/application/modules/Sitepagenote/externals/styles/style_sitepagenote.css')
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Sitepagenote/views/scripts/pageNoteHeader.tpl';
?>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnoteaddphoto', 3)  && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)): ?>
  <div class="layout_right" id="communityad_noteupload">
    <?php echo $this->content()->renderWidget("sitepage.page-ads", array('limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnoteaddphoto', 3), 'tab' => 'noteupload', 'communityadid' => 'communityad_noteupload', 'isajax' => 0)); ?>
  </div>
<?php endif; ?>
<div class="layout_middle">
  <?php echo $this->form->render($this) ?>
</div>
<script type="text/javascript">
  var note_id = '<?php echo $this->note_id ?>';
</script>