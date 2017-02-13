<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
//include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<?php if ($this->paginator->getTotalItemCount()): ?>
  <form id='filter_form_page' class='global_form_box' method='get' action='<?php echo $this->url(array(), 'sitepagepoll_browse', true) ?>' style='display: none;'>
    <input type="hidden" id="page" name="page"  value=""/>
  </form>

<div class="sm-content-list">
  <ul data-role="listview" data-inset="false" >
    <?php foreach ($this->paginator as $sitepagepoll): ?>
      <li data-icon="arrow-r">
        <a href="<?php echo $sitepagepoll->getHref(); ?>">
          <?php $sitepagepoll_object = Engine_Api::_()->getItem('sitepage_page', $sitepagepoll->page_id); ?>
          <?php echo $this->itemPhoto($sitepagepoll->getOwner(), 'thumb.icon'); ?>
          <h3 class="polls_browse_info_title">
            <?php echo $sitepagepoll->getTitle() ?>            
          </h3>
          <p> <?php echo $this->translate("in "); ?><strong><?php echo $sitepagepoll->page_title; ?></strong></p>
          <p>
            <?php echo $this->translate('Created by '); ?><b><?php echo $sitepagepoll->getOwner()->getTitle() ?></b>
          </p>
          <p class ="ui-li-aside">
            <b><?php echo $this->translate(array('%s vote', '%s votes', $sitepagepoll->vote_count), $this->locale()->toNumber($sitepagepoll->vote_count)) ?> </b>
          </p>
            <?php if(false):?>
            -
            <?php echo $this->translate(array('%s view', '%s views', $sitepagepoll->views), $this->locale()->toNumber($sitepagepoll->views)) ?>

            -
            <?php echo $this->translate(array('%s like', '%s likes', $sitepagepoll->like_count), $this->locale()->toNumber($sitepagepoll->like_count)) ?>

            -
            <?php echo $this->translate(array('%s comment', '%s comments', $sitepagepoll->comment_count), $this->locale()->toNumber($sitepagepoll->comment_count)) ?>
            <?php endif; ?>
          
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
  </div>
<?php if( $this->paginator->count() > 1 ): ?>
		<?php echo $this->paginationControl($this->paginator, null, null, array(
			'query' => $this->formValues,
		)); ?>
	<?php endif; ?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no search results to display.'); ?>
    </span>
  </div>
<?php endif; ?>
