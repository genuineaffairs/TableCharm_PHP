<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<h2><?php echo $this->translate('Search') ?></h2>
<div id="searchform" class="sm-ui-search">
  <?php echo $this->form->render($this) ?>
</div>
<?php if (empty($this->paginator)): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Please enter a search query.') ?>
    </span>
  </div>
<?php elseif ($this->paginator->getTotalItemCount() <= 0): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No results were found.') ?>
    </span>
  </div>
<?php else: ?>
  <div class="sm-ui-search-result">
    <?php echo $this->translate(array('%s result found', '%s results found', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
  </div>
  <div class="sm-ui-search-result-list">                       
    <ul data-role="listview" data-icon="arrow-r">
  <?php
  foreach ($this->paginator as $item):
    $item = $this->item($item->type, $item->id);
    if (!$item)
      continue;
    ?>
        <li>
          <a href="<?php echo $item->getHref(); ?>">
              <?php echo $this->itemPhoto($item, 'thumb.icon'); ?>
            <h3>
              <?php if ('' != $this->query): ?>
                <?php echo $this->highlightText($item->getTitle(), $this->query) ?>
              <?php else: ?>
                <?php echo $item->getTitle() ?>
    <?php endif; ?>
            </h3>
            <p>
              <?php if ('' != $this->query): ?>
                <?php echo $this->highlightText($item->getDescription(), $this->query); ?>
              <?php else: ?>
                <?php echo $item->getDescription(); ?>
    <?php endif; ?>
            </p>
          </a> 
        </li>
  <?php endforeach; ?>
    </ul>
  </div>
  <div>
    <?php
    echo $this->paginationControl($this->paginator, null, null, array(
        'query' => array(
            'query' => $this->query,
            'type' => $this->type,
        ),
    ));
    ?>
  </div>
<?php endif; ?>