<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  function documentRate(rating, document_id) {
    $.mobile.activePage.find('#rating_text').html("<?php echo $this->translate('Thank you for rating to this document!'); ?>");
    for (var x = 1; x <= 5; x++) {
      $.mobile.activePage.find('#rate_' + x).attr('onclick', '');
    }
    sm4.core.request.send({
      type: "POST",
      dataType: "json",
      'url': '<?php echo $this->url(array('module' => 'document', 'controller' => 'index', 'action' => 'rating'), 'default', true) ?>',
      'data': {
        'format': 'json',
        'rating': rating,
        'document_id': document_id
      },
      beforeSend: function() {
        $.mobile.activePage.data('document_rated', 1);
        var document_total_rating = $.mobile.activePage.data('document_total_rating');
        document_total_rating = document_total_rating + 1;
        var document_rate_previous = ($.mobile.activePage.data('document_rate_previous') + rating) / document_total_rating;
        $.mobile.activePage.data('document_total_rating', document_total_rating);
        $.mobile.activePage.data('document_rate_previous', document_rate_previous);
        document_set_rating();
      },
      success: function(response)
      {
        $.mobile.activePage.find('#rating_text').html(response[0].total + '<?php echo $this->string()->escapeJavascript($this->translate(" rating")) ?>');
        $.mobile.activePage.data('current_total_rate', response[0].total);
      }
    });

  }

  function document_rating_out() { //alert($.mobile.activePage.find('#rating_text').html());
    $.mobile.activePage.find('#rating_text').html(" <?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count), $this->locale()->toNumber($this->rating_count)) ?>");
    if ($.mobile.activePage.data('document_rate_previous') !== 0) {
      document_set_rating();
    }
    else {
      for (var x = 1; x <= 5; x++) {
        $.mobile.activePage.find('#rate_' + x).attr('class', 'rating_star_big_generic rating_star_big_disabled');
      }
    }
  }

  function document_set_rating() {
    var rating = $.mobile.activePage.data('document_rate_previous');
    var current_total_rate = $.mobile.activePage.data('current_total_rate');
    if (current_total_rate) {
      var current_total_rate = $.mobile.activePage.data('current_total_rate');
      if (current_total_rate === 1) {
        $.mobile.activePage.find('#rating_text').html(current_total_rate + '<?php echo $this->string()->escapeJavascript($this->translate(" rating")) ?>');
      }
      else {
        $.mobile.activePage.find('#rating_text').html(current_total_rate + '<?php echo $this->string()->escapeJavascript($this->translate(" rating")) ?>');
      }
    }
    else {
      $.mobile.activePage.find('#rating_text').html("<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count), $this->locale()->toNumber($this->rating_count)) ?>");
    }
    
    for (var x = 1; x <= parseInt(rating); x++) {
      $.mobile.activePage.find('#rate_' + x).attr('class', 'rating_star_big_generic rating_star_big');
    }

    for (var x = parseInt(rating) + 1; x <= 5; x++) {
      $.mobile.activePage.find('#rate_' + x).attr('class', 'rating_star_big_generic rating_star_big_disabled');
    }

    var remainder = Math.round(rating) - rating;
    if (remainder <= 0.5 && remainder != 0) {
      var last = parseInt(rating) + 1;
      $.mobile.activePage.find('#rate_' + last).attr('class', 'rating_star_big_generic rating_star_big_half');
    }
  }

  function document_rating_over(rating) {
    if ($.mobile.activePage.data('document_rated') == 1) {
      $.mobile.activePage.find('#rating_text').html("<?php echo $this->translate('you have already rated this document'); ?>");
    }
    else if ( <?php echo $this->viewer_id; ?> === 0) {
      $.mobile.activePage.find('#rating_text').html("<?php echo $this->translate('Only logged-in user can rate'); ?>");
    }
    else {
      $.mobile.activePage.find('#rating_text').html("<?php echo $this->translate('Please click to rate'); ?>");
      for (var x = 1; x <= 5; x++) {
        if (x <= rating) {
          $.mobile.activePage.find('#rate_' + x).attr('class', 'rating_star_big_generic rating_star_big');
        } else {
          $.mobile.activePage.find('#rate_' + x).attr('class', 'rating_star_big_generic rating_star_big_disabled');
        }
      }
    }
  }
  sm4.core.runonce.add(function() {
    $.mobile.activePage.data('document_rate_previous',<?php echo $this->document->rating; ?>);
    $.mobile.activePage.data('document_rated', '<?php echo $this->document_rated; ?>');
    $.mobile.activePage.data('document_total_rating',<?php echo $this->rating_count; ?>);
    document_set_rating();
  });
</script>
<div class="ui-page-content">
  <div class="sm-ui-cont-head">
    <div class="sm-ui-cont-cont-info">
      <div class="sm-ui-cont-author-name">
        <?php echo $this->document->getTitle(); ?> 
      </div>
      <?php if (!empty($this->viewer_id) && $this->can_rate == 1): ?>
        <div id="document_rating" class="" onmouseout="document_rating_out();">
          <span id="rate_1" <?php if (!$this->document_rated && $this->viewer_id): ?>onclick="documentRate(1,<?php echo $this->document->document_id; ?>);"<?php endif; ?> onmouseover="document_rating_over(1);"></span>
          <span id="rate_2" <?php if (!$this->document_rated && $this->viewer_id): ?>onclick="documentRate(2,<?php echo $this->document->document_id; ?>);"<?php endif; ?> onmouseover="document_rating_over(2);"></span>
          <span id="rate_3" <?php if (!$this->document_rated && $this->viewer_id): ?>onclick="documentRate(3,<?php echo $this->document->document_id; ?>);"<?php endif; ?> onmouseover="document_rating_over(3);"></span>
          <span id="rate_4" <?php if (!$this->document_rated && $this->viewer_id): ?>onclick="documentRate(4,<?php echo $this->document->document_id; ?>);"<?php endif; ?> onmouseover="document_rating_over(4);"></span>
          <span id="rate_5" <?php if (!$this->document_rated && $this->viewer_id): ?>onclick="documentRate(5,<?php echo $this->document->document_id; ?>);"<?php endif; ?> onmouseover="document_rating_over(5);"></span>
          <span id="rating_text" class="rating_text"><?php echo $this->translate('click to rate'); ?></span>
        </div>
      <?php endif; ?>	
      <div class="sm-ui-cont-cont-date">
        <?php echo $this->translate('Created by %s', $this->document->getOwner()->toString()); ?>
        -
        <?php echo $this->timestamp($this->document->creation_date); ?>
      </div>
      <div class="sm-ui-cont-cont-date">
        <?php if ($this->category_name != '' && $this->subcategory_name == '') : ?>
          <?php echo $this->translate('Category:'); ?>
          <?php echo $this->htmlLink($this->url(array('category_id' => $this->document->category_id, 'categoryname' => $this->categoryTable->getCategorySlug($this->category_name)), 'document_browse'), $this->translate($this->category_name)) ?>
        <?php elseif ($this->category_name != '' && $this->subcategory_name != ''): ?> 
          <?php echo $this->translate('Category:'); ?>
          <?php echo $this->htmlLink($this->url(array('category_id' => $this->document->category_id, 'categoryname' => $this->categoryTable->getCategorySlug($this->category_name)), 'document_browse'), $this->translate($this->category_name)) ?>
          <?php
          if (!empty($this->category_name)): echo '&raquo;';
          endif;
          ?>      
          <?php echo $this->htmlLink($this->url(array('category_id' => $this->document->category_id, 'categoryname' => $this->categoryTable->getCategorySlug($this->category_name), 'subcategory_id' => $this->document->subcategory_id, 'subcategoryname' => $this->categoryTable->getCategorySlug($this->subcategory_name)), 'document_browse'), $this->translate($this->subcategory_name)) ?>
          <?php if (!empty($this->subsubcategory_name)): echo '&raquo;'; ?>
            <?php echo $this->htmlLink($this->url(array('category_id' => $this->document->category_id, 'categoryname' => $this->categoryTable->getCategorySlug($this->category_name), 'subcategory_id' => $this->document->subcategory_id, 'subcategoryname' => $this->categoryTable->getCategorySlug($this->subcategory_name), 'subsubcategory_id' => $this->document->subsubcategory_id, 'subsubcategoryname' => $this->categoryTable->getCategorySlug($this->subsubcategory_name)), 'document_browse'), $this->translate($this->subsubcategory_name)) ?>
          <?php endif; ?>
        <?php endif; ?>

        <?php if (count($this->documentTags)): ?>
          - <?php echo $this->translate("Tags:"); ?> 
          <?php foreach ($this->documentTags as $tag): ?>
            <a href= "<?php echo $this->url(array('action' => 'browse', 'tag' => $tag->getTag()->tag_id), 'document_browse', true) ?>" >#<?php echo $tag->getTag()->text ?></a>&nbsp;
          <?php endforeach; ?>
<?php endif; ?>
      </div>
      <div class="sm-ui-cont-cont-date">
        <?php echo $this->translate(array('%s comment', '%s comments', $this->document->comment_count), $this->locale()->toNumber($this->document->comment_count)) ?>,
        <?php echo $this->translate(array('%s view', '%s views', $this->document->views), $this->locale()->toNumber($this->document->views)) ?>,
<?php echo $this->translate(array('%s like', '%s likes', $this->document->like_count), $this->locale()->toNumber($this->document->like_count)) ?>
      </div>
    </div>
  </div>

  <div class="sm-ui-cont-cont-des">
<?php echo $this->document->document_description; ?>
  </div>

  <?php $custom_field_values = $this->fieldValueLoop($this->document, $this->fieldStructure); ?>
  <?php echo htmlspecialchars_decode($custom_field_values); ?>

<?php if ($this->document->status == 1): ?>

    <!--The document full text comes here if downloading has been enabled for this document-->
    <?php if (!empty($this->viewer_id)): ?>
      <?php if ($this->link && $this->doc_full_text): ?>
        <noscript>
        <?php echo $this->doc_full_text; ?>
        </noscript>
      <?php endif; ?>
    <?php else: ?>
      <?php if ($this->doc_full_text): ?>
        <noscript>
        <?php echo $this->doc_full_text; ?>
        </noscript>
      <?php endif; ?>
  <?php endif; ?>

    <div id='embedded_doc_<?php echo $this->document->getGuid() ?>' class="document_view_ipaper"><a href="http://www.scribd.com"></a></div>
    <script type="text/javascript">
    sm4.core.runonce.add(function() {
      var scribd_doc = scribd.Document.getDoc('<?php echo $this->document->doc_id; ?>', '<?php echo $this->document->access_key; ?>');
  <?php if ((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) == 'https'): ?>
        scribd_doc.addParam('use_ssl', true);
  <?php endif; ?>
      scribd_doc.addParam('jsapi_version', 2);
      scribd_doc.addParam('default_embed_format', 'html5');
      var height = $('body').width() - 25;
      if (height > <?php echo $this->documentViewerHeight; ?>)
        height =<?php echo $this->documentViewerHeight; ?>;
      scribd_doc.addParam('height', height);
      scribd_doc.addParam('width', '100%');
  <?php if (!empty($this->document->secure_allow)): ?>
        scribd_doc.addParam("use_ssl", 'true');
        scribd_doc.grantAccess('<?php echo $this->uid; ?>', '<?php echo $this->sessionId; ?>', '<?php echo $this->signature; ?>');
  <?php endif; ?>
      scribd_doc.write('embedded_doc_<?php echo $this->document->getGuid() ?>');
    });
    </script>

    <?php elseif ($this->document->status == 0): ?>
    <div class="document_alert_box">
      <?php echo $this->htmlImage('application/modules/Document/externals/images/document_alert.png', '', array('style' => 'vertical-align:middle;margin-right:5px;', 'border' => '0')) ?>
    <?php echo $this->translate('Document format conversion in progress.'); ?>
    </div>
    <?php elseif ($this->document->status == 3 && $this->document->owner_id == $this->viewer_id): ?>
    <div class="document_alert_box">
      <?php echo $this->htmlImage('application/modules/Document/externals/images/document_alert.png', '', array('style' => 'vertical-align:middle;margin-right:5px;', 'border' => '0')) ?>
  <?php echo $this->translate('This document has been deleted at Scribd. Please ') . $this->htmlLink(array('route' => 'document_delete', 'document_id' => $this->document->document_id), $this->translate('Delete')) . $this->translate(' this document or ') . $this->htmlLink(array('route' => 'document_edit', 'document_id' => $this->document->document_id), $this->translate('Edit')) . $this->translate(' it to upload a new file.') ?>
    </div>	 

    <?php elseif ($this->document->status == 3 && $this->document->owner_id != $this->viewer_id): ?>
    <div class="document_alert_box">
      <?php echo $this->htmlImage('application/modules/Document/externals/images/document_alert.png', '', array('style' => 'vertical-align:middle;margin-right:5px;', 'border' => '0')) ?>	
  <?php echo $this->translate('This document has been deleted at Scribd'); ?>
    </div>	

    <?php elseif ($this->document->status == 2 && $this->document->owner_id == $this->viewer_id): ?>	
    <div class="document_alert_box">
      <?php echo $this->htmlImage('application/modules/Document/externals/images/document_alert.png', '', array('style' => 'vertical-align:middle;margin-right:5px;', 'border' => '0')) ?>
    <?php echo $this->translate('Format conversion for this document failed. Please ') . $this->htmlLink(array('route' => 'document_delete', 'document_id' => $this->document->document_id), $this->translate('Delete')) . $this->translate(' this document or ') . $this->htmlLink(array('route' => 'document_edit', 'document_id' => $this->document->document_id), $this->translate('Edit')) . $this->translate(' it to upload a new file.') ?>
    </div>	
    <?php elseif ($this->document->status == 2 && $this->document->owner_id != $this->viewer_id): ?>
    <div class="document_alert_box">
      <?php echo $this->htmlImage('application/modules/Document/externals/images/document_alert.png', '', array('style' => 'vertical-align:middle;margin-right:5px;', 'border' => '0')) ?>	
    <?php echo $this->translate('Format conversion for this document failed.'); ?>
    </div>
<?php endif; ?>

  <div class="documents_user_options">
    <p>
      <span class="documents_license">
<?php if ($this->document->document_license == 'by-nc'): ?>
          <a rel="license" href="http://creativecommons.org/licenses/by-nc/3.0/" target="_blank" ><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-nc/3.0/88x31.png" align="right" /></a><br /><?php echo $this->translate('This work is licensed under a '); ?><a rel="license" href="http://creativecommons.org/licenses/by-nc/3.0/" target="_blank"><?php echo $this->translate('Creative Commons Attribution-Noncommercial 3.0 Unported License.'); ?></a>

<?php elseif ($this->document->document_license == 'by-nc-nd'): ?>
          <a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/3.0/" target="_blank"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-nd/3.0/88x31.png" align="right" /></a><br /><?php echo $this->translate('This work is licensed under a '); ?><a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/3.0/" target="_blank"><?php echo $this->translate('Creative Commons Attribution-Noncommercial-No Derivative Works 3.0 Unported License.'); ?></a>

<?php elseif ($this->document->document_license == 'by-nc-sa'): ?>
          <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/" target="_blank"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-sa/3.0/88x31.png" align="right" /></a><br /><?php echo $this->translate('This work is licensed under a '); ?><a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/" target="_blank"><?php echo $this->translate('Creative Commons Attribution-Noncommercial-Share Alike 3.0 Unported License.'); ?></a>

<?php elseif ($this->document->document_license == 'by-nd'): ?>
          <a rel="license" href="http://creativecommons.org/licenses/by-nd/3.0/" target="_blank"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-nd/3.0/88x31.png" align="right" /></a><br /><?php echo $this->translate('This work is licensed under a '); ?><a rel="license" href="http://creativecommons.org/licenses/by-nd/3.0/" target="_blank"><?php echo $this->translate('Creative Commons Attribution-No Derivative Works 3.0 Unported License.'); ?></a>

<?php elseif ($this->document->document_license == 'by-sa'): ?>
          <a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/" target="_blank"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-sa/3.0/88x31.png" align="right" /></a><br /><?php echo $this->translate('This work is licensed under a '); ?><a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/" target="_blank"><?php echo $this->translate('Creative Commons Attribution-Share Alike 3.0 Unported License.'); ?></a>

<?php elseif ($this->document->document_license == 'by'): ?>
          <a rel="license" href="http://creativecommons.org/licenses/by/3.0/" target="_blank"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by/3.0/88x31.png" align="right" /></a><br /><?php echo $this->translate('This work is licensed under a '); ?><a rel="license" href="http://creativecommons.org/licenses/by/3.0/" target="_blank"><?php echo $this->translate('Creative Commons Attribution 3.0 Unported License.'); ?></a>

        <?php elseif ($this->document->document_license == 'pd'): ?>
          <?php echo $this->translate('This document has been released into the public domain.'); ?>

        <?php elseif ($this->document->document_license == 'c'): ?>
          <?php echo $this->translate('This document is'); ?> &copy; <?php echo date('Y'); ?>  <?php echo $this->translate('by'); ?> <?php echo $this->document->getOwner()->toString(); ?> - <?php echo $this->translate('all rights reserved.'); ?>
<?php endif; ?>
      </span>
    </p>
  </div>
</div>
