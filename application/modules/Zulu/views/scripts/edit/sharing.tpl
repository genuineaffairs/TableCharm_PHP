<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: profile.tpl 9984 2013-03-20 00:00:04Z john $
 * @author     John
 */
?>
<div class="headline">
  <h2>
    <?php if ($this->viewer->isSelf($this->user)):?>
      <?php echo $this->translate('Edit My Profile & Medical Record');?>
    <?php else:?>
      <?php echo $this->translate('%1$s\'s Profile', $this->htmlLink($this->user->getHref(), $this->user->getTitle()));?>
    <?php endif;?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>

<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'fields', array(
      'topLevelId' => (int) @$this->topLevelId,
      'topLevelValue' => (int) @$this->topLevelValue
    ))
?>

<?php if(!Engine_Api::_()->zulu()->isMobileMode()) : ?>
<?php
  $this->headTranslate(array(
    'Everyone', 'All Members', 'Friends', 'Only Me',
  ));
?>
<script type="text/javascript">
  window.addEvent('domready', function() {
    en4.user.buildFieldPrivacySelector($$('.global_form *[data-field-id]'));
  });
</script>

<?php else : ?>

<script type="text/javascript">
  sm4.core.runonce.add(function() {
    sm4.user.buildFieldPrivacySelector($.mobile.activePage.find('.global_form').find('[data-field-id]'));
  });
</script>

<?php endif; ?>

<?php echo $this->partial($this->script[0], $this->script[1], array('form' => $this->form)) ?>