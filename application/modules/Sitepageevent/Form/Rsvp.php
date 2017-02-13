<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Rsvp.php 6590 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_Form_Rsvp extends Engine_Form {

  public function init() {

    $this
            ->setMethod('POST')
            ->setAction($_SERVER['REQUEST_URI'])
    ;

    $this->addElement('Radio', 'rsvp', array(
        'multiOptions' => array(
            2 => 'Attending',
            1 => 'Maybe Attending',
            0 => 'Not Attending',
        ),
    ));
  }

}

?>