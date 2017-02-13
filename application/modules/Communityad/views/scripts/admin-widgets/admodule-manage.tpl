<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: admodule-manage.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
  <?php echo $this->translate('Community Ads Plugin'); ?>
</h2>

<script type="text/javascript">

function multiDelete()
{
  return confirm("<?php echo $this->translate("Are you sure you want to remove the selected modules as advertisable? Users will not be able to directly advertise their content from these modules after being removed.") ?>");
}

function selectAll()
{
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}
</script>

<?php if( count($this->navigation) ): ?>
<div class='communityad_admin_tabs'>
  <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>
<h3 style="margin-bottom:6px;"><?php echo $this->translate("Manage Modules for Ads"); ?></h3>
<?php
	// Show Success message.
	if(isset($this->success_message))
	{
		echo '<ul class="form-notices" style="margin:0px;"><li style="float:left;">' . $this->translate('Successfully create') . ' ' . ucfirst($this->success_message) . ' ' . $this->translate('Communityad.') . '</li></ul>';
	}
?>
  <p>
  	<?php echo $this->translate("Here, you can manage modules whose content could be advertised. This plugin enables content belonging to any module to be advertised. You can add new modules of your site as advertisable, and remove them too. Thus, this interface enables you to extend this plugin to ANY CONTENT MODULE of your site.<br />If you include content type belonging to a module in an ad package, users will be able to advertise their content of that type using that package. If you do not want to allow users to be able to directly advertise a content type, then simply do not include that content type in a package while creating an ad package.<br />For more tips on this section, visit the FAQ page."); ?>
  </p>
  <br style="clear:both;" />
	
  <?php
	// Show link for "Create Featured Content".
		echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'widgets', 'action' => 'admodule-create'), $this->translate("Add New Module"), array('class'=>'buttonlink cmad_icon_create'));
	?>
	<br /><br />

<?php
	if( count($this->paginator) ):
?>
<form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete();">
  <table class='admin_table'>
    <thead>
      <tr>
        <th class='admin_table_short'>
        	<input onclick='selectAll();' type='checkbox' class='checkbox' />
        </th>
        <th class='admin_table_short' align="center">
					<?php 
						echo $this->translate("ID");
					?>
        </th>
         <th align="left">
        	<?php echo $this->translate("Module Name"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Module Title"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Table Item"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Title Field"); ?>
        </th>
        <th class="left">
        	<?php echo $this->translate("Body Field"); ?>
        </th>
<!--        <th class="left">
        	<?php //echo $this->translate("Image Field"); ?>
        </th>-->
        <th class="left">
        	<?php echo $this->translate("Owner Field"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Options"); ?>
        </th>
      </tr>
    </thead>
    <tbody>
			<?php $is_module_flag = 0; ?>
    	<?php foreach ($this->paginator as $item):?>
			<?php $module_name = $item->module_name; $modules_array = $this->enabled_modules_array;  ?>
			<?php if( in_array( $module_name, $modules_array )) { ?>
        <tr>
          <td><input type='checkbox' name='delete_<?php echo $item->module_id;?>' value='<?php echo $item->module_id ?>' class='checkbox' value="<?php echo $item->module_id ?>" <?php if( !empty($item->is_delete) ) { echo 'DISABLED'; } ?>/></td>
          <td class="admin_table_centered"><?php echo $item->module_id; ?></td>
					<td ><?php if( !empty($item->module_name) ){ echo $item->module_name; }else { echo '-'; } ?></td>
					<td ><?php if( !empty($item->module_name) ){ echo $item->module_title; }else { echo '-'; } ?></td>
					<td ><?php if( !empty($item->table_name) ){ if( strstr($item->table_name, "sitereview") ){ echo "sitereview_listing"; } else { echo $item->table_name; } }else { echo '-'; } ?></td>
					<td ><?php if( !empty($item->title_field) ){ echo $item->title_field; }else { echo '-'; } ?></td>
					<td ><?php if( !empty($item->body_field) ){ echo $item->body_field; }else { echo '-'; } ?></td>
					<!--<td ><?php //if( !empty($item->image_field) ){ echo $item->image_field; }else { echo '-'; } ?></td>-->
					<td ><?php if( !empty($item->owner_field) ){ echo $item->owner_field; }else { echo '-'; } ?></td>					
          <td >
          <?php
						echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'widgets', 'action' => 'admodule-create', 'module_id' => $item->module_id), $this->translate("edit")) ;
						if( empty($item->is_delete) ) {
							echo ' | ' . $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'widgets', 'action' => 'module-delete', 'module_id' => $item->module_id), $this->translate("delete"), array('class' => 'smoothbox'));
						}						
					?>
          </td>
        </tr><?php $is_module_flag = 1; } ?>
      <?php  endforeach; ?>
    </tbody>
  </table>
  <br />
	<?php if( !empty($is_module_flag) ) { ?>
  <div class='buttons'>
  	<button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
  </div>
	<?php } ?>
</form>
<br />
<div>
	<?php echo $this->paginationControl($this->paginator); ?>
</div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no modules available.") ?>
    </span>
  </div>
<?php endif; ?>