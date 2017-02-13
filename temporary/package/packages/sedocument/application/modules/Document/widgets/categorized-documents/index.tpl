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
<?php 
	$this->headLink()->appendStylesheet($this->seaddonsBaseUrl()
  	              . '/application/modules/Seaocore/externals/styles/styles.css');
?>
<script type="text/javascript">

	function showDocumentPhoto(ImagePath, category_id, document_id) {
    var elem = document.getElementById('document_elements_'+category_id).getElementsByTagName('a'); 
    for(var i = 0; i < elem.length; i++)
    { 
			var cat_documentid = elem[i].id;
			$(cat_documentid).erase('class');
		}
    $('document_link_class_'+document_id).set('class', 'active');
    
		$('documentImage_'+category_id).src = ImagePath;
	}

</script>

<ul class="seaocore_categories_box">
  <li> 
    <?php $ceil_count = 0; $k = 0; ?>
    <?php for ($i = 0; $i <= count($this->categories); $i++) { ?>
			<?php if($ceil_count == 0) :?>      
				<div>      
			<?php endif;?>  
			<div class="seaocore_categories_list_row">
				<?php $ceil_count++;?>				
				<?php $category = "";
					if (isset($this->categories[$k]) && !empty($this->categories[$k])) {
						$category = $this->categories[$k];
					}
					$k++;

					if (empty($category)) {
						break;
					}
				?>

				<div class="seaocore_categories_list">
					<?php $total_subcat = Count($category['category_documents']); ?>
					<h6>
						<?php echo $this->htmlLink($this->url(array('category_id' => $category['category_id'], 'categoryname' => Engine_Api::_()->getDbTable('categories', 'document')->getCategorySlug($category['category_name'])), 'document_browse'), $this->translate($category['category_name'])) ?>
					</h6>	
					<div class="sub_cat" id="subcat_<?php echo $category['category_id'] ?>">

						<?php $total_count = 1; ?>
		
						<?php foreach ($category['category_documents'] as $categoryDocuments) : ?>

							<?php 
								$imageSrc = $categoryDocuments['imageSrc']; 
								if(empty($imageSrc)) {
									$imageSrc = './application/modules/Document/externals/images/document16.png';
								}

								$category_id = $category['category_id'];
								$document_id = $categoryDocuments['document_id'];
							?>

							<?php
								$truncation = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.title.truncation', 0);
								if(empty($truncation)) {
									$tmpBody = strip_tags($categoryDocuments['document_title']);
									$document_title = ( Engine_String::strlen($tmpBody) > 25 ? Engine_String::substr($tmpBody, 0, 25) . '..' : $tmpBody );
								}
								else {
									$document_title = $categoryDocuments['document_title'];
								}
							?>

							<?php if($total_count == 1): ?>
								<div class="seaocore_categories_img" >
									<img src="<?php echo $imageSrc; ?>" id="documentImage_<?php echo $category['category_id'] ?>" alt="" class="thumb_icon" />
								</div>
								<div id='document_elements_<?php echo $category_id;?>'>

								<?php echo $this->htmlLink(Engine_Api::_()->document()->getHref($categoryDocuments['document_id'], $categoryDocuments['owner_id'], $categoryDocuments['document_title']), $document_title." (".$categoryDocuments['populirityCount'].")", array('onmouseover' => "javascript:showDocumentPhoto('$imageSrc', '$category_id', '$document_id');",'title' => $categoryDocuments['document_title'], 'class'=>'active', 'id'=>"document_link_class_$document_id"));?>

							<?php else: ?>
								<?php echo $this->htmlLink(Engine_Api::_()->document()->getHref($categoryDocuments['document_id'], $categoryDocuments['owner_id'], $categoryDocuments['document_title']), $document_title." (".$categoryDocuments['populirityCount'].")", array('onmouseover' => "javascript:showDocumentPhoto('$imageSrc', '$category_id', '$document_id');",'title' => $categoryDocuments['document_title'], 'id'=>"document_link_class_$document_id"));?>
							<?php endif; ?>

							<?php $total_count++; ?>
            <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
     <?php if($ceil_count %2 == 0) :?>      
     </div>
     <?php $ceil_count=0; ?>
     <?php endif;?>
    <?php } ?> 
  </li>	
</ul>