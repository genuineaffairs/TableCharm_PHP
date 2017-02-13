<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manage.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php if (($this->current_count >= $this->quota) && !empty($this->quota)): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('You have already created the maximum number of videos allowed. If you would like to post a new video, please delete an old one first.'); ?>
    </span>
  </div>
  <br/>
<?php endif; ?>

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
  <div class="sm-content-list ui-list-manage-page">
    <ul data-role="listview" data-inset="false">
      <?php foreach ($this->paginator as $item): ?>
        <li data-icon="cog" data-inset="true">
          <a href="<?php echo $item->getHref() ?>">
            <?php
            if ($item->photo_id) {
              echo $this->itemPhoto($item, 'thumb.icon');
            } else {
              echo '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Video/externals/images/video.png">';
            }
            ?>
            <div class="ui-icon-play ui-icon ui-listview-play-btn"></div>
            <div class="ui-list-content">
              <h3><?php echo $item->getTitle() ?></h3>
              <?php if ($item->duration): ?>
                <p class="ui-li-aside">
                  <?php
                  if ($item->duration >= 3600) {
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
              <p> 
                <?php echo $this->translate(array('%1$s view', '%1$s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>
              </p>
            </div>
          </a>
          <a href="#manage_<?php echo $item->getGuid() ?>" data-rel="popup"></a>
          <div data-role="popup" id="manage_<?php echo $item->getGuid() ?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15"  data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window">
            <div data-inset="true" style="min-width:150px;" class="sm-options-popup">
              <h3><?php echo $item->getTitle() ?></h3>  
              <a class="ui-btn-default ui-btn-action" href="<?php echo $this->url(array('module' => 'video', 'controller' => 'index', 'action' => 'edit', 'video_id' => $item->video_id), 'default', 'true'); ?>"><?php echo $this->translate('Edit Video') ?></a>
              <?php
              if ($item->status != 2) {
                echo $this->htmlLink(array('route' => 'default', 'module' => 'video', 'controller' => 'index', 'action' => 'delete', 'video_id' => $item->video_id, 'format' => 'smoothbox'), $this->translate('Delete Video'), array(
                    'class' => 'smoothbox ui-btn-default ui-btn-danger'));
              }
              ?>					
              <a href="#" data-rel="back" class="ui-btn-default">
                <?php echo $this->translate('Cancel'); ?>
              </a>
            </div> 
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>	
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('You do not have any videos.'); ?>
      <?php if ($this->can_create): ?>
        <?php echo $this->translate('Get started by %1$sposting%2$s a new video.', '<a href="' . $this->url(array('action' => 'create')) . '">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>