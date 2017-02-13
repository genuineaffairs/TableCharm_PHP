<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
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
  <?php foreach ($this->listLikedPolls as $sitepagepoll): ?>
    <li>
      <?php
      echo $this->htmlLink(
              $sitepagepoll->getHref(), $this->itemPhoto($sitepagepoll->getOwner(), 'thumb.icon', $sitepagepoll->getOwner()->getTitle()), array('class' => 'list_thumb', 'title' => $sitepagepoll->title)
      )
      ?>
      <div class='sitepage_sidebar_list_info'>
        <div class='sitepage_sidebar_list_title'> 
          <?php echo $this->htmlLink($sitepagepoll->getHref(), Engine_Api::_()->sitepagepoll()->truncation($sitepagepoll->getTitle()), array('title' => $sitepagepoll->getTitle())); ?> 
        </div>
        <div class='sitepage_sidebar_list_details'> 
					<?php
							$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
							$tmpBody = strip_tags($sitepagepoll->page_title);
							$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
						?>
						<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepagepoll->page_id, $sitepagepoll->owner_id, $sitepagepoll->getSlug()), $page_title, array('title' => $sitepagepoll->page_title)) ?>
        </div>
        <div class='sitepage_sidebar_list_details'>
          <?php echo $this->translate(array('%s like', '%s likes', $sitepagepoll->count_likes), $this->locale()->toNumber($sitepagepoll->count_likes)) ?>,
          <?php echo $this->translate(array('%s vote', '%s votes', $sitepagepoll->vote_count), $this->locale()->toNumber($sitepagepoll->vote_count)) ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
  <li class="sitepage_sidebar_list_seeall">
		<a href='<?php echo $this->url(array('likedpoll'=> 1), 'sitepagepoll_browse', true) ?>'><?php echo $this->translate('See All');?> &raquo;</a>
	</li>
</ul>