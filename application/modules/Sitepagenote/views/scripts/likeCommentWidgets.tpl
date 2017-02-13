<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: likeCommentWidgets.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<ul class="sitepage_sidebar_list">
  <?php if (count($this->paginator) > 0): ?>
    <?php foreach ($this->paginator as $sitepagenote): ?>
      <li>        
        <?php 
          // IN THIS FILE PHOTO CODE IS THERE WHICH IS SIMILAR IN WIDGETS.
				  include APPLICATION_PATH . '/application/modules/Sitepagenote/views/scripts/noteWidgets.tpl';
				?> 
        <div class='sitepage_sidebar_list_info'>
          <div class='sitepage_sidebar_list_title'>
            <?php echo $this->htmlLink($sitepagenote->getHref(), $item_title, array('title' => $sitepagenote->getTitle())) ?>
          </div>
          <div class='sitepage_sidebar_list_details'>		       
            <?php echo $this->translate(array('%s comment', '%s comments', $sitepagenote->comment_count), $this->locale()->toNumber($sitepagenote->comment_count)) ?>
    					|
            <?php echo $this->translate(array('%s like', '%s likes', $sitepagenote->like_count), $this->locale()->toNumber($sitepagenote->like_count)) ?>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
  <?php endif; ?>
</ul> 	