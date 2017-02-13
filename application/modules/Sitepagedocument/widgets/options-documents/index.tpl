<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
	echo $this->navigation()
				->menu()
				->setContainer($this->gutterNavigation)
				->setUlClass('quicklinks seaocore_gutter_blocks clr')
				->render();
?>
