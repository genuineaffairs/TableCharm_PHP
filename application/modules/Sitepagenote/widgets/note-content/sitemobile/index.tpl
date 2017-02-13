<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php  $breadcrumb = array(
	array("href"=>$this->sitepage->getHref(),"title"=>$this->sitepage->getTitle(),"icon"=>"arrow-r"),
	array("href"=>$this->sitepage->getHref(array('tab' => $this->tab_selected_id)),"title"=>"Notes","icon"=>"arrow-r"),
	array("title"=>$this->sitepagenote->getTitle(),"icon"=>"arrow-d","class" => "ui-btn-active ui-state-persist"));
	echo $this->breadcrumb($breadcrumb);
?>

<div class="ui-page-content">
  <div class="sm-ui-cont-head">
    <div class="sm-ui-cont-author-photo">
			<?php if ($this->sitepagenote->photo_id == 0): ?>
				<?php if ($this->sitepage->photo_id == 0): ?>
					<?php echo $this->itemPhoto($this->sitepagenote, 'thumb.icon', $this->sitepagenote->getTitle()) ?>   
				<?php else: ?>
					<?php echo $this->itemPhoto($this->sitepage, 'thumb.icon', $this->sitepagenote->getTitle()) ?>
				<?php endif; ?>
			<?php else: ?>
				<?php echo $this->itemPhoto($this->sitepagenote, 'thumb.icon', $this->sitepagenote->getTitle()) ?>
			<?php endif; ?>
    </div>	
    <div class="sm-ui-cont-cont-info">
      <div class="sm-ui-cont-author-name">
        <?php echo $this->sitepagenote->getTitle()?>
      </div>
      <div class="sm-ui-cont-cont-date">
        <?php echo $this->translate("Posted by");?> <?php echo $this->htmlLink($this->sitepagenote->getOwner(), $this->sitepagenote->getOwner()->getTitle()) ?>
        -
				<?php echo $this->timestamp($this->sitepagenote->creation_date) ?>
				<?php if( !empty($this->sitepagenote->category_id) ): ?> - 
					<?php echo $this->htmlLink(array(
						'route' => 'sitepagenote_browse',
						'action' => 'browse',
						'note_category_id' => $this->sitepagenote->category_id,
					), $this->translate((string)$this->sitepagenote->categoryName())) ?>
				<?php endif ?>
				<?php if (count($this->noteTags) > 0): $tagCount = 0; ?> -
					<?php foreach ($this->noteTags as $tag): ?>
						<?php if (!empty($tag->getTag()->text)): ?>
							<?php if (empty($tagCount)): ?>
								<a href='<?php echo $this->url(array('action' => 'browse'), "sitepagenote_browse"); ?>?tag=<?php echo $tag->getTag()->tag_id ?>'>#<?php echo $tag->getTag()->text ?></a>
								<?php $tagCount++;
							else: ?>
								<a href='<?php echo $this->url(array('action' => 'browse'), "sitepagenote_browse"); ?>?tag=<?php echo $tag->getTag()->tag_id ?>'>#<?php echo $tag->getTag()->text ?></a>
							<?php endif; ?>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
			<div class="sm-ui-cont-cont-date">
				<?php echo $this->translate(array('%s view', '%s views', $this->sitepagenote->view_count), $this->locale()->toNumber($this->sitepagenote->view_count)) ?>
			</div>
    </div>	
  </div>
  <div class="sm-ui-cont-cont-des">
		<?php echo nl2br($this->sitepagenote->body) ?>
  </div>
	<?php if($this->sitepagenote->total_photos != 0): ?>
		<div class="sitepage_album_box" id="sitepagealbum_content">
			<ul class="thumbs thumbs_nocaptions" id="thumbs_nocaptions">
				<?php foreach( $this->photoNotes as $photo ): ?>         
					<?php $phototitle = $photo->title;?>
					<li id="thumbs-photo-<?php echo $photo->photo_id ?>">	 
						<a href="<?php echo $this->url(array('owner_id' => $photo->user_id, 'album_id' => $photo->album_id, 'photo_id' => $photo->photo_id,'tab' => $this->tab_selected_id), 'sitepagenote_image_specific') ?>"  title="<?php echo $phototitle;?>"  class="thumbs_photo">
							<span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
						</a>
						<br/>
						<?php if($this->viewer_id == $this->sitepagenote->owner_id || $this->can_edit == 1): ?>
							<?php echo $this->htmlLink(array('route'=>'sitepagenote_removeimage', 'note_id'=>$this->sitepagenote->note_id, 'photo_id' => $photo->photo_id, 'owner_id' => $photo->user_id,'tab' => $this->tab_selected_id), $this->translate('Delete'),array(
						'class' => 'buttonlink smoothbox'
						)) ?> 
						<?php endif; ?>
					</li>
				<?php endforeach;?>
			</ul>
		</div>
	<?php endif; ?>	
</div>