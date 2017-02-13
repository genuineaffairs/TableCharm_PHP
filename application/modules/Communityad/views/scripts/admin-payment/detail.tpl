<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: detail.tpl  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2 class="payment_transaction_detail_headline">
  <?php echo $this->translate("Transaction Details") ?>
</h2>

<dl class="payment_transaction_details">
  <dd>
    <?php echo $this->translate('Transaction ID') ?>
  </dd>
  <dt>
    <?php echo $this->locale()->toNumber($this->transaction->transaction_id) ?>
  </dt>

  <dd>
    <?php echo $this->translate('Member') ?>
  </dd>
  <dt>
    <?php if( $this->user && $this->user->getIdentity() ): ?>
      <?php echo $this->htmlLink($this->user->getHref(), $this->user->getTitle(), array('target' => '_parent')) ?>
      <?php //echo $this->user->__toString() ?>
      <?php if( !_ENGINE_ADMIN_NEUTER ): ?>
        <?php echo $this->translate('(%1$s)', '<a href="mailto:' .
            $this->escape($this->user->email) . '">' . $this->user->email . '</a>') ?>
      <?php endif; ?>
    <?php else: ?>
      <i><?php echo $this->translate('Deleted Member') ?></i>
    <?php endif; ?>
  </dt>
  <dd>
     <?php echo $this->translate('Ad Title') ?>
  </dd>
  <dt> <?php
    echo $this->htmlLink(
			array('route' => 'communityad_userad', 'ad_id' => $this->order->source_id),
			 ucfirst($this->title), array('target' => '_blank', 'title' => $this->translate(ucfirst($this->title)))); ?>
  </dt>
  <dd>
    <?php echo $this->translate('Payment Gateway') ?>
  </dd>
  <dt>
    <?php if( $this->gateway ): ?>
      <?php echo $this->translate($this->gateway->title) ?>
    <?php else: ?>
      <i><?php echo $this->translate('Unknown Gateway') ?></i>
    <?php endif; ?>
  </dt>

  <dd>
    <?php echo $this->translate('Payment Type') ?>
  </dd>
  <dt>
    <?php echo $this->translate(ucfirst($this->transaction->type)) ?>
  </dt>

  <dd>
    <?php echo $this->translate('Payment State') ?>
  </dd>
  <dt>
    <?php echo $this->translate(ucfirst($this->transaction->state)) ?>
  </dt>

  <dd>
    <?php echo $this->translate('Payment Amount') ?>
  </dd>
  <dt>
    <?php echo $this->locale()->toCurrency($this->transaction->amount, $this->transaction->currency) ?>
    <?php echo $this->translate('(%s)', $this->transaction->currency) ?>
  </dt>

  <dd>
    <?php echo $this->translate('Gateway Transaction ID') ?>
  </dd>
  <dt>
    <?php if( !empty($this->transaction->gateway_transaction_id) ): ?>
      <?php echo $this->htmlLink(array(
          'route' => 'admin_default',
          'module' => 'communityad',
          'controller' => 'payment',
          'action' => 'detail-transaction',
          'transaction_id' => $this->transaction->transaction_id,
        ), $this->transaction->gateway_transaction_id, array(
          //'class' => 'smoothbox',
          'target' => '_blank',
      )) ?>
    <?php else: ?>
      -
    <?php endif; ?>
  </dt>

  <?php if( !empty($this->transaction->gateway_parent_transaction_id) ): ?>
  <dd>
    <?php echo $this->translate('Gateway Parent Transaction ID') ?>
  </dd>
  <dt>
    <?php echo $this->htmlLink(array(
        'route' => 'admin_default',
        'module' => 'communityad',
          'controller' => 'payment',
        'action' => 'detail-transaction',
        'transaction_id' => $this->transaction->transaction_id,
        'show-parent' => 1,
      ), $this->transaction->gateway_parent_transaction_id, array(
        //'class' => 'smoothbox',
        'target' => '_blank',
    )) ?>
  </dt>
  <?php endif; ?>

  <dd>
    <?php echo $this->translate('Gateway Order ID') ?>
  </dd>
  <dt>
    <?php if( !empty($this->transaction->gateway_order_id) ): ?>
      <?php echo $this->htmlLink(array(
          'route' => 'admin_default',
          'module' => 'communityad',
          'controller' => 'payment',
          'action' => 'detail-order',
          'transaction_id' => $this->transaction->transaction_id,
        ), $this->transaction->gateway_order_id, array(
          //'class' => 'smoothbox',
          'target' => '_blank',
      )) ?>
    <?php else: ?>
      -
    <?php endif; ?>
  </dt>

  <dd>
    <?php echo $this->translate('Date') ?>
  </dd>
  <dt>
    <?php echo $this->locale()->toDateTime($this->transaction->timestamp) ?>
  </dt>

  <dd>
    <?php echo $this->translate('Options') ?>
  </dd>
  <dt>
    <?php if( $this->order && !empty($this->order->source_id) &&
        $this->order->source_type == 'userads' ): ?>
      <?php echo $this->htmlLink(array(
        'reset' => false,
        'controller' => 'communityad',
        'action' => 'detail',
        'userad_id' => $this->order->source_id,
        'transaction_id' => null,
      ), $this->translate('Related Advertisement'), array(
        'target' => '_parent'
      )) ?>
      <?php else: ?>
      -
    <?php endif; ?>
  </dt>
</dl>