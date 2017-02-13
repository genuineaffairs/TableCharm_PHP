<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepageevent
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
  <?php foreach ($this->paginator as $sitepageevent): ?>      
		<?php $this->sitepage_subject = Engine_Api::_()->getItem('sitepage_page', $sitepageevent->page_id);?>
		<li> 
			<?php
			echo $this->htmlLink(
			$sitepageevent->getHref(), $this->itemPhoto($sitepageevent, 'thumb.icon', $sitepageevent->getTitle()), array('class' => 'list_thumb', 'title' => $sitepageevent->getTitle())
			)
			?>
			<div class='sitepage_sidebar_list_info'>
				<div class='sitepage_sidebar_list_title'>
          <?php
          $truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepageevent.truncation.limit', 13);
          $tmpBody = strip_tags($sitepageevent->title);
          $event_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
          $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
          $tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepageevent.profile-sitepageevents', $sitepageevent->page_id, $layout);
          ?>
          <?php echo $this->htmlLink($sitepageevent->getHref(), $event_title, array('title' => $sitepageevent->title)) ?>
        </div>
				<div class='sitepage_sidebar_list_details'>
					<?php
						$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
						$tmpBody = strip_tags($sitepageevent->page_title);
						$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
					?>
					<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepageevent->page_id, $sitepageevent->user_id, $sitepageevent->getSlug()), $page_title, array('title' => $sitepageevent->page_title)) ?> 
				</div>
				<div class="sitepage_sidebar_list_details">  
					<?php echo $this->translate(array('%s guest', '%s guests', $sitepageevent->member_count), $this->locale()->toNumber($sitepageevent->member_count)) ?>,
					<?php echo $this->translate(array('%s view', '%s views', $sitepageevent->view_count), $this->locale()->toNumber($sitepageevent->view_count)) ?>
				</div>	
			</div>
    </li>
  <?php endforeach; ?>
  <li class="sitepage_sidebar_list_seeall">
		<a href='<?php echo $this->url(array('featuredevent'=> 1), 'sitepageevent_browse', true) ?>'><?php echo $this->translate('See All');?> &raquo;</a>
	</li>
</ul>