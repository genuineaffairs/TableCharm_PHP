<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepagenote
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
  <?php foreach ($this->paginator as $sitepagenote): ?>
    <li>             
      <?php $this->sitepage_subject = Engine_Api::_()->getItem('sitepage_page', $sitepagenote->page_id);?>
      <?php 
        // IN THIS FILE PHOTO CODE IS THERE WHICH IS SIMILAR IN WIDGETS.
			  include APPLICATION_PATH . '/application/modules/Sitepagenote/views/scripts/noteWidgets.tpl';
			?>
      <div class='sitepage_sidebar_list_info'>
        <div class='sitepage_sidebar_list_title'>
          <?php
	          $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
	          $tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagenote.profile-sitepagenotes', $sitepagenote->page_id, $layout);
	          echo $this->htmlLink($sitepagenote->getHref(), $item_title, array('title' => $sitepagenote->title));
          ?>
        </div>
        <div class='sitepage_sidebar_list_details'>
          <?php
	          $truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
	          $tmpBody = strip_tags($sitepagenote->page_title);
	          $page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
          ?>
          <?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepagenote->page_id, $sitepagenote->owner_id, $sitepagenote->getSlug()), $page_title, array('title' => $sitepagenote->page_title)) ?> 
        </div>    
        <div class="sitepage_sidebar_list_details"> 
					<?php echo $this->translate(array('%s like', '%s likes', $sitepagenote->like_count), $this->locale()->toNumber($sitepagenote->like_count)) ?>,
					<?php echo $this->translate(array('%s view', '%s views', $sitepagenote->view_count), $this->locale()->toNumber($sitepagenote->view_count)) ?>
				</div>	   
      </div>
    </li>
  <?php endforeach; ?>
  <li class="sitepage_sidebar_list_seeall">
		<a href='<?php echo $this->url(array('likednote'=> 1), 'sitepagenote_browse', true) ?>'><?php echo $this->translate('See All');?> &raquo;</a>
	</li>
</ul>