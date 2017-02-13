<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Epayment
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
?> 

<div class="headline">
  <h2>
    <?php echo $this->translate('Resumes');?>
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

<div class='layout_right'>
  <div class="resume_edit_gutter"> 
    <?php echo $this->partial('resume/_info.tpl', 'resume', array('resume' => $this->resume, 'dashboardNavigation' => $this->dashboardNavigation));?>
  </div>   
</div>
<div class='layout_middle'>
  <div class='global_form'>
    <div>
        <div>
          <h3><?php echo $this->translate('Resume Payment Cancel');?></h3>
          <p>
            <?php echo $this->translate('Your payment for your resume posting has not been processed due to error/cancel on PayPal.'); ?>
          </p>
          <br />
          <p>
            <?php echo $this->htmlLink($this->resume->getCheckoutHref(), $this->translate('Check Out'))?>
            <?php echo $this->translate('or')?>
            <?php echo $this->htmlLink($this->resume->getActionHref('packages'), $this->translate('view available packages'))?>
          </p>          
      </div>
    </div>
  </div>
</div>





