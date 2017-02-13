<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: home-sponsored.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if ($this->direction == 1): ?>
  <?php foreach ($this->documents as $document): ?>
    <div class="SlideItMoo_element seaocore_sponsored_carousel_items">
      <div class="seaocore_sponsored_carousel_items_thumb">

				<?php if(!empty($document->photo_id)):?>
					<?php echo $this->htmlLink($document->getHref(), $this->itemPhoto($document, 'thumb.icon'), array('title' => $document->document_title, 'rel' => 'lightbox[galerie]') ); ?>
				<?php else: ?>
					<?php echo $this->htmlLink($document->getHref(), '<img src="'. Engine_Api::_()->document()->sslThumbnail($document->thumbnail) .'" class="thumb_icon" />', array('title' => $document->document_title, 'rel' => 'lightbox[galerie]') ); ?>
				<?php endif; ?>

      </div>
      <div class="seaocore_sponsored_carousel_items_info">
        <div class="seaocore_sponsored_carousel_items_title">
          <?php echo $this->htmlLink($document->getHref(), Engine_Api::_()->document()->truncateText($document->document_title, $this->titletruncation), array('title' => $document->document_title)) ?>
        </div>       
        <div class="seaocore_sponsored_carousel_items_stat seaocore_txt_light">
          <?php echo $this->translate('posted by'); ?>
          <?php echo $this->htmlLink($document->getOwner()->getHref(), Engine_Api::_()->document()->truncateText($document->getOwner()->getTitle(), 10), array('title' => $document->getOwner()->getTitle())) ?>
        </div>
      </div>
    </div>	 
  <?php endforeach; ?>
<?php else: ?>
  <?php $count = $this->totalDocuments; ?>
  <?php for ($i = $count; $i < Count($this->documents); $i++): ?>
    <div class="SlideItMoo_element seaocore_sponsored_carousel_items">
      <div class="seaocore_sponsored_carousel_items_thumb">

				<?php if(!empty($this->documents[$i]->photo_id)):?>
					<?php echo $this->htmlLink(Engine_Api::_()->document()->getHref($this->documents[$i]->document_id, $this->documents[$i]->owner_id, $this->documents[$i]->document_title), $this->itemPhoto($this->documents[$i], 'thumb.icon'), array('title' => $this->documents[$i]->document_title, 'rel' => 'lightbox[galerie]', 'class' => "")) ?>
				<?php else: ?>
					<?php echo $this->htmlLink(Engine_Api::_()->document()->getHref($this->documents[$i]->document_id, $this->documents[$i]->owner_id, $this->documents[$i]->document_title), '<img src="'. Engine_Api::_()->document()->sslThumbnail($this->documents[$i]->thumbnail) .'" class="thumb_icon" />', array('title' => $this->documents[$i]->document_title, 'rel' => 'lightbox[galerie]', 'class' => "")) ?>
				<?php endif; ?>

      </div>
      <div class="seaocore_sponsored_carousel_items_info">
        <div class="seaocore_sponsored_carousel_items_title">
          <?php echo $this->htmlLink(Engine_Api::_()->document()->getHref($this->documents[$i]->document_id, $this->documents[$i]->owner_id, $this->documents[$i]->document_title), Engine_Api::_()->document()->truncateText($this->documents[$i]->document_title, $this->titletruncation), array('title' => $this->documents[$i]->document_title)) ?>
        </div>      
        <div class="seaocore_sponsored_carousel_items_stat seaocore_txt_light">
          <?php echo $this->translate('posted by'); ?>
          <?php echo $this->htmlLink($this->documents[$i]->getOwner()->getHref(), Engine_Api::_()->document()->truncateText($this->documents[$i]->getOwner()->getTitle(), 10), array('title' => $this->documents[$i]->getOwner()->getTitle())) ?>
        </div>
      </div>
    </div>	 
  <?php endfor; ?>
  <?php for ($i = 0; $i < $count; $i++): ?>
    <div class="SlideItMoo_element seaocore_sponsored_carousel_items">
      <div class="seaocore_sponsored_carousel_items_thumb">

				<?php if(!empty($this->documents[$i]->photo_id)):?>
					<?php echo $this->htmlLink(Engine_Api::_()->document()->getHref($this->documents[$i]->document_id, $this->documents[$i]->owner_id, $this->documents[$i]->document_title), $this->itemPhoto($this->documents[$i], 'thumb.icon'), array('title' => $this->documents[$i]->document_title, 'rel' => 'lightbox[galerie]', 'class' => "")) ?>
				<?php else: ?>
					<?php echo $this->htmlLink(Engine_Api::_()->document()->getHref($this->documents[$i]->document_id, $this->documents[$i]->owner_id, $this->documents[$i]->document_title), '<img src="'. Engine_Api::_()->document()->sslThumbnail($this->documents[$i]->thumbnail) .'" class="thumb_icon" />', array('title' => $this->documents[$i]->document_title, 'rel' => 'lightbox[galerie]', 'class' => "")) ?>
				<?php endif; ?>

      </div>
      <div class="seaocore_sponsored_carousel_items_info">
        <div class="seaocore_sponsored_carousel_items_title">

          <?php echo $this->htmlLink(Engine_Api::_()->document()->getHref($this->documents[$i]->document_id, $this->documents[$i]->owner_id, $this->documents[$i]->document_title), Engine_Api::_()->document()->truncateText($this->documents[$i]->document_title, $this->titletruncation), array('title' => $this->documents[$i]->document_title)) ?>

        </div>      
        <div class="seaocore_sponsored_carousel_items_stat seaocore_txt_light">
          <?php echo $this->translate('posted by'); ?>
          <?php echo $this->htmlLink($this->documents[$i]->getOwner()->getHref(), Engine_Api::_()->document()->truncateText($this->documents[$i]->getOwner()->getTitle(), 10), array('title' => $this->documents[$i]->getOwner()->getTitle())) ?>
        </div>
      </div>
    </div>
  <?php endfor; ?>
<?php endif; ?>