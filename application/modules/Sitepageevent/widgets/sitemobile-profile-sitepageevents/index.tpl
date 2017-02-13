<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
?>

<?php if($this->paginator->getTotalItemCount() > 0) :?>
	<div class="sm-content-list" id="profile_sitepageevents">
<?php if($this->can_create): ?>
		<div data-role="controlgroup" data-type="horizontal">
			<a data-role="button" data-icon="plus" data-iconpos="left" data-inset = 'false' data-mini="true" data-corners="true" data-shadow="true" href='<?php echo $this->url(array('page_id' => $this->sitepage_subject->page_id, 'tab_id' => $this->identity), 'sitepageevent_create', true) ?>' class='buttonlink icon_sitepageevent_new'><?php echo $this->translate('Create_an_Event'); ?></a>
		</div>
<?php endif; ?>	
		<ul data-role="listview" data-icon="arrow-r">
			<?php foreach ($this->paginator as $item): ?>
				<li>
					<a href="<?php echo $item->getHref(); ?>">
						<?php echo $this->itemPhoto($item, 'thumb.icon'); ?>
						<h3><?php echo $item->getTitle() ?></h3>
						<p><?php echo $this->translate('Led by ') ?><strong><?php echo $item->getOwner()->getTitle(); ?></strong> - 
						<strong><?php echo $this->translate(array('%s guest', '%s guests', $item->membership()->getMemberCount()), $this->locale()->toNumber($item->membership()->getMemberCount())) ?></strong></p>
						<p>      <?php
      // Convert the dates for the viewer
      $startDateObject = new Zend_Date(strtotime($item->starttime));
      $endDateObject = new Zend_Date(strtotime($item->endtime));
      if ($this->viewer() && $this->viewer()->getIdentity()) {
        $tz = $this->viewer()->timezone;
        $startDateObject->setTimezone($tz);
        $endDateObject->setTimezone($tz);
      }
      ?>
      <?php if ($item->starttime == $item->endtime): ?>


          <?php echo $this->locale()->toDate($startDateObject) ?>

 - 

          <?php echo $this->locale()->toTime($startDateObject) ?>


      <?php elseif ($startDateObject->toString('y-MM-dd') == $endDateObject->toString('y-MM-dd')): ?>


          <?php echo $this->locale()->toDate($startDateObject) ?>

 - 

          <?php echo $this->locale()->toTime($startDateObject) ?>
          -
          <?php echo $this->locale()->toTime($endDateObject) ?>

      <?php else: ?>  

          <?php
          echo $this->translate('%1$s at %2$s', $this->locale()->toDate($startDateObject), $this->locale()->toTime($startDateObject)
          )
          ?>
	- 
          <?php
          echo $this->translate('%1$s at %2$s', $this->locale()->toDate($endDateObject), $this->locale()->toTime($endDateObject)
          )
          ?>

      <?php endif ?></p>
					</a> 
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ($this->paginator->count() > 1): ?>
			<?php
			echo $this->paginationAjaxControl(
							$this->paginator, $this->identity, 'profile_sitepageevents');
			?>
		<?php endif; ?>
  </div>
<?php else:?>

	<div class="tip">
		<span>
			<?php echo $this->translate('No events have been created in this Page yet.'); ?>
			<?php if ($this->can_create): ?>
				<?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('page_id' => $this->sitepage_subject->page_id, 'tab_id' => $this->identity), 'sitepageevent_create') . '">', '</a>'); ?>
			<?php endif; ?>
		</span>	
 </div>	

<?php endif; ?>