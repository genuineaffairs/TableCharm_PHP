<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<div class="sm-content-list">
	<ul id="profile_groups" data-role="listview" data-icon="arrow-r">
		<?php foreach ($this->paginator as $group): ?>
			<li>
				<a href="<?php echo $group->getHref(); ?>">
					<?php echo $this->itemPhoto($group, 'thumb.normal'); ?>
					<h3><?php echo $group->getTitle() ?></h3>
					<p><strong> <?php echo $this->translate(array('%s member', '%s members', $group->member_count), $this->locale()->toNumber($group->member_count)) ?></strong></p>
				</a> 
			</li>
		<?php endforeach; ?>
	</ul>
	<?php if ($this->paginator->count() > 1): ?>
		<?php
		echo $this->paginationAjaxControl(
						$this->paginator, $this->identity, 'profile_groups');
		?>
	<?php endif; ?>
</div>	