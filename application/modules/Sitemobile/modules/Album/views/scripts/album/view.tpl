<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: view.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<div class="ui-page-content">
  <?php if ('' != trim($this->album->getDescription())): ?>
    <div class="sm-ui-cont-cont-des">
      <?php echo $this->album->getDescription() ?>
    </div>
  <?php endif ?>
  <ul class="thumbs thumbs_nocaptions">
    <?php foreach ($this->paginator as $photo): ?>
      <li>
        <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
          <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
  <?php if ($this->paginator->count() > 0): ?>
    <?php echo $this->paginationControl($this->paginator); ?>
  <?php endif; ?>
</div>