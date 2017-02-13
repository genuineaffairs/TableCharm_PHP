<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<div class="sm-content-list ui-listgrid-view ui-listgrid-view-no-caption" id="profile_albums">
	<ul data-role="listview" data-icon="arrow-r">
		<?php foreach ($this->paginator as $album): ?>
			<li class="sm-ui-browse-items">
				<a href="<?php echo $album->getHref(); ?>">
					<p class="ui-li-aside-show ui-li-aside" style="display:none;"><?php echo $this->locale()->toNumber($album->count())?></p>
					<?php echo $this->itemPhoto($album, 'thumb.icon'); ?>
					<div class="ui-list-content">
            <h3><?php echo $this->string()->chunk($this->string()->truncate($album->getTitle(), 45), 10); ?></h3>
					</div>
					<p class="ui-li-aside"><?php echo $this->locale()->toNumber($album->count())?></p>
				</a> 
			</li>
		<?php endforeach; ?>   
	</ul>
</div>  
<?php if ($this->paginator->count() > 1): ?>
	<?php
		echo $this->paginationAjaxControl(
					$this->paginator, $this->identity, 'profile_albums');
	?>
<?php endif; ?>