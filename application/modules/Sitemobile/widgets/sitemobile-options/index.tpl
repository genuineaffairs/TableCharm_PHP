<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $id_unq = rand(10, 99); ?>

<?php if ($this->navigation) : ?> 
  <div id='profile_options'>
    <?php
    //THIS IS RENDERED BY APPLICATION/MODULES/CORE/VIEWS/SCRIPTS/_NAVICONS.TPL
    echo $this->navigation()
           ->menu()
           ->setContainer($this->navigation)
           ->setPartial(array('_navIcons.tpl', 'core'))
           ->render()
    ?>
  </div>
<?php endif; ?>