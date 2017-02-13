<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: tag.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/css.php?request=/application/modules/Sitepagenote/externals/styles/style_sitepagenote.css')
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Sitepagenote/views/scripts/pageNoteHeader.tpl';
?>

<div class='layout_right'>
  <?php echo $this->form->render($this) ?>
  <?php if ($this->canCreatenote): ?>
    <div class="quicklinks">
      <ul>
        <li>
          <?php
          echo $this->htmlLink(array('route' => 'sitepagenote_create', 'page_id' => $this->sitepage->page_id, 'tab' => $this->tab_selected_id), $this->translate('Write a Note'), array(
              'class' => 'buttonlink icon_sitepagenote_new'
          ))
          ?>
      </ul>
    </div>
  <?php endif; ?>
  <br />
</div>

<div class='layout_middle'>
  <?php if ($this->tag): ?>
    <div class="sitepagepage_note_tag_options">
      <?php echo $this->translate('Notes using the tag') ?>
      #<?php echo $this->tag ?>
      <a href="<?php echo $this->url(array('tab' => $this->tab_selected_id, 'page_id' => $this->sitepage->page_id), 'sitepagenote_tagcreate', true); ?>">(x)</a>
    </div>
  <?php endif; ?>

  <?php if ($this->paginator->getTotalItemCount() > 0): ?>
  <ul class="seaocore_browse_list" id='sitepagenote_search'>
    <?php foreach ($this->paginator as $sitepagenote): ?>
    <li>
      <div class="seaocore_browse_list_photo">
        <?php if ($sitepagenote->photo_id == 0): ?>
          <?php if ($this->sitepage->photo_id == 0): ?>
            <?php echo $this->htmlLink($sitepagenote->getHref(), $this->itemPhoto($sitepagenote, 'thumb.normal', $sitepagenote->getTitle())) ?>   
          <?php else: ?>
            <?php echo $this->htmlLink($sitepagenote->getHref(), $this->itemPhoto($this->sitepage, 'thumb.normal', $sitepagenote->getTitle())) ?>
          <?php endif; ?>
        <?php else: ?>			   
          <?php echo $this->htmlLink($sitepagenote->getHref(), $this->itemPhoto($sitepagenote, 'thumb.normal', $sitepagenote->getTitle())) ?>
        <?php endif; ?>
      </div> 				
      <div class="seaocore_browse_list_info">
        <div class="seaocore_browse_list_info_title">
          <h3><?php echo $this->htmlLink($sitepagenote->getHref(), $sitepagenote->title) ?></h3>
        </div>
        <div class="seaocore_browse_list_info_date">
          <?php echo $this->translate('Posted by %s', $this->htmlLink($sitepagenote->getOwner(), $sitepagenote->getOwner()->getTitle())) ?>
          <?php echo $this->timestamp($sitepagenote->creation_date) ?>
              -
          <?php echo $this->translate(array('%s view', '%s views', $sitepagenote->view_count), $this->locale()->toNumber($sitepagenote->view_count)) ?>
              -
          <?php echo $this->translate(array('%s comment', '%s comments', $sitepagenote->comment_count), $this->locale()->toNumber($sitepagenote->comment_count)) ?>
              -
        <?php echo $this->translate(array('%s like', '%s likes', $sitepagenote->like_count), $this->locale()->toNumber($sitepagenote->like_count)) ?>
        </div>
        <?php if (!empty($sitepagenote->body)): ?>
        <div class="seaocore_browse_list_info_blurb">
          <?php
          $sitepagenote_body = strip_tags($sitepagenote->body);
          $sitepagenote_body = Engine_String::strlen($sitepagenote_body) > 200 ? Engine_String::substr($sitepagenote_body, 0, 200) . '..' : $sitepagenote_body;
          ?>
          <?php echo $sitepagenote_body ?>
        </div>
        <?php endif; ?>
      </div>
    </li>
    <?php endforeach; ?>
  </ul>
  <?php elseif ($this->paginator->count() <= 0): ?>	
    <div class="tip" id='sitepagennote_search'>
      <span>
        <?php echo $this->translate('No notes were found matching your search criteria.'); ?>
      </span>
    </div>
  <?php endif; ?>
  <?php
  echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->formValues,
      'pageAsQuery' => true,
  ));
  ?>
</div>