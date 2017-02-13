<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: browse.tpl 9785 2012-09-25 08:34:18Z pamela $
 * @author     John Boehr <john@socialengine.com>
 */
?>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
	<div class="sm-content-list ui-listgrid-view">
		<ul data-role="listview" data-inset="false" data-icon="arrow-r">
		  <?php foreach( $this->paginator as $item ): ?>
				<li>  
					<a href="<?php echo $item->getHref(); ?>">
					<?php
						if( $item->photo_id ) {
							echo $this->itemPhoto($item, 'thumb.profile');
						} else {
							echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Video/externals/images/video.png">';
						}
					?>
					<div class="ui-listview-play-btn"><i class="ui-icon ui-icon-play"></i></div>
					<h3><?php echo $item->getTitle() ?></h3>
					<?php if( $item->duration ): ?>
						<p class="ui-li-aside">
							<?php
								if( $item->duration >= 3600 ) {
									$duration = gmdate("H:i:s", $item->duration);
								} else {
									$duration = gmdate("i:s", $item->duration);
								}
								//$duration = ltrim($duration, '0:');
			//              if( $duration[0] == '0' ) {
			//                $duration= substr($duration, 1);
			//              }
								echo $duration;
							?>
						</p>
					<?php endif ?>
          <?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $item->page_id); ?>
					<p><?php echo $this->translate('in'); ?>
						<strong><?php echo $sitepage_object->title;  ?></strong>
					</p>
				<!--	<p> 
						<?php //echo $this->translate(array('%1$s view', '%1$s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>
					</p>-->
					<p class="ui-li-aside-rating"> 
						<?php if( $item->rating > 0 ): ?>
							<?php for( $x=1; $x<=$item->rating; $x++ ): ?>
								<span class="rating_star_generic rating_star"></span>
							<?php endfor; ?>
							<?php if( (round($item->rating) - $item->rating) > 0): ?>
								<span class="rating_star_generic rating_star_half"></span>
							<?php endif; ?>
						<?php endif; ?>
					</p>
					</a> 
				</li>
		  <?php endforeach; ?>
		</ul>
	</div>
	<?php echo $this->paginationControl($this->paginator, null, null, array(
			'query' => $this->formValues,
			'pageAsQuery' => true,
		)); ?>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no search results to display.');?>
    </span>
  </div>
<?php endif; ?>