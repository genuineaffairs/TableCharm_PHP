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
class Resume_Form_Resume_Renew extends Resume_Form_Resume_Checkout
{
  public function init()
  {
    parent::init();
    
    $this->setTitle('Renew Resume Package')
      ->setDescription('You are about to renew the following resume package, please review and confirm the details below, then press "Pay by PayPal" button to proceed.');
  }
}