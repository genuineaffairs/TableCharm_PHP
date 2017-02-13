<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
  <div class="sm-content-list">
    <ul data-role="listview" data-inset="false" data-icon="arrow-r">
      <?php foreach ($this->paginator as $blog): ?>
        <li>
          <a href="<?php echo $blog->getHref(); ?>">
            <h3><?php echo $blog->getTitle() ?></h3>
            <p>
              <?php echo $this->translate('Posted by'); ?>
              <strong><?php echo $blog->getOwner()->getTitle(); ?></strong>
            </p>
            <p>
              <?php echo $this->timestamp(strtotime($blog->creation_date)) ?>
            </p>
          </a> 
        </li>
      <?php endforeach; ?>
    </ul>
    <?php
    echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
            //'params' => $this->formValues,
    ));
    ?>
  </div>	
<?php elseif ($this->category || $this->show == 2 || $this->search): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has written a blog entry with that criteria.'); ?>
      <?php if (TRUE): // @todo check if user is allowed to create a poll ?>
        <?php echo $this->translate('Be the first to %1$swrite%2$s one!', '<a href="' . $this->url(array('action' => 'create'), 'blog_general') . '">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has written a blog entry yet.'); ?>
      <?php if ($this->canCreate): ?>
        <?php echo $this->translate('Be the first to %1$swrite%2$s one!', '<a href="' . $this->url(array('action' => 'create'), 'blog_general') . '">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>