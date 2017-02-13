<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
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
  <?php foreach ($this->paginator as $event): ?>
    <li>    
      <?php echo $this->htmlLink($event->getHref(), $this->itemPhoto($event, 'thumb.icon'), array('title' => $event->page_title)) ?>
      <div class='sitepage_sidebar_list_info'>
        <div class='sitepage_sidebar_list_title'>
          <?php
          $truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepageevent.truncation.limit', 13);
          $tmpBody = strip_tags($event->title);
          $event_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
          $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
          $tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepageevent.profile-sitepageevents', $event->page_id, $layout);
          ?>
          <?php echo $this->htmlLink($event->getHref(), $event_title, array('title' => $event->title)) ?>
        </div>
        <div class='sitepage_sidebar_list_details'>
          <?php
          $truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
          $tmpBody = strip_tags($event->page_title);
          $page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
          ?>
          <?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($event->page_id, $event->owner_id, $event->getSlug()), $page_title, array('title' => $event->page_title)) ?> 
        </div>    
        <div class='sitepage_sidebar_list_details'>
	        <?php 
	        $startDateObject = new Zend_Date(strtotime($event->starttime));
          if ($this->viewer() && $this->viewer()->getIdentity()) {    
				    $tz = $this->viewer()->timezone; 				    
						$startDateObject->setTimezone($tz);	
          }    
					?>
          <?php
          echo $this->translate('%1$s at %2$s', $this->locale()->toDate($startDateObject), $this->locale()->toTime($startDateObject)
          )
          ?>
        </div>
      </div>
    </li> 
  <?php endforeach; ?>
  <li class="sitepage_sidebar_list_seeall">
		<a href='<?php echo $this->url(array('upcomingevent'=> 1), 'sitepageevent_browse', true) ?>'><?php echo $this->translate('See All');?> &raquo;</a>
	</li>
</ul>