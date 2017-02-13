<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div id='profile_options'>
  <?php
  // Render the menu
  echo $this->navigation()
          ->menu()
          ->setContainer($this->gutterNavigation)
          ->setPartial(array('_navIcons.tpl', 'core'))
          ->render();
  ?>
</div>