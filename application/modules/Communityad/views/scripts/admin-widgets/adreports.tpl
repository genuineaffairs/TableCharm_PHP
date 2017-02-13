<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: adreports.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
  <?php echo $this->translate('Community Ads Plugin') ?>
</h2>

<script type="text/javascript">

function multiDelete()
{
  return confirm("<?php echo $this->translate("Are you sure that you want to dismiss the selected reports? They will not be recoverable after being dismissed.") ?>");
  
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

<h3 style="margin-bottom:5px;"><?php echo $this->translate('Abuse Reports') ?></h3>
<?php
	// Show Success message.
	if(isset($this->success_message))
	{
		echo '<ul class="form-notices" style="margin:0px;"><li style="float:left;">' . $this->translate('Successfully created') . ' ' . ucfirst($this->success_message) . ' ' . $this->translate('Communityad.') . '</li></ul>';
	}
 $counter=$this->paginator->getTotalItemCount();
?>
<p style="display:block;">
  <?php echo $this->translate("This page lists all of the ads your users have reported as abusive for being misleading, offensive, inappropriate, licensed violating, or other reasons. You can view reported ads, dismiss reports, or take action on ads that have been reported."); ?>
</p><br>
	
<?php	if( !empty($counter) ) { ?>
	<div>
		<?php echo $this->translate(array('%s Advertisement found.', '%s Advertisements found.', $counter), $this->locale()->toNumber($counter)); ?>
	</div><br />
<?php } ?>
	
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
        <th class='admin_table_short'>
					<?php 
						if( empty($this->id_orderby) ) { $orderby = 1; } else { $orderby = 0; }
						echo "<a href=" . $this->url(array('module' => 'communityad', 'controller' => 'widgets', 'action' => 'adreports', 'idSorting' => $orderby), 'admin_default', true) . ">" . $this->translate("ID") . '</a>';
					?>
        </th>
        <th>
        	<?php echo $this->translate("Ad Title"); ?>
        </th>
        <th>
        	<?php echo $this->translate("Reporter"); ?>
        </th>
        <th>
        	<?php echo $this->translate("Date"); ?>
        </th>
        <th>
        	<?php echo $this->translate("Reasons"); ?>
        </th>
        <th>
        	<?php echo $this->translate("Options"); ?>
        </th>
      </tr>
    </thead>
    <tbody>
    	<?php foreach ($this->paginator as $item):?>
        <tr>
          <td><input type='checkbox' name='delete_<?php echo $item->adcancel_id;?>' value='<?php echo $item->adcancel_id ?>' class='checkbox' value="<?php echo $item->adcancel_id ?>"/></td>
          <td><?php echo $item->adcancel_id; ?></td>
          <td><?php if( !empty($item->cads_title) ){ echo $item->cads_title; } else { echo '-'; } ?></td>
          <td><?php echo $item->displayname; ?></td>
          <td><?php echo date('M d, Y', strtotime($item->creation_date)) ?></td>
					<td><?php if($item->report_type == 'Other'){ echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'widgets', 'action' => 'reportdiscription', 'reportId' => $item->adcancel_id), $this->translate($item->report_type), array('class' => 'smoothbox')); } else { echo $this->translate($item->report_type); } ?></td>
          <td>
          <?php 
						echo $this->htmlLink(array('route' => 'communityad_userad', 'ad_id' => $item->ad_id), $this->translate("view ad"), array('target' => '_blank')) . ' | '; 
						echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'viewad', 'action' => 'editad', 'id' => $item->ad_id), $this->translate("take action")) . ' | '; 
						echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'widgets', 'action' => 'deletereports', 'reportId' => $item->adcancel_id), $this->translate("dismiss"), array('class' => 'smoothbox'));
					?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <br />
  <div class='buttons'>
  	<button type='submit'><?php echo $this->translate("Dismiss Selected") ?></button>
  </div>
</form>
<br />
<div>
	<?php echo $this->paginationControl($this->paginator); ?>
</div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are currently no outstanding abuse reports for ads.") ?>
    </span>
  </div>
<?php endif; ?>

<style type="text/css">
table.admin_table thead tr th 
{
	text-align:left;
}
.paginationControl{
	margin-bottom:15px;
}
</style>