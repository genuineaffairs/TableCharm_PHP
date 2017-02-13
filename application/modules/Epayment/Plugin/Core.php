<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Epayment
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */

class Epayment_Plugin_Core
{

  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User ) {
      // Delete epayments
      $epaymentTable = Engine_Api::_()->getDbtable('epayments', 'epayment');
      $epaymentSelect = $epaymentTable->select()->where('user_id = ?', $payload->getIdentity());
      foreach( $epaymentTable->fetchAll($epaymentSelect) as $epayment ) {
        $epayment->delete();
      }
    }
  }
}