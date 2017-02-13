<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: help-and-learnmore.tpl  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
  <?php echo $this->translate('Community Ads Plugin') ?>
</h2>

<script type="text/javascript">

function multiDelete()
{
  return confirm("<?php echo $this->translate("Are you sure you want to delete the selected 'Help & Learn More' pages? They will not be recoverable after being deleted.") ?>");
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

<h3><?php echo $this->translate('Manage Advertising Help Pages') ?></h3>
 <p><?php echo $this->translate("This page enables you to manage the advertising help pages of your site, and create an attractive overview of advertising on your site to get advertisers interested. You can offer guidelines to advertisers about different types of Ads, ad creation, management, statistics and other aspects. You can also offer tips to advertisers to create highly effective ads for your community.<br />
The Help and Learn More section is comprised of multiple pages. You can create, edit, delete and disable pages. The default pages that come with this plugin cannot be deleted, but can be disabled.") ?>
</p>
<br style="clear:both;" />
<?php
	// Show Success message.
	if(isset($this->success_message))
	{
		echo '<ul class="form-notices" style="margin:0px;"><li style="float:left;">' . $this->translate('Successfully create') . ' ' . ucfirst($this->success_message) . ' ' . $this->translate('Communityad.') . '</li></ul>';
	}
?>
<p class="" style="display:block;">
	<?php
	// Show link for "Create Featured Content".
		echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'helps', 'action' => 'help-page-create'), $this->translate("Create a new Help Page"), array('class'=>'buttonlink cmad_icon_create'));
	?>
</p>
<br />

<?php
	if( count($this->paginator) ):
?>
<form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete();" style="float:left;width:50%">
  <table class='admin_table'>
    <thead>
      <tr>
        <th class='admin_table_short'>
        	<input onclick='selectAll();' type='checkbox' class='checkbox' />
        </th>
        <th class='admin_table_short' align="center">
					<?php 
						if( empty($this->id_orderby) ) { $orderby = 1; } else { $orderby = 0; }
						echo $this->translate("ID");
					?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Page Title"); ?>
        </th>
        <th align="center">
        	<?php echo $this->translate("Status"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Options"); ?>
        </th>
      </tr>
    </thead>
    <tbody>
    	<?php foreach ($this->paginator as $item): ?>
        <tr>
          <td><input type='checkbox' name='delete_<?php echo $item->infopage_id;?>' value='<?php echo $item->infopage_id ?>' class='checkbox' value="<?php echo $item->infopage_id ?>" <?php if( empty($item->delete) ){ echo 'DISABLED'; } ?> /></td>
          <td class="admin_table_centered"><?php echo $item->infopage_id; ?></td>
					<?php						
						if ( !empty($item->title) ) {
							$tmpBody = strip_tags($item->title);
							$title = Engine_String::strlen($tmpBody) > 20 ? Engine_String::substr($tmpBody, 0, 20) . '..' : $tmpBody;
						} else {
							$title = '-';
						}
					?>
          <td><?php echo $title; ?></td>
	  <?php if (empty($item->status)) {
      ?>
        <td class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'helps', 'action' => 'status',  'id' => $item->infopage_id, 'status' => 1), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/images/enabled0.gif', '', array('title' => $this->translate('Enable Page'))), array('class' => 'smoothbox')) ?></td>
      <?php } else {
      ?>
        <td class=" admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'helps', 'action' => 'status',  'id' => $item->infopage_id, 'status' => 0), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/images/enabled1.gif', '', array('title' => $this->translate('Disable Page'))), array('class' => 'smoothbox')) ?></td>
      <?php } ?>
          <td>
          <?php
						if ( empty($item->page_id) ) {
							$pageId = 'none';
						} else {
							$pageId = $item->page_id;
						}
						echo $this->htmlLink(array('route' => 'communityad_help_and_learnmore', 'page_id' => $item->infopage_id), $this->translate("view"), array('target' => '_blank'));

						if ( empty($item->contect_team) ) {
							if( empty($item->page_default) ) {
								echo " | " . $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'helps', 'action' => 'help-page-create', 'page_id' => $item->infopage_id), $this->translate("edit"));
							}else {
								echo " | " . $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'helps', 'action' => 'default-help-msg', 'page_id' => $item->infopage_id), $this->translate("edit"), array('class' => 'smoothbox'));
							}
						}
						if ( !empty($item->delete) ) {
							echo ' | ';
							echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'helps', 'action' => 'help-page-delete', 'page_id' => $item->infopage_id), $this->translate("delete"), array('class' => 'smoothbox'));
						}
					?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <br />
  <div class='buttons'>
  	<button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
  </div>
</form>
<br />
<div>
	<?php echo $this->paginationControl($this->paginator); ?>
</div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no help pages available.") ?>
    </span>
  </div>
<?php endif; ?>