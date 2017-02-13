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

<div class="sitepageevent_sidebar">
  <?php if (count($this->paginator) > 0): ?>
    <div class="sitepageevent_sidebar_header">
      <span><?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>$this->tab_id)),$this->translate(array('%s Upcoming Event', '%s Upcoming Events', count($this->paginator)), $this->locale()->toNumber(count($this->paginator)))) ?></span>
      <span><?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>$this->tab_id)),$this->translate('See All')) ?></span>
    </div>		
    <ul>
      <?php foreach ($this->paginator as $sitepageevent): ?>
        <li> 
          <?php
	          echo $this->htmlLink(
	               $sitepageevent->getHref(), $this->itemPhoto($sitepageevent, 'thumb.icon', $sitepageevent->getTitle()), array('class' => 'sitepageevent_sidebar_photo', 'title' => $sitepageevent->getTitle())
	          );
        
            $truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepageevent.truncation.limit', 13);
            $tmpBody = strip_tags($sitepageevent->getTitle());
	          $item_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
          ?>
          <div class='sitepageevent_info'>
            <div class='sitepageevent_title'>
            
              <?php echo $this->htmlLink($sitepageevent->getHref(), $item_title, array('title' => $sitepageevent->getTitle())) ?>
            </div>
            <div class='sitepageevent_details'>
							<?php 
			        $startDateObject = new Zend_Date(strtotime($sitepageevent->starttime));
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
            <div class='sitepageevent_details'>
              <?php echo $this->translate(array('%s view', '%s views', $sitepageevent->view_count), $this->locale()->toNumber($sitepageevent->view_count)) ?>
    							  |
              <?php echo $this->translate(array('%s guest', '%s guests', $sitepageevent->member_count), $this->locale()->toNumber($sitepageevent->member_count)) ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>			
  <?php endif; ?>
</div> 