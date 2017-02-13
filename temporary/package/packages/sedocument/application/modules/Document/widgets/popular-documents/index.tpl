<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<ul class="seaocore_sidebar_list">
	<?php  $this->partialLoop()->setObjectKey('document');
				 echo $this->partialLoop('application/modules/Document/views/scripts/partialloop_widget.tpl', $this->paginator);
	?>
</ul>