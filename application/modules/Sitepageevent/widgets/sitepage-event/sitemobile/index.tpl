<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: browse.tpl 9785 2012-09-25 08:34:18Z pamela $
 * @author     John Boehr <john@socialengine.com>
 */  
?>

<?php if( count($this->paginator) > 0 ): ?>
  <form id='filter_form_page' class='global_form_box' method='get' action='<?php echo $this->url(array(), 'sitepageevent_browse', true) ?>' style='display: none;'>
    <input type="hidden" id="page" name="page"  value=""/>
  </form>
<div class="sm-content-list">
<!--<h3 class="sitepage_mypage_head"><?php echo $this->translate('Events');?></h3>-->
	<ul data-role="listview" data-icon="arrow-r">
		<?php foreach( $this->paginator as $sitepageevent ): ?>
			<li class="sm-ui-browse-items">
				<a href="<?php echo $sitepageevent->getHref();?>">
					<?php echo $this->itemPhoto($sitepageevent, 'thumb.icon'); ?>
					<h3><?php echo $sitepageevent->getTitle() ?></h3>
             <p><?php echo $this->translate("in ") ?>
            <strong><?php echo $sitepageevent->page_title ?></strong></p>
					<p>
						<?php echo $this->translate(array('%s guest', '%s guests', $sitepageevent->membership()->getMemberCount()), $this->locale()->toNumber($sitepageevent->membership()->getMemberCount())) ?>
						<?php echo $this->translate('led by') ?>
						<strong><?php echo $sitepageevent->getOwner()->getTitle(); ?></strong>
					</p>
					<p><?php echo $this->locale()->toDateTime($sitepageevent->starttime) ?></p>
				</a> 
			</li>
		<?php endforeach; ?>
	</ul>
</div>
	<?php if( $this->paginator->count() > 1 ): ?>
		<?php echo $this->paginationControl($this->paginator, null, null, array(
			'query' => $this->formValues,
		)); ?>
	<?php endif; ?>
<?php else: ?>
  <div class="tip">
    <span>
			<?php echo $this->translate('There are no search results to display.') ?>
    </span>
  </div>
<?php endif; ?>
