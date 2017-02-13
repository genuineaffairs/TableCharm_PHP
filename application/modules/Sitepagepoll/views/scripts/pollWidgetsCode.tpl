<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: pollWidgetsCode.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
      echo $this->htmlLink(
              $sitepagepoll->getHref(), $this->itemPhoto($sitepagepoll->getOwner(), 'thumb.icon', $sitepagepoll->getOwner()->getTitle()), array('class' => 'list_thumb', 'title' => $sitepagepoll->title)
      )
      ?>
      <div class='sitepage_sidebar_list_info'>
        <div class='sitepage_sidebar_list_title'> 
          <?php echo $this->htmlLink($sitepagepoll->getHref(), Engine_Api::_()->sitepagepoll()->truncation($sitepagepoll->getTitle()), array('title' => $sitepagepoll->getTitle())); ?> 
        </div>
        