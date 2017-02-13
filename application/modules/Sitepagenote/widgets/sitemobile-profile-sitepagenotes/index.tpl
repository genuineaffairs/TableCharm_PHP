<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if ($this->paginator->getTotalItemCount() > 0): ?>

	<div class="sm-content-list" id="profile_sitepagenotes">

		<?php if ($this->can_create): ?>
			<div class="seaocore_add" data-role="controlgroup" data-type="horizontal">
				<a data-role="button" data-icon="plus" data-iconpos="left" data-inset = 'false' data-mini="true" data-corners="true" data-shadow="true" href='<?php echo $this->url(array('page_id' => $this->sitepageSubject->page_id, 'tab' => $this->identity), 'sitepagenote_create', true) ?>' ><?php echo $this->translate('Write a Note'); ?></a>
			</div>
		<?php endif; ?>

		<ul data-role="listview" data-icon="arrow-r">
			<?php foreach ($this->paginator as $item): ?>
				<li>
					<a href="<?php echo $item->getHref(); ?>">
						<?php if ($item->photo_id == 0): ?>
							<?php if ($this->sitepageSubject->photo_id == 0): ?>
								<?php echo $this->itemPhoto($item, 'thumb.icon', $item->getTitle()) ?>   
							<?php else: ?>
								<?php echo $this->itemPhoto($this->sitepageSubject, 'thumb.icon', $item->getTitle()) ?>
							<?php endif; ?>
						<?php else: ?>
							<?php echo $this->itemPhoto($item, 'thumb.icon', $item->getTitle()) ?>
						<?php endif; ?>
						<h3><?php echo $item->getTitle() ?></h3>
						<p>   
							<?php echo $this->translate('Posted by') ?>
							<strong><?php echo $item->getOwner()->getTitle(); ?></strong>
						</p>
						<p>
							<?php echo $this->timestamp(strtotime($item->creation_date)) ?>
						</p>
					</a> 
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ($this->paginator->count() > 1): ?>
			<?php
			echo $this->paginationAjaxControl(
							$this->paginator, $this->identity, 'profile_sitepagenotes');
			?>
		<?php endif; ?>

	</div>
<?php else:?>

	<div class="tip">
		<span>
			<?php echo $this->translate('No notes have been written in this Page yet.'); ?>
			<?php if ($this->can_create): ?>
				<?php echo $this->translate('Be the first to %1$swrite%2$s one!', '<a href="' . $this->url(array('page_id' => $this->sitepageSubject->page_id, 'tab' => $this->identity), 'sitepagenote_create') . '">', '</a>'); ?>
			<?php endif; ?>
		</span>
	</div>

<?php endif; ?>