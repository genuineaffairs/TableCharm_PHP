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
<ul class="ui-listview collapsible-listview" >
    <?php $k = 0; ?>
    <?php for ($i = 0; $i <= count($this->categories); $i++) { ?>
        <li class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-btn-up-c">
            <?php
            $category = "";
            if (isset($this->categories[$k]) && !empty($this->categories[$k])) {
                $category = $this->categories[$k];
            }

            $k++;

            if (empty($category)) {
                break;
            } 
            ?>

            <?php $total_subcat = count($category['sub_categories']); ?>

            <!-- START FIRST CATEGORY DISPLAY WORK-->
            <?php if ($total_subcat > 0 && $this->show2ndlevelCategory) : ?>
                <div class="collapsible_icon" ><span class="ui-icon ui-icon-plus ui-icon-shadow">&nbsp;</span></div>
            <?php else: ?>
                <div class="collapsible_icon_none" ><span class="ui-icon ui-icon-circle ui-icon-shadow">&nbsp;</span></div>
            <?php endif; ?>

            <div class="ui-btn-inner ui-li" ><div class="ui-btn-text">
                    <a class="ui-link-inherit" href="<?php echo $this->url(array('category_id' => $category['category_id'], 'categoryname' => $this->tableCategory->getCategorySlug($category['category_name'])), 'document_browse') ?>"  >
                        <?php echo $this->translate($category['category_name']); ?> (<?php echo $category['count'] ?>)</a>             </div><span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>      
            <!--END FIRST CATEGORY DISPLAY WORK-->

            <!-- START SECOND CATEGORY DISPLAY WORK-->
            <?php if (!empty($this->show2ndlevelCategory)): ?>
                <ul class="collapsible">
                    <?php foreach ($category['sub_categories'] as $subcategory) : ?>
                        <?php
                        $subcategoryname = $this->translate($subcategory['sub_cat_name']);
                        $subcategoryname .= ' (' . ($subcategory['count']) . ')';
                        ?>
                    
                        <li class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li  ui-btn-up-c">    
                            <?php if (count($subcategory['tree_sub_cat']) > 0 &&  $this->show3rdlevelCategory) : ?>
                                <div class="collapsible_icon" ><span class="ui-icon ui-icon-plus ui-icon-shadow">&nbsp;</span></div>
                            <?php else: ?>
                                <div class="collapsible_icon_none" ><span class="ui-icon ui-icon-circle ui-icon-shadow">&nbsp;</span></div>
                            <?php endif; ?>

                            <div class="ui-btn-inner ui-li" ><div class="ui-btn-text">
                                    <a class="ui-link-inherit" href="<?php echo $this->url(array('category_id' => $category['category_id'], 'categoryname' => $this->tableCategory->getCategorySlug($category['category_name']), 'subcategory_id' => $subcategory['sub_cat_id'], 'subcategoryname' => $this->tableCategory->getCategorySlug($this->translate($subcategory['sub_cat_name']))), 'document_browse') ?>"  >
                                        <?php echo $this->translate($subcategoryname); ?></a>           
                                </div><span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>  
                            <!--END SECOND CATEGORY DISPLAY WORK-->

                            <!-- START THIRD CATEGORY DISPLAY WORK-->
                            <?php if (!empty($this->show3rdlevelCategory)): ?>
                                <ul class="collapsible">
                                    <?php if (count($subcategory['tree_sub_cat']) > 0): ?>

                                        <?php foreach ($subcategory['tree_sub_cat'] as $subsubcategory) : ?>
                                            <?php
                                            $subsubcategoryname = $this->translate($subsubcategory['tree_sub_cat_name']);
                                            $subsubcategoryname .= ' (' . ($subsubcategory['count']) . ') ';
                                            ?>
                                            <li class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li  ui-btn-up-c">                              
                                                <?php if (isset($subsubcategory['tree_sub_cat'])) : ?>
                                                    <div class="collapsible_icon" ><span class="ui-icon ui-icon-plus ui-icon-shadow">&nbsp;</span></div>
                                                <?php else: ?>
                                                    <div class="collapsible_icon_none" ><span class="ui-icon ui-icon-circle ui-icon-shadow">&nbsp;</span></div>
                                                <?php endif; ?>

                                                <div class="ui-btn-inner ui-li" ><div class="ui-btn-text">
                                                        <a class="ui-link-inherit" href="<?php echo $this->url(array('category_id' => $category['category_id'], 'categoryname' => $this->tableCategory->getCategorySlug($category['category_name']), 'subcategory_id' => $subcategory['sub_cat_id'], 'subcategoryname' => $this->tableCategory->getCategorySlug($this->translate($subcategory['sub_cat_name'])), 'subsubcategory_id' => $subsubcategory['tree_sub_cat_id'], 'subsubcategoryname' => $this->tableCategory->getCategorySlug($this->translate($subsubcategory['tree_sub_cat_name']))), 'document_browse') ?>"  >
                                                            <?php echo $this->translate($subsubcategoryname); ?></a>           
                                                    </div><span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>  
                                                <!--END THIRD CATEGORY DISPLAY WORK-->
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </li>
    <?php } ?>

</ul>
