<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Fields.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Zulu_Plugin_Edit_ClinicalFields extends Zulu_Plugin_Common_ClinicalFields_Abstract {

    protected $_formClass = 'Zulu_Form_Edit_ClinicalFields';
    
    public function getUser() {
        if (Engine_Api::_()->core()->hasSubject()) {
            return Engine_Api::_()->core()->getSubject();
        }
        return null;
    }
}