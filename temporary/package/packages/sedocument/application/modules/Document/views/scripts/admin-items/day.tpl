<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: day.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">

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
</script>

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<h2><?php echo $this->translate("Documents Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<div class='clear'>
  <div class='settings'>
    <form id='multidelete_form' method="post" action="<?php echo $this->url(array('module' => 'document', 'controller' => 'items', 'action' => 'multi-delete'), 'admin_default'); ?>" onSubmit="return multiDelete()" class="global_form">
      <div>
        <h3><?php echo $this->translate("Document of the Day widget") ?> </h3>
        <p class="description">
          <?php echo $this->translate("Add and Manage the documents on your site to be shown in the Document of the Day widget. You can also mark these documents for future dates such that the marked document automatically shows up as Document of the Day on the desired date. Note that for this document of the day to be shown, you must first place the Document of the Day widget at the desired location.") ?>
        </p>
        <?php
        echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'document', 'controller' => 'items', 'action' => 'add-item'), $this->translate('Add a Document of the Day'), array(
            'class' => 'smoothbox buttonlink',
            'style' => 'background-image: url(application/modules/Core/externals/images/admin/new_category.png);'))
        ?>	<br/>	<br/>
        <?php if ($this->paginator->getTotalItemCount() > 0): ?>
          <table class='admin_table' width="80%">
            <thead>
              <tr>
								<?php $class = ( $this->order == 'engine4_documents.document_title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                <th style='width: 1%;' align="left"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
                <th width="550" align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_documents.document_title', 'DESC');"><?php echo $this->translate("Document Title") ?></a></th>

                <?php $class = ( $this->order == 'start_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                <th width="70" align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('start_date', 'DESC');"><?php echo $this->translate("Start Date") ?></a></th>

                <?php $class = ( $this->order == 'end_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                <th width="70" align="left" class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('end_date', 'DESC');"><?php echo $this->translate("End Date") ?></a></th>

                <th width="70" align="left"><?php echo $this->translate("Options") ?></th>
              </tr>
            </thead>
            <tbody>
							<?php foreach ($this->paginator as $item):?> 
                <tr>
                  <td><input name='delete_<?php echo $item->itemoftheday_id; ?>' type='checkbox' class='checkbox' value="<?php echo $item->itemoftheday_id ?>"/></td>

									<td class='admin_table_bold'>
										<?php $item_title = Engine_Api::_()->document()->truncateText($item->document_title, 100); ?>
										<?php echo $this->htmlLink($item->getHref(), $item_title, array('title' => $item->document_title, 'target' => '_blank')) ?>
									</td>

                  <td align="left"><?php echo $item->start_date ?></td>

                  <td align="left"><?php echo $item->end_date ?></td>

                  <td align="left">
										<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'document', 'controller' => 'items', 'action' => 'edit-item', 'id' => $item->itemoftheday_id), $this->translate('edit'), array('class' => 'smoothbox',)) ?> | 
										<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'document', 'controller' => 'items', 'action' => 'delete-item', 'id' => $item->itemoftheday_id), $this->translate('delete'), array('class' => 'smoothbox',)) ?> 
										
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table><br />
          <div class='buttons'>
            <button type='submit'><?php echo $this->translate('Delete Selected'); ?></button>
          </div>
        <?php else: ?>
          <div class="tip"><span><?php echo $this->translate("No documents have been marked as Document of the Day."); ?></span> </div><?php endif; ?>
      </div>
    </form>
  </div>
</div>
<?php echo $this->paginationControl($this->paginator); ?>