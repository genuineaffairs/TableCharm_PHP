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
	<div class="sm-content-list" id="profile_sitepagepolls">

		<?php if ($this->can_create): ?>
			<div class="seaocore_add" data-role="controlgroup" data-type="horizontal">
				<a data-role="button" data-icon="plus" data-iconpos="left" data-inset = 'false' data-mini="true" data-corners="true" data-shadow="true" href='<?php echo $this->url(array('page_id' => $this->page_id, 'tab' => $this->identity), 'sitepagepoll_create', true) ?>' ><?php echo $this->translate('Create a Poll'); ?></a>
			</div>
		<?php endif; ?>

		<ul data-role="listview" data-icon="arrow-r" >
			<?php foreach ($this->paginator as $item): ?>
				<li>
					<a href="<?php echo $item->getHref(); ?>">
						<h3><?php echo $item->getTitle() ?></h3>
						<p>   
							<?php echo $this->translate('Created by') ?>
							<strong><?php echo $item->getOwner()->getTitle(); ?></strong>
						</p>
						<p>
							<?php echo $this->timestamp(strtotime($item->creation_date)) ?>
						</p>
            <p class="ui-li-aside">
							<b><?php echo $this->translate(array('%s vote', '%s votes', $item->vote_count), $this->locale()->toNumber($item->vote_count)) ?></b>
             </p>
					</a> 
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ($this->paginator->count() > 1): ?>
			<?php
			echo $this->paginationAjaxControl(
							$this->paginator, $this->identity, 'profile_sitepagepolls');
			?>
		<?php endif; ?>

	</div>
<?php else:?>
	<div class="tip">
		<span>
			<?php echo $this->translate('No polls have been created in this Page yet.'); ?>
			<?php if ($this->can_create): ?>
				<?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('page_id' => $this->page_id, 'tab' => $this->identity), 'sitepagepoll_create') . '">', '</a>'); ?>
			<?php endif; ?>
		</span>
 </div>	
<?php endif; ?>