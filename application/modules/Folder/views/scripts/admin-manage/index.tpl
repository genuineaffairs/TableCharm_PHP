<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Folder
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
?>
<?php // echo date('r'); ?>
<?php // echo $this->locale()->toDateTime(date('Y-m-d h:i:s')); ?>
<script type="text/javascript">

var currentOrder = '<?php echo $this->order ?>';
var currentOrderDirection = '<?php echo $this->order_direction ?>';
var changeOrder = function(order, default_direction){
  // Just change direction
  if( order == currentOrder ) {
    $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
  } else {
    $('order').value = order;
    $('order_direction').value = default_direction;
  }
  $('folder_admin_manage_filter').submit();
}


  var delectSelected =function(){
    var checkboxes = $$('input[type=checkbox]');
    var selecteditems = [];

    checkboxes.each(function(item, index){
      var checked = item.get('checked', false);
      var value = item.get('value', false);
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });

    $('ids').value = selecteditems;
    $('delete_selected').submit();
  }

  function selectAll()
  {
    var checkboxes = $$('input.checkboxes');
    var selecteditems = [];

    var chked = $('checkboxes_toggle').get('checked');
    
    checkboxes.each(function(item, index){
      item.set('checked', chked);
    });
  }
</script>

<h2><?php echo $this->translate("Folder / File Sharings Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("This page lists all of the folders your users have posted. You can use this page to monitor these folders and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific folders. Leaving the filter fields blank will show all the folders on your social network.") ?>
</p>
<br />

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<br />

<div class='admin_results'>
  <div>
    <?php $folderCount = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%d folder found", "%d folders found", $folderCount), ($folderCount)) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->formValues
    )); ?>  
    
  </div>
</div>
<?php //print_r($this->params)?>
<br />

<?php if( count($this->paginator) ): ?>

<table class='admin_table' id='folder_list_folders'>
  <thead>
    <tr>
      <th class='admin_table_short'><input onclick="selectAll()" type='checkbox' id='checkboxes_toggle' /></th>
      <th class='admin_table_short'>ID</th>
      <th class='folder_header_title'><?php echo $this->translate("Folder Name") ?></th>
      <th class='folder_header_package'><?php echo $this->translate("Parent") ?></th>
      <th class='folder_header_epayment'><?php echo $this->translate("Category"); ?></th>
      <th class='folder_header_status'><?php echo $this->translate("Files") ?></th>
      <th class='folder_header_expires'><?php echo $this->translate("Created") ?></th>
      <th class='folder_header_icon'><?php echo $this->translate("Icon") ?> [<a href="javascript:void(0);" onclick="Smoothbox.open($('folder_icons_legend')); return false;">?</a>]</th>
      <th class='folder_header_options'><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): // $this->string()->chunk($item->getTitle(), 5) ?>
      <tr>
        <td><input type='checkbox' class='checkboxes' value="<?php echo $item->folder_id ?>"/></td>
        <td><?php echo $item->folder_id ?></td>
        <td><?php echo $this->htmlLink($item->getHref(), $this->radcodes()->text()->truncate($item->getTitle(),32), array('target' => '_blank')) ?>
        	<div class="folder_text_desc">
            <?php echo $this->translate('by %s', $item->getOwner()->toString())?>
            |
                <?php echo $this->translate(array("%s view", "%s views", $item->view_count), $this->locale()->toNumber($item->view_count)); ?>
                - <?php echo $this->translate(array("%s comment", "%s comments", $item->comment_count), $this->locale()->toNumber($item->comment_count)); ?>
                - <?php echo $this->translate(array('%1$s like', '%1$s likes', $item->like_count), $this->locale()->toNumber($item->like_count)); ?>
          </div>
        </td>
        <td>
          <?php 
            try {
              echo $item->getParent()->toString(); 
            }
            catch (Core_Model_Item_Exception $ex)
            {
              echo '<span style="color: red">'.$this->translate('Missing Parent: %s_%s', $item->parent_type, $item->parent_id).'</span>';
            }      
          ?>
          <div class="folder_text_desc"><?php echo $item->getParentTypeText(); ?></div>
        </td>
        <td>
          <?php echo $item->getCategory()->toString(); ?>
        </td>
        <td>
          <?php echo count($item); ?>
        </td>
        <td>
          <?php echo $this->locale()->toDate($item->creation_date); ?>
        </td>   
        <td>
            <?php echo $this->htmlLink(
            array('route' => 'admin_default', 'module' => 'folder', 'controller' => 'manage', 'action' => 'featured', 'folder_id' => $item->folder_id),
            $this->htmlImage('./application/modules/Folder/externals/images/featured'.($item->featured ? "" : "_off").'.png'),
            array('class' => 'smoothbox', 'title' => $this->translate($item->featured ? "Featured" : "Not Featured"))) ?>
            <?php echo $this->htmlLink(
            array('route' => 'admin_default', 'module' => 'folder', 'controller' => 'manage', 'action' => 'sponsored', 'folder_id' => $item->folder_id),
            $this->htmlImage('./application/modules/Folder/externals/images/sponsored'.($item->sponsored ? "" : "_off").'.png'),
            array('class' => 'smoothbox', 'title' => $this->translate($item->sponsored ? "Sponsored" : "Not Sponsored"))) ?>
        </td>
        <td>
          <?php echo $this->htmlLink(array('route'=>'folder_specific', 'action'=>'edit', 'folder_id'=>$item->folder_id), $this->translate('edit'), array('target'=>'_blank'))?>
          |
          <?php echo $this->htmlLink(
            array('route' => 'admin_default', 'module' => 'folder', 'controller' => 'manage', 'action' => 'delete', 'folder_id' => $item->folder_id),
            $this->translate("delete"),
            array('class' => 'smoothbox')) ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<br />

<div class='buttons'>
  <button onclick="javascript:delectSelected();" type='submit'>
    <?php echo $this->translate("Delete Selected") ?>
  </button>
</div>

<form id='delete_selected' method='post' action='<?php echo $this->url(array('action' =>'deleteselected')) ?>'>
  <input type="hidden" id="ids" name="ids" value=""/>
</form>
<br/>

<?php //print_r($this->params)?>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no folders posted by your members yet.") ?>
    </span>
  </div>
<?php endif; ?>


<div style="display: none">
    
  <ul class="radcodes_admin_icons_legend" id="folder_icons_legend">
    <li><?php echo $this->htmlImage('./application/modules/Folder/externals/images/featured.png');?><?php echo $this->translate('Featured')?></li>
    <li><?php echo $this->htmlImage('./application/modules/Folder/externals/images/featured_off.png');?><?php echo $this->translate('Not Featured')?></li>
    <li><?php echo $this->htmlImage('./application/modules/Folder/externals/images/sponsored.png');?><?php echo $this->translate('Sponsored')?></li>
    <li><?php echo $this->htmlImage('./application/modules/Folder/externals/images/sponsored_off.png');?><?php echo $this->translate('Not Sponsored')?></li>  
  
  </ul>
  
</div>
