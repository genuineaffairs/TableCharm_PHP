<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Folder
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
?>
<?php $folder = $this->attachment->getFolder();
$category = $folder->getCategory();
?>

<div class='folder_profile_breadcrumb'>
  <ul>
	<li><?php echo $this->htmlLink(array('route' => 'folder_general'),
		  $this->translate('Folder Home Page')
		);?> &raquo;</li>
	<li><?php echo $this->htmlLink(array('route' => 'folder_general', 'action' => 'browse'),
		  $this->translate('Browse Folders')
		);?> &raquo;</li>
	<li><?php echo $this->htmlLink(array('route' => 'folder_general', 'action' => 'browse', 'category'=>$category->getIdentity()),
		  $this->translate($category->getTitle())
		);?> &raquo;</li>
    <li><?php echo $folder->toString(); ?></li>
  </ul>
</div>
