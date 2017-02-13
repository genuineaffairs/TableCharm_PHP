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

<?php // Check if it is a request coming from mobile app ?>
<?php $from_app = Zend_Controller_Front::getInstance()->getRequest()->getParam('from_app'); ?>
<?php if($from_app != 1) : ?>
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
<?php endif; ?>

<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'zulu', array(
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

  <?php if ($this->sa_participation_list) : ?>
  <script type="text/javascript">
    //<![CDATA[
    <?php echo $this->sa_participation_list; ?>    //]]>
  </script>
  <?php endif; ?>
  <script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Zulu/externals/js/profile-fields.js"></script>
  <script type="text/javascript">
    sm4.core.runonce.add(function() {
      <?php if($from_app != 1) : ?>
      sm4.user.buildFieldPrivacySelector($.mobile.activePage.find('.global_form').find('[data-field-id]'));
      <?php endif; ?>
      jQuery.profileInit();
    });
  </script>

<?php endif; ?>

<?php echo $this->form->render($this) ?>