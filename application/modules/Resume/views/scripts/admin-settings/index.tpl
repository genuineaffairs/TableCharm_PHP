<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
?>

<h2><?php echo $this->translate("Resumes Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

  <div class='clear'>
  
    <?php if ($this->notice == 'license'): ?>
      <div class="tip">
        <span>
          <?php echo $this->translate('Please enter your license key associated with this plugin.');?>
        </span>
      </div>
    <?php endif; ?>
    
    <?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('epayment.paypalemail')): ?>
      <div class="tip">
        <span>
          <?php echo $this->translate('You have not configured your epayment account settings. Please go to <a href="%s">ePayment :: Global Settings</a> to update your settings now.',
            $this->url(array('module'=>'epayment', 'controller'=>'settings'), 'admin_default')
          );?>
        </span>
      </div>
    <?php endif; ?>
  
    <div class='settings'>

      <?php echo $this->form->render($this); ?>

    </div>
  </div>
     