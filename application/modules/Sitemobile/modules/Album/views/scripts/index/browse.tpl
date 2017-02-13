<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: browse.tpl 9800 2012-10-17 01:16:09Z richard $
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
  <div class="sm-content-list ui-listgrid-view">
    <ul data-role="listview" data-inset="false" data-icon="arrow-r">
      <?php foreach ($this->paginator as $album): ?>
        <li>
          <a href="<?php echo $album->getHref(); ?>">
            <?php echo $this->itemPhoto($album, 'thumb.icon'); ?>
            <h3><?php echo $this->string()->chunk($this->string()->truncate($album->getTitle(), 45), 10); ?></h3>
            <p class="ui-li-aside"><?php echo $this->locale()->toNumber($album->count()); //echo $this->translate(array('%s photo', '%s photos', $album->count()), $this->locale()->toNumber($album->count()))  ?></p>

            <p><?php echo $this->translate('Posted By'); ?>
              <strong><?php echo $album->getOwner()->getTitle(); ?></strong>
            </p>
          </a> 
        </li>
      <?php endforeach; ?>   
    </ul>
  </div>  
  <?php if ($this->paginator->count() > 1): ?>
    <?php
    echo $this->paginationControl(
            $this->paginator, null, null, array(
        'pageAsQuery' => false,
        'query' => $this->searchParams
    ));
    ?>
  <?php endif; ?>
<?php elseif ($this->searchParams['category_id']): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has created an album with that criteria.'); ?>
      <?php if ($this->canCreate): ?>
        <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'upload')) . '">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>    
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has created an album yet.'); ?>
      <?php if ($this->canCreate): ?>
        <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'upload')) . '">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>