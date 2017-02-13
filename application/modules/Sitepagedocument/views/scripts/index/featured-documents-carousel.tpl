<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: featured-documents-carousel.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if ($this->direction == 1) { ?>
 <?php  $j=0; $offset=$this->offset; ?>

    <?php foreach ($this->featuredDocuments as $document): ?>
      <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
			$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagedocument.profile-sitepagedocuments', $document->page_id, $layout); ?>
      <?php if($j% $this->itemsVisible ==0):?>
        <div class="Sitepagecontent_SlideItMoo_element Sitepagedocument_SlideItMoo_element" style="width:<?php echo 146 * $this->inOneRow; ?>px;">
        <div class="Sitepagecontent_SlideItMoo_contentList">
      <?php endif;?>
      <div class="featured_thumb_content">
			<?php
				//SSL WORK
				$this->https = 0;
				if (!empty($_SERVER["HTTPS"]) && 'on' == strtolower($_SERVER["HTTPS"])) {
					$this->https = 1;
				}

				if($this->https) {
					$this->manifest_path = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.manifestUrl', "page-documents");
					$document->thumbnail = $this->baseUrl().'/'.$this->manifest_path."/ssl?url=".urlencode($document->thumbnail);
				}
			?>
			<?php echo $this->htmlLink($document->getHref(), '<span><img src="'. $document->thumbnail .'" alt="" /></span>', array('class'=>'thumb_img')) ?>
			<span class="show_content_des">
				<?php
				$owner = $document->getOwner();
				//$parent = $document->getParent();
				echo $this->htmlLink($document->getHref(), $this->string()->truncate($document->getTitle(),25),array('title'=> $document->getTitle()));
				?>
        <?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $document->page_id);?>
				<?php
				$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
				$tmpBody = strip_tags($sitepage_object->title);
				$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
				?>
				<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($document->page_id, $document->owner_id, $document->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?>  
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
      <div class="Sitepagecontent_SlideItMoo_element Sitepagedocument_SlideItMoo_element" style="width:<?php echo 146 * $this->inOneRow; ?>px;">
        <div class="Sitepagecontent_SlideItMoo_contentList">
      <?php endif; ?>
          <?php if ($i < $this->count): ?>
            <div class="featured_thumb_content">
              <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
							$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagedocument.profile-sitepagedocuments', $this->featuredDocuments[$i]->page_id, $layout); ?>
							<?php
								//SSL WORK
								$this->https = 0;
								if (!empty($_SERVER["HTTPS"]) && 'on' == strtolower($_SERVER["HTTPS"])) {
								$this->https = 1;
								}

								if($this->https) {
								$this->manifest_path = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.manifestUrl', "page-documents");
								$this->featuredDocuments[$i]->thumbnail = $this->baseUrl().'/'.$this->manifest_path."/ssl?url=".urlencode($this->featuredDocuments[$i]->thumbnail);
								}
							?>
              <?php echo $this->htmlLink($this->featuredDocuments[$i]->getHref(), '<span><img src="'. $this->featuredDocuments[$i]->thumbnail .'" alt="" />', array('class'=>'thumb_img')) ?>
							<span class="show_content_des">
            		<?php
                $owner = $this->featuredDocuments[$i]->getOwner();
               // $parent = $this->featuredDocuments[$i]->getParent();
                echo $this->htmlLink($this->featuredDocuments[$i]->getHref(), $this->string()->truncate($this->featuredDocuments[$i]->getTitle(),25),array('title'=> $this->featuredDocuments[$i]->getTitle()));
                ?>
								<?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $this->featuredDocuments[$i]->page_id);?>
								<?php
								$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
								$tmpBody = strip_tags($sitepage_object->title);
								$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
								?>
								<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->featuredDocuments[$i]->page_id, $this->featuredDocuments[$i]->owner_id, $this->featuredDocuments[$i]->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?>
                <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1)):?>
									<?php echo $this->translate('by ').
											$this->htmlLink($owner->getHref(), $this->string()->truncate($owner->getTitle(),25));?>
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
			$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagedocument.profile-sitepagedocuments', $this->featuredDocuments[$i]->page_id, $layout); ?>
   <?php if ($j % $this->itemsVisible == 0): ?>
      <div class="Sitepagecontent_SlideItMoo_element Sitepagedocument_SlideItMoo_element" style="width:<?php echo 146 * $this->inOneRow; ?>px;">
        <div class="Sitepagecontent_SlideItMoo_contentList">
      <?php endif; ?>        
            <div class="featured_thumb_content">
	            <?php
								//SSL WORK
								$this->https = 0;
								if (!empty($_SERVER["HTTPS"]) && 'on' == strtolower($_SERVER["HTTPS"])) {
								$this->https = 1;
								}

								if($this->https) {
								$this->manifest_path = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.manifestUrl', "page-documents");
								$this->featuredDocuments[$i]->thumbnail = $this->baseUrl().'/'.$this->manifest_path."/ssl?url=".urlencode($this->featuredDocuments[$i]->thumbnail);
								}
							?>
							</a>
              <?php echo $this->htmlLink($this->featuredDocuments[$i]->getHref(), '<span><img src="'. $this->featuredDocuments[$i]->thumbnail .'" alt="" />', array('class'=>'thumb_img')) ?>
							<span class="show_content_des">
            		<?php
                $owner = $this->featuredDocuments[$i]->getOwner();
                //$parent = $this->featuredDocuments[$i]->getParent();
                echo $this->htmlLink($this->featuredDocuments[$i]->getHref(), $this->string()->truncate($this->featuredDocuments[$i]->getTitle(),25),array('title'=> $this->featuredDocuments[$i]->getTitle()));
                ?>
								<?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $this->featuredDocuments[$i]->page_id);?>
								<?php
								$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
								$tmpBody = strip_tags($sitepage_object->title);
								$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
								?>
								<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->featuredDocuments[$i]->page_id, $this->featuredDocuments[$i]->owner_id, $this->featuredDocuments[$i]->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?>
                <?php echo $this->translate('by ').
                     $this->htmlLink($owner->getHref(), $this->string()->truncate($owner->getTitle(),25));?>
            	</span>
	          </div>
         <?php $j++; $offset++; ?>
        <?php if ($j % $this->itemsVisible == 0): ?>
          </div>
        </div>
      <?php endif; ?>
  <?php endfor; ?>
 <?php } ?>
