<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: featured-notes-carousel.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if ($this->direction == 1) { ?>
 <?php  $j=0; $offset=$this->offset; ?>

    <?php foreach ($this->featuredNotes as $note): ?>
      <?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $note->page_id);?>
      <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
			$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagenote.profile-sitepagenotes', $note->page_id, $layout); ?>
      <?php if($j% $this->itemsVisible ==0):?>
        <div class="Sitepagecontent_SlideItMoo_element Sitepagenote_SlideItMoo_element" style="width:<?php echo 146 * $this->inOneRow; ?>px;">
        <div class="Sitepagecontent_SlideItMoo_contentList">
      <?php endif;?>
      <div class="featured_thumb_content">
			<?php if($note->photo_id == 0):?>
				<?php 
				if($sitepage_object->photo_id == 0):?>
					<a class="thumb_img" href="<?php echo $note->getHref(array( 'page_id' => $note->page_id, 'note_id' => $note->note_id,'slug' => $note->getSlug(), 'tab' => $tab_id)); ?>">
						<span><?php echo $this->itemPhoto($note, 'thumb.profile', $note->getTitle()) ?></span>
					</a>
				<?php else:?>
					<a class="thumb_img" href="<?php echo $note->getHref(array( 'page_id' => $note->page_id, 'note_id' => $note->note_id,'slug' => $note->getSlug(), 'tab' => $tab_id)); ?>">
						<span style="background-image: url(<?php echo $sitepage_object->getPhotoUrl('thumb.normal'); ?>);"></span>
				</a>
			  <?php endif;?>
			<?php else:?>
				<a class="thumb_img" href="<?php echo $note->getHref(array( 'page_id' => $note->page_id, 'note_id' => $note->note_id,'slug' => $note->getSlug(), 'tab' => $tab_id)); ?>">
						<span style="background-image: url(<?php echo $note->getPhotoUrl('thumb.normal'); ?>);"></span>
				</a>
			<?php endif;?>
			<span class="show_content_des">
				<?php
				$owner = $note->getOwner();
				//$parent = $note->getParent();
				echo $this->htmlLink($note->getHref(), $this->string()->truncate($note->getTitle(),25),array('title'=> $note->getTitle()));
				?>
				<?php
				$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
				$tmpBody = strip_tags($sitepage_object->title);
				$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
				?>
				<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($note->page_id, $note->owner_id, $note->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?> 
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1)):?>
					<?php echo $this->translate('by ').
								$this->htmlLink($owner->getHref(), $this->string()->truncate($owner->getTitle(),25),array('title'=> $owner->getTitle()));?> 
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
      <div class="Sitepagecontent_SlideItMoo_element Sitepagenote_SlideItMoo_element" style="width:<?php echo 146 * $this->inOneRow; ?>px;">
        <div class="Sitepagecontent_SlideItMoo_contentList">
      <?php endif; ?>
          <?php if ($i < $this->count): ?>
            <div class="featured_thumb_content">
              <?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $this->featuredNotes[$i]->page_id);?>
              <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
							$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagenote.profile-sitepagenotes', $this->featuredNotes[$i]->page_id, $layout); ?>
							<?php if($this->featuredNotes[$i]->photo_id == 0):?>
								<?php 
								if($sitepage_object->photo_id == 0):?>
									<a class="thumb_img" href="<?php echo $this->featuredNotes[$i]->getHref(array( 'page_id' => $this->featuredNotes[$i]->page_id, 'note_id' => $this->featuredNotes[$i]->note_id,'slug' => $this->featuredNotes[$i]->getSlug(), 'tab' => $tab_id)); ?>">
										<span><?php echo $this->itemPhoto($this->featuredNotes[$i], 'thumb.profile', $this->featuredNotes[$i]->getTitle()) ?></span>
									</a>
								<?php else:?>
									<a class="thumb_img" href="<?php echo $this->featuredNotes[$i]->getHref(array( 'page_id' => $this->featuredNotes[$i]->page_id, 'note_id' => $this->featuredNotes[$i]->note_id,'slug' => $this->featuredNotes[$i]->getSlug(), 'tab' => $tab_id)); ?>">
										<span style="background-image: url(<?php echo $sitepage_object->getPhotoUrl('thumb.normal'); ?>);"></span>
								</a>
							  <?php endif;?>
							<?php else:?>
								<a class="thumb_img" href="<?php echo $this->featuredNotes[$i]->getHref(array( 'page_id' => $this->featuredNotes[$i]->page_id, 'note_id' => $this->featuredNotes[$i]->note_id,'slug' => $this->featuredNotes[$i]->getSlug(), 'tab' => $tab_id)); ?>">
										<span style="background-image: url(<?php echo $this->featuredNotes[$i]->getPhotoUrl('thumb.normal'); ?>);"></span>
								</a>
							<?php endif;?>
							<span class="show_content_des">
            		<?php
                $owner = $this->featuredNotes[$i]->getOwner();
               // $parent = $this->featuredNotes[$i]->getParent();
                echo
                     $this->htmlLink($this->featuredNotes[$i]->getHref(), $this->string()->truncate($this->featuredNotes[$i]->getTitle(),25),array('title'=> $this->featuredNotes[$i]->getTitle()));
                ?>
								<?php
								$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
								$tmpBody = strip_tags($sitepage_object->title);
								$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
								?>
								<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->featuredNotes[$i]->page_id, $this->featuredNotes[$i]->owner_id, $this->featuredNotes[$i]->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?>
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
			$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagenote.profile-sitepagenotes', $this->featuredNotes[$i]->page_id, $layout); ?>
   <?php if ($j % $this->itemsVisible == 0): ?>
      <div class="Sitepagecontent_SlideItMoo_element Sitepagenote_SlideItMoo_element" style="width:<?php echo 146 * $this->inOneRow; ?>px;">
        <div class="Sitepagecontent_SlideItMoo_contentList">
      <?php endif; ?>        
            <div class="featured_thumb_content">
							<?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $this->featuredNotes[$i]->page_id);?>
							<?php if($this->featuredNotes[$i]->photo_id == 0):?>
								<?php 
								if($sitepage_object->photo_id == 0):?>
									<a class="thumb_img" href="<?php echo $this->featuredNotes[$i]->getHref(array( 'page_id' => $this->featuredNotes[$i]->page_id, 'note_id' => $this->featuredNotes[$i]->note_id,'slug' => $this->featuredNotes[$i]->getSlug(), 'tab' => $tab_id)); ?>">
										<span><?php echo $this->itemPhoto($this->featuredNotes[$i], 'thumb.profile', $this->featuredNotes[$i]->getTitle()) ?></span>
									</a>
								<?php else:?>
									<a class="thumb_img" href="<?php echo $this->featuredNotes[$i]->getHref(array( 'page_id' => $this->featuredNotes[$i]->page_id, 'note_id' => $this->featuredNotes[$i]->note_id,'slug' => $this->featuredNotes[$i]->getSlug(), 'tab' => $tab_id)); ?>">
										<span style="background-image: url(<?php echo $sitepage_object->getPhotoUrl('thumb.normal'); ?>);"></span>
								</a>
							  <?php endif;?>
							<?php else:?>
								<a class="thumb_img" href="<?php echo $this->featuredNotes[$i]->getHref(array( 'page_id' => $this->featuredNotes[$i]->page_id, 'note_id' => $this->featuredNotes[$i]->note_id,'slug' => $this->featuredNotes[$i]->getSlug(), 'tab' => $tab_id)); ?>">
										<span style="background-image: url(<?php echo $this->featuredNotes[$i]->getPhotoUrl('thumb.normal'); ?>);"></span>
								</a>
							<?php endif;?>
							<span class="show_content_des">
            		<?php
                $owner = $this->featuredNotes[$i]->getOwner();
               // $parent = $this->featuredNotes[$i]->getParent();
                echo
                     $this->htmlLink($this->featuredNotes[$i]->getHref(), $this->string()->truncate($this->featuredNotes[$i]->getTitle(),25),array('title'=> $this->featuredNotes[$i]->getTitle()));
                ?>
								<?php
								$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
								$tmpBody = strip_tags($sitepage_object->title);
								$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
								?>
								<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->featuredNotes[$i]->page_id, $this->featuredNotes[$i]->owner_id, $this->featuredNotes[$i]->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?>
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
