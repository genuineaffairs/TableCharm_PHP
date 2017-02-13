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

<h2><?php echo $this->translate('Documents Plugin'); ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<h3><?php echo $this->translate('Manage Documents'); ?></h3>

<p>
  <?php echo $this->translate('This page lists all the documents uploaded by the users. Here, you can monitor documents, delete them, make documents featured / un-featured, sponsored / un-sponsored and also approve / dis-approve them.');?>
</p>

<br />
<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction){

    if( order == currentOrder ) {
      $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
			$('order').value = order;
    } else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('search_filter_form').submit();
  }

	function multiDelete()
	{
		return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected documents ?")) ?>');
	}

	function selectAll()
	{
	  var i;
	  var multidelete_form = $('multidelete_form');
	  var inputs = multidelete_form.elements;
	  for (i = 1; i < inputs.length - 1; i++) {
	    if (!inputs[i].disabled) {
	      inputs[i].checked = inputs[0].checked;
    	}
  	}
	}
</script>

<div class="admin_search document_admin_search">
  <div class="search">
    <form id ="search_filter_form" method="get" class="global_form_box">
      <div>
	      <label>
	      	<?php echo  $this->translate("Title") ?>
	      </label>
	      <?php if( empty($this->document_title)):?>
	      	<input type="text" name="document_title" /> 
	      <?php else: ?>
	      	<input type="text" name="document_title" value="<?php echo $this->translate($this->document_title)?>"/>
	      <?php endif;?>
      </div>
      <div>
      	<label>
      		<?php echo  $this->translate("Owner") ?>
      	</label>	
      	<?php if( empty($this->owner)):?>
      		<input type="text" name="owner" /> 
      	<?php else: ?> 
      		<input type="text" name="owner" value="<?php echo $this->translate($this->owner)?>" />
      	<?php endif;?>
      </div>
      <div>
	    	<label>
	      	<?php echo  $this->translate("Featured") ?>	
	      </label>
        <select id="featured" name="featured">
          <option value="0" ></option>
          <option value="2" <?php if( $this->featured == 2) echo "selected";?> ><?php echo $this->translate("Yes") ?></option>
          <option value="1" <?php if( $this->featured == 1) echo "selected";?> ><?php echo $this->translate("No") ?></option>
         </select>
      </div>
      <div>
	    	<label>
	      	<?php echo  $this->translate("Sponsored") ?>	
	      </label>
        <select id="sponsored" name="sponsored">
            <option value="0"  ></option>
          <option value="2" <?php if( $this->sponsored == 2) echo "selected";?> ><?php echo $this->translate("Yes") ?></option>
          <option value="1"  <?php if( $this->sponsored == 1) echo "selected";?>><?php echo $this->translate("No") ?></option>
         </select>
      </div>  
      <div>
	    	<label>
	      	<?php echo  $this->translate("Approved") ?>
	      </label>
        <select id="approved" name="approved">
          <option value="0" ></option>
          <option value="2" <?php if( $this->approved == 2) echo "selected";?> ><?php echo $this->translate("Yes") ?></option>
          <option value="1" <?php if( $this->approved == 1) echo "selected";?> ><?php echo $this->translate("No") ?></option>
        </select>
      </div>
      <?php  $categories = Engine_Api::_()->getDbTable('categories', 'document')->getCategories(); ?>
      <?php if(count($categories) > 0) :?>
        <div>
          <label>
            <?php echo  $this->translate("Category") ?>
          </label>
           <select id="" name="category_id" onchange="subcategory(this.value, '', '', '');">
            <option value="<?php echo $this->category_id; ?>"></option>
             <?php if (count($categories) != 0) : ?>
              <?php $categories_prepared[0] = "";
                  foreach ($categories as $category) {
                    $categories_prepared[$category->category_id] = $category->category_name; ?>
                    <option value="<?php echo $category->category_id;?>" <?php if( $this->category_id == $category->category_id) echo "selected";?>><?php echo $this->translate($category->category_name);?></option>
                 <?php } ?>
             <?php endif ; ?>
          </select>
        </div>

			<div id="subcategory_backgroundimage" class="cat_loader"><img src="application/modules/Core/externals/images/loading.gif" /></div>
		 	<div id="subcategory_id-label">
				<label>
						<?php echo  $this->translate("Subcategory") ?>	
				</label>
				
				<select name="subcategory_id" id="subcategory_id" onchange="changesubcategory(this.value, '')"></select>
			</div>
      <div id="subsubcategory_backgroundimage" class="cat_loader"><img src="application/modules/Core/externals/images/loading.gif" /></div>
		 	<div id="subsubcategory_id-label">
				<label>
						<?php echo  $this->translate('3%s Level Category', "<sup>rd</sup>") ?>
				</label>
				<select name="subsubcategory_id" id="subsubcategory_id"></select>
			</div>
      <?php endif;?>
      <div>
	    	<label>
	      	<?php echo  $this->translate("Browse By") ?>	
	      </label>
        <select id="" name="document_browse">
          <option value="" ></option>
          <option value="views" <?php if( $this->document_browse == 'views') echo "selected";?> ><?php echo $this->translate("Most Viewed") ?></option>
          <option value="document_id" <?php if( $this->document_browse == 'document_id') echo "selected";?> ><?php echo $this->translate("Most Recent") ?></option>
					<option value="comment_count" <?php if( $this->document_browse == 'comment_count') echo "selected";?> ><?php echo $this->translate("Most Commented") ?></option>  
					<option value="like_count" <?php if( $this->document_browse == 'like_count') echo "selected";?> ><?php echo $this->translate("Most Liked") ?></option>					        
         </select>
      </div>

      <div>
				<input type= "hidden" id="order" name="order" value="<?php echo $this->order; ?>">
      </div>

      <div>
				<input type= "hidden" id="order_direction" name="order_direction" value="<?php echo $this->order_direction ?>">
      </div>

      <div class="document_search_button">
        <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
      </div>
    </form>
  </div>
</div>
<br />

<?php if($this->paginator->getTotalItemCount()): ?>

	<div class='admin_members_results'>
		<div>
			<?php echo $this->translate(array('%s document found', '%s documents found', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
		</div>
		<?php echo $this->paginationControl($this->paginator, null, null, array(
				'pageAsQuery' => true,
				'query' => $this->formValues,
			));
		?>
	</div>

	<br />

	<form id='multidelete_form' method="post" action="<?php echo $this->url(array('action'=>'multi-delete'));?>" onSubmit="return multiDelete()">
		<table class='admin_table seaocore_admin_table'>
			<thead>
				<tr>
					<th style='width: 1%;' align="center"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
					<th style='width: 1%;' align="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('document_id', 'DESC');"><?php echo $this->translate('ID'); ?></a></th>
					<th style='width: 2%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('document_title', 'ASC');"><?php echo $this->translate('Title'); ?></a></th>
					<th style='width: 2%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate('Owner');?></a></th>
					<th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('featured', 'ASC');"><?php echo $this->translate('Featured'); ?></a></th>
					<th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('sponsored', 'ASC');"><?php echo $this->translate('Sponsored'); ?></a></th>
					<th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('approved', 'ASC');"><?php echo $this->translate('Approved'); ?></a></th>
					<th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('category_name', 'ASC');"><?php echo $this->translate('Category');?></a></th>
					<th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('views', 'ASC');"><?php echo $this->translate('Views'); ?></a></th>
					<th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('comment_count', 'ASC');"><?php echo $this->translate('Comments'); ?></a></th>
					<th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('like_count', 'ASC');"><?php echo $this->translate('Likes'); ?></a></th>
					<th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate('Creation Date'); ?></a></th>
					<th style='width: 3%;' class='admin_table_options' align="left"><?php echo $this->translate('Options'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if( count($this->paginator) ): ?>
					<?php foreach( $this->paginator as $item ): ?>
						<tr>
							
							<td class="admin_table_centered"><input name='delete_<?php echo $item->document_id;?>' type='checkbox' class='checkbox' value="<?php echo $item->document_id ?>"/></td>
							
							<td class="admin_table_centered"><?php echo $item->document_id ?></td>
							
							<td class='admin_table_bold'>
								<?php $item_title = Engine_Api::_()->document()->truncateText($item->document_title, 10); ?>
								<?php echo $this->htmlLink($item->getHref(), $item_title, array('title' => $item->document_title, 'target' => '_blank')) ?>
							</td>
							<?php 
								$owner_name = $this->user($item->owner_id)->username;
								$truncate_owner_name = Engine_Api::_()->document()->truncateText($owner_name, 10);
							?>		
							<td class='admin_table_bold'><?php echo $this->htmlLink($this->item('user', $item->owner_id)->getHref()	, $truncate_owner_name, array('target' => '_blank')) ?></td>
							
							<?php if($item->featured == 1):?>
								<td align="center" class="admin_table_centered">
									<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'document', 'controller' => 'admin-manage', 'action' => 'featured', 'document_id' => $item->document_id), $this->htmlImage('application/modules/Seaocore/externals/images/featured.gif', '', array('title'=> $this->translate('Make Un-featured')))) ?>
								</td>
							<?php else: ?>  
								<td align="center" class="admin_table_centered"> 
									<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'document', 'controller' => 'admin-manage', 'action' => 'featured', 'document_id' => $item->document_id), $this->htmlImage('application/modules/Seaocore/externals/images/unfeatured.gif', '', array('title'=> $this->translate('Make Featured')))) ?>
								</td>
							<?php endif; ?>

							<?php if($item->sponsored == 1):?>
								<td align="center" class="admin_table_centered"> <?php   echo $this->htmlLink(array('route' => 'default', 'module' => 'document', 'controller' => 'admin-manage', 'action' => 'sponsored', 'document_id' => $item->document_id), $this->htmlImage('application/modules/Seaocore/externals/images/sponsored.png', '', array('title'=> $this->translate('Make Unsponsored')))); ?>
							<?php else: ?>
								<td align="center" class="admin_table_centered"> <?php   echo $this->htmlLink(array('route' => 'default', 'module' => 'document', 'controller' => 'admin-manage', 'action' => 'sponsored', 'document_id' => $item->document_id), $this->htmlImage('application/modules/Seaocore/externals/images/unsponsored.png', '', array('title'=> $this->translate('Make Sponsored')))); ?>
								</td>
							<?php endif; ?>   

							<?php if($item->approved == 1):?>
								<td align="center" class="admin_table_centered">
									<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'document', 'controller' => 'admin-manage', 'action' => 'approved', 'document_id' => $item->document_id), $this->htmlImage('application/modules/Seaocore/externals/images/approved.gif', '', array('title'=> $this->translate('Dis-approve document')))) ?> 
								</td>       
							<?php else: ?>  
								<td align="center" class="admin_table_centered"> 
									<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'document', 'controller' => 'admin-manage', 'action' => 'approved', 'document_id' => $item->document_id), $this->htmlImage('application/modules/Seaocore/externals/images/disapproved.gif', '', array('title'=> $this->translate('Approve document')))) ?>
								</td>
							<?php endif; ?>
							
							<td align="center">
								<?php if($item->category_id && $item->category_name):?>
									<?php echo $item->category_name; ?>
								<?php else: ?>
									---
								<?php endif;?>
							</td>
							
							<td align="center" class="admin_table_centered"><?php echo $item->views ?></td>
							
							<td align="center" class="admin_table_centered"><?php echo $item->comment_count ?></td>

							<td align="center" class="admin_table_centered"><?php echo $item->like_count ?></td>
							
							<td align="center"><?php echo $item->creation_date ?></td>
							
							<td class='admin_table_options'>
								<a href="<?php echo $this->url(array('user_id' => $item->owner_id, 'document_id' => $item->document_id), 'document_detail_view') ?>">
									<?php echo $this->translate("view"); ?> 
								</a>
								|
								<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'document', 'controller' => 'admin-manage', 'action' => 'delete', 'document_id' => $item->document_id), $this->translate('delete'), array(
									'class' => 'smoothbox',
								)) ?> 
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
		<br />
		<div class='buttons'>
			<button type='submit'><?php echo $this->translate('Delete Selected'); ?></button>
		</div>
	</form>
<?php else: ?>
	<div class="tip">
		<span>
			<?php echo $this->translate('No results were found.');?>
		</span>
	</div>
<?php endif; ?>

<script type="text/javascript">
	var subcategory = function(category_id, sub, subcatname, subsubcate)
	{
    if($('subcategory_backgroundimage'))
		$('subcategory_backgroundimage').style.display = 'block';
    if($('subcategory_id'))
		$('subcategory_id').style.display = 'none';
    if($('subcategory_id-label'))
		$('subcategory_id-label').style.display = 'none';
    if($('subsubcategory_id'))
		$('subsubcategory_id').style.display = 'none';
    if($('subsubcategory_id-label'))
		$('subsubcategory_id-label').style.display = 'none';
    changesubcategory(sub,subsubcate)
	  var url = '<?php echo $this->url(array('action' => 'sub-category'), 'document_general', true);?>';
		en4.core.request.send(new Request.JSON({      	
			 url : url,
			data : {
				format : 'json',
				category_id_temp : category_id
				
			},
			onSuccess : function(responseJSON) {
        if($('subcategory_backgroundimage'))
				$('subcategory_backgroundimage').style.display = 'none';				
				clear('subcategory_id');				
	    	var  subcatss = responseJSON.subcats;
	      addOption($('subcategory_id')," ", '0');
        for (i=0; i< subcatss.length; i++) {
         addOption($('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']);
           $('subcategory_id').value = sub;
        }				
				if(category_id == 0) {
					clear('subcategory_id');
          if($('subcategory_id'))
          $('subcategory_id').style.display = 'none';
          if($('subcategory_id-label'))
          $('subcategory_id-label').style.display = 'none';
          if($('subsubcategory_id'))
          $('subsubcategory_id').style.display = 'none';
          if($('subsubcategory_id-label'))
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

	var cat = '<?php echo $this->category_id ?>';
	if(cat != '') {
		var sub = '<?php echo $this->subcategory_id; ?>';
		var subcatname = "<?php echo $this->subcategory_name; ?>";
    var subsubcate = '<?php echo $this->subsubcategory_id; ?>';
		subcategory(cat, sub, subcatname,subsubcate);
	}

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
    function changesubcategory(subcatid,subsubcate) {
      if($('buttons-wrapper')) {
		  	$('buttons-wrapper').style.display = 'none';
			}
      if(subcatid != 0)
      $('subsubcategory_backgroundimage').style.display = 'block';
      var url = '<?php echo $this->url(array('action' => 'subsub-category'), 'document_general', true);?>';
      var request = new Request.JSON({
        url : url,
        data : {
          format : 'json',
          subcategory_id_temp : subcatid
        },
        onSuccess : function(responseJSON) {
          $('subsubcategory_backgroundimage').style.display = 'none';
  	  		if($('buttons-wrapper')) {
				  	$('buttons-wrapper').style.display = 'block';
					}

          clear('subsubcategory_id');
          var  subsubcatss = responseJSON.subsubcats;

          addSubOption($('subsubcategory_id')," ", '0');
          for (i=0; i< subsubcatss.length; i++) {
            addSubOption($('subsubcategory_id'), subsubcatss[i]['category_name'], subsubcatss[i]['category_id']);
              $('subsubcategory_id').value = subsubcate;
          }
        }
      });
      request.send();
    }
  if($('subcategory_id'))
  $('subcategory_id').style.display = 'none';
  if($('subcategory_id-label'))
	$('subcategory_id-label').style.display = 'none';
  if($('subsubcategory_id'))
	$('subsubcategory_id').style.display = 'none';
  if($('subsubcategory_id-label'))
	$('subsubcategory_id-label').style.display = 'none';
</script>