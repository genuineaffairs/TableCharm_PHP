<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<?php if( !$this->noForm ): ?>

 
  <?php echo $this->form->setAttrib('class', 'global_form_box no_form')->render($this) ?>
    
<?php endif; ?>
