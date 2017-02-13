<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  function sitepagedocumentRate(rating, document_id) {
    $.mobile.activePage.find('#rating_text').html("<?php echo $this->translate('Thank you for rating to this document!'); ?>");
    for (var x = 1; x <= 5; x++) {
      $.mobile.activePage.find('#rate_' + x).attr('onclick', '');
    }
    sm4.core.request.send({
      type: "POST",
      dataType: "json",
      'url': '<?php echo $this->url(array('module' => 'sitepagedocument', 'controller' => 'index', 'action' => 'rating'), 'default', true) ?>',
      'data': {
        'format': 'json',
        'rating': rating,
        'document_id': document_id
      },
      beforeSend: function() {
        $.mobile.activePage.data('sitepagedocument_rated', 1);
        var sitepagedocument_total_rating = $.mobile.activePage.data('sitepagedocument_total_rating');
        sitepagedocument_total_rating = sitepagedocument_total_rating + 1;
        var sitepagedocument_rate_previous = ($.mobile.activePage.data('sitepagedocument_rate_previous') + rating) / sitepagedocument_total_rating;
        $.mobile.activePage.data('sitepagedocument_total_rating', sitepagedocument_total_rating);
        $.mobile.activePage.data('sitepagedocument_rate_previous', sitepagedocument_rate_previous);
        sitepagedocument_set_rating();
      },
      success: function(response)
      {
        $.mobile.activePage.find('#rating_text').html(response[0].total + '<?php echo $this->string()->escapeJavascript($this->translate(" rating")) ?>');
        $.mobile.activePage.data('current_total_rate', response[0].total);
      }
    });

  }

  function sitepagedocument_rating_out() { //alert($.mobile.activePage.find('#rating_text').html());
    $.mobile.activePage.find('#rating_text').html(" <?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count), $this->locale()->toNumber($this->rating_count)) ?>");
    if ($.mobile.activePage.data('sitepagedocument_rate_previous') !== 0) {
      sitepagedocument_set_rating();
    }
    else {
      for (var x = 1; x <= 5; x++) {
        $.mobile.activePage.find('#rate_' + x).attr('class', 'rating_star_big_generic rating_star_big_disabled');
      }
    }
  }

  function sitepagedocument_set_rating() {
    var rating = $.mobile.activePage.data('sitepagedocument_rate_previous');
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

  function sitepagedocument_rating_over(rating) {
    if ($.mobile.activePage.data('sitepagedocument_rated') == 1) {
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
    $.mobile.activePage.data('sitepagedocument_rate_previous',<?php echo $this->sitepagedocument->rating; ?>);
    $.mobile.activePage.data('sitepagedocument_rated', '<?php echo $this->sitepagedocument_rated; ?>');
    $.mobile.activePage.data('sitepagedocument_total_rating',<?php echo $this->rating_count; ?>);
    sitepagedocument_set_rating();
  });
</script>
<?php 
$breadcrumb = array(
    array("href"=>$this->sitepage_subject->getHref(),"title"=>$this->sitepage_subject->getTitle(),"icon"=>"arrow-r"),
    array("href"=>$this->sitepage_subject->getHref(array('tab' => $this->tab_selected_id)),"title"=>"Documents","icon"=>"arrow-r"),
    array("title"=>$this->sitepagedocument->getTitle(),"icon"=>"arrow-d","class" => "ui-btn-active ui-state-persist"));

echo $this->breadcrumb($breadcrumb);
?>
	<div class="ui-page-content">
      <div class="sm-ui-cont-head">
        <div class="sm-ui-cont-cont-info">
          <div class="sm-ui-cont-author-name">
          <?php echo $this->sitepagedocument->getTitle(); ?> 
          </div>
      <?php if(!empty($this->viewer_id) && $this->can_rate == 1): ?>
	      <div id="sitepagedocument_rating" class="" onmouseout="sitepagedocument_rating_out();">
			    <span id="rate_1" <?php if (!$this->sitepagedocument_rated && $this->viewer_id):?>onclick="sitepagedocumentRate(1,<?php echo $this->sitepagedocument->document_id; ?>);"<?php endif; ?> onmouseover="sitepagedocument_rating_over(1);"></span>
			    <span id="rate_2" <?php if (!$this->sitepagedocument_rated && $this->viewer_id):?>onclick="sitepagedocumentRate(2,<?php echo $this->sitepagedocument->document_id; ?>);"<?php endif; ?> onmouseover="sitepagedocument_rating_over(2);"></span>
			    <span id="rate_3" <?php if (!$this->sitepagedocument_rated && $this->viewer_id):?>onclick="sitepagedocumentRate(3,<?php echo $this->sitepagedocument->document_id; ?>);"<?php endif; ?> onmouseover="sitepagedocument_rating_over(3);"></span>
			    <span id="rate_4" <?php if (!$this->sitepagedocument_rated && $this->viewer_id):?>onclick="sitepagedocumentRate(4,<?php echo $this->sitepagedocument->document_id; ?>);"<?php endif; ?> onmouseover="sitepagedocument_rating_over(4);"></span>
			    <span id="rate_5" <?php if (!$this->sitepagedocument_rated && $this->viewer_id):?>onclick="sitepagedocumentRate(5,<?php echo $this->sitepagedocument->document_id; ?>);"<?php endif; ?> onmouseover="sitepagedocument_rating_over(5);"></span>
			    <span id="rating_text" class="rating_text"><?php echo $this->translate('click to rate');?></span>
			  </div>
      <?php endif; ?>	
      <div class="sm-ui-cont-cont-date">
      	<?php echo $this->translate('Created by %s', $this->sitepagedocument->getOwner()->toString());?>
        -
        <?php echo $this->timestamp($this->sitepagedocument->creation_date); ?>
				<?php if( !empty($this->sitepagedocument->category_id) ): ?> - 
					<?php echo $this->htmlLink(array(
						'route' => 'sitepagedocument_browse',
						'action' => 'browse',
						'document_category_id' => $this->sitepagedocument->category_id,
					), $this->translate((string)$this->sitepagedocument->categoryName())) ?>
				<?php endif ?>
          
      </div>
      <div class="sm-ui-cont-cont-date">
      	<?php echo $this->translate(array('%s view', '%s views', $this->sitepagedocument->views), $this->locale()->toNumber($this->sitepagedocument->views)) ?>
      </div>
      </div>
      </div>
      <div class="sm-ui-cont-cont-des">
     <?php echo $this->viewMore(nl2br($this->sitepagedocument->sitepagedocument_description));?>
      </div>
      
		<?php echo $this->fieldValueLoop($this->sitepagedocument, $this->fieldStructure) ?>
	  
	  <?php if ($this->sitepagedocument->status == 1): ?>
		
			<!--The Page document full text comes here if downloading has been enabled for this Page document-->
			<?php if(!empty($this->viewer_id)):?>
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
			<?php endif;?>
          
				<div id='embedded_doc' class="document_view_ipaper"><a href="http://www.scribd.com"></a></div>
				<script type="text/javascript">
					var scribd_doc = scribd.Document.getDoc('<?php echo $this->sitepagedocument->doc_id; ?>', '<?php echo $this->sitepagedocument->access_key; ?>');
					var onDocReady = function(e){
						// scribd_doc.api.setPage(3);
					}

					scribd_doc.addParam( 'jsapi_version', 2 );
					scribd_doc.addParam( 'default_embed_format', 'html5' );
					var height = $('body').width() - 25;
                                        if (height > <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.viewer.height', 600); ?>)
                                          height = <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.viewer.height', 600); ?>;
                                        scribd_doc.addParam('height', height);
					scribd_doc.addParam( 'width', '100%');
					<?php if (!empty($this->sitepagedocument->secure_allow)): ?>
						scribd_doc.addParam("use_ssl", 'true'); 
						scribd_doc.grantAccess('<?php echo $this->uid; ?>', '<?php echo $this->sessionId; ?>', '<?php echo $this->signature; ?>');
					<?php endif; ?>
					
					scribd_doc.addEventListener( 'docReady', onDocReady );
					scribd_doc.write( 'embedded_doc' );

				</script>
		
		<?php elseif($this->sitepagedocument->status == 0): ?>
			<div class="sitepagedocument_alert_box">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepagedocument/externals/images/sitepagedocument_alert.png', '', array('border'=>'0')) ?>
				<?php echo $this->translate('Document format conversion in progress.'); ?>
			</div>
		<?php elseif($this->sitepagedocument->status == 3 && $this->sitepagedocument->owner_id == $this->viewer_id): ?>
			<div class="sitepagedocument_alert_box">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepagedocument/externals/images/sitepagedocument_alert.png', '', array('border'=>'0')) ?>
				<?php echo 	$this->translate('This document has been deleted at Scribd. Please ').$this->htmlLink(array('route' => 'sitepagedocument_delete', 'document_id' => $this->sitepagedocument->document_id, 'page_id' => $this->sitepagedocument->page_id), $this->translate('Delete')).$this->translate(' this document or ').$this->htmlLink(array('route' => 'sitepagedocument_edit', 'document_id' => $this->sitepagedocument->document_id, 'page_id' => $this->sitepagedocument->page_id), $this->translate('Edit')).$this->translate(' it to upload a new file.')	?>
			</div>	 
			
		<?php elseif($this->sitepagedocument->status == 3 && $this->sitepagedocument->owner_id != $this->viewer_id): ?>
			<div class="sitepagedocument_alert_box">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepagedocument/externals/images/sitepagedocument_alert.png', '', array('border'=>'0')) ?>	
					<?php echo 	$this->translate('This document has been deleted at Scribd.'); ?>
				</div>	
					
		<?php elseif($this->sitepagedocument->status == 2 && $this->sitepagedocument->owner_id == $this->viewer_id): ?>
			<div class="sitepagedocument_alert_box">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepagedocument/externals/images/sitepagedocument_alert.png', '', array('border'=>'0')) ?>
				<?php echo 	$this->translate('Format conversion for this document failed. Please ').$this->htmlLink(array('route' => 'sitepagedocument_delete', 'document_id' => $this->sitepagedocument->document_id, 'page_id' => $this->sitepagedocument->page_id), $this->translate('Delete')).$this->translate(' this document or ').$this->htmlLink(array('route' => 'sitepagedocument_edit', 'document_id' => $this->sitepagedocument->document_id,  'page_id' => $this->sitepagedocument->page_id), $this->translate('Edit')).$this->translate(' it to upload a new file.')	?>
			</div>	
		<?php elseif($this->sitepagedocument->status == 2 && $this->sitepagedocument->owner_id != $this->viewer_id): ?>
			<div class="sitepagedocument_alert_box">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepagedocument/externals/images/sitepagedocument_alert.png', '', array('border'=>'0')) ?>	
				<?php echo 	$this->translate('Format conversion for this document failed.');?>
			</div>
		<?php endif; ?>
			<div class="sitepagedocuments_view_options">
				<p style="margin-top:0px;">
		   		<span class="sitepagedocuments_license">
		   			<?php if($this->sitepagedocument->sitepagedocument_license == 'by-nc'): ?>
						<a rel="license" href="http://creativecommons.org/licenses/by-nc/3.0/" target="_blank"><img alt="Creative Commons License" class="border0" src="http://i.creativecommons.org/l/by-nc/3.0/88x31.png" align="right" /></a><br /><?php echo $this->translate('This work is licensed under a '); ?><a rel="license" href="http://creativecommons.org/licenses/by-nc/3.0/" target="_blank"><?php echo $this->translate('Creative Commons Attribution-Noncommercial 3.0 Unported License.'); ?></a>
						
						<?php elseif($this->sitepagedocument->sitepagedocument_license == 'by-nc-nd'): ?>
						<a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/3.0/" target="_blank"><img alt="Creative Commons License" class="border0" src="http://i.creativecommons.org/l/by-nc-nd/3.0/88x31.png" align="right" /></a><br /><?php echo $this->translate('This work is licensed under a '); ?><a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/3.0/" target="_blank"><?php echo $this->translate('Creative Commons Attribution-Noncommercial-No Derivative Works 3.0 Unported License.'); ?></a>
						
						<?php elseif($this->sitepagedocument->sitepagedocument_license == 'by-nc-sa'): ?>
						<a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/" target="_blank"><img alt="Creative Commons License" class="border0" src="http://i.creativecommons.org/l/by-nc-sa/3.0/88x31.png" align="right" /></a><br /><?php echo $this->translate('This work is licensed under a '); ?><a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/" target="_blank"><?php echo $this->translate('Creative Commons Attribution-Noncommercial-Share Alike 3.0 Unported License.'); ?></a>
						
						<?php elseif($this->sitepagedocument->sitepagedocument_license == 'by-nd'): ?>
						<a rel="license" href="http://creativecommons.org/licenses/by-nd/3.0/" target="_blank"><img alt="Creative Commons License" class="border0" src="http://i.creativecommons.org/l/by-nd/3.0/88x31.png" align="right" /></a><br /><?php echo $this->translate('This work is licensed under a '); ?><a rel="license" href="http://creativecommons.org/licenses/by-nd/3.0/" target="_blank"><?php echo $this->translate('Creative Commons Attribution-No Derivative Works 3.0 Unported License.'); ?></a>
						
						<?php elseif($this->sitepagedocument->sitepagedocument_license == 'by-sa'): ?>
						<a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/" target="_blank"><img alt="Creative Commons License" class="border0" src="http://i.creativecommons.org/l/by-sa/3.0/88x31.png" align="right" /></a><br /><?php echo $this->translate('This work is licensed under a '); ?><a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/" target="_blank"><?php echo $this->translate('Creative Commons Attribution-Share Alike 3.0 Unported License.'); ?></a>
						
						<?php elseif($this->sitepagedocument->sitepagedocument_license == 'by'): ?>
						<a rel="license" href="http://creativecommons.org/licenses/by/3.0/" target="_blank"><img alt="Creative Commons License" class="border0" src="http://i.creativecommons.org/l/by/3.0/88x31.png" align="right" /></a><br /><?php echo $this->translate('This work is licensed under a '); ?><a rel="license" href="http://creativecommons.org/licenses/by/3.0/" target="_blank"><?php echo $this->translate('Creative Commons Attribution 3.0 Unported License.'); ?></a>
						
						<?php elseif($this->sitepagedocument->sitepagedocument_license == 'pd'): ?>
						<?php echo $this->translate('This Page document has been released into the public domain.'); ?>
						
						<?php elseif($this->sitepagedocument->sitepagedocument_license == 'c'): ?>
						<?php echo $this->translate('This document is'); ?> &copy; <?php echo date('Y'); ?>  <?php echo $this->translate('by'); ?> <?php echo $this->sitepagedocument->getOwner()->toString();?> - <?php echo $this->translate('all rights reserved.'); ?>
						<?php endif; ?>
		   		</span>
				</p>
			</div>
			<div class="clear"></div>
</div>