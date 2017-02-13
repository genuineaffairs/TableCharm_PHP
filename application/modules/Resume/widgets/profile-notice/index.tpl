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
    <div class="tip">
      <span>
        <strong><?php echo $this->translate('Important Notice:')?></strong>
        <ul>
<?php if (!$this->resume->isPublished()): ?>      
  <li><?php echo $this->translate('This CV is in DRAFT mode, once you have reviewed its content, make sure you publish it to LIVE mode.')?></li>
<?php endif; ?>      
<?php if (!$this->resume->isApprovedStatus()):?>
  <li><?php echo $this->translate('This CV is not yet approved, and not viewable by public.')?></li>
<?php endif; ?>

<?php if ($this->resume->requiresEpayment() && !$this->resume->getRecentEpayment()): ?>
  <li><?php echo $this->translate('Payment is required, if you have not paid yet, please <a href="%s">make payment</a> now.', $this->resume->getCheckoutHref())?></li>
<?php endif; ?>

<?php if ($this->resume->isExpired()): ?>
  <li><?php echo $this->translate('This CV posting has been expired. Please <a href="%s">renew or upgrade</a> now.', $this->resume->getActionHref('packages'))?></li>
<?php endif; ?>
        </ul>
      </span>
    </div>  