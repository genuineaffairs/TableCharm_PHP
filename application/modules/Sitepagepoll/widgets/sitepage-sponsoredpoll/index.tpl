<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<ul class="sitepage_sidebar_list">
  <?php foreach ($this->recentlyview as $sitepagepoll): ?>
    <li>
      <?php 
	        // THERE SOME SIMILAR CODE IN WIDGETS LIKE COMMENTS AND VIEWS AND PHOTO ITEM.
				  include APPLICATION_PATH . '/application/modules/Sitepagepoll/views/scripts/partialpol.tpl';
				?> 
    </li>
  <?php endforeach; ?>
  <li class="sitepage_sidebar_list_seeall">
		<a href='<?php echo $this->url(array('sponsoredpoll'=> 1), 'sitepagepoll_browse', true) ?>'><?php echo $this->translate('See All');?> &raquo;</a>
	</li>
</ul>