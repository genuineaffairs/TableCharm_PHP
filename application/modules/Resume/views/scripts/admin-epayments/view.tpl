<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @package   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
?>

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
  <div class="epayment_item_nav">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->gutterNavigation)
        ->render();
    ?>
  </div>
  <h3><?php echo $this->translate('View Payment: #%s', $this->epayment->getIdentity())?></h3>
   <br />
  <table class="epayment_view">
    <tr>
      <th><?php echo $this->translate("Member")?></th>
      <td><?php echo $this->epayment->getOwner()->toString()?>
        #<?php echo $this->epayment->user_id?>
      </td>
    </tr>  
    <tr>
      <th><?php echo $this->translate("Resume")?></th>
      <td><?php echo $this->epayment->getParent()->toString()?>
        #<?php echo $this->epayment->resource_id?>
      </td>
    </tr>
    <tr>
      <th><?php echo $this->translate("Package")?></th>
      <td><?php echo $this->epayment->getPackage()->toString()?>
        #<?php echo $this->epayment->package_id?>
      </td>
    </tr>
    <tr>
      <th><?php echo $this->translate("Date Received")?></th>
      <td><?php echo $this->locale()->toDateTime($this->epayment->creation_date) ?></td>
    </tr>    
    <tr>
      <th><?php echo $this->translate("Payer Name")?></th>
      <td><?php echo $this->epayment->payer_name; ?></td>
    </tr>
    <tr>
      <th><?php echo $this->translate("Payer Account")?></th>
      <td><?php echo $this->epayment->payer_account; ?></td>
    </tr>    
    <tr>
      <th><?php echo $this->translate("Transaction Code")?></th>
      <td><?php echo $this->epayment->transaction_code?></td>
    </tr>
    <tr>
      <th><?php echo $this->translate("Amount")?></th>
      <td><?php echo $this->epayment->printAmount()?></td>
    </tr>
    <tr>
      <th><?php echo $this->translate("Status")?></th>
      <td><?php echo $this->translate($this->epayment->getStatusText())?></td>
    </tr>
    <tr>
      <th><?php echo $this->translate("Processed")?></th>
      <td>
        <?php if ($this->epayment->processed): ?>
          <?php echo $this->locale()->toDateTime($this->epayment->processed_date);?>
        <?php else: ?>
          <?php echo $this->translate('No')?>
        <?php endif; ?>
      </td>
    </tr>
    <tr>
      <th><?php echo $this->translate("Notes")?></th>
      <td><?php echo nl2br($this->epayment->notes); ?></td>
    </tr>
    <tr>
      <th><?php echo $this->translate("IPN Data")?></th>
      <td>
        <ul>
        <?php foreach ($this->epayment->data as $key => $value): ?>
          <li>
            <label><?php echo $key?>:</label> 
            <?php echo $value; ?>
          </li>
        <?php endforeach; ?>
        </ul>      
      </td>
    </tr>    
  </table>
