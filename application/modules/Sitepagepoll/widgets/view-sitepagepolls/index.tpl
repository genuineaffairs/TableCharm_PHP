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
  <?php foreach ($this->listViewedPolls as $sitepagepoll): ?>
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
          <?php echo $this->translate(array('%s view', '%s views', $sitepagepoll->views), $this->locale()->toNumber($sitepagepoll->views)) ?> |
          <?php echo $this->translate(array('%s vote', '%s votes', $sitepagepoll->vote_count), $this->locale()->toNumber($sitepagepoll->vote_count)) ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>