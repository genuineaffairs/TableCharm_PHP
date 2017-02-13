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

	var form;

  var categoryAction =function(category,sub,categoryname, url)
  {
	  if($('filter_form')) {
	    var form = document.getElementById('filter_form');

			if($('category')){
				form.elements['category'].value = category;
			}

			if($('category_id')){
				form.elements['category_id'].value = category;
			}

			if($('categoryname')){
				form.elements['categoryname'].value = categoryname;
			}

			if($('subcategory')){
				form.elements['subcategory'].value = 0;
			}

			if($('subcategoryname')){
				form.elements['subcategoryname'].value = 0;
			}

			form.submit();
	  } 
		else if($('filter_form_category')){
			var form = document.getElementById('filter_form_category');

			if($('category')){
				form.elements['category'].value = category;
			}

			if($('category_id')){
				form.elements['category_id'].value = category;
			}

			if($('categoryname')){
				form.elements['categoryname'].value = categoryname;
			}

			if($('subcategory')){
				form.elements['subcategory'].value = 0;
			}

			if($('subcategoryname')){
				form.elements['subcategoryname'].value = 0;
			}

			form.submit();

//       if(url == '') {
//         window.location.href='<?php //echo $this->url(array('action' => 'browse'), 'document_browse', true) ?>';
//       } else {
//         window.location.href= url;
//       }
		}
  }

 	var subcategoryAction = function(category,subcategory,categoryname,subcategoryname, url, subsubcategory, subsubcategoryname)
 	{
    if($('filter_form')) {
      var form = document.getElementById('filter_form');

      if($('category')){
				form.elements['category'].value = category;
				form.elements['category_id'].value = 0;
    	}

	    if($('categoryname')){
	     form.elements['categoryname'].value = categoryname;
	    }

  	  if($('subcategory')){
	     form.elements['subcategory'].value = subcategory;
       form.elements['subcategory_id'].value = 0;
	    }
	    if($('subcategoryname')){
	     form.elements['subcategoryname'].value = subcategoryname;
	    }
      
	    if($('subsubcategory')){
	     form.elements['subsubcategory'].value = subsubcategory;
       form.elements['subsubcategory_id'].value = 0;
	    }

	    if($('subsubcategoryname')){
	     form.elements['subsubcategoryname'].value = subsubcategoryname;
	    }

      form.submit();
    } 
		else if($('filter_form_category')){
			var form = document.getElementById('filter_form_category');

			if($('category')){
				form.elements['category'].value = category;
			}

			if($('category_id')){
				form.elements['category_id'].value = category;
			}

			if($('categoryname')){
				form.elements['categoryname'].value = categoryname;
			}

			if($('subcategory')){
				form.elements['subcategory'].value = 0;
			}

			if($('subcategoryname')){
				form.elements['subcategoryname'].value = 0;
			}

			form.submit();

// 			if(url == '') {
//         window.location.href='<?php //echo $this->url(array('action' => 'browse'), 'document_browse', true) ?>';
//       } else {
//         window.location.href= url;
//       }
		}
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

  function show_subsubcat(cat_id)
  {
    if(document.getElementById('subsubcat_' + cat_id)) {
      if(document.getElementById('subsubcat_' + cat_id).style.display == 'block') {
        document.getElementById('subsubcat_' + cat_id).style.display = 'none';
        document.getElementById('img_' + cat_id).src = './application/modules/Document/externals/images/plus16.gif';
      }
      else if(document.getElementById('subsubcat_' + cat_id).style.display == '') {
        document.getElementById('subsubcat_' + cat_id).style.display = 'none';
        document.getElementById('img_' + cat_id).src = './application/modules/Document/externals/images/plus16.gif';
      }
      else {
        document.getElementById('subsubcat_' + cat_id).style.display = 'block';
        document.getElementById('img_' + cat_id).src = './application/modules/Document/externals/images/minus16.gif';
      }
    }
  }
</script>

<?php if (count($this->categories)):?>
	<form id='filter_form_category' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'browse'), 'document_browse', true) ?>' style='display: none;'>
		<input type="hidden" id="category" name="category"  value=""/>
		<input type="hidden" id="category_id" name="category_id"  value=""/>
		<input type="hidden" id="categoryname" name="categoryname"  value=""/>
		<input type="hidden" id="subsubcategory" name="subsubcategory" value=""/>
		<input type="hidden" id="subcategory" name="subcategory"  value=""/>
		<input type="hidden" id="subcategoryname" name="subcategoryname"  value=""/>
		<input type="hidden" id="subsubcategoryname" name="subsubcategoryname" value=""/>
	</form>
  <ul class="seaocore_browse_category">
    <li>
      <a href="javascript:subcategoryAction(0,0,0,0,0,0,0)" <?php if ($this->category == 0): ?>class="bold"<?php endif; ?>><?php echo $this->translate("All Categories") ?></a>
    </li>
    <?php foreach ($this->categories as $category):?>
      <?php $total_subcat = count($category['sub_categories']); ?>

      <?php if ($total_subcat > 0): ?>
        <li>
          <div class="cat" >
            <a href="javascript:show_subcat('<?php echo $category['category_id'] ?>')" id='button_<?php echo $category['category_id'] ?>'>
              <?php if ($this->category != $category['category_id']): ?>
                <img alt=""  src='./application/modules/Document/externals/images/plus16.gif' class='icon' border='0' id='img_<?php echo $category['category_id'] ?>'/>
              <?php elseif ($this->subcategorys != 0 && $this->category == $category['category_id']): ?>
                <img alt="" src='./application/modules/Document/externals/images/minus16.gif' class='icon' border='0' id='img_<?php echo $category['category_id'] ?>'/>
              <?php elseif ($this->category != 0 && $this->category == $category['category_id']): ?>
                <img alt="" src='./application/modules/Document/externals/images/plus16.gif' class='icon' border='0' id='img_<?php echo $category['category_id'] ?>'/>
              <?php endif; ?>
           </a>
            <a <?php if ($this->category == $category['category_id']): ?>class="bold"<?php endif; ?> href='javascript:void(0);' onclick="javascript:subcategoryAction('<?php echo $category['category_id'] ?>',0, '<?php echo Engine_Api::_()->getDbTable('categories', 'document')->getCategorySlug($category['category_name']) ?>',0, '<?php echo $this->url(array('category' => $category["category_id"]), 'document_browse', true);?>', 0, 0);">
              <?php echo $this->translate($category['category_name']) ?>
            </a>
          </div>
          
            <div class="subcat" id="subcat_<?php echo $category['category_id'] ?>" <?php if ($this->category != $category['category_id'] || $this->subcategorys == 0): ?>style="display:none;"<?php endif; ?> >
              <?php foreach ($category['sub_categories'] as $subcategory) : ?>
                <?php $total_subsubcat = count($subcategory['tree_sub_cat']); ?>
                <?php if ($total_subsubcat > 0): ?>
                	<div class="subcat_second">
	                  <a href="javascript:show_subsubcat('<?php echo $subcategory['sub_cat_id'] ?>')" id='button_<?php echo $subcategory['sub_cat_id'] ?>'>
	                    <?php if ($this->subcategorys != $subcategory['sub_cat_id']): ?>
	                    <img alt=""  src='./application/modules/Document/externals/images/plus16.gif' class='icon' border='0' id='img_<?php echo $subcategory['sub_cat_id'] ?>'/>
	                  <?php elseif ($this->subsubcategorys != 0 && $this->subcategorys == $subcategory['sub_cat_id']): ?>
	                    <img alt="" src='./application/modules/Document/externals/images/minus16.gif' class='icon' border='0' id='img_<?php echo $subcategory['sub_cat_id'] ?>'/>
	                  <?php elseif ($this->subcategorys != 0 && $this->subcategorys == $subcategory['sub_cat_id']): ?>
	                    <img alt="" src='./application/modules/Document/externals/images/plus16.gif' class='icon' border='0' id='img_<?php echo $subcategory['sub_cat_id'] ?>'/>
	                  <?php endif; ?>
	                  </a>
	                  <a <?php if ($this->subcategorys == $subcategory['sub_cat_id']): ?>class="bold"<?php endif; ?>  href='javascript:void(0);' onclick="javascript:subcategoryAction('<?php echo $category['category_id'] ?>','<?php echo $subcategory['sub_cat_id'] ?>','<?php echo Engine_Api::_()->getDbTable('categories', 'document')->getCategorySlug($category['category_name']) ?>','<?php echo Engine_Api::_()->getDbTable('categories', 'document')->getCategorySlug($subcategory['sub_cat_name']) ?>','<?php echo $this->url(array('category' => $category["category_id"], 'subcategory' => $subcategory["sub_cat_id"]), 'document_browse', true);?>', 0, 0);">
	                    <?php echo $this->translate($subcategory['sub_cat_name']) ?>
	                  </a>
	                </div>  
                  <div class="subcat_third" id="subsubcat_<?php echo $subcategory['sub_cat_id'] ?>" <?php if ($this->subcategorys != $subcategory['sub_cat_id'] || $this->subsubcategorys == 0): ?>style="display:none;"<?php endif; ?> >
                      <?php if(isset($subcategory['tree_sub_cat'])):?>
                      <?php foreach ($subcategory['tree_sub_cat'] as $subsubcategory) : ?>
                        <a <?php if ($this->subsubcategorys == $subsubcategory['tree_sub_cat_id']): ?>class="bold"<?php endif; ?>  href='javascript:void(0);' onclick="javascript:subcategoryAction('<?php echo $category['category_id'] ?>','<?php echo $subcategory['sub_cat_id'] ?>','<?php echo Engine_Api::_()->getDbTable('categories', 'document')->getCategorySlug($category['category_name']) ?>','<?php echo Engine_Api::_()->getDbTable('categories', 'document')->getCategorySlug($subcategory['sub_cat_name']) ?>','<?php echo $this->url(array('category' => $category["category_id"], 'subcategory' => $subcategory["sub_cat_id"]), 'document_browse', true);?>', '<?php echo $subsubcategory['tree_sub_cat_id'] ?>', '<?php echo $subsubcategory['tree_sub_cat_name']?>');">
                        	<img src="./application/modules/Document/externals/images/gray_arrow.png" alt="">
                          <?php echo $this->translate($subsubcategory['tree_sub_cat_name']); ?>
                        </a>
                      <?php endforeach; ?>
                      <?php endif;?>
                  </div>
                <?php else:?>
                	<div class="subcat_second">
	                  <img  src="./application/modules/Document/externals/images/minus16_disabled.gif" class='icon' border="0" />
	                  <a <?php if ($this->subcategorys == $subcategory['sub_cat_id']): ?>class="bold"<?php endif; ?>  href='javascript:void(0);' onclick="javascript:subcategoryAction('<?php echo $category['category_id'] ?>','<?php echo $subcategory['sub_cat_id'] ?>','<?php echo Engine_Api::_()->getDbTable('categories', 'document')->getCategorySlug($category['category_name']) ?>','<?php echo Engine_Api::_()->getDbTable('categories', 'document')->getCategorySlug($subcategory['sub_cat_name']) ?>','<?php echo $this->url(array('category' => $category["category_id"], 'subcategory' => $subcategory["sub_cat_id"]), 'document_browse', true);?>',0, 0);">
	                   <?php echo $this->translate($subcategory['sub_cat_name']); ?>
	                  </a>
	                </div>  
                <?php endif;?>
               <?php endforeach; ?>
            </div>
        </li>
      <?php else: ?>
        <li>
          <div class="cat">
            <img  src="./application/modules/Document/externals/images/minus16_disabled.gif" class='icon' border="0" />
            <a <?php if ($this->category == $category['category_id']): ?>class="bold"<?php endif; ?>  href='javascript:void(0);' onclick="javascript:categoryAction('<?php echo $category["category_id"] ?>',0, '<?php echo Engine_Api::_()->getDbTable('categories', 'document')->getCategorySlug($category["category_name"]) ?>','<?php echo $this->url(array('category' => $category["category_id"]), 'document_browse', true);?>');"><?php echo $this->translate($category['category_name']) ?>
            </a>
          </div>
        </li>
      <?php endif; ?>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
