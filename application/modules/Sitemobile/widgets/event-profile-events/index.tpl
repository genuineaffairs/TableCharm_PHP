<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<div class="sm-content-list">
	<ul id="profile_events" data-role="listview" data-icon="arrow-r">
		<?php foreach ($this->paginator as $event): ?>
			<li>
				<a href="<?php echo $event->getHref(); ?>">
					<?php echo $this->itemPhoto($event, 'thumb.icon'); ?>
					<h3><?php echo $event->getTitle() ?></h3>
					<p><strong><?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()), $this->locale()->toNumber($event->membership()->getMemberCount())) ?></strong></p>
					<p><?php echo $this->locale()->toDateTime($event->starttime) ?></p>
				</a> 
			</li>
		<?php endforeach; ?>
	</ul>
	<?php if ($this->paginator->count() > 1): ?>
		<?php
		echo $this->paginationAjaxControl(
						$this->paginator, $this->identity, 'profile_events');
		?>
	<?php endif; ?>
</div>