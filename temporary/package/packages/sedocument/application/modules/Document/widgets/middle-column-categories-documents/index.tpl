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

<ul class="seaocore_categories_box">
  <li>
    <?php $ceil_count = 0; $k = 0; ?>
    <?php for ($i = 0; $i <= count($this->categories); $i++) { ?>

			<?php if($ceil_count == 0) :?>
				<div>
			<?php endif;?>

			<div class="seaocore_categories_list_col">
				<?php $ceil_count++; ?>
					<?php 
						$category = "";
						if (isset($this->categories[$k]) && !empty($this->categories[$k])) {
							$category = $this->categories[$k];
						}

						$k++; 

						if(empty($category)) { 
							break;
						}
					?>

          <div class="seaocore_categories_list">
            <?php $total_subcat = count($category['sub_categories']); ?>

            <h6>

						<?php echo $this->htmlLink($this->url(array('category_id' => $category['category_id'], 'categoryname' => $this->tableCategory->getCategorySlug($category['category_name'])), 'document_browse'), $this->translate($category['category_name'])) ?> (<?php echo $category['count'] ?>)

            </h6>
            
						<?php if(!empty($this->show2ndlevelCategory)):?>
							<div class="sub_cat" id="subcat_<?php echo $category['category_id'] ?>">
								<?php foreach ($category['sub_categories'] as $subcategory) : ?>
									<?php $subcategoryname = '<img src="./application/modules/Document/externals/images/gray_bullet.png" alt="">' . $this->translate($subcategory['sub_cat_name']) ;                 
										$subcategoryname .= ' (' . ($subcategory['count']) . ')';
									?>

									<?php echo $this->htmlLink($this->url(array('category_id' => $category['category_id'], 'categoryname' => $this->tableCategory->getCategorySlug($category['category_name']), 'subcategory_id' => $subcategory['sub_cat_id'], 'subcategoryname' => $this->tableCategory->getCategorySlug($this->translate($subcategory['sub_cat_name']))), 'document_browse'), $this->translate($subcategoryname)) ?>

									<?php if(!empty($this->show3rdlevelCategory)):?>
										<?php if(isset($subcategory['tree_sub_cat'])):?>

											<?php foreach ($subcategory['tree_sub_cat'] as $subsubcategory) : ?>
												<?php $subsubcategoryname = '<img src="./application/modules/Document/externals/images/gray_arrow.png" alt="">' . $this->translate($subsubcategory['tree_sub_cat_name']);                                      
													$subsubcategoryname .= ' (' . ($subsubcategory['count']) . ') ';                
												?>

												<?php echo $this->htmlLink($this->url(array('category_id' => $category['category_id'], 'categoryname' => $this->tableCategory->getCategorySlug($category['category_name']), 'subcategory_id' => $subcategory['sub_cat_id'], 'subcategoryname' => $this->tableCategory->getCategorySlug($this->translate($subcategory['sub_cat_name'])), 'subsubcategory_id' => $subsubcategory['tree_sub_cat_id'], 'subsubcategoryname' => $this->tableCategory->getCategorySlug($this->translate($subsubcategory['tree_sub_cat_name']))), 'document_browse'), $this->translate($subsubcategoryname)) ?>

											<?php endforeach; ?>
										 <?php endif;?>
										<?php endif;?>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
				</div>
			<?php if($ceil_count %3 == 0) :?>
				</div>
				<?php $ceil_count = 0; ?>
			<?php endif;?>
    <?php } ?>
  </li>
</ul>
