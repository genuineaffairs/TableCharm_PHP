<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
  <div class="sm-content-list"  id="profile_sitepagedocuments">
		<?php if($this->can_create): ?>
			<div data-role="controlgroup" data-type="horizontal">
				<a data-role="button" data-icon="plus" data-iconpos="left" data-inset = 'false' data-mini="true" data-corners="true" data-shadow="true" href='<?php echo $this->url(array('page_id' => $this->page_id, 'tab_id' => $this->identity), 'sitepagedocument_create', true) ?>' class='buttonlink icon_sitepagedocument_new'><?php echo $this->translate('Add a Document'); ?></a>
			</div>
		<?php endif; ?>

		<ul data-role="listview" data-icon="arrow-r">
			<?php foreach ($this->paginator as $sitepagedocument): ?>
				<?php $flag = 1; ?>
				<?php if($sitepagedocument->draft == 0 && $sitepagedocument->approved == 1 && $sitepagedocument->status == 1):?>	
					<?php $flag = 1; ?>
				<?php elseif($this->level_id == 1 || $this->viewer_id == $this->sitepage_subject->owner_id || $this->viewer_id == $sitepagedocument->owner_id || $this->can_edit == 1): ?>
					<?php $flag = 1; ?>
				<?php endif; ?>
				<?php if($flag == 1): ?>
					<li>
						<a href="<?php echo $sitepagedocument->getHref(); ?>" >
						<?php if($this->https):?>
							<?php $sitepagedocument->thumbnail = $this->baseUrl().'/'.$this->manifest_path."/ssl?url=".urlencode($sitepagedocument->thumbnail); ?>
						<?php endif; ?>
              <?php if(false):?>
                <?php if(!empty($sitepagedocument->thumbnail)): ?>
                  <?php echo '<img src="'. $sitepagedocument->thumbnail .'" />'?>
                  <?php else:?>
                  <?php echo '<img src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitepagedocument/externals/images/sitepagedocument_thumb.png" />'?>
                <?php endif;?>
              <?php endif; ?>
							<h3><?php echo $sitepagedocument->getTitle(); ?></h3>
							<?php if($sitepagedocument->status == 0): ?>
							<p>
								<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepagedocument/externals/images/sitepagedocument_wait.gif', '', array('class' => 'icon')) ?>
								<?php echo $this->translate("Document format conversion in progress.") ?>
							</p>
						<?php elseif($sitepagedocument->status == 2): ?>
								<p style="color:red;"><?php echo $this->translate("Format conversion for this document failed."); ?></p>
							<?php elseif($sitepagedocument->status == 3): ?>
								<p style="color:red;"><?php echo $this->translate("This document has been deleted at Scribd. The document creator has been notified so that he may edit it to upload a new file."); ?></p>
							<?php endif;?>
						<p>   
							<?php echo $this->translate('Created by') ?>
							<strong><?php echo $sitepagedocument->getOwner()->getTitle(); ?></strong>
						</p>
						<p>  
							<?php echo $this->timestamp($sitepagedocument->creation_date) ?>
						</p>
						</a> 
					</li> 
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>

		<?php if ($this->paginator->count() > 1): ?>
			<?php
			echo $this->paginationAjaxControl(
							$this->paginator, $this->identity, 'profile_sitepagedocuments');
			?>
		<?php endif; ?>
	</div>
<?php else:?>
	<div class="tip">
		<span>
			<?php echo $this->translate('No documents have been added to this Page yet.'); ?>
			<?php if ($this->can_create):  ?>
				<?php echo $this->translate('Be the first to %1$sadd%2$s one!', '<a href="'.$this->url(array('page_id' => $this->page_id, 'tab' => $this->identity), 'sitepagedocument_create').'">', '</a>'); ?>
			<?php endif; ?>
		</span>
	</div>	
<?php endif;?>