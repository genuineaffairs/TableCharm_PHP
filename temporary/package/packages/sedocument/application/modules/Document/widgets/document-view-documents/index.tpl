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

  var tagViewDocumentAction = function(tag){

		var form = document.getElementById('filter_form_view_document');
		form.elements['tag'].value = tag;

    $('filter_form_view_document').submit();
  }
  
//   var categoryViewDocumentAction =function(category){
// 
// 		var form = document.getElementById('filter_form_view_document');
// 		form.elements['category'].value = category;
// 		form.elements['category_id'].value = category;
// 
//     $('filter_form_view_document').submit();
//   }

  var viewer = <?php echo $this->viewer_id;?>;
  var document_rate_previous = <?php echo $this->document->rating;?>;
  var document_id = <?php echo $this->document->document_id;?>;
  var document_total_rating = <?php echo $this->rating_count;?>;
  var document_rated = '<?php echo $this->document_rated;?>';
	var check_rating = 0;
	var current_total_rate;
	var rating_var = '<?php echo $this->string()->escapeJavascript($this->translate(" rating")) ?>';
	
  function rate(rating) {
	    $('rating_text').innerHTML = "<?php echo $this->translate('Thank you for rating to this document!');?>";
	    for(var x=1; x<=5; x++) {
	      $('rate_'+x).set('onclick', '');
	    }
	    (new Request.JSON({
	      'format': 'json',
	      'url' : '<?php echo $this->url(array('module' => 'document', 'controller' => 'index', 'action' => 'rating'), 'default', true) ?>',
	      'data' : {
	        'format' : 'json',
	        'rating' : rating,
	        'document_id': document_id
	      },
	      'onRequest' : function(){
	        document_rated = 1;
	        document_total_rating = document_total_rating+1;
	        document_rate_previous = (document_rate_previous+rating)/document_total_rating;
	        document_set_rating();
	      },
	      'onSuccess' : function(responseJSON, responseText)
	      {
	        $('rating_text').innerHTML = responseJSON[0].total+rating_var;
	        current_total_rate = responseJSON[0].total;
	        check_rating = 1;
	        
	      }
	    })).send();
	    
	  }
  
  function document_rating_out() {
	  $('rating_text').innerHTML = " <?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";
    if (document_rate_previous != 0){
      document_set_rating();
    }
    else {
      for(var x=1; x<=5; x++) {
        $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
      }
    }
  }

  function document_set_rating() {
    var rating = document_rate_previous;
    if(check_rating == 1) {
      if(current_total_rate == 1) {
    	  $('rating_text').innerHTML = current_total_rate+rating_var;
      }
      else {      
		  	$('rating_text').innerHTML = current_total_rate+rating_var;
    	}
	  }
	  else { 
    	$('rating_text').innerHTML = "<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";
	  }
    for(var x=1; x<=parseInt(rating); x++) {
      $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big');
    }

    for(var x=parseInt(rating)+1; x<=5; x++) {
      $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
    }

    var remainder = Math.round(rating)-rating;
    if (remainder <= 0.5 && remainder !=0){
      var last = parseInt(rating)+1;
      $('rate_'+last).set('class', 'rating_star_big_generic rating_star_big_half');
    }
  }

  function document_rating_over(rating) {
	    if (document_rated == 1){
	      $('rating_text').innerHTML = "<?php echo $this->translate('you have already rated this document');?>";
	      //document_set_rating();
	    }
	    else if (viewer == 0){
	      $('rating_text').innerHTML = "<?php echo $this->translate('Only logged-in user can rate');?>";
	    }
	    else{
	      $('rating_text').innerHTML = "<?php echo $this->translate('Please click to rate');?>";
	      for(var x=1; x<=5; x++) {
	        if(x <= rating) {
	          $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big');
	        } else {
	          $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
	        }
	      }
	    }
	  }
	  
  en4.core.runonce.add(document_set_rating);
</script>

<form id='filter_form_view_document' method='get' action='<?php echo $this->url(array('action' => 'browse'), 'document_browse', true) ?>'>
	<input type="hidden" id="tag" name="tag" value=""/>
<!--	<input type="hidden" id="category" name="category" value=""/>
	<input type="hidden" id="category_id" name="category_id" value=""/>-->
</form>

<div class="seaocore_gutter_view">
	<div class='seaocore_gutter_view_title'>
		<h3><?php echo $this->document->getTitle(); ?></h3>
	</div>
  <?php if(!empty($this->viewer_id) && $this->can_rate == 1): ?>
    <div id="document_rating" class="rating document_rating" onmouseout="document_rating_out();">
	    <span id="rate_1" <?php if (!$this->document_rated && $this->viewer_id):?>onclick="rate(1);"<?php endif; ?> onmouseover="document_rating_over(1);"></span>
	    <span id="rate_2" <?php if (!$this->document_rated && $this->viewer_id):?>onclick="rate(2);"<?php endif; ?> onmouseover="document_rating_over(2);"></span>
	    <span id="rate_3" <?php if (!$this->document_rated && $this->viewer_id):?>onclick="rate(3);"<?php endif; ?> onmouseover="document_rating_over(3);"></span>
	    <span id="rate_4" <?php if (!$this->document_rated && $this->viewer_id):?>onclick="rate(4);"<?php endif; ?> onmouseover="document_rating_over(4);"></span>
	    <span id="rate_5" <?php if (!$this->document_rated && $this->viewer_id):?>onclick="rate(5);"<?php endif; ?> onmouseover="document_rating_over(5);"></span>
	    <span id="rating_text" class="rating_text"><?php echo $this->translate('click to rate');?></span>
	  </div>
  <?php endif; ?>	
  <div class="seaocore_gutter_view_stat">
  	<?php echo $this->translate('Created by %s %s', $this->document->getOwner()->toString(), $this->timestamp($this->document->creation_date)) ?>
		<?php if(!empty($this->document->category_id)){ echo '-'; } ?>

		<?php if ($this->category_name != '' && $this->subcategory_name == '') : ?>
			<?php echo $this->translate('Category:'); ?>
			<?php echo $this->htmlLink($this->url(array('category_id' => $this->document->category_id, 'categoryname' => $this->categoryTable->getCategorySlug($this->category_name)), 'document_browse'), $this->translate($this->category_name)) ?>
		<?php elseif ($this->category_name != '' && $this->subcategory_name != ''): ?> 
			<?php echo $this->translate('Category:'); ?>
			<?php echo $this->htmlLink($this->url(array('category_id' => $this->document->category_id, 'categoryname' => $this->categoryTable->getCategorySlug($this->category_name)), 'document_browse'), $this->translate($this->category_name)) ?>
			<?php if (!empty($this->category_name)): echo '&raquo;';endif; ?>      
			<?php echo $this->htmlLink($this->url(array('category_id' => $this->document->category_id, 'categoryname' => $this->categoryTable->getCategorySlug($this->category_name), 'subcategory_id' => $this->document->subcategory_id, 'subcategoryname' => $this->categoryTable->getCategorySlug($this->subcategory_name)), 'document_browse'), $this->translate($this->subcategory_name)) ?>
			<?php if(!empty($this->subsubcategory_name)): echo '&raquo;';?>
				<?php echo $this->htmlLink($this->url(array('category_id' => $this->document->category_id, 'categoryname' => $this->categoryTable->getCategorySlug($this->category_name), 'subcategory_id' => $this->document->subcategory_id, 'subcategoryname' => $this->categoryTable->getCategorySlug($this->subcategory_name),'subsubcategory_id' => $this->document->subsubcategory_id, 'subsubcategoryname' => $this->categoryTable->getCategorySlug($this->subsubcategory_name)), 'document_browse'),$this->translate($this->subsubcategory_name)) ?>
			<?php endif; ?>
		<?php endif; ?>

   	<?php if (count($this->documentTags )):?>
  		- <?php echo $this->translate("Tags:"); ?> 
    	<?php foreach ($this->documentTags as $tag): ?>
      	<a href='javascript:void(0);' onclick='javascript:tagViewDocumentAction(<?php echo $tag->getTag()->tag_id; ?>);'>#<?php echo $tag->getTag()->text?></a>&nbsp;
    	<?php endforeach; ?>
  	<?php endif; ?>
  </div>
  <div class="seaocore_gutter_view_stat"> 
  	<?php echo $this->translate(array('%s comment', '%s comments', $this->document->comment_count), $this->locale()->toNumber($this->document->comment_count)) ?>,
  	<?php echo $this->translate(array('%s view', '%s views', $this->document->views), $this->locale()->toNumber($this->document->views)) ?>,
  	<?php echo $this->translate(array('%s like', '%s likes', $this->document->like_count), $this->locale()->toNumber($this->document->like_count)) ?>
  </div>
  
  <div class="seaocore_gutter_view_body">
  	<?php echo $this->document->document_description ?>
  </div>
  
	<?php $custom_field_values = $this->fieldValueLoop($this->document, $this->fieldStructure); ?>
	<?php echo htmlspecialchars_decode($custom_field_values); ?>
	  
	<?php if ($this->document->status == 1): ?>

			<!--The document full text comes here if downloading has been enabled for this document-->
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

			<?php if(!empty($this->document_viewer) || !empty($this->document->secure_allow)): ?>

				<script type='text/javascript' src='https://www.scribd.com/javascripts/view.js'></script>
				<div id='embedded_flash' class="document_view_ipaper"><a href="http://www.scribd.com"></a></div>
				<script type="text/javascript">
					var scribd_doc = scribd.Document.getDoc('<?php echo $this->document->doc_id; ?>', '<?php echo $this->document->access_key; ?>');
					var oniPaperReady = function(e){
						// scribd_doc.api.setPage(3);
					}
          
          <?php if((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) == 'https'): ?>
            scribd_doc.addParam( 'use_ssl', true );
          <?php endif;?>    
          
					scribd_doc.addParam( 'jsapi_version', 1 );
					scribd_doc.addParam( 'height', <?php echo $this->documentViewerHeight; ?> );
					scribd_doc.addParam( 'width', <?php echo $this->documentViewerWidth; ?> );
					scribd_doc.addParam( 'full_screen_type', 'flash');
					scribd_doc.addParam( 'mode', '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('document.flash.mode', 'list') ?>');
					scribd_doc.addParam( 'hide_disabled_buttons', '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('document.disable.button', 0) ?>');
					scribd_doc.addParam( 'hide_full_screen_button', '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('document.fullscreen.button', 0) ?>');

					<?php if (!empty($this->document->secure_allow)): ?>
						scribd_doc.addParam("use_ssl", 'true'); 
						scribd_doc.grantAccess('<?php echo $this->uid; ?>', '<?php echo $this->sessionId; ?>', '<?php echo $this->signature; ?>');
					<?php endif; ?>
					
					scribd_doc.addEventListener( 'iPaperReady', oniPaperReady );
					scribd_doc.write( 'embedded_flash' );
				</script>

			<?php else: ?>

				<script type='text/javascript' src='https://www.scribd.com/javascripts/scribd_api.js'></script>
				<div id='embedded_doc' class="document_view_ipaper"><a href="http://www.scribd.com"></a></div>
				<script type="text/javascript">
					var scribd_doc = scribd.Document.getDoc('<?php echo $this->document->doc_id; ?>', '<?php echo $this->document->access_key; ?>');
					var onDocReady = function(e){
						// scribd_doc.api.setPage(3);
					}
          
          <?php if((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) == 'https'): ?>
            scribd_doc.addParam( 'use_ssl', true );
          <?php endif;?>              

					scribd_doc.addParam( 'jsapi_version', 2 );
					scribd_doc.addParam( 'default_embed_format', 'html5' );
					scribd_doc.addParam( 'height', <?php echo $this->documentViewerHeight; ?> );
					scribd_doc.addParam( 'width', <?php echo $this->documentViewerWidth; ?> );
					<?php if (!empty($this->document->secure_allow)): ?>
						scribd_doc.addParam("use_ssl", 'true'); 
						scribd_doc.grantAccess('<?php echo $this->uid; ?>', '<?php echo $this->sessionId; ?>', '<?php echo $this->signature; ?>');
					<?php endif; ?>
					
					scribd_doc.addEventListener( 'docReady', onDocReady );
					scribd_doc.write( 'embedded_doc' );

				</script>

			<?php endif; ?>

		<?php elseif($this->document->status == 0): ?>
			<div class="document_alert_box">
				<?php echo $this->htmlImage('application/modules/Document/externals/images/document_alert.png', '', array('style'=>'vertical-align:middle;margin-right:5px;','border'=>'0')) ?>
				<?php echo $this->translate('Document format conversion in progress.'); ?>
			</div>
		<?php elseif($this->document->status == 3 && $this->document->owner_id == $this->viewer_id): ?>
			<div class="document_alert_box">
				<?php echo $this->htmlImage('application/modules/Document/externals/images/document_alert.png', '', array('style'=>'vertical-align:middle;margin-right:5px;','border'=>'0')) ?>
				<?php echo 	$this->translate('This document has been deleted at Scribd. Please ').$this->htmlLink(array('route' => 'document_delete', 'document_id' => $this->document->document_id), $this->translate('Delete')).$this->translate(' this document or ').$this->htmlLink(array('route' => 'document_edit', 'document_id' => $this->document->document_id), $this->translate('Edit')).$this->translate(' it to upload a new file.')	?>
			</div>	 
			
		<?php elseif($this->document->status == 3 && $this->document->owner_id != $this->viewer_id): ?>
			<div class="document_alert_box">
				<?php echo $this->htmlImage('application/modules/Document/externals/images/document_alert.png', '', array('style'=>'vertical-align:middle;margin-right:5px;','border'=>'0')) ?>	
					<?php echo 	$this->translate('This document has been deleted at Scribd'); ?>
				</div>	
					
		<?php elseif($this->document->status == 2 && $this->document->owner_id == $this->viewer_id): ?>	
			<div class="document_alert_box">
				<?php echo $this->htmlImage('application/modules/Document/externals/images/document_alert.png', '', array('style'=>'vertical-align:middle;margin-right:5px;','border'=>'0')) ?>
				<?php echo 	$this->translate('Format conversion for this document failed. Please ').$this->htmlLink(array('route' => 'document_delete', 'document_id' => $this->document->document_id), $this->translate('Delete')).$this->translate(' this document or ').$this->htmlLink(array('route' => 'document_edit', 'document_id' => $this->document->document_id), $this->translate('Edit')).$this->translate(' it to upload a new file.')	?>
			</div>	
		<?php elseif($this->document->status == 2 && $this->document->owner_id != $this->viewer_id): ?>
			<div class="document_alert_box">
				<?php echo $this->htmlImage('application/modules/Document/externals/images/document_alert.png', '', array('style'=>'vertical-align:middle;margin-right:5px;','border'=>'0')) ?>	
				<?php echo 	$this->translate('Format conversion for this document failed.');?>
			</div>
		<?php endif; ?>
		
		<div class="documents_user_options">
			<p>
	   		<span class="documents_license">
	   			<?php if($this->document->document_license == 'by-nc'): ?>
					<a rel="license" href="http://creativecommons.org/licenses/by-nc/3.0/" target="_blank" ><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-nc/3.0/88x31.png" align="right" /></a><br /><?php echo $this->translate('This work is licensed under a '); ?><a rel="license" href="http://creativecommons.org/licenses/by-nc/3.0/" target="_blank"><?php echo $this->translate('Creative Commons Attribution-Noncommercial 3.0 Unported License.'); ?></a>
					
					<?php elseif($this->document->document_license == 'by-nc-nd'): ?>
					<a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/3.0/" target="_blank"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-nd/3.0/88x31.png" align="right" /></a><br /><?php echo $this->translate('This work is licensed under a '); ?><a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/3.0/" target="_blank"><?php echo $this->translate('Creative Commons Attribution-Noncommercial-No Derivative Works 3.0 Unported License.'); ?></a>
					
					<?php elseif($this->document->document_license == 'by-nc-sa'): ?>
					<a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/" target="_blank"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-sa/3.0/88x31.png" align="right" /></a><br /><?php echo $this->translate('This work is licensed under a '); ?><a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/" target="_blank"><?php echo $this->translate('Creative Commons Attribution-Noncommercial-Share Alike 3.0 Unported License.'); ?></a>
					
					<?php elseif($this->document->document_license == 'by-nd'): ?>
					<a rel="license" href="http://creativecommons.org/licenses/by-nd/3.0/" target="_blank"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-nd/3.0/88x31.png" align="right" /></a><br /><?php echo $this->translate('This work is licensed under a '); ?><a rel="license" href="http://creativecommons.org/licenses/by-nd/3.0/" target="_blank"><?php echo $this->translate('Creative Commons Attribution-No Derivative Works 3.0 Unported License.'); ?></a>
					
					<?php elseif($this->document->document_license == 'by-sa'): ?>
					<a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/" target="_blank"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-sa/3.0/88x31.png" align="right" /></a><br /><?php echo $this->translate('This work is licensed under a '); ?><a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/" target="_blank"><?php echo $this->translate('Creative Commons Attribution-Share Alike 3.0 Unported License.'); ?></a>
					
					<?php elseif($this->document->document_license == 'by'): ?>
					<a rel="license" href="http://creativecommons.org/licenses/by/3.0/" target="_blank"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by/3.0/88x31.png" align="right" /></a><br /><?php echo $this->translate('This work is licensed under a '); ?><a rel="license" href="http://creativecommons.org/licenses/by/3.0/" target="_blank"><?php echo $this->translate('Creative Commons Attribution 3.0 Unported License.'); ?></a>
					
					<?php elseif($this->document->document_license == 'pd'): ?>
					<?php echo $this->translate('This document has been released into the public domain.'); ?>
					
					<?php elseif($this->document->document_license == 'c'): ?>
					<?php echo $this->translate('This document is'); ?> &copy; <?php echo date('Y'); ?>  <?php echo $this->translate('by'); ?> <?php echo $this->document->getOwner()->toString();?> - <?php echo $this->translate('all rights reserved.'); ?>
					<?php endif; ?>
	   		</span>
			</p>
		</div>
  </div>
  <?php echo $this->action("list", "comment", "core", array("type"=>"document", "id"=>$this->document->getIdentity())) ?>