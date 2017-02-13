<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manage.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
  <div class="sm-content-list ui-list-manage-page">
    <ul data-role="listview" data-inset="false">
      <?php foreach ($this->paginator as $album): ?>
        <li data-icon="cog" data-inset="true">
          <a href="<?php echo $album->getHref() ?>">
            <?php echo $this->itemPhoto($album, 'thumb.icon') ?>
            <div class="ui-list-content">
              <h3><?php echo $album->getTitle() ?></h3>
            </div>
            <p class="ui-li-aside"><?php echo $this->locale()->toNumber($album->count()) ?></p>
          </a>
          <a href="#manage_<?php echo $album->getGuid() ?>" data-rel="popup"></a>
          <div data-role="popup" id="manage_<?php echo $album->getGuid() ?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15"  data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window">
            <div data-inset="true" style="min-width:150px;" class="sm-options-popup">
              <h3><?php echo $album->getTitle() ?></h3>
              <a href="<?php echo $this->url(array('action' => 'edit', 'album_id' => $album->album_id), 'album_specific', 'true'); ?>" class="ui-btn-default ui-btn-action">
                <?php echo $this->translate('Edit Settings'); ?>
              </a>
              <a href="<?php echo $this->url(array('action' => 'delete', 'album_id' => $album->album_id, 'format' => 'smoothbox'), 'album_specific', 'true'); ?>"  class="smoothbox ui-btn-default ui-btn-danger">
                <?php echo $this->translate('Delete Album'); ?>
              </a>
              <a href="#" data-rel="back" class="ui-btn-default">
                <?php echo $this->translate('Cancel'); ?>
              </a>
            </div> 
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
    <?php if ($this->paginator->count() > 1): ?>
      <?php echo $this->paginationControl($this->paginator, null, null); ?>
    <?php endif; ?>
  </div>	
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('You do not have any albums yet.'); ?>
      <?php if ($this->canCreate): ?>
        <?php echo $this->translate('Get started by %1$screating%2$s your first album!', '<a href="' . $this->url(array('action' => 'upload')) . '">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>