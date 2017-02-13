<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _gateway.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="headline">
  <h2>
    <?php echo $this->translate('Advertising'); ?>
  </h2>
  <?php if (count($this->navigation)) { ?>
   <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
    </div>
  <?php } ?>
</div>
<?php if( $this->status == 'pending' ): // Check for pending status ?>
  <?php echo $this->translate('The payment of your ad is pending. You will receive an email when the payment completes.') ?>
<?php else: ?>

  <form method="get" action="<?php echo $this->escape($this->url(array(),'communityad_process_payment')) ?>"
        class="global_form" enctype="application/x-www-form-urlencoded">
    <div>
      <div>
        <h3>
          <?php echo $this->translate('Order your advertisement') ?>
        </h3>
        <p class="form-description">
          <?php echo $this->translate('You have created an ad that requires payment. You will be taken to a secure checkout area where you can pay for your ad. Remember to continue back to our site after your purchase.') ?>
        </p>
        <p style="font-weight: bold; padding-top: 15px; padding-bottom: 15px;">
  
            <?php echo $this->translate('Please pay a one-time fee to continue:') ?>
        
          <?php echo $this->package->getPackageDescription() ?>
        </p>
        <div class="form-elements">
          <div id="buttons-wrapper" class="form-wrapper">
              <?php foreach( $this->gateways as $gatewayInfo ):
                $gateway = $gatewayInfo['gateway'];
                $plugin = $gatewayInfo['plugin'];
                $first = ( !isset($first) ? true : false );
                ?>
                <?php if( !$first ): ?>
                  <?php echo $this->translate('or') ?>
                <?php endif; ?>
                <button type="submit" name="execute" onclick="$('gateway_id').set('value', '<?php echo $gateway->gateway_id ?>')">
                  <?php echo $this->translate('Pay with %1$s', $this->translate($gateway->title)) ?>
                </button>
              <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
    <input type="hidden" name="gateway_id" id="gateway_id" value="" />
  </form>

<?php endif; ?>