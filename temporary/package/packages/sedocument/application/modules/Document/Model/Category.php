<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Category.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Document_Model_Category extends Core_Model_Item_Abstract
{	
    public function getTitle($inflect = false) {
        if ($inflect) {
            return ucwords($this->category_name);
        } else {
            return $this->category_name;
        }
    }
}