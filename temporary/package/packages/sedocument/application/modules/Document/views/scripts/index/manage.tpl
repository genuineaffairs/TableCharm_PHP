<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headLink()->appendStylesheet($this->seaddonsBaseUrl()
  	              . '/application/modules/Seaocore/externals/styles/styles.css');
?>

<script type="text/javascript">

  var searchDocuments = function() {

		var formElements = $('filter_form').getElements('li');
		formElements.each( function(el) {
			var field_style = el.style.display;
			if(field_style == 'none') {
				el.destroy();
			}
		});

    document.getElementById('filter_form').submit();
  }

</script>

<div class="headline">
	<h2>
	  <?php echo $this->translate('Documents'); ?>
	</h2>
	<div class='tabs'>
	  <?php echo $this->navigation($this->navigation)->render() ?>
	</div>
</div>

<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
//         'topLevelId' => (int) @$this->topLevelId,
//         'topLevelValue' => (int) @$this->topLevelValue
))
?>

<div class='layout_right'>
	<div class="seaocore_search_criteria">
		<?php echo $this->form->render($this) ?>
	</div>
	<?php if ($this->can_create): ?>
		<div class="quicklinks">
	    <ul>
	      <li> 
	      	<a href='<?php echo $this->url(array(), 'document_create', true) ?>' class='buttonlink icon_type_document_new'><?php echo $this->translate('Create New Document');?></a> </li>
	    </ul>
	  </div>
	<?php endif; ?>  
</div>

<div class='layout_middle'>
	<?php if( $this->paginator->count() > 0): ?> 
		<ul class="seaocore_browse_list">
	    <?php foreach( $this->paginator as $document ):?>
	      <li>
	        <div class='seaocore_browse_list_photo'>
						<?php if(!empty($document->photo_id)): ?>
							<?php echo $this->htmlLink($document->getHref(), $this->itemPhoto($document, 'thumb.normal'), array('title' => $document->document_title)) ?>
						<?php elseif(!empty($document->thumbnail)): ?>
							<?php echo $this->htmlLink($document->getHref(), '<img src="'. Engine_Api::_()->document()->sslThumbnail($document->thumbnail) .'" class="thumb_normal" />', array('title' => $document->document_title)) ?>
						<?php else: ?>
							<?php echo $this->htmlLink($document->getHref(), '<img src="application/modules/Document/externals/images/document_thumb.png" class="thumb_normal" />', array('title' => $document->document_title)) ?>
						<?php endif;?>
	        </div>
					<div class="seaocore_browse_list_options">
						<?php if($this->can_view): ?>
							<?php echo $this->htmlLink($document->getHref(), $this->translate('View Document'), array('class' => 'buttonlink icon_type_document')) ?>
						<?php endif; ?>
						<?php if($this->can_edit): ?>
		        	<?php if($document->draft == 1) echo $this->htmlLink(array('route' => 'document_publish', 'document_id' => $document->document_id), $this->translate('Publish Document'), array(
		          'class'=>'buttonlink smoothbox icon_document_publish')) ?>   
		        	<?php echo $this->htmlLink(array('route' => 'document_edit', 'document_id' => $document->document_id), $this->translate('Edit Document'), array('class' => 'buttonlink icon_type_document_edit')) ?>
		        <?php endif; ?>
		        <?php if($this->can_delete): ?>
		        	<?php echo $this->htmlLink(array('route' => 'document_delete', 'document_id' => $document->document_id), $this->translate('Delete Document'), array(
		          'class'=>'buttonlink icon_type_document_delete')) ?>
		        <?php endif; ?>
		        <?php if($this->can_profile_doc && empty($document->profile_doc)): ?>
		        	<?php echo $this->htmlLink(array('route' => 'document_profile_doc', 'document_id' => $document->document_id), $this->translate('Make Profile Document'), array('class'=>'buttonlink smoothbox icon_type_document_mark')) ?>
						<?php elseif($this->can_profile_doc && !empty($document->profile_doc)): ?>
		        	<?php echo $this->htmlLink(array('route' => 'document_profile_doc', 'document_id' => $document->document_id), $this->translate('Remove as Profile Document'), array('class'=>'buttonlink smoothbox icon_type_document_unmark')) ?>
		        <?php endif; ?>  
		      </div>
	      	<div class='seaocore_browse_list_info'>
	        	<div class='seaocore_browse_list_info_title'>
	        		<span>
	          		<?php if($document->featured == 1): ?>
	          			<?php echo $this->htmlImage('application/modules/Seaocore/externals/images/featured.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
	          		<?php endif;?>
	        		</span>

	        		<span>
	          		<?php if($document->sponsored == 1): ?>
	          			<?php echo $this->htmlImage('application/modules/Seaocore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
	          		<?php endif;?>
	        		</span>
	          	<span>
	         	 		<?php if($document->approved != 1): ?>
	          			<?php echo $this->htmlImage('application/modules/Seaocore/externals/images/disapproved.gif', '', array('class' => 'icon', 'title' => $this->translate('Not Approved'))) ?>
	        			<?php endif;?>
	        		</span>
	           	
		        	<?php if(($document->rating > 0) && ($this->show_rate == 1)):?>
								<?php 
									$currentRatingValue = $document->rating;
									$difference = $currentRatingValue- (int)$currentRatingValue;
									if($difference < .5) {
										$finalRatingValue = (int)$currentRatingValue;
									}
									else {
										$finalRatingValue = (int)$currentRatingValue + .5;
									}	
								?>
								<span class="list_rating_star">
	          			<?php for($x = 1; $x <= $document->rating; $x++): ?>
										<span class="rating_star_generic rating_star" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">
										</span>
									<?php endfor; ?>
									<?php if((round($document->rating) - $document->rating) > 0):?>
										<span class="rating_star_generic rating_star_half" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">
										</span>
									<?php endif; ?>
								</span>	
	        		<?php endif; ?>
	        		
	        		<p>
								<?php
									$truncation = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.title.truncation', 0);
									$item_title = $document->document_title;
									if(empty($truncation)) {
										$item_title = Engine_Api::_()->document()->truncateText($item_title, 60);
									}
								?>
	        			<?php echo $this->htmlLink($document->getHref(), $item_title, array('title' => $document->document_title)) ?>
	        		</p>
	          </div>
	          <?php if($document->status == 0): ?>
	          	<div class="document_alert-message">
	          		<?php echo $this->htmlImage('application/modules/Document/externals/images/document_wait.gif', '', array('class' => 'icon')) ?>
	          		<?php echo $this->translate("Document format conversion in progress.") ?>
	          	</div>
	          <?php elseif($document->status == 2): ?>
	          	<div class="document_alert-message">
	            	<?php echo $this->htmlImage('application/modules/Document/externals/images/document_alert16.gif', '', array('class' => 'icon')) ?>
	            	<?php echo $this->translate("Format conversion for this document failed.") ?>
	            </div>
	          <?php elseif($document->status == 3): ?>
							<?php if(empty($this->can_edit) || empty($this->can_delete)): ?>
								<div class="document_alert-message">
									<?php echo $this->htmlImage('application/modules/Document/externals/images/document_alert16.gif', '', array('class' => 'icon')) ?>
									<?php echo $this->translate("This document has been deleted at Scribd") ?>
								</div>
							<?php else: ?>
								<div class="document_alert-message">
									<?php echo $this->htmlImage('application/modules/Document/externals/images/document_alert16.gif', '', array('class' => 'icon')) ?>
									<?php echo 	$this->translate('This document has been deleted at Scribd. Please ').$this->htmlLink(array('route' => 'document_delete', 'document_id' => $document->document_id), $this->translate('Delete')).$this->translate(' this document or ').$this->htmlLink(array('route' => 'document_edit', 'document_id' => $document->document_id), $this->translate('Edit')).$this->translate(' it to upload a new file.')	?>
								</div>
							<?php endif; ?>
						<?php endif;?>
	          <div class='seaocore_browse_list_info_date'>
	            <?php echo $this->translate('Created about %s', $this->timestamp($document->creation_date)) ?>,
	         		<?php echo $this->translate(array('%s comment', '%s comments', $document->comment_count), $this->locale()->toNumber($document->comment_count)) ?>,
	         		<?php echo $this->translate(array('%s view', '%s views', $document->views), $this->locale()->toNumber($document->views)) ?>,
	         		<?php echo $this->translate(array('%s like', '%s likes', $document->like_count), $this->locale()->toNumber($document->like_count)) ?>,
							<?php if($document->category_id): ?>
								<?php $category = Engine_Api::_()->getDbtable('categories', 'document')->getCategory($document->category_id); ?>
	             	<?php echo $this->translate('Category:');?> <a href='javascript:void(0);' onclick='javascript:categoryManageAction(<?php echo $document->category_id?>);'><?php echo $category->category_name ?></a>  
	            <?php endif; ?> 
	          </div>
		        <div class='seaocore_browse_list_info_blurb'>
		        	<?php echo Engine_Api::_()->document()->truncateText($document->document_description, 420); ?>
		        </div>
		      </div>
				</li>
		  <?php endforeach; ?>
		</ul>
	<?php elseif($this->search || $this->category || $this->draft):?>
		<div class="tip">
	    <span>
				<?php echo $this->translate('You do not have any documents matching your search criteria.'); ?>
		  </span>
	  </div>
	<?php else:?>
		<div class="tip">
	    <span>
			<?php echo $this->translate('You do not have any documents.'); ?>
		  <?php if ($this->can_create):?>
		  	<?php echo $this->translate('Get started by %1$screating%2$s one!', '<a href="'.$this->url(array(), 'document_create').'">', '</a>'); ?>
		  <?php endif; ?>
	  	</span>
	  </div>
	<?php endif; ?>
	<div>
		<?php echo $this->paginationControl($this->paginator, null, null, array('query' => $this->formValues,'pageAsQuery' => true,)); ?>
	</div>
</div>

<script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }

  var categoryManageAction = function(category){
    $('page').value = 1;
    $('category').value = category;

		if($type($('category_id'))) {
			$('category_id').value = category;

			var profile_type = getProfileType(category);
			$('profile_type').value = profile_type;
			changeFields($('profile_type'));
			subcategories(this.value, '', '');

		}

    $('filter_form').submit();
  }

  window.addEvent('onChangeFields', function() {
    var firstSep = $$('li.browse-separator-wrapper')[0];
    var lastSep;
    var nextEl = firstSep;
    var allHidden = true;
    do {
      nextEl = nextEl.getNext();
      if( nextEl.get('class') == 'browse-separator-wrapper' ) {
        lastSep = nextEl;
        nextEl = false;
      } else {
        allHidden = allHidden && ( nextEl.getStyle('display') == 'none' );
      }
    } while( nextEl );
    if( lastSep ) {
      lastSep.setStyle('display', (allHidden ? 'none' : ''));
    }
  });
</script>

<script type="text/javascript">

	var getProfileType = function(category_id) {
		var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('profilemaps', 'document')->getMapping()); ?>;
		for(i = 0; i < mapping.length; i++) {
			if(mapping[i].category_id == category_id)
				return mapping[i].profile_type;
		}
		return 0;
	}

  var form;

	if($('filter_form')) {
		var form = document.getElementById('filter_form');
	} else if($('filter_form_category')){
		var form = document.getElementById('filter_form_category');
	}

  var subcategories = function(category_id, sub, subcatname, subsubcat)
  {  

    if($('category_id') && form.elements['category_id']){
      form.elements['category_id'].value = '<?php echo $this->category_id?>';
    }
    if($('subcategory_id') && form.elements['subcategory_id']){
      form.elements['subcategory_id'].value = '<?php echo $this->subcategory_id?>';
    }
    if($('subsubcategory_id') && form.elements['subsubcategory_id']){
      form.elements['subsubcategory_id'].value = '<?php echo $this->subsubcategory_id?>';
    }
    if(category_id != '' && form.elements['category_id']){
      form.elements['category_id'].value = category_id;
    }
    if(category_id != 0) {
      if(sub == '')
     subsubcat = 0;
      changesubcategory(sub, subsubcat);
    }
    
  	var url = '<?php echo $this->url(array('action' => 'sub-category'), 'document_general', true);?>';
    en4.core.request.send(new Request.JSON({      	
      url : url,
      data : {
        format : 'json',
        category_id_temp : category_id
      },
      onSuccess : function(responseJSON) {
      	clear('subcategory_id');
        var  subcatss = responseJSON.subcats;        
        addOption($('subcategory_id')," ", '0');
        for (i=0; i< subcatss.length; i++) {
          addOption($('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']);  
          $('subcategory_id').value = sub;
          form.elements['subcategory'].value = $('subcategory_id').value;
        	form.elements['categoryname'].value = subcatss[i]['categoryname_temp'];
          form.elements['category'].value = category_id;
          form.elements['subcategory_id'].value = $('subcategory_id').value;
          if(form.elements['subsubcategory'])
          form.elements['subsubcategory'].value = subsubcat;
          if(form.elements['subsubcategory_id'])
          form.elements['subsubcategory_id'].value = subsubcat;
        }

        if(subcatss.length == 0) {
	      	form.elements['categoryname'].value = 0;
        }
        
        if(category_id == 0) {
          clear('subcategory_id');
          clear('subsubcategory_id');
          $('subcategory_id').style.display = 'none';
          $('subcategory_id-label').style.display = 'none';
          $('subsubcategory_id').style.display = 'none';
          $('subsubcategory_id-label').style.display = 'none';
        }
      }
    }));    
  };
  function clear(ddName)
  { 
    for (var i = (document.getElementById(ddName).options.length-1); i >= 0; i--) 
    { 
      document.getElementById(ddName).options[ i ]=null; 	      
    } 
  }
  function addOption(selectbox,text,value )
  {
    var optn = document.createElement("OPTION");
    optn.text = text;
    optn.value = value;			
    if(optn.text != '' && optn.value != '') {
      $('subcategory_id').style.display = 'block';
      $('subcategory_id-label').style.display = 'block';
      selectbox.options.add(optn);
    } 
    else {
      $('subcategory_id').style.display = 'none';
      $('subcategory_id-label').style.display = 'none';
      selectbox.options.add(optn);
    }
  }
  
	var changesubcategory = function(subcatid, subsubcat)
	{
		var url = '<?php echo $this->url(array('action' => 'subsub-category'), 'document_general', true);?>';
		var request = new Request.JSON({
			url : url,
			data : {
				format : 'json',
				subcategory_id_temp : subcatid
			},
			onSuccess : function(responseJSON) {
				clear('subsubcategory_id');
				var  subsubcatss = responseJSON.subsubcats;
				addSubOption($('subsubcategory_id')," ", '0');
				for (i=0; i< subsubcatss.length; i++) {
					addSubOption($('subsubcategory_id'), subsubcatss[i]['category_name'], subsubcatss[i]['category_id']);
					$('subsubcategory_id').value = subsubcat;
					if(form.elements[' subsubcategory_id'])
					form.elements[' subsubcategory_id'].value = $('subsubcategory_id').value;
					if(form.elements[' subsubcategory'])
					form.elements['subsubcategory'].value = $('subsubcategory_id').value;
					if($('subsubcategory_id')) {
						$('subsubcategory_id').value = subsubcat;
					}
				}

				if(subcatid == 0) {
					clear('subsubcategory_id');
					if($('subsubcategory_id-label'))
					$('subsubcategory_id-label').style.display = 'none';
				}
			}
		});
		request.send();
	};

	function addSubOption(selectbox,text,value )
	{
		var optn = document.createElement("OPTION");
		optn.text = text;
		optn.value = value;
		if(optn.text != '' && optn.value != '') {
			$('subsubcategory_id').style.display = 'block';
				if($('subsubcategory_id-wrapper'))
				$('subsubcategory_id-wrapper').style.display = 'block';
				if($('subsubcategory_id-label'))
				$('subsubcategory_id-label').style.display = 'block';
			selectbox.options.add(optn);
		} else {
			$('subsubcategory_id').style.display = 'none';
				if($('subsubcategory_id-wrapper'))
				$('subsubcategory_id-wrapper').style.display = 'none';
				if($('subsubcategory_id-label'))
				$('subsubcategory_id-label').style.display = 'none';
			selectbox.options.add(optn);
		}
	}

  var cat = '<?php echo $this->category_id ?>';		

  if(cat != '') {
    var sub = '<?php echo $this->subcategory_id; ?>';
    var subcatname = '<?php echo $this->subcategory_name; ?>';
    var subsubcat = '<?php echo $this->subsubcategory_id; ?>';
    subcategories(cat, sub, subcatname,subsubcat);
  }
  
  function show_subcat(cat_id) 
  {		
    if(document.getElementById('subcat_' + cat_id)) {
      if(document.getElementById('subcat_' + cat_id).style.display == 'block') {		
        document.getElementById('subcat_' + cat_id).style.display = 'none';
        document.getElementById('img_' + cat_id).src = './application/modules/Document/externals/images/plus16.gif';
      } 
      else if(document.getElementById('subcat_' + cat_id).style.display == '') {			
        document.getElementById('subcat_' + cat_id).style.display = 'none';
        document.getElementById('img_' + cat_id).src = './application/modules/Document/externals/images/plus16.gif';
      }
      else {			
        document.getElementById('subcat_' + cat_id).style.display = 'block';
        document.getElementById('img_' + cat_id).src = './application/modules/Document/externals/images/minus16.gif';
      }		
    }
  }
</script>