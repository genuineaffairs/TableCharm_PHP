<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: featured-events-carousel.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if ($this->direction == 1) { ?>
 <?php  $j=0; $offset=$this->offset; ?>

    <?php foreach ($this->featuredEvents as $event): ?>
      <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
			$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepageevent.profile-sitepageevents', $event->page_id, $layout); ?>
      <?php if($j% $this->itemsVisible ==0):?>
        <div class="Sitepagecontent_SlideItMoo_element Sitepageevent_SlideItMoo_element" style="width:<?php echo 146 * $this->inOneRow; ?>px;">
        <div class="Sitepagecontent_SlideItMoo_contentList">
      <?php endif;?>
      <div class="featured_thumb_content">
			<?php if($event->photo_id == 0)	:?>
				<a class="thumb_img" href="<?php echo $event->getHref(array( 'page_id' => $event->page_id, 'event_id' => $event->event_id,'slug' => $event->getSlug(), 'tab' => $tab_id)); ?>">
					<span><?php echo $this->itemPhoto($event, 'thumb.profile', $event->getTitle()) ?></span>
				</a>
			<?php else :?>
				<a class="thumb_img" href="<?php echo $event->getHref(array( 'page_id' => $event->page_id, 'event_id' => $event->event_id,'slug' => $event->getSlug(), 'tab' => $tab_id)); ?>">
					<span style="background-image: url(<?php echo $event->getPhotoUrl('thumb.normal'); ?>);"></span>
				</a>
			<?php endif; ?>	
			<span class="show_content_des">
				<?php
				$owner = $event->getOwner();
				//$parent = $event->getParent();
				echo 
							$this->htmlLink($event->getHref(), $this->string()->truncate($event->getTitle(),25),array('title'=> $event->getTitle()));
				?>
        <?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $event->page_id);?>
				<?php
				$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
				$tmpBody = strip_tags($sitepage_object->title);
				$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
				?>
				<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($event->page_id, $event->owner_id, $event->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?>
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1)):?>
					<?php echo $this->translate('by ').
								$this->htmlLink($owner->getHref(), $this->string()->truncate($owner->getTitle(),25),array('title'=>$owner->getTitle()));?>  
        <?php endif;?>
			</span>
      </div>
        <?php $j++; $offset++;?>
       <?php if(($j% $this->itemsVisible) ==0):?>
           </div>
        </div>    
       <?php endif;?>     
    <?php endforeach; ?>
    <?php if($j <($this->totalItemsInSlide)):?>
       <?php for ($j;$j<($this->totalItemsInSlide); $j++ ): ?>
      <div class="featured_thumb_content">
      </div>
       <?php endfor; ?>
         </div>
      </div>
    <?php endif;?>
     
<?php } else {?>
<?php $count=$this->itemsVisible;
$j=0;  $offset=$this->offset+$count;?>
  <?php for ($i =$count; $i < $this->totalItemsInSlide; $i++):?> 
      <?php if ($j % $this->itemsVisible == 0): ?>
      <div class="Sitepagecontent_SlideItMoo_element Sitepageevent_SlideItMoo_element" style="width:<?php echo 146 * $this->inOneRow; ?>px;">
        <div class="Sitepagecontent_SlideItMoo_contentList">
      <?php endif; ?>
          <?php if ($i < $this->count): ?>
            <div class="featured_thumb_content">
              <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
							$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepageevent.profile-sitepageevents', $this->featuredEvents[$i]->page_id, $layout); ?>
							
							<?php if($this->featuredEvents[$i]->photo_id == 0)	:?>
								<a class="thumb_img" href="<?php echo $this->featuredEvents[$i]->getHref(array( 'page_id' => $this->featuredEvents[$i]->page_id, 'event_id' => $this->featuredEvents[$i]->event_id,'slug' => $this->featuredEvents[$i]->getSlug(), 'tab' => $tab_id)); ?>">
									<span><?php echo $this->itemPhoto($this->featuredEvents[$i], 'thumb.profile', $this->featuredEvents[$i]->getTitle()) ?></span>
								</a>
							<?php else :?>
								<a class="thumb_img" href="<?php echo $this->featuredEvents[$i]->getHref(array( 'page_id' => $this->featuredEvents[$i]->page_id, 'event_id' => $this->featuredEvents[$i]->event_id,'slug' => $this->featuredEvents[$i]->getSlug(), 'tab' => $tab_id)); ?>">
									<span style="background-image: url(<?php echo $this->featuredEvents[$i]->getPhotoUrl('thumb.normal'); ?>);"></span>
								</a>
							<?php endif; ?>	
							
							<span class="show_content_des">
            		<?php
                $owner = $this->featuredEvents[$i]->getOwner();
               // $parent = $this->featuredEvents[$i]->getParent();
                echo 
                     $this->htmlLink($this->featuredEvents[$i]->getHref(), $this->string()->truncate($this->featuredEvents[$i]->getTitle(),25),array('title'=> $this->featuredEvents[$i]->getTitle()));
                ?>
								<?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $this->featuredEvents[$i]->page_id);?>
								<?php
								$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
								$tmpBody = strip_tags($sitepage_object->title);
								$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
								?>
								<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->featuredEvents[$i]->page_id, $this->featuredEvents[$i]->owner_id, $this->featuredEvents[$i]->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?>
                <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1)):?>
									<?php echo $this->translate('by ').
										$this->htmlLink($owner->getHref(), $this->string()->truncate($owner->getTitle(),25),array('title'=>$owner->getTitle()));?>
                <?php endif;?>
            	</span>
             </div>
          <?php else: ?>
             <div class="featured_thumb_content">
             </div>
          <?php endif; ?>
      <?php $j++; $offset++;?>
      <?php if (($j % $this->itemsVisible) == 0): ?>
          </div>
        </div>
      <?php endif; ?>     
     
  <?php endfor;?>
 <?php $j=0; $offset=$this->offset; ?>
 <?php for ($i = 0; $i < $count; $i++): ?>
   <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
			$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepageevent.profile-sitepageevents', $this->featuredEvents[$i]->page_id, $layout); ?>
   <?php if ($j % $this->itemsVisible == 0): ?>
      <div class="Sitepagecontent_SlideItMoo_element Sitepageevent_SlideItMoo_element" style="width:<?php echo 146 * $this->inOneRow; ?>px;">
        <div class="Sitepagecontent_SlideItMoo_contentList">
      <?php endif; ?>        
            <div class="featured_thumb_content">
							<?php if($this->featuredEvents[$i]->photo_id == 0)	:?>
								<a class="thumb_img" href="<?php echo $this->featuredEvents[$i]->getHref(array( 'page_id' => $this->featuredEvents[$i]->page_id, 'event_id' => $this->featuredEvents[$i]->event_id,'slug' => $this->featuredEvents[$i]->getSlug(), 'tab' => $tab_id)); ?>">
									<span><?php echo $this->itemPhoto($this->featuredEvents[$i], 'thumb.profile', $this->featuredEvents[$i]->getTitle()) ?></span>
								</a>
							<?php else :?>
								<a class="thumb_img" href="<?php echo $this->featuredEvents[$i]->getHref(array( 'page_id' => $this->featuredEvents[$i]->page_id, 'event_id' => $this->featuredEvents[$i]->event_id,'slug' => $this->featuredEvents[$i]->getSlug(), 'tab' => $tab_id)); ?>">
									<span style="background-image: url(<?php echo $this->featuredEvents[$i]->getPhotoUrl('thumb.normal'); ?>);"></span>
								</a>
							<?php endif; ?>	
							<span class="show_content_des">
            		<?php
                $owner = $this->featuredEvents[$i]->getOwner();
                //$parent = $this->featuredEvents[$i]->getParent();
                echo 
                     $this->htmlLink($this->featuredEvents[$i]->getHref(), $this->string()->truncate($this->featuredEvents[$i]->getTitle(),25),array('title'=> $this->featuredEvents[$i]->getTitle()));
                ?>
								<?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $this->featuredEvents[$i]->page_id);?>
								<?php
								$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
								$tmpBody = strip_tags($sitepage_object->title);
								$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
								?>
								<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->featuredEvents[$i]->page_id, $this->featuredEvents[$i]->owner_id, $this->featuredEvents[$i]->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?>
                <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1)):?>
									<?php echo $this->translate('by ').
										$this->htmlLink($owner->getHref(), $this->string()->truncate($owner->getTitle(),25),array('title'=>$owner->getTitle()));?>
                <?php endif;?>
            	</span>
	          </div>
         <?php $j++; $offset++; ?>
        <?php if ($j % $this->itemsVisible == 0): ?>
          </div>
        </div>
      <?php endif; ?>
  <?php endfor; ?>
 <?php } ?>
