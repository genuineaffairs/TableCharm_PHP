<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @package   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
?>

<?php if ($this->can_delete): ?> 
  <?php echo $this->form->render($this) ?>
<?php else: ?>
  <div class="global_form_popup">
    <?php echo $this->translate('You cannot delete resume package which has resume or epayment associated with it. You can edit and disable this package if you do not want public to view it.')?>
  </div>
<?php endif;?>