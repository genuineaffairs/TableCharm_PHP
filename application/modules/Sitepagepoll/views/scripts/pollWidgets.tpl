<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: pollWidgets.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
		// THERE SOME SIMILAR CODE IN WIDGETS LIKE COMMENTS AND VIEWS AND PHOTO ITEM.
		include APPLICATION_PATH . '/application/modules/Sitepagepoll/views/scripts/pollWidgetsCode.tpl';
	?> 
	<div class='sitepage_sidebar_list_details'>
		<?php echo $this->translate(array('%s comment', '%s comments', $sitepagepoll->comment_count), $this->locale()->toNumber($sitepagepoll->comment_count)) ?> |
		<?php echo $this->translate(array('%s vote', '%s votes', $sitepagepoll->vote_count), $this->locale()->toNumber($sitepagepoll->vote_count)) ?>
	</div>
</div>