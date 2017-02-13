<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
?>

    <h3 class="sep resume_main_header">
      <span><?php echo $this->translate('Resume Payment History'); ?></span>
    </h3>
    <p>
      <?php echo $this->translate('This page displays payments have been made for this particular resume.')?>
    </p>
    <br />
    <?php if ($this->paginator->getTotalItemCount() > 0): ?>
      <table class='resume_epayments'>
        <thead>
          <tr>
            <th><?php echo $this->translate('ID')?></th>
            <th><?php echo $this->translate('Package')?></th>
            <th><?php echo $this->translate('Transaction Code')?></th>
            <th><?php echo $this->translate('Amount')?></th>
            <th><?php echo $this->translate('Status')?></th>
            <th><?php echo $this->translate('Date')?></th>            
          </tr>
        </thead>
        <tbody>
          <?php foreach ($this->paginator as $epayment): ?>
            <tr>
              <td><?php echo $epayment->getIdentity()?></td>
              <td><?php echo $epayment->getPackage()->toString()?></td>
              <td><?php echo $epayment->transaction_code?></td>
              <td><?php echo $epayment->printAmount() ?></td>
              <td><?php echo $this->translate($epayment->getStatusText())?></td>
              <td><?php echo $this->locale()->toDate($epayment->creation_date); ?></td>  
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="tip">
        <span>
          <?php echo $this->translate('No payment transactions found for this resume.');?>
        </span>
      </div>
    <?php endif; ?>

