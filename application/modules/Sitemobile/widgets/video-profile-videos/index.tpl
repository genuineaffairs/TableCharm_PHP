<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<div class="sm-content-list ui-listgrid-view ui-listgrid-view-no-caption" id="profile_videos">
  <ul data-role="listview" data-inset="false" data-icon="arrow-r">
<?php foreach ($this->paginator as $item): ?>
      <li>  
        <a href="<?php echo $item->getHref(); ?>">
  <?php
  if ($item->photo_id) {
    echo $this->itemPhoto($item, 'thumb.profile');
  } else {
    echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Video/externals/images/video.png">';
  }
  ?>
          <div class="ui-listview-play-btn"><i class="ui-icon ui-icon-play"></i></div>
          <div class="ui-list-content">
            <h3><?php echo $item->getTitle() ?></h3>
  <?php if ($item->rating > 0): ?>
              <p class="ui-li-aside-rating"> 
              <?php for ($x = 1; $x <= $item->rating; $x++): ?>
                  <span class="rating_star_generic rating_star"></span>
                <?php endfor; ?>
                <?php if ((round($item->rating) - $item->rating) > 0): ?>
                  <span class="rating_star_generic rating_star_half"></span>
                <?php endif; ?>
              </p>
              <?php endif; ?>
          </div>
            <?php if ($item->duration): ?>
            <p class="ui-li-aside">
            <?php
            if ($item->duration >= 3600) {
              $duration = gmdate("H:i:s", $item->duration);
            } else {
              $duration = gmdate("i:s", $item->duration);
            }
            echo $duration;
            ?>
            </p>
            <?php endif ?>

        </a> 
      </li>
<?php endforeach; ?>
  </ul>
</div>

<?php if ($this->paginator->count() > 1): ?>
  <?php
  echo $this->paginationAjaxControl(
          $this->paginator, $this->identity, 'profile_videos');
  ?>
<?php endif; ?>