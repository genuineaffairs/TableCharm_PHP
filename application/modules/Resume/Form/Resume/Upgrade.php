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
class Resume_Form_Resume_Upgrade extends Resume_Form_Resume_Checkout
{
  public function init()
  {
    parent::init();
    
    $this->setTitle('Upgrade Resume Package')
      ->setDescription('You are about to upgrade resume posting to a different resume package, please review and confirm the details below, then press "Pay by PayPal" button to proceed.');
  }
}