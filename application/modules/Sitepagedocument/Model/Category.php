<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Category.php 6590 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagedocument_Model_Category extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;

  public function getUsedCount()
  {
    $documentTable = Engine_Api::_()->getItemTable('sitepagedocument');
    return $documentTable->select()
        ->from($documentTable, new Zend_Db_Expr('COUNT(document_id)'))
        ->where('category_id = ?', $this->category_id)
        ->query()
        ->fetchColumn();
  }

  public function isOwner($owner)
  {
    return false;
  }

  public function getOwner($recurseType = null)
  {
    return $this;
  }
}
