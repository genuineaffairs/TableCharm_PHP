<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
  include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
?>

<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/style_sitepage_profile.css')
?>

<?php 
	$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/hideWidgets.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/hideTabs.js');
?>

<?php 
	$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepagedocument/externals/scripts/MooTooltips.js');
?>

<?php 

    //SSL WORK
    $https = 0;
    if (!empty($_SERVER["HTTPS"]) && 'on' == strtolower($_SERVER["HTTPS"])) {
      $https = 1;
    }
    $manifest_path = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.manifestUrl', "page-documents");

?>

<script type="text/javascript">
function smoothboxdocument(thisobj) {
	var Obj_Url = thisobj.href;
	Smoothbox.open(Obj_Url);
}
</script>
<?php if (!empty($this->show_content)) : ?>
	<?php if(!empty($this->show_carousel) && $this->total_highlightedDocuments > 0):?>
		<script language="javascript" type="text/javascript">
				en4.core.runonce.add(function() {
				var browserName=navigator.appName;
				var sitepagedocument_height = 120;
				if (browserName=="Microsoft Internet Explorer")
				{
					sitepagedocument_height =105;
				} 
				new MooTooltips({
					hovered:'.tipper',		// the element that when hovered shows the tip
					ToolTipClass:'ToolTips',	// tooltip display class
					toolTipPosition:1, // -1 top; 1: bottom - set this as a default position value if none is set on the element
					sticky:false,		// remove tooltip if closed
					fromTop:sitepagedocument_height,		// distance from mouse or object
					fromLeft: -74,	// distance from left
					duration:100,		// fade effect transition duration
					fadeDistance: 0    // the distance the tooltip starts the morph
				});		
			});
		</script>
	<?php endif; ?>
<?php endif; ?>

<?php if (!empty($this->show_content)) : ?>
	<script type="text/javascript">
		var sitepageDocumentSearchText = '<?php echo $this->search ?>';
		var sitepageDocumentPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
		en4.core.runonce.add(function() {
			var url = en4.core.baseUrl + 'widget/index/mod/sitepagedocument/name/profile-sitepagedocuments';
			$('sitepage_documents_search_input_text').addEvent('keypress', function(e) {
				if( e.key != 'enter' ) return;
				if($('sitepage_documents_search_input_checkbox') && $('sitepage_documents_search_input_checkbox').checked == true) {
					var checkbox_value = 1;
				}
				else {
					var checkbox_value = 0;
				}
				if($('sitepagedocument_search') != null) {
					$('sitepagedocument_search').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepagedocument/externals/images/spinner_temp.gif" /></center>'; 
				}
					en4.core.request.send(new Request.HTML({
					'url' : url,
					'data' : {
						'format' : 'html',
						'subject' : en4.core.subject.guid,
						'search' : $('sitepage_documents_search_input_text').value,
						'selectbox' : $('sitepage_documents_search_input_selectbox').value,
						'checkbox' : checkbox_value,
						'isajax' : 1,
						'tab' : '<?php echo $this->content_id ?>'
					},
					onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {       	
					$('id_'+ <?php echo $this->content_id ?>).innerHTML = responseHTML;
						if(window.addCarousal) {
							addCarousal();     
						}      	
				}
				}), {
				//'element' : $('id_' + <?php //echo $this->content_id ?>)
				});
			});
		});
		
	 function showsearchdocumentcontent () {
		 
		  var url = en4.core.baseUrl + 'widget/index/mod/sitepagedocument/name/profile-sitepagedocuments';
			$('sitepage_documents_search_input_text').addEvent('keypress', function(e) {
				if( e.key != 'enter' ) return;
				if($('sitepage_documents_search_input_checkbox') && $('sitepage_documents_search_input_checkbox').checked == true) {
					var checkbox_value = 1;
				}
				else {
					var checkbox_value = 0;
				}
				if($('sitepagedocument_search') != null) {
					$('sitepagedocument_search').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepagedocument/externals/images/spinner_temp.gif" /></center>'; 
				}
					en4.core.request.send(new Request.HTML({
					'url' : url,
					'data' : {
						'format' : 'html',
						'subject' : en4.core.subject.guid,
						'search' : $('sitepage_documents_search_input_text').value,
						'selectbox' : $('sitepage_documents_search_input_selectbox').value,
						'checkbox' : checkbox_value,
						'isajax' : 1,
						'tab' : '<?php echo $this->content_id ?>'
					},
					onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {       	
					$('id_'+ <?php echo $this->content_id ?>).innerHTML = responseHTML;
					showsearchdocumentcontent();
						if(window.addCarousal) {
							addCarousal();     
						}      	
				}
				}), {
				//'element' : $('id_' + <?php //echo $this->content_id ?>)
				});
			});
		  
		}

		function Orderdocumentselect()
		{
			var sitepageDocumentSearchSelectbox = '<?php echo $this->selectbox ?>';
			var sitepageDocumentPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
			var url = en4.core.baseUrl + 'widget/index/mod/sitepagedocument/name/profile-sitepagedocuments';
			if($('sitepage_documents_search_input_checkbox') && $('sitepage_documents_search_input_checkbox').checked == true) {
				var checkbox_value = 1;
			}
			else {
				var checkbox_value = 0;
			}
			if($('sitepagedocument_search') != null) {
				$('sitepagedocument_search').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepagedocument/externals/images/spinner_temp.gif" /></center>'; 
			} 
			en4.core.request.send(new Request.HTML({
				'url' : url,
				'data' : {
					'format' : 'html',
					'subject' : en4.core.subject.guid,
					'search' : $('sitepage_documents_search_input_text').value,
					'selectbox' : $('sitepage_documents_search_input_selectbox').value,
					'checkbox' : checkbox_value,
					'isajax' : 1,
					'tab' : '<?php echo $this->content_id ?>'
				},
					onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {       	
					$('id_'+ <?php echo $this->content_id ?>).innerHTML = responseHTML;
						if(window.addCarousal) {
							addCarousal();     
						} 	
				}
			}), {
						//'element' : $('id_' + <?php //echo $this->content_id ?>)
					});
		}

		function Mydocument() {
			var sitepageDocumentSearchCheckbox = '<?php echo $this->checkbox ?>';
			var sitepageDocumentPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
			var url = en4.core.baseUrl + 'widget/index/mod/sitepagedocument/name/profile-sitepagedocuments';
			if($('sitepage_documents_search_input_checkbox') && $('sitepage_documents_search_input_checkbox').checked == true) {
				var checkbox_value = 1;
			}
			else {
				var checkbox_value = 0;
			}
			if($('sitepagedocument_search') != null) {
					$('sitepagedocument_search').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepagedocument/externals/images/spinner_temp.gif" /></center>'; 
			}
				en4.core.request.send(new Request.HTML({
				'url' : url,
				'data' : {
					'format' : 'html',
					'subject' : en4.core.subject.guid,
					'search' : $('sitepage_documents_search_input_text').value,
					'selectbox' : $('sitepage_documents_search_input_selectbox').value,
					'checkbox' : checkbox_value,
					'isajax' : 1,
					'tab' : '<?php echo $this->content_id ?>'
				},
				onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {       	
				$('id_'+ <?php echo $this->content_id ?>).innerHTML = responseHTML;
					if(window.addCarousal) {
						addCarousal();     
					}     	
			} 
			}), {
				//'element' : $('id_' + <?php //echo $this->content_id ?>)
			});
		}

		var paginateSitepageDocuments = function(page) {
			var url = en4.core.baseUrl + 'widget/index/mod/sitepagedocument/name/profile-sitepagedocuments';
			if($('sitepage_documents_search_input_checkbox') && $('sitepage_documents_search_input_checkbox').checked == true) {
				var checkbox_value = 1;
			}
			else {
				var checkbox_value = 0;
			}
			en4.core.request.send(new Request.HTML({
				'url' : url,
				'data' : {
					'format' : 'html',
					'subject' : en4.core.subject.guid,
					'search' : sitepageDocumentSearchText,
					'selectbox' : $('sitepage_documents_search_input_selectbox').value,
					'checkbox' : checkbox_value,
					'page' : page,
					'isajax' : 1,
					'tab' : '<?php echo $this->content_id ?>'
				},
				onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {       	
					$('id_'+ <?php echo $this->content_id ?>).innerHTML = responseHTML;
						if(window.addCarousal) {
							addCarousal();     
						}       	
				}
			}), {
			// 'element' : $('id_' + <?php //echo $this->content_id ?>)
			});
		}
	</script>
<?php endif;?>

<?php if (empty($this->isajax)) : ?>
	<div id="id_<?php echo $this->content_id; ?>">
<?php endif;?>

<?php if (!empty($this->show_content)) : ?>
	<?php if($this->showtoptitle == 1):?>
		<div class="layout_simple_head" id="layout_document">
      <?php echo $this->translate($this->sitepage_subject->getTitle());?><?php echo $this->translate("'s Documents");?>
		</div>
	<?php endif;?>		
	<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addocumentwidget', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage_subject)):?>
			<div class="layout_right" id="communityad_document">

				<?php
				echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addocumentwidget', 3),"loaded_by_ajax"=>1,'widgetId'=>'page_document')); 			 
				?>
			</div>
			<div class="layout_middle">
	<?php endif;?>

	<?php if($this->can_create): ?>
		<div class="seaocore_add">
			<a href='<?php echo $this->url(array('page_id' => $this->sitepage_subject->page_id, 'tab' => $this->identity_temp), 'sitepagedocument_create', true) ?>' class='buttonlink icon_sitepagedocument_new'><?php echo $this->translate('Add a Document');?></a>
		</div>
	<?php endif; ?>

	<?php if(!empty($this->show_carousel) && $this->total_highlightedDocuments > 0):?>
		<?php if( $this->paginator->count() > 0): ?>
			<?php $current_index = 0; ?>
			<?php $total_featured = $this->paginator->count(); ?>
			<?php if($total_featured > 0 ): ?>
				<?php $current_index = 3 ?>
				<?php if($current_index == 0): ?>
					<?php $previous_index = ($total_featured-1) ?>
				<?php else: ?>
					<?php $previous_index = $current_index-1 ?>
				<?php endif; ?>
				<?php if($current_index == $total_featured-1): ?>
					<?php $next_index = 0?>
				<?php else: ?>
					<?php $next_index = $current_index+1 ?>
				<?php endif; ?>
			<?php endif;?>	
			<div class="browse_sitepagedocument_carousel">
				<div class="sitepagedocument_carousel">
					<div class="fleft bold mleft5"><?php echo $this->translate('Highlighted');?></div>
					<table cellpadding='0' cellspacing='0' align='center' class="clear">
						<tr>
							<td>
								<a href='javascript:void(0);' onClick='moveLeft();this.blur()'>
								<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepagedocument/externals/images/doc_car_left.png', '', array('align'=>'left', 'onMouseOver'=>'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepagedocument/externals/images/doc_car_left_over.png";','onMouseOut'=>'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepagedocument/externals/images/doc_car_left.png";', 'border'=>'0')) ?>
								</a>
							</td>
							<td>
								<div id='sitepagedocument_carousel' class="sitepagedocument_carousel_sitepagedocuments">
									<table cellpadding='0' cellspacing='0'>
										<tr>
										<td id='thumb0' class="browse_carousel_item"><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepagedocument/externals/images/media_placeholder.png' border='0' width='70' /></td>
											<?php foreach($this->highlightedDocuments as $key => $highlightedSitepagedocument):  ?>
												<td id='thumb<?php echo $key+1 ?>' class='browse_carousel_item' align="center" width="120">
													<div class="tipper" rel="{content:'focus_tooltip_sitepagedocument_featured_<?php echo $highlightedSitepagedocument->document_id; ?>'}">

														<?php if($https):?>
															<?php $highlightedSitepagedocument->thumbnail = $this->baseUrl().'/'.$manifest_path."/ssl?url=".urlencode($highlightedSitepagedocument->thumbnail);?>
														<?php endif; ?>

														<?php echo $this->htmlLink($highlightedSitepagedocument->getHref(), '<img src="'. $highlightedSitepagedocument->thumbnail .'" width="120" height="120" />', array() ) ?>
													</div>
													  
													<div id="focus_tooltip_sitepagedocument_featured_<?php echo $highlightedSitepagedocument->document_id; ?>" style="display:none;">	
														<div class="sitepagedocument_tooltip">
															<div class="sitepagedocument_tooltip_content">
																<div class="tooltip_arrow">
																	<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepagedocument/externals/images/tooltip_arrow.png' alt="" />
																</div>
																<div class="sitepagedocument_tooltip_title">
																		<?php echo $this->htmlLink($highlightedSitepagedocument->getHref(), $highlightedSitepagedocument->sitepagedocument_title, array('title' => $highlightedSitepagedocument->sitepagedocument_title)) ?> 
																</div>
																	<div class="sitepagedocument_tooltip_details">
																	<?php echo $this->translate('Created by %s about %s', $highlightedSitepagedocument->getOwner()->toString(), $this->timestamp($highlightedSitepagedocument->creation_date)) ?>,<br />
																		<?php echo $this->translate(array('%s comment', '%s comments', $highlightedSitepagedocument->comment_count), $this->locale()->toNumber($highlightedSitepagedocument->comment_count)) ?>,
																		<?php echo $this->translate(array('%s view', '%s views', $highlightedSitepagedocument->views), $this->locale()->toNumber($highlightedSitepagedocument->views)) ?>,
																		<?php echo $this->translate(array('%s like', '%s likes', $highlightedSitepagedocument->like_count), $this->locale()->toNumber($highlightedSitepagedocument->like_count)) ?>
																	<?php if(($highlightedSitepagedocument->rating > 0) && ($this->show_rate == 1)):?>

																		<?php 
																			$currentRatingValue = $highlightedSitepagedocument->rating;
																			$difference = $currentRatingValue- (int)$currentRatingValue;
																			if($difference < .5) {
																				$finalRatingValue = (int)$currentRatingValue;
																			}
																			else {
																				$finalRatingValue = (int)$currentRatingValue + .5;
																			}	
																		?>

																		<br /><?php for($x=1; $x<=$highlightedSitepagedocument->rating; $x++): ?><span class="rating_star_big_generic rating_star" title="<?php echo $finalRatingValue.$this->translate('rating'); ?>"></span><?php endfor; ?><?php if((round($highlightedSitepagedocument->rating)-$highlightedSitepagedocument->rating)>0):?><span class="rating_star_big_generic rating_star_half" title="<?php echo $finalRatingValue ?> rating"></span><?php endif; ?><br />
																	<?php endif; ?>
																</div>
															</div>
														</div>
													</div>
												</td>
											<?php endforeach; ?> 
										<td id='thumb<?php echo $total_featured+1; ?>' class="browse_carousel_item"><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepagedocument/externals/images/media_placeholder.png' border='0' width='70'></td>
										</tr>
									</table>
								</div>
							</td>
							<td>
								<a href='javascript:void(0);' onClick='moveRight();this.blur()'>
								<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepagedocument/externals/images/doc_car_right.png', '', array('align'=>'left', 'onMouseOver'=>'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepagedocument/externals/images/doc_car_right_over.png";','onMouseOut'=>'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepagedocument/externals/images/doc_car_right.png";', 'border'=>'0')) ?>
								</a>
							</td>
						</tr>
					</table>
				</div>
			</div>		
	<?php endif; ?>

	<br />
<?php endif; ?>

<?php if( $this->paginator->count() <= 0 && (empty($this->search) && empty($this->checkbox) && empty($this->selectbox))): ?>
	<div class="sitepage_list_filters" style="display:none;">
<?php else: ?>
	<div class="sitepage_list_filters">
<?php endif; ?>

<?php if(!empty($this->viewer_id)): ?>
	<div class="sitepage_list_filter_first">
		<?php if($this->checkbox != 1): ?>
			<input id="sitepage_documents_search_input_checkbox" type="checkbox" value="1" onclick="Mydocument();" /><?php echo $this->translate("Show my documents");?>
		<?php else: ?>
			<input id="sitepage_documents_search_input_checkbox" type="checkbox" value="2"  checked = "checked" onclick="Mydocument();" /><?php echo $this->translate("Show my documents");?>
		<?php endif; ?>
	</div>
<?php endif; ?>
	
<div class="sitepage_list_filter_field">
	<?php echo $this->translate("Search: ");?>
	<input id="sitepage_documents_search_input_text" type="text" value="<?php echo $this->search; ?>" />
</div>

<div class="sitepage_list_filter_field">
	<?php echo $this->translate('Browse by:');?>
	
	<select name="default_visibility" id="sitepage_documents_search_input_selectbox" onchange = "Orderdocumentselect()">
    <option value=""></option>
		<?php if($this->selectbox == 'document_id'): ?>
			<option value="document_id" selected='selected'><?php echo $this->translate("Most Recent"); ?></option>
		<?php else:?>
			<option value="document_id"><?php echo $this->translate("Most Recent"); ?></option>
		<?php endif;?>
		<?php if($this->selectbox == 'rating'): ?>
			<option value="rating" selected='selected'><?php echo $this->translate("Most Rated"); ?></option>
		<?php else:?>
			<option value="rating"><?php echo $this->translate("Most Rated"); ?></option>
		<?php endif;?>
		<?php if($this->selectbox == 'comment_count'): ?>
			<option value="comment_count" selected='selected'><?php echo $this->translate("Most Commented"); ?></option>
		<?php else:?>
			<option value="comment_count"><?php echo $this->translate("Most Commented"); ?></option>
		<?php endif;?>
		<?php if($this->selectbox == 'like_count'): ?>
			<option value="like_count" selected='selected'><?php echo $this->translate("Most Liked"); ?></option>
		<?php else:?>
			<option value="like_count"><?php echo $this->translate("Most Liked"); ?></option>
		<?php endif;?>
		<?php if($this->selectbox == 'views'): ?>
			<option value="views" selected='selected'><?php echo $this->translate("Most Viewed"); ?></option>
		<?php else:?>
			<option value="views"><?php echo $this->translate("Most Viewed"); ?></option>
		<?php endif;?>
		<?php if($this->selectbox == 'featured'): ?>
			<option value="featured" selected='selected'><?php echo $this->translate("Featured"); ?></option>
		<?php else:?>
			<option value="featured"><?php echo $this->translate("Featured"); ?></option>
		<?php endif;?>
    <?php if($this->selectbox == 'highlighted'): ?>
			<option value="highlighted" selected='selected'><?php echo $this->translate("Highlighted"); ?></option>
		<?php else:?>
			<option value="highlighted"><?php echo $this->translate("Highlighted"); ?></option>
		<?php endif;?>
	</select>
	</div>
</div>

<div id='sitepagedocument_search'>
<?php if( $this->paginator->count() > 0): ?>
<ul class="sitepage_profile_list" id='sitepagedocument_search'>
  <?php foreach ($this->paginator as $sitepagedocument): ?>

		<?php $flag = 1; ?>
		<?php if($sitepagedocument->draft == 0 && $sitepagedocument->approved == 1 && $sitepagedocument->status == 1):?>	
			<?php $flag = 1; ?>
		<?php elseif($this->level_id == 1 || $this->viewer_id == $this->sitepage_subject->owner_id || $this->viewer_id == $sitepagedocument->owner_id || $this->can_edit == 1): ?>
			<?php $flag = 1; ?>
		<?php endif; ?>

		<?php if($flag == 1): ?>
			<?php if($sitepagedocument->highlighted == 1):?>
			<li class="sitepage_list_highlight">
			<?php else:?>
			<li>
			<?php endif; ?>

				<?php if(!empty($sitepagedocument->thumbnail)): ?>

					<?php if($https):?>
						<?php $sitepagedocument->thumbnail = $this->baseUrl().'/'.$manifest_path."/ssl?url=".urlencode($sitepagedocument->thumbnail); ?>
					<?php endif; ?>

					<?php echo $this->htmlLink($sitepagedocument->getHref(), '<img src="'. $sitepagedocument->thumbnail .'" />', array('title' => $sitepagedocument->sitepagedocument_title) ) ?>
				<?php else: ?>
					<?php echo $this->htmlLink($sitepagedocument->getHref(), '<img src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitepagedocument/externals/images/sitepagedocument_thumb.png" />', array('title' => $sitepagedocument->sitepagedocument_title) ) ?>
				<?php endif;?>
				
				<div class="sitepage_profile_list_options">
					<?php $slug = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($sitepagedocument->sitepagedocument_title))), '-'); ?>
					<?php echo $this->htmlLink($sitepagedocument->getHref(), $this->translate('View Document'), array('class'=>'buttonlink icon_sitepagedocument_viewall')) ?>
					<?php if($sitepagedocument->owner_id == $this->viewer_id || $this->can_edit == 1): ?>
						<?php if($sitepagedocument->draft == 1) echo $this->htmlLink(array('route' => 'sitepagedocument_publish', 'document_id' => $sitepagedocument->document_id, 'tab' => $this->identity_temp), $this->translate('Publish Document'), array(
						'class'=>'buttonlink icon_sitepagedocument_publish', 'onclick' => 'smoothboxdocument(this);return false')) ?>   
						<?php echo $this->htmlLink(array('route' => 'sitepagedocument_edit', 'document_id' => $sitepagedocument->document_id, 'page_id' => $this->sitepage_subject->page_id, 'tab' => $this->identity_temp), $this->translate('Edit Document'), array('class' => 'buttonlink icon_sitepagedocument_edit')) ?>
			
						<?php echo $this->htmlLink(array('route' => 'sitepagedocument_delete', 'document_id' => $sitepagedocument->document_id, 'page_id' => $sitepagedocument->page_id, 'tab' => $this->identity_temp), $this->translate('Delete Document'), array(
						'class'=>'buttonlink icon_sitepagedocument_delete')) ?>
					<?php endif; ?>
					<?php if(($this->level_id == 1)):?>
						<?php if($sitepagedocument->featured == 1) echo $this->htmlLink(array('route' => 'sitepagedocument_featured', 'document_id' => $sitepagedocument->document_id, 'tab' => $this->identity_temp), $this->translate('Make Un-featured'), array(
							'class'=>'buttonlink seaocore_icon_unfeatured' , 'onclick' => 'smoothboxdocument(this);return false')) ?>
						<?php if($sitepagedocument->featured == 0) echo $this->htmlLink(array('route' => 'sitepagedocument_featured', 'document_id' => $sitepagedocument->document_id, 'tab' => $this->identity_temp), $this->translate('Make Featured'), array(
							'class'=>'buttonlink seaocore_icon_featured',  'onclick' => 'smoothboxdocument(this);return false')) ?>  
					<?php endif;?>
         
          <?php if(($this->can_edit || $this->level_id == 1) && ($this->canMakeHighlighted)):?>
						<?php if($sitepagedocument->highlighted == 1) echo $this->htmlLink(array('route' => 'sitepagedocument_highlighted', 'document_id' => $sitepagedocument->document_id, 'tab' => $this->identity_temp), $this->translate('Make Un-highlighted'), array(
							'class'=>'buttonlink icon_sitepage_unhighlighted' , 'onclick' => 'smoothboxdocument(this);return false')) ?>
						<?php if($sitepagedocument->highlighted == 0) echo $this->htmlLink(array('route' => 'sitepagedocument_highlighted', 'document_id' => $sitepagedocument->document_id, 'tab' => $this->identity_temp), $this->translate('Make Highlighted'), array(
							'class'=>'buttonlink icon_sitepage_highlighted',  'onclick' => 'smoothboxdocument(this);return false')) ?>  
					<?php endif;?>
  
				</div>

				<div class='sitepage_profile_list_info'>
					<div class='sitepage_profile_list_title'>
						<span>
							<?php if($sitepagedocument->approved != 1): ?>
								<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/icons/sitepagedocument_approved0.gif', '', array('class' => 'icon', 'title' => $this->translate('Not Approved'))) ?>
							<?php endif;?>
						</span>
						<span>
							<?php if($sitepagedocument->featured == 1 && $this->canMakeFeatured == 1): ?>
								<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/featured.png', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
							<?php endif;?>
						</span>
						<?php echo $this->htmlLink($sitepagedocument->getHref(), $sitepagedocument->sitepagedocument_title, array('title' => $sitepagedocument->sitepagedocument_title)) ?>
					</div>
					
					<?php if($sitepagedocument->status == 0): ?>
						<div class="sitepagedocument_alert-message">
							<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepagedocument/externals/images/sitepagedocument_wait.gif', '', array('class' => 'icon')) ?>
							<?php echo $this->translate("Document format conversion in progress.") ?>
						</div>
					<?php elseif($sitepagedocument->status == 2): ?>
						<div class="sitepagedocument_alert-message">
							<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepagedocument/externals/images/sitepagedocument_alert16.gif', '', array('class' => 'icon')) ?>
							<?php echo $this->translate('Format conversion for this document failed. Please ').$this->htmlLink(array('route' => 'sitepagedocument_delete', 'document_id' => $sitepagedocument->document_id, 'page_id' => $sitepagedocument->page_id, 'tab' => $this->identity_temp), $this->translate('Delete')).$this->translate(' this document or ').$this->htmlLink(array('route' => 'sitepagedocument_edit', 'document_id' => $sitepagedocument->document_id, 'page_id' => $this->sitepage_subject->page_id, 'tab' => $this->identity_temp), $this->translate('Edit')).$this->translate(' it to upload a new file.') ?>
						</div>
						<?php elseif($sitepagedocument->status == 3): ?>
							<div class="sitepagedocument_alert-message">
								<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepagedocument/externals/images/sitepagedocument_alert16.gif', '', array('class' => 'icon')) ?>
								<?php echo $this->translate("This document has been deleted at Scribd. The document creator has been notified so that he may edit it to upload a new file. You may delete this document by clicking ").$this->htmlLink(array('route' => 'sitepagedocument_delete', 'document_id' => $sitepagedocument->document_id, 'page_id' => $sitepagedocument->page_id, 'tab' => $this->identity_temp), $this->translate('here')); ?>
							</div>
						<?php endif;?>

						<div class="clear"></div>
			
					<div class='sitepage_profile_list_info_date'>
						<?php echo $this->translate('Created %s by %s', $this->timestamp($sitepagedocument->creation_date), $sitepagedocument->getOwner()->toString()) ?>,
						<?php echo $this->translate(array('%s comment', '%s comments', $sitepagedocument->comment_count), $this->locale()->toNumber($sitepagedocument->comment_count)) ?>, 
						<?php echo $this->translate(array('%s view', '%s views', $sitepagedocument->views), $this->locale()->toNumber($sitepagedocument->views)) ?>,
						<?php echo $this->translate(array('%s like', '%s likes', $sitepagedocument->like_count), $this->locale()->toNumber($sitepagedocument->like_count)) ?>
						<span class="fright">
							<?php if(($sitepagedocument->rating > 0) && ($this->show_rate == 1)):?>

								<?php 
									$currentRatingValue = $sitepagedocument->rating;
									$difference = $currentRatingValue- (int)$currentRatingValue;
									if($difference < .5) {
										$finalRatingValue = (int)$currentRatingValue;
									}
									else {
										$finalRatingValue = (int)$currentRatingValue + .5;
									}	
								?>

								<?php for($x=1; $x<=$sitepagedocument->rating; $x++): ?><span class="rating_star_big_generic rating_star" title="<?php echo $finalRatingValue ?> <?php echo $this->translate('rating');?>"></span><?php endfor; ?><?php if((round($sitepagedocument->rating)-$sitepagedocument->rating)>0):?><span class="rating_star_big_generic rating_star_half" title="<?php echo $finalRatingValue.$this->translate("rating"); ?>"></span><?php endif; ?>
							<?php endif; ?>
						</span>
					</div>

					<div class='sitepage_profile_list_info_des'>
						<?php echo $sitepagedocument->truncateText($sitepagedocument->sitepagedocument_description, 200); ?>
					</div>
				</div>
			</li>
		<?php endif; ?>
  <?php endforeach; ?>
</ul>
<?php if( $this->paginator->count() > 1 ): ?>
  <div>
    <?php if( $this->paginator->getCurrentPageNumber() > 1 ): ?>
      <div id="user_sitepage_members_previous" class="paginator_previous">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
          'onclick' => 'paginateSitepageDocuments(sitepageDocumentPage - 1)',
          'class' => 'buttonlink icon_previous'
        )); ?>
      </div>
    <?php endif; ?>
    <?php if( $this->paginator->getCurrentPageNumber() < $this->paginator->count() ): ?>
      <div id="user_sitepage_members_next" class="paginator_next">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
          'onclick' => 'paginateSitepageDocuments(sitepageDocumentPage + 1)',
          'class' => 'buttonlink_right icon_next'
        )); ?>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>
<?php elseif($this->paginator->count() <= 0 && ($this->search != '' || $this->checkbox == 1 || $this->selectbox == 'views' ||  $this->selectbox == 'comment_count' ||  $this->selectbox == 'like_count' || $this->selectbox == 'featured' || $this->selectbox == 'rating')):?>	
	<div class="tip" id='sitepagedocument_search'>
		<span>
			<?php echo $this->translate('No documents were found matching your search criteria.');?>
		</span>
	</div>
<?php else: ?>
	<div class="tip" id='sitepagedocument_search'>
		<span>
			<?php echo $this->translate('No documents have been added to this Page yet.'); ?>
			<?php if ($this->can_create):  ?>
				<?php echo $this->translate('Be the first to %1$sadd%2$s one!', '<a href="'.$this->url(array('page_id' => $this->sitepage_subject->page_id, 'tab' => $this->identity_temp), 'sitepagedocument_create').'">', '</a>'); ?>
			<?php endif; ?>
		</span>
	</div>	
<?php endif; ?>
</div>
	<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addocumentwidget', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage_subject)):?>
			</div>
	<?php endif; ?>
<?php endif;?>

<?php if (empty($this->isajax)) : ?>
	</div>
<?php endif;?>

<?php if (!empty($this->show_content)) : ?>
	<?php if(!empty($this->show_carousel) && $this->total_highlightedDocuments > 0):?>
		<script type="text/javascript">
			var current_id = 1;

			var myFx_docuement_featured;
			window.addEvent('load', function() {
				var DocumenttabId = '<?php echo $this->module_tabid;?>';
			
				var DocumentTabIdCurrent = '<?php echo $this->identity_temp; ?>';
				if (DocumentTabIdCurrent == DocumenttabId) {
					
				myFx_docuement_featured = new Fx.Scroll('sitepagedocument_carousel');
				current_id = parseInt(<?php echo $current_index - 2 ; ?>);
				if(myFx_docuement_featured) {
					var position = $('thumb'+current_id).getPosition($('sitepagedocument_carousel'));
					myFx_docuement_featured.set(position.x, position.y);
				}
				}

			});

			function addCarousal () {
				if ($('sitepagedocument_carousel')) {
  				myFx_docuement_featured = new Fx.Scroll('sitepagedocument_carousel');
  				current_id = parseInt(<?php echo $current_index - 2 ; ?>);
  				var position = $('thumb'+current_id).getPosition($('sitepagedocument_carousel'));
  				myFx_docuement_featured.set(position.x, position.y);
				}
			}
			function moveLeft() {
				if($('thumb'+(current_id-1))) {
					myFx_docuement_featured.toElement('thumb'+(current_id-1));
					myFx_docuement_featured.toLeft();
					current_id = parseInt(current_id-1);
				}
			}

			function moveRight() { 
				if($('thumb'+(current_id+1))) { 
					myFx_docuement_featured.toElement('thumb'+(current_id+1));
					myFx_docuement_featured.toRight();
					current_id = parseInt(current_id+1);
				}
			}
		</script>
	<?php endif; ?>
<?php endif; ?>

<script type="text/javascript">
  var document_ads_display = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addocumentwidget', 3);?>';
  var adwithoutpackage = '<?php echo Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage_subject) ?>';
	var is_ajax_divhide = '<?php echo $this->isajax;?>';
	var execute_Request_Document = '<?php echo $this->show_content;?>';
	//window.addEvent('domready', function () {
	var show_widgets = '<?php echo $this->widgets ?>';
	var DocumenttabId = '<?php echo $this->module_tabid;?>';
  var DocumentTabIdCurrent = '<?php echo $this->identity_temp; ?>';
  var page_communityad_integration = '<?php echo $page_communityad_integration; ?>';
	if (DocumentTabIdCurrent == DocumenttabId) {
		if(page_showtitle != 0) {
			if($('profile_status') && show_widgets == 1) {
		  	$('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitepage_subject->getTitle())?><?php echo $this->translate(' &raquo; ');?><?php echo $this->translate('Documents');?></h2>";	
			}
			if($('layout_document')) {
		    $('layout_document').style.display = 'block';		
		  }		
		}	
    hideWidgetsForModule('sitepagedocument');
		prev_tab_id = '<?php echo $this->content_id; ?>'; 
		prev_tab_class = 'layout_sitepagedocument_profile_sitepagedocuments';    
		execute_Request_Document = true;
		hideLeftContainer (document_ads_display, page_communityad_integration, adwithoutpackage);
	} 
  else if (is_ajax_divhide != 1) {  	
		if($('global_content').getElement('.layout_sitepagedocument_profile_sitepagedocuments')) {
			$('global_content').getElement('.layout_sitepagedocument_profile_sitepagedocuments').style.display = 'none';
	  } 	
	}
	//});

	$$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function() {
		$('global_content').getElement('.layout_sitepagedocument_profile_sitepagedocuments').style.display = 'block';
		if(page_showtitle != 0) {
			if($('profile_status') && show_widgets == 1) {
				$('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitepage_subject->getTitle())?><?php echo $this->translate(' &raquo; ');?><?php echo $this->translate('Documents');?></h2>";	
			}
		}			
    hideWidgetsForModule('sitepagedocument');
		$('id_' + <?php echo $this->content_id ?>).style.display = "block";
    if ($('id_' + prev_tab_id) != null &&  prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {
      $$('.'+ prev_tab_class).setStyle('display', 'none');
    }
		if (prev_tab_id != '<?php echo $this->content_id; ?>') {
			execute_Request_Document = false;
			prev_tab_id = '<?php echo $this->content_id; ?>';			
			prev_tab_class = 'layout_sitepagedocument_profile_sitepagedocuments';
			
		}
		if(execute_Request_Document == false) {
			ShowContent('<?php echo $this->content_id; ?>', execute_Request_Document, '<?php echo $this->identity_temp?>', 'document', 'sitepagedocument', 'profile-sitepagedocuments', page_showtitle, 'null', document_ads_display, page_communityad_integration, adwithoutpackage);
			execute_Request_Document = true;    		
		}   		
		if('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1);?>' && document_ads_display == 0)
{setLeftLayoutForPage();}	    
	});

</script>
