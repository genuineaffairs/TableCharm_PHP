<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
// Render the admin js
echo $this->render('_jsAdmin.tpl')
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/pluginLink.tpl'; ?>
<h2><?php echo $this->translate('Directory / Pages - Documents Extension'); ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate('Create custom fields/questions for Page documents, which your members will be asked to fill while creating Page documents. To reorder the custom questions, click on their names and drag them up or down.'); ?>
</p>

<br />

<div class="admin_fields_options">
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addquestion"><?php echo $this->translate('Add Question') ?></a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addheading" style="display:none;"><?php echo $this->translate('Add Heading') ?></a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_saveorder" style="display:none;"><?php echo $this->translate('Save Order') ?></a>
</div>

<br />

<ul class="admin_fields">
  <?php foreach ($this->topLevelMaps as $field): ?>
    <?php echo $this->adminFieldMeta($field) ?>
  <?php endforeach; ?>
</ul>