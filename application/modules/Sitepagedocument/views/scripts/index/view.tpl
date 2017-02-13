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
<?php 
  include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitepagedocument/externals/styles/style_sitepagedocument.css')
?>
<script type="text/javascript">
	function smoothboxdocument(thisobj) {
		var Obj_Url = thisobj.href;
		Smoothbox.open(Obj_Url);
	}
</script>

<script type="text/javascript">
  var viewer = <?php echo $this->viewer_id;?>;
  var sitepagedocument_rate_previous = <?php echo $this->sitepagedocument->rating;?>;
  var document_id = <?php echo $this->sitepagedocument->document_id;?>;
  var sitepagedocument_total_rating = <?php echo $this->rating_count;?>;
  var sitepagedocument_rated = '<?php echo $this->sitepagedocument_rated;?>';
	var check_rating = 0;
	var current_total_rate;
	var rating_var =  '<?php echo $this->string()->escapeJavascript($this->translate(" rating")) ?>';

  function rate(rating) {
	    $('rating_text').innerHTML = "<?php echo $this->translate('Thank you for rating to this document!');?>";
	    for(var x=1; x<=5; x++) {
	      $('rate_'+x).set('onclick', '');
	    }
	    (new Request.JSON({
	      'format': 'json',
	      'url' : '<?php echo $this->url(array('module' => 'sitepagedocument', 'controller' => 'index', 'action' => 'rating'), 'default', true) ?>',
	      'data' : {
	        'format' : 'json',
	        'rating' : rating,
	        'document_id': document_id
	      },
	      'onRequest' : function(){
	        sitepagedocument_rated = 1;
	        sitepagedocument_total_rating = sitepagedocument_total_rating+1;
	        sitepagedocument_rate_previous = (sitepagedocument_rate_previous+rating)/sitepagedocument_total_rating;
	        sitepagedocument_set_rating();
	      },
	      'onSuccess' : function(responseJSON, responseText)
	      {
	        $('rating_text').innerHTML = responseJSON[0].total+rating_var;
	        current_total_rate =  responseJSON[0].total;
	        check_rating = 1;
	        
	      }
	    })).send();
	    
	  }

  function sitepagedocument_rating_out() {
	  $('rating_text').innerHTML = " <?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";
    if (sitepagedocument_rate_previous != 0){
      sitepagedocument_set_rating();
    }
    else {
      for(var x=1; x<=5; x++) {
        $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
      }
    }
  }

  function sitepagedocument_set_rating() {
    var rating = sitepagedocument_rate_previous;
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

  function sitepagedocument_rating_over(rating) {
	    if (sitepagedocument_rated == 1){
	      $('rating_text').innerHTML = "<?php echo $this->translate('you have already rated this document');?>";
	      //sitepagedocument_set_rating();
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
  en4.core.runonce.add(sitepagedocument_set_rating);
</script>

<div class="sitepage_viewpages_head">
	<?php echo $this->htmlLink($this->sitepage_subject->getHref(), $this->itemPhoto($this->sitepage_subject, 'thumb.icon', '', array('align' => 'left'))) ?>
	<h2>
	  <?php echo $this->sitepage_subject->__toString() ?>
	  <?php echo $this->translate('&raquo; ');?>
	  <?php echo $this->htmlLink($this->sitepage_subject->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Documents')) ?>
	  <?php echo $this->translate('&raquo; ');?>
	  <?php echo $this->sitepagedocument->sitepagedocument_title ?>
	</h2>
</div>

<div class='layout_right'>
  <div class='sitepagedocuments_gutter_photo'>
  	<?php echo $this->htmlLink($this->owner->getHref(), $this->itemPhoto($this->owner)) ?>
    <?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle(), array('class' => 'sitepagedocuments_gutter_name')) ?>
  </div>
  <div class="quicklinks sitepagedocument_options">
    <ul>
			<li>
			<?php echo $this->htmlLink($this->sitepage_subject->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Back to Page'),array('class'=>'buttonlink  icon_sitepagedocument_back')) ?>
			</li>
			 <?php if($this->can_create):?>
			 <li>
				<a href='<?php echo $this->url(array('page_id' => $this->page_id, 'tab' => $this->tab_selected_id), 'sitepagedocument_create', true) ?>' class='buttonlink icon_sitepagedocument_new'><?php echo $this->translate('Add Document');?></a>
			 </li>
			<?php endif; ?>
	    <?php if($this->viewer_id == $this->sitepagedocument->owner_id || $this->can_edit == 1): ?>    
	    <?php if($this->sitepagedocument->draft == 1):?>
	    	<li>
					<?php echo $this->htmlLink(array('route' => 'sitepagedocument_publish', 'document_id' => $this->sitepagedocument->document_id, 'slug' => $this->sitepagedocument->getSlug(), 'tab' => $this->tab_selected_id), $this->translate('Publish Document'), array(
	          'class'=>'buttonlink icon_sitepagedocument_publish', 'onclick' => 'smoothboxdocument(this);return false')) ?>
	      </li>
			<?php endif; ?>
      	<li>  
	      	<?php echo $this->htmlLink(array('route' => 'sitepagedocument_edit', 'document_id' => $this->sitepagedocument->document_id, 'page_id' => $this->sitepagedocument->page_id, 'slug' => $this->sitepagedocument->getSlug(), 'tab' => $this->tab_selected_id), $this->translate('Edit Document'), array('class' => 'buttonlink icon_sitepagedocument_edit')) ?>
	    	</li>

	    	<li>
	    		<?php echo $this->htmlLink(array('route' => 'sitepagedocument_delete', 'document_id' => $this->sitepagedocument->document_id, 'page_id' => $this->sitepagedocument->page_id, 'slug' => $this->sitepagedocument->getSlug(), 'tab' => $this->tab_selected_id), $this->translate('Delete Document'), array('class'=>'buttonlink  icon_sitepagedocument_delete')) ?>
	    	</li>
	    <?php endif; ?>
			<!--  Start: "Suggest to Friends" link -->
	    <?php if( !empty($this->documentSuggLink) && empty($this->sitepagedocument->draft) && !empty($this->sitepagedocument->approved) && !empty($this->sitepagedocument->status) ): ?>				
	    	<li>
	    		<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'suggestion', 'controller' => 'index', 'action' => 'popups', 'sugg_id' => $this->sitepagedocument->document_id, 'sugg_type' => 'page_document'), $this->translate('Suggest to Friends'), array(
	          'class'=>'buttonlink icon_page_friend_suggestion smoothbox')) ?>
	    	</li>
	    <?php endif; ?>	
			<!--  End: "Suggest to Friends" link -->
  	</ul>
  </div>	

	<?php if($this->documentSitepageTotal): ?>
		<div class="sitepagedocument_view_sidebar generic_layout_container">
			<h3><?php echo $this->htmlLink($this->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($this->page_id), 'tab' => $this->tab_selected_id), 'sitepage_entry_view'), $this->sitepage_subject->title, array()) ?><?php echo $this->translate("'s Documents")?></h3>
			<ul class="sitepage_sidebar_list">
				<?php $count = 1;?>
			  	<?php foreach( $this->documentSitepage as $documentSitepage ): ?>
			  		<?php if($count>2):?>
			  			<li class="bold">
				  			<?php echo $this->htmlLink($this->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($this->page_id), 'tab' => $this->tab_selected_id), 'sitepage_entry_view'), $this->translate('More &raquo;'), array('class'=>'fright')) ?>
			  			</li>
			  			<?php break;?>
			  		<?php endif;?>
				    <li>

							<?php if($this->https):?> 
								<?php $documentSitepage->thumbnail = $this->baseUrl().'/'.$this->manifest_path."/ssl?url=".urlencode($documentSitepage->thumbnail);?>
							<?php endif; ?>

				       <?php echo $this->htmlLink($documentSitepage->getHref(), '<img src="'. $documentSitepage->thumbnail .'" class="sitepagedocument_thumb thumb_icon"  />', array('title' => $documentSitepage->sitepagedocument_title) ) ?>
				      <div class='sitepage_sidebar_list_info'>
				       	<div class='sitepage_sidebar_list_title'>
									<?php if(Engine_Api::_()->getApi('settings', 'core')->sitepagedocument_title_truncation): ?>
										<?php echo $this->htmlLink($documentSitepage->getHref(), $documentSitepage->truncateText($documentSitepage->sitepagedocument_title, 13), array('title' => $documentSitepage->sitepagedocument_title)) ?>
									<?php else:?>
										<?php echo $this->htmlLink($documentSitepage->getHref(), $documentSitepage->sitepagedocument_title, array('title' => $documentSitepage->sitepagedocument_title)) ?>
									<?php endif;?>
				        </div>
				        <div class='sitepage_sidebar_list_details'>
				        	<?php echo $this->translate(array('%s comment', '%s comments', $documentSitepage->comment_count), $this->locale()->toNumber($documentSitepage->comment_count)) ?> |
				        	<?php echo $this->translate(array('%s view', '%s views', $documentSitepage->views), $this->locale()->toNumber($documentSitepage->views)) ?>
				        </div>
				        <div class='sitepage_sidebar_list_details'>	
				        	<?php if(( $documentSitepage->rating > 0) && ($this->can_rate == 1)):?>
	          			<?php for($x=1; $x<= $documentSitepage->rating; $x++): ?><span class="rating_star_big_generic rating_star sitepage-rating-star" title="<?php echo $documentSitepage->rating.$this->translate("rating"); ?>"></span><?php endfor; ?><?php if((round( $documentSitepage->rating)- $documentSitepage->rating)>0):?><span class="rating_star_big_generic rating_star_half sitepage-rating-star" title="<?php echo $documentSitepage->rating.$this->translate("rating"); ?>" ></span><?php endif; ?>


	        		<?php endif; ?>
				        </div>
				      </div>
				    </li>
			    <?php $count++ ; ?>
			  <?php endforeach; ?>
			</ul>
		</div>
  <?php endif; ?>  

	<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('page.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addocumentview', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage_subject)):?>
	  <div id="communityad_documentview">

<?php
		echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addocumentview', 3),"loaded_by_ajax"=>0,'widgetId'=>'page_documentview')); 			 
	?>
	  </div> 
	<?php endif;?>	
</div>
    
<div class='layout_middle'>
	<ul class="sitepagedocuments_view">
    <li>
      <h3> 
      	<?php echo $this->sitepagedocument->getTitle(); ?> 
      </h3>
      <div class="sitepagedocuments_view_date">
      	<?php echo $this->translate('Created by %s %s', $this->sitepagedocument->getOwner()->toString(), $this->timestamp($this->sitepagedocument->creation_date)) ?>
        
      </div>
      <div class="sitepagedocuments_view_date"> 
      	<?php echo $this->translate(array('%s comment', '%s comments', $this->sitepagedocument->comment_count), $this->locale()->toNumber($this->sitepagedocument->comment_count)) ?>,
      	<?php echo $this->translate(array('%s view', '%s views', $this->sitepagedocument->views), $this->locale()->toNumber($this->sitepagedocument->views)) ?>,
				<?php echo $this->translate(array('%s like', '%s likes', $this->sitepagedocument->like_count), $this->locale()->toNumber($this->sitepagedocument->like_count)) ?>
      </div>
      <?php if(!empty($this->viewer_id) && $this->can_rate == 1): ?>
	      <div id="sitepagedocument_rating" class="rating" onmouseout="sitepagedocument_rating_out();">
			    <span id="rate_1" <?php if (!$this->sitepagedocument_rated && $this->viewer_id):?>onclick="rate(1);"<?php endif; ?> onmouseover="sitepagedocument_rating_over(1);"></span>
			    <span id="rate_2" <?php if (!$this->sitepagedocument_rated && $this->viewer_id):?>onclick="rate(2);"<?php endif; ?> onmouseover="sitepagedocument_rating_over(2);"></span>
			    <span id="rate_3" <?php if (!$this->sitepagedocument_rated && $this->viewer_id):?>onclick="rate(3);"<?php endif; ?> onmouseover="sitepagedocument_rating_over(3);"></span>
			    <span id="rate_4" <?php if (!$this->sitepagedocument_rated && $this->viewer_id):?>onclick="rate(4);"<?php endif; ?> onmouseover="sitepagedocument_rating_over(4);"></span>
			    <span id="rate_5" <?php if (!$this->sitepagedocument_rated && $this->viewer_id):?>onclick="rate(5);"<?php endif; ?> onmouseover="sitepagedocument_rating_over(5);"></span>
			    <span id="rating_text" class="rating_text"><?php echo $this->translate('click to rate');?></span>
			  </div>
      <?php endif; ?>	
      <div class="sitepagedocuments_view_body">
      
         <!--FACEBOOK LIKE BUTTON START HERE-->
         <?php  $fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
        if (!empty ($fbmodule)) :
          $enable_facebookse = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse'); 
          if (!empty ($enable_facebookse) && !empty($fbmodule->version)) :
            $fbversion = $fbmodule->version; 
            if (!empty($fbversion) && ($fbversion >= '4.1.5')) { ?>
               <div class="sitepagedocument_fblike_button">
                <?php echo Engine_Api::_()->facebookse()->isValidFbLike(); ?>
              </div>
            
            <?php } ?>
          <?php endif; ?>
     <?php endif; ?>
     <?php echo $this->sitepagedocument->sitepagedocument_description ?>
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

			<script type='text/javascript' src='https://www.scribd.com/javascripts/scribd_api.js'></script>
				<div id='embedded_flash' class="sitepagedocument_ipaper"><a href="http://www.scribd.com"></a></div>
			<script type="text/javascript">
				var scribd_doc = scribd.Document.getDoc('<?php echo $this->sitepagedocument->doc_id; ?>', '<?php echo $this->sitepagedocument->access_key; ?>');
				var oniPaperReady = function(e){
					// scribd_doc.api.setPage(3);
				}
        
          <?php if(!empty($_SERVER["HTTPS"]) && 'on' == strtolower($_SERVER["HTTPS"])): ?>
            scribd_doc.addParam( 'use_ssl', true );
          <?php endif;?>       
        
				scribd_doc.addParam( 'jsapi_version', 1 );
				scribd_doc.addParam( 'height', <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.viewer.height', 600); ?> );
				scribd_doc.addParam( 'width', <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.viewer.width', 730); ?> );
				scribd_doc.addParam("full_screen_type", 'flash');
				<?php if (!empty($this->sitepagedocument->secure_allow)): ?>
				scribd_doc.addParam("use_ssl", 'true'); 
				scribd_doc.grantAccess('<?php echo $this->uid; ?>', '<?php echo $this->sessionId; ?>', '<?php echo $this->signature; ?>');
				<?php endif; ?>
				
				scribd_doc.addEventListener( 'iPaperReady', oniPaperReady );
				scribd_doc.write( 'embedded_flash' );
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
				<p style="*margin-top:0;">
					<?php if(!empty($this->sitepagedocument->owner_id)): ?>
						<?php if ($this->link && $this->sitepagedocument->download_allow): ?>
		
								<?php echo $this->htmlLink($this->link, $this->translate('Download'), array (
		          	'class'=>'buttonlink icon_sitepagedocument_download', 'target' => '_blank')) ?>
		        <?php endif; ?>
		      <?php endif; ?>&nbsp;
										
					<?php if(!empty($this->sitepagedocument->owner_id)): ?>
						<?php if($this->sitepagedocument->email_allow == 1 && $this->sitepagedocument->status == 1 && $this->email_allow): ?>
	  					<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepagedocument', 'controller' => 'index', 'action' => 'email', 'id' => $this->sitepagedocument->document_id), $this->translate('Email as attachment'), array(
	              'class' => 'buttonlink smoothbox icon_sitepagedocuments_email',
	            )) ?>
	          <?php endif; ?>	
	        <?php endif; ?>&nbsp;&nbsp;&nbsp;
	        <?php if(!empty($this->viewer_id) && $this->can_share == 1 && $this->sitepagedocument->status == 1 && $this->sitepagedocument->draft != 1 && $this->sitepagedocument->approved == 1): ?>
	        	<?php echo $this->htmlLink(Array('module'=> 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => 'sitepagedocument_document', 'id' => $this->sitepagedocument->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox')); ?>
						 <?php if($this->sitepagedocument_report == 1 && !empty($this->viewer_id)): ?>
						-
						<?php endif; ?>
	        <?php endif;?>
	        <?php if($this->sitepagedocument_report == 1 && !empty($this->viewer_id)): ?>
				  <?php echo $this->htmlLink(Array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' => $this->report->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox ')); ?>
						<?php endif;?>	
					<?php $show_button_share = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.share', 1); ?>
					<?php if($show_button_share == 1): ?>
						<span class="fright">
							<?php echo $this->htmlLink('http://delicious.com/save?v=5&noui&jump=close&url='.$this->curr_url.'&title='.$this->sitepagedocument->getTitle(), '<img src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepagedocument/externals/images/socialbookmarking_delicious16.gif" class="photo" />', array('target' => '_blank') ) ?>
							
							<?php echo $this->htmlLink('http://digg.com/submit?phase=2&media=news&url='.$this->curr_url.'&title='.$this->sitepagedocument->getTitle(), '<img src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepagedocument/externals/images/socialbookmarking_digg16.gif" class="photo" />', array('target' => '_blank') ) ?>
							
							<?php echo $this->htmlLink('http://www.facebook.com/share.php?u='.$this->curr_url.'&t='.$this->sitepagedocument->getTitle(), '<img src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepagedocument/externals/images/socialbookmarking_facebook16.gif" class="photo" />', array('target' => '_blank') ) ?>
							
							<?php echo $this->htmlLink('http://cgi.fark.com/cgi/fark/farkit.pl?u='.$this->curr_url.'&h='.$this->sitepagedocument->getTitle(), '<img src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepagedocument/externals/images/socialbookmarking_fark16.gif" class="photo" />', array('target' => '_blank') ) ?>
							
							<?php echo $this->htmlLink('http://www.myspace.com/Modules/PostTo/Pages/?u='.$this->curr_url.'&t='.$this->sitepagedocument->getTitle(), '<img src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepagedocument/externals/images/socialbookmarking_myspace16.gif" class="photo" />', array('target' => '_blank') ) ?>
							
							<?php echo $this->htmlLink('http://twitthis.com/twit?url='.$this->curr_url.'&title='.$this->sitepagedocument->getTitle(), '<img src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepagedocument/externals/images/socialbookmarking_twitter16.png" class="photo" />', array('target' => '_blank') ) ?>
						</span>
					<?php endif; ?>
				</p>
			</div>
			<div class="clear"></div>
		</li>
  </ul>

	<?php echo $this->action("list", "comment", "core", array("type"=>"sitepagedocument_document", "id"=>$this->sitepagedocument->getIdentity())) ?>

</div>