<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?><?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/pluginLink.tpl'; ?>
<h2><?php echo $this->translate('Directory / Pages - Polls Extension'); ?></h2>

<?php if ( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<h3>
  <?php echo $this->translate('Manage Page Polls'); ?>
</h3>

<p>
  <?php echo $this->translate('Here, you can see all the Page polls your users have created. You can use this page to monitor these polls and delete offensive material if necessary. Here, you can also approve / dis-approve Page polls.'); ?>
</p>

<br />
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
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected page polls ?")) ?>');
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

<?php
  if ( !empty($this->paginator) ) {
    $counter = $this->paginator->getTotalItemCount();
  }
    if ( !empty($counter) ):
?>
	<div class='admin_members_results'>
		<div>
			<?php echo $this->translate(array('%s page poll found.', '%s page polls found.', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
		</div>
		<?php echo $this->paginationControl($this->paginator); ?>
	</div>
	<br />

	<form id='multidelete_form' method="post" action="<?php echo $this->url(array('action' => 'multi-delete')); ?>" onSubmit="return multiDelete()">
		<table class='admin_table' border="0">
			<thead>
				<tr>
					<th style='width: 1%;' align="left"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
					<th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('poll_id', 'DESC');"><?php echo $this->translate('ID'); ?></a></th>
					<th style='width: 1%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate('Title'); ?></a></th>
					<th style='width: 1%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate('Owner'); ?></a></th>
					<th style='width: 1%;' align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('sitepage_title', 'ASC');"><?php echo $this->translate('Page Name'); ?></a></th>
					<th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('approved', 'ASC');"><?php echo $this->translate('Approved'); ?></a></th>
					<th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('views', 'ASC');"><?php echo $this->translate('Views'); ?></a></th>
					<th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('vote_count', 'ASC');"><?php echo $this->translate('Votes'); ?></a></th>
					<th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('comment_count', 'ASC');"><?php echo $this->translate('Comments'); ?></a></th>
					<th style='width: 1%;' class="admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('like_count', 'DESC');"><?php echo $this->translate('Likes'); ?></a></th>
					<th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate('Creation Date'); ?></a></th>
					<th style='width: 1%;' class='admin_table_options'><?php echo $this->translate('Options'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( !empty($counter) ): ?>
					<?php foreach ( $this->paginator as $item ): ?>
							<tr>
								<td><input name='delete_<?php echo $item->poll_id; ?>' type='checkbox' class='checkbox' value="<?php echo $item->poll_id ?>"/></td>
								<td class="admin_table_centered"><?php echo $item->poll_id ?></td>

								<?php $item_sitepagetitle = Engine_Api::_()->sitepagepoll()->truncation($item->title); ?>
								<td class='admin_table_bold'><?php echo $this->htmlLink($item->getHref(), $item_sitepagetitle, array('title' => $item->title, 'target' => '_blank')) ?></td>

								<td class='admin_table_bold'><?php echo $this->htmlLink($this->item('user', $item->owner_id)->getHref(), $item->truncateOwner($this->user($item->owner_id)->username), array('title' => $item->username, 'target' => '_blank')) ?></td>

								<?php $item_sitepagetitle = Engine_Api::_()->sitepagepoll()->truncation($item->sitepage_title); ?>
								<td class='admin_table_bold'><?php echo $this->htmlLink($this->item('sitepage_page', $item->page_id)->getHref(), $item_sitepagetitle, array('title' => $item->sitepage_title, 'target' => '_blank')) ?></td>

								<?php if ( $item->approved == 1 ): ?>
										<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'sitepagepoll_approved', 'poll_id' => $item->poll_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepagepoll/externals/images/sitepagepoll_approved1.gif', '', array('title' => $this->translate('Dis-approve page poll')))) ?>
										</td>
								<?php else: ?>
											<td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'sitepagepoll_approved', 'poll_id' => $item->poll_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepagepoll/externals/images/sitepagepoll_approved0.gif', '', array('title' => $this->translate('Approve page poll')))) ?>
											</td>
								<?php endif; ?>

								<td align="center" class="admin_table_centered"><?php echo $item->views ?></td>
								<td align="center" class="admin_table_centered"><?php echo $item->vote_count ?></td>
								<td align="center" class="admin_table_centered"><?php echo $item->comment_count ?></td>
								<td align="center" class="admin_table_centered"><?php echo $item->like_count ?></td>
								<td><?php echo $this->translate(gmdate('M d,Y g:i A', strtotime($item->creation_date))) ?></td>

								<td class='admin_table_options'>
									<?php echo $this->htmlLink(array('route' => 'sitepagepoll_detail_view', 'user_id' => $item->owner_id, 'poll_id' => $item->poll_id), $this->translate('view'), array('target' => '_blank')) ?>
											</a>
																						|
									<?php
											echo $this->htmlLink(array('route' => 'sitepagepolladmin_delete', 'poll_id' => $item->poll_id), $this->translate('delete'), array(
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
      <?php echo $this->translate('No results were found.'); ?>
    </span>
  </div>
<?php endif; ?>
<style type="text/css">
  table.admin_table tbody tr td {
    white-space: nowrap;
		font-size: 11px;
		padding: 7px 1px;
  }
  table.admin_table tbody th td {
    white-space: nowrap;
		font-size: 11px;
		padding: 7px 1px;
  }
</style>