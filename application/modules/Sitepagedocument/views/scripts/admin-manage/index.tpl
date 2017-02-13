<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/pluginLink.tpl'; ?>
<h2><?php echo $this->translate('Directory / Pages - Documents Extension'); ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<h3><?php echo $this->translate('Manage Page Documents'); ?></h3>
<p>
  <?php echo $this->translate('Here, you can see all the documents your users have posted for the Pages on your site. Here you can monitor these documents and delete offensive ones if necessary. You can also make page documents featured / un-featured, highlighted / un-highlighted,approve / dis-approve by clicking on the corresponding icons. (Note: Highlighted documents will be distinguished from the rest documents by highlighted background.)');?>
</p>
<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction){

    if( order == currentOrder ) {
      $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }

	function multiDelete()
	{
		return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected Page documents ?")) ?>');
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

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<br />

<?php 
	if( !empty($this->paginator) ) {
		$counter=$this->paginator->getTotalItemCount(); 
	}
	if(!empty($counter)): 
?>

	<div class='admin_members_results'>
		<div>
			<?php echo $this->translate(array('%s Page document found.', '%s Page documents found.', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
		</div>
		<?php echo $this->paginationControl($this->paginator); ?>
	</div>

	<br />

	<form id='multidelete_form' method="post" action="<?php echo $this->url(array('action'=>'multi-delete'));?>" onSubmit="return multiDelete()">
		<table class='admin_table seaocore_admin_table'>
			<thead>
				<tr>
					<th style='width: 1%;'><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
					<th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('document_id', 'DESC');"><?php echo $this->translate('ID'); ?></a></th>
					<th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('sitepagedocument_title', 'ASC');"><?php echo $this->translate('Title'); ?></a></th>
					<th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate('Owner');?></a></th>
					<th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('sitepage_title', 'ASC');"><?php echo $this->translate('Page Title');?></a></th>
          <th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('highlighted', 'ASC');"><?php echo $this->translate('Highlighted'); ?></a></th>
					<th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('featured', 'ASC');"><?php echo $this->translate('Featured'); ?></a></th>
					<th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('approved', 'ASC');"><?php echo $this->translate('Approved'); ?></a></th>
					<th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('views', 'ASC');"><?php echo $this->translate('Views'); ?></a></th>
					<th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('comment_count', 'ASC');"><?php echo $this->translate('Comments'); ?></a></th>
					<th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('like_count', 'ASC');"><?php echo $this->translate('Likes'); ?></a></th>
					<th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate('Creation Date'); ?></a></th>
					<th style='width: 1%;' class='admin_table_options'><?php echo $this->translate('Options'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php  if( !empty($counter)):?>
					<?php foreach( $this->paginator as $item ): ?>
						<tr>
							
							<td><input name='delete_<?php echo $item->document_id;?>' type='checkbox' class='checkbox' value="<?php echo $item->document_id ?>"/></td>
							
							<td><?php echo $item->document_id ?></td>
							
							<td class="admin_table_bold"><?php echo $this->htmlLink($item->getHref(), $item->truncateText($item->sitepagedocument_title, 10), array('title' => $item->sitepagedocument_title, 'target' => '_blank')) ?></td>
							
							<td class="admin_table_bold"><?php echo $this->htmlLink($this->item('user', $item->owner_id)->getHref()	, $item->truncateText($this->user($item->owner_id)->username, 10), array('title' => $item->username, 'target' => '_blank')) ?></td>
							
							<td class="admin_table_bold"><?php echo $this->htmlLink($this->item('sitepage_page', $item->page_id)->getHref(), $item->truncateText($item->sitepage_title, 10), array('title' => $item->sitepage_title, 'target' => '_blank')) ?></td>

              <?php if($item->highlighted == 1):?>
								<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepagedocument', 'controller' => 'admin', 'action' => 'highlighted', 'id' => $item->document_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/icons/highlighted.png', '', array('title'=> $this->translate('Make Un-highlighted')))) ?> 
								</td>       
							<?php else: ?>  
								<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepagedocument', 'controller' => 'admin', 'action' => 'highlighted', 'id' => $item->document_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/icons/unhighlighted.png', '', array('title'=> $this->translate('Make Highlighted')))) ?>
								</td>
							<?php endif; ?>
		
							<?php if($item->featured == 1):?>
								<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepagedocument', 'controller' => 'admin', 'action' => 'featured', 'id' => $item->document_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/featured.png', '', array('title'=> $this->translate('Make Un-featured')))) ?> 
								</td>       
							<?php else: ?>  
								<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepagedocument', 'controller' => 'admin', 'action' => 'featured', 'id' => $item->document_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/unfeatured.png', '', array('title'=> $this->translate('Make Featured')))) ?>
								</td>
							<?php endif; ?>
		
							<?php if($item->approved == 1):?>
								<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepagedocument', 'controller' => 'admin', 'action' => 'approved', 'id' => $item->document_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif', '', array('title'=> $this->translate('Dis-approve Page document')))) ?> 
								</td>       
							<?php else: ?>  
								<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepagedocument', 'controller' => 'admin', 'action' => 'approved', 'id' => $item->document_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/disapproved.gif', '', array('title'=> $this->translate('Approve Page document')))) ?>
								</td>
							<?php endif; ?>
							
							<td align="center" class="admin_table_centered"><?php echo $item->views ?></td>
							
							<td align="center" class="admin_table_centered"><?php echo $item->comment_count ?></td>
							
							<td align="center" class="admin_table_centered"><?php echo $item->like_count ?></td>

							<td><?php echo $this->translate(gmdate('M d,Y g:i A',strtotime($item->creation_date))) ?></td>
							
							<td class='admin_table_options'>
							<?php echo $this->htmlLink($item->getHref(), 'view', array('target' => '_blank')) ?>
								|
								<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitepagedocument', 'controller' => 'admin', 'action' => 'delete', 'id' => $item->document_id), $this->translate('delete'), array(
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