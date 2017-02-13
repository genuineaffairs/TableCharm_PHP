<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: about.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Alex
 */
?>

<h2><?php echo $this->translate('About Us') ?></h2>
<?php
	$str = $this->translate('_CORE_ABOUT_US');
	if ($str == strip_tags($str)) {
	// there is no HTML tags in the text
	echo '<p>'.nl2br($str).'</p>';
	} else {
		echo $str;
	}
?>