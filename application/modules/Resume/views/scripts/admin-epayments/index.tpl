<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @epayment   Application_Extensions
 * @epayment   Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
?>
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
  $('epayment_admin_filter_form').submit();
}

</script>

<h2><?php echo $this->translate("Resumes Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p class="description">
  <?php echo $this->translate("This page lists payments that have been made from your members for posting resumes.") ?>
  <?php echo $this->translate("Do NOT forget to process payments if you have configured package's auto-process as manual.")?>
</p>

<br/>
<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'resume', 'controller' => 'epayments', 'action' => 'create'),
   $this->translate('Add New Payment'), array(
  'class' => 'buttonlink icon_epayment_create',
)) ?>

<br />
<br />


<div class='admin_search'>
  <?php echo $this->form->render($this) ?>
</div>

<br />

<div class='admin_results'>
  <div>
    <?php $epaymentCount = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%d payment found", "%d payments found", $epaymentCount), ($epaymentCount)) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->formValues
    )); ?>  
    
  </div>
</div>

<br />
<?php if(count($this->paginator)>0):?>

<table class='admin_table'>
<thead>
  <tr>
    <th class='admin_table_short'>ID</th>
    <th><?php echo $this->translate("Resume/Package/Member") ?></th>
    <th><a href="javascript:void(0);" onclick="javascript:changeOrder('transaction_code', 'ASC');"><?php echo $this->translate("Transaction Code") ?></a></th>
    <th><a href="javascript:void(0);" onclick="javascript:changeOrder('amount', 'ASC');"><?php echo $this->translate("Amount") ?></a></th>
    <th><a href="javascript:void(0);" onclick="javascript:changeOrder('payer_name', 'ASC');"><?php echo $this->translate("Payer") ?></a></th>
    <th><a href="javascript:void(0);" onclick="javascript:changeOrder('status', 'ASC');"><?php echo $this->translate("Status")?></a></th>
    <th><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'ASC');"><?php echo $this->translate("Date")?></a></th>
    <th><a href="javascript:void(0);" onclick="javascript:changeOrder('processed', 'ASC');"><?php echo $this->translate("Processed")?></a></th>
    <th><?php echo $this->translate("Options") ?></th>
  </tr>
</thead>
<tbody id='epayment_list'>
  <?php foreach ($this->paginator as $epayment): 
    $resume = $epayment->getParent();
    $package = $epayment->getPackage();
  ?>
    <tr>
      <td><?php echo $epayment->getIdentity(); ?></td>
      <td><?php echo $resume->toString()?>
        <div class="resume_text_desc"><?php echo $package->toString()?>
        <?php echo $this->translate('by %s', $epayment->getOwner()->toString())?></div>
      </td>
      <td><?php echo $epayment->transaction_code; ?></td>
      <td><?php echo $epayment->printAmount(); ?></td>
      <td><?php echo $this->htmlLink('mailto:'.$epayment->payer_account, $this->radcodes()->text()->truncate($epayment->payer_name, 16))?></td>
      <td><?php echo $this->translate($epayment->getStatusText())?></td>
      <td><?php echo $this->locale()->toDate($epayment->creation_date)?></td>
      <td>
        <?php if ($epayment->processed): ?>
          <?php echo $this->translate('Yes')?> | <?php echo $this->locale()->toDate($epayment->processed_date); ?>
          <div class="resume_text_desc"><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'resume', 'controller' => 'epayments', 'action' => 'process', 'epayment_id' =>$epayment->epayment_id), $this->translate('reprocess'), array(
          //'class' => 'smoothbox',
        )) ?></div> 
        <?php else: ?>
          <?php echo $this->translate('No')?> |
          <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'resume', 'controller' => 'epayments', 'action' => 'process', 'epayment_id' =>$epayment->epayment_id), $this->translate('process'), array(
          //'class' => 'smoothbox',
        )) ?>
        <?php endif; ?>
      </td>
      <td>
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'resume', 'controller' => 'epayments', 'action' => 'view', 'epayment_id' =>$epayment->epayment_id), $this->translate('view'), array(
          //'class' => 'smoothbox',
        )) ?>
        |      
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'resume', 'controller' => 'epayments', 'action' => 'edit', 'epayment_id' =>$epayment->epayment_id), $this->translate('edit'), array(
          //'class' => 'smoothbox',
        )) ?>
        |
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'resume', 'controller' => 'epayments', 'action' => 'delete', 'epayment_id' =>$epayment->epayment_id), $this->translate('delete'), array(
          //'class' => 'smoothbox',
        )) ?>       
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php else:?>
  <br/>
  <div class="tip">
  <span><?php echo $this->translate("There are currently no payments.") ?></span>
  </div>
<?php endif;?>

     