<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
?>

<?php if ($this->paginator->getTotalItemCount()): ?>

  <form id='filter_form_page' class='global_form_box' method='get' action='<?php echo $this->url(array(), 'sitepagenote_browse', true) ?>' style='display: none;'>
    <input type="hidden" id="page" name="page"  value=""/>
  </form>
<div class="sm-content-list">
  <ul data-role="listview" data-inset="false" >
    <?php foreach ($this->paginator as $sitepagenote): ?>
      <?php $this->sitepageSubject = Engine_Api::_()->getItem('sitepage_page', $sitepagenote->page_id); ?>
      <li id="sitepagenote-item-<?php echo $sitepagenote->note_id ?>" data-icon ="arrow-r">
        <a href="<?php echo $sitepagenote->getHref(); ?>">
          <?php if ($sitepagenote->photo_id == 0): ?>
            <?php if ($this->sitepageSubject->photo_id == 0): ?>
              <?php echo $this->itemPhoto($sitepagenote, 'thumb.icon'); ?>
            <?php else: ?>
              <?php echo $this->itemPhoto($this->sitepageSubject, 'thumb.icon'); ?>
            <?php endif; ?>
          <?php else: ?>
            <?php echo $this->itemPhoto($sitepagenote, 'thumb.icon'); ?>
          <?php endif; ?>	
          <h3><?php echo $sitepagenote->getTitle(); ?></h3>
          <p><?php echo $this->translate("in ") ?>
            <strong><?php echo $sitepagenote->page_title ?></strong></p>
          <p><?php echo $this->translate('Posted by') ?>
            <strong><?php echo $sitepagenote->getOwner()->getTitle(); ?></strong></p>
          <p><?php echo $this->timestamp($sitepagenote->creation_date) ?></p>
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
