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
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';

	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitepagedocument/externals/styles/style_sitepagedocument.css')
?>

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
   
	<ul class="sitepagedocuments_view">
    <li>
      <h3> 
      	<?php echo $this->sitepagedocument->getTitle(); ?> 
      </h3>
      <?php if(!empty($this->viewer_id) && $this->can_rate == 1): ?>
	      <div id="sitepagedocument_rating" class="rating sitepagedocument_rating" onmouseout="sitepagedocument_rating_out();">
			    <span id="rate_1" <?php if (!$this->sitepagedocument_rated && $this->viewer_id):?>onclick="rate(1);"<?php endif; ?> onmouseover="sitepagedocument_rating_over(1);"></span>
			    <span id="rate_2" <?php if (!$this->sitepagedocument_rated && $this->viewer_id):?>onclick="rate(2);"<?php endif; ?> onmouseover="sitepagedocument_rating_over(2);"></span>
			    <span id="rate_3" <?php if (!$this->sitepagedocument_rated && $this->viewer_id):?>onclick="rate(3);"<?php endif; ?> onmouseover="sitepagedocument_rating_over(3);"></span>
			    <span id="rate_4" <?php if (!$this->sitepagedocument_rated && $this->viewer_id):?>onclick="rate(4);"<?php endif; ?> onmouseover="sitepagedocument_rating_over(4);"></span>
			    <span id="rate_5" <?php if (!$this->sitepagedocument_rated && $this->viewer_id):?>onclick="rate(5);"<?php endif; ?> onmouseover="sitepagedocument_rating_over(5);"></span>
			    <span id="rating_text" class="rating_text"><?php echo $this->translate('click to rate');?></span>
			  </div>
      <?php endif; ?>	
      <div class="sitepagedocuments_view_date">
      	<?php echo $this->translate('Created by %s %s', $this->sitepagedocument->getOwner()->toString(), $this->timestamp($this->sitepagedocument->creation_date)) ?>
        
        <?php if( !empty($this->sitepagedocument->category_id) ): ?> - 
          <?php echo $this->translate('Category:')?>
              <?php echo $this->htmlLink(array(
                'route' => 'sitepagedocument_browse',
                'document_category_id' => $this->sitepagedocument->category_id,
              ), $this->translate((string)$this->sitepagedocument->categoryName())) ?>
        <?php endif ?>         
        
      </div>
      <div class="sitepagedocuments_view_date"> 
      	<?php echo $this->translate(array('%s comment', '%s comments', $this->sitepagedocument->comment_count), $this->locale()->toNumber($this->sitepagedocument->comment_count)) ?>,
      	<?php echo $this->translate(array('%s view', '%s views', $this->sitepagedocument->views), $this->locale()->toNumber($this->sitepagedocument->views)) ?>,
				<?php echo $this->translate(array('%s like', '%s likes', $this->sitepagedocument->like_count), $this->locale()->toNumber($this->sitepagedocument->like_count)) ?>
      </div>
      <div class="sitepagedocuments_view_body">
      
         <!--FACEBOOK LIKE BUTTON START HERE-->
         <?php  $fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
        if (!empty ($fbmodule)) :
          $enable_facebookse = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse'); 
          if (!empty ($enable_facebookse) && !empty($fbmodule->version)) :
            $fbversion = $fbmodule->version; 
            if (!empty($fbversion) && ($fbversion >= '4.1.5')) { ?>
               <div class="sitepagedocument_fblike_button">
                 <script type="text/javascript">
                    var fblike_moduletype = 'sitepagedocument_document';
		                var fblike_moduletype_id = '<?php echo $this->sitepagedocument->getIdentity() ?>';
                 </script>
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

			<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.scribd.viewer', 1)): ?>

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
					scribd_doc.addParam( 'full_screen_type', 'flash');
					scribd_doc.addParam( 'mode', '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.flash.mode', 'list') ?>');
					scribd_doc.addParam( 'hide_disabled_buttons', '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.disable.button', 0) ?>');
					scribd_doc.addParam( 'hide_full_screen_button', '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.fullscreen.button', 0) ?>');

					<?php if (!empty($this->sitepagedocument->secure_allow)): ?>
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
					var scribd_doc = scribd.Document.getDoc('<?php echo $this->sitepagedocument->doc_id; ?>', '<?php echo $this->sitepagedocument->access_key; ?>');
					var onDocReady = function(e){
						// scribd_doc.api.setPage(3);
					}
          
          <?php if(!empty($_SERVER["HTTPS"]) && 'on' == strtolower($_SERVER["HTTPS"])): ?>
            scribd_doc.addParam( 'use_ssl', true );
          <?php endif;?>         

					scribd_doc.addParam( 'jsapi_version', 2 );
					scribd_doc.addParam( 'default_embed_format', 'html5' );
					scribd_doc.addParam( 'height', <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.viewer.height', 600); ?> );
					scribd_doc.addParam( 'width', <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.viewer.width', 730); ?> );
					<?php if (!empty($this->document->secure_allow)): ?>
						scribd_doc.addParam("use_ssl", 'true'); 
						scribd_doc.grantAccess('<?php echo $this->uid; ?>', '<?php echo $this->sessionId; ?>', '<?php echo $this->signature; ?>');
					<?php endif; ?>
					
					scribd_doc.addEventListener( 'docReady', onDocReady );
					scribd_doc.write( 'embedded_doc' );

				</script>

			<?php endif; ?>
		
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
		</li>
  </ul>

	<?php echo $this->action("list", "comment", "seaocore", array("type"=>"sitepagedocument_document", "id"=>$this->sitepagedocument->getIdentity())) ?>