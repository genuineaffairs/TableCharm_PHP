<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manage.tpl 9800 2012-10-17 01:16:09Z richard $
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
    <ul data-role="listview" data-inset="false">
      <?php foreach ($this->paginator as $blog): ?>
        <li data-icon="cog" data-inset="true">
          <a href="<?php echo $blog->getHref(); ?>">
            <h3><?php echo $blog->getTitle() ?></h3>
            <p>   
              <?php echo $this->translate('Posted by') ?>
              <strong><?php echo $blog->getOwner()->getTitle(); ?></strong>
            </p>
            <p>
              <?php echo $this->timestamp(strtotime($blog->creation_date)) ?>
            </p>
          </a>
          <a href="#manage_<?php echo $blog->getGuid() ?>" data-rel="popup"></a>
          <div data-role="popup" id="manage_<?php echo $blog->getGuid() ?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15"  data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window">
            <div data-inset="true" style="min-width:150px;" class="sm-options-popup">
              <h3><?php echo $blog->getTitle() ?></h3>
              <?php
              echo $this->htmlLink(array(
                  'action' => 'edit',
                  'blog_id' => $blog->getIdentity(),
                  'route' => 'blog_specific',
                  'reset' => true,
                      ), $this->translate('Edit Entry'), array(
                  'class' => 'ui-btn-default ui-btn-action'
              ))
              ?>
              <?php
              echo $this->htmlLink(array('route' => 'default', 'module' => 'blog', 'controller' => 'index', 'action' => 'delete', 'blog_id' => $blog->getIdentity()), $this->translate('Delete Entry'), array(
                  'class' => 'smoothbox ui-btn-default ui-btn-danger',
              ));
              ?>            
              <a href="#" data-rel="back" class="ui-btn-default">
    <?php echo $this->translate('Cancel'); ?>
              </a>
            </div> 
          </div>
        </li>
    <?php endforeach; ?>
    </ul>
    <?php
    echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
    ));
    ?>
  </div>	
    <?php elseif ($this->search): ?>
  <div class="tip">
    <span>
  <?php echo $this->translate('You do not have any blog entries that match your search criteria.'); ?>
    </span>
  </div>
    <?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('You do not have any blog entries.'); ?>
  <?php if ($this->canCreate): ?>
    <?php echo $this->translate('Get started by %1$swriting%2$s a new entry.', '<a href="' . $this->url(array('action' => 'create'), 'blog_general') . '">', '</a>'); ?>
  <?php endif; ?>
    </span>
  </div>
<?php endif; ?>