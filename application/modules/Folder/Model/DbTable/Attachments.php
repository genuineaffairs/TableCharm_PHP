<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Folder
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Attachments.php 9071 2011-07-20 23:43:30Z john $
 * @author     Sami
 */

/**
 * @category   Application_Extensions
 * @package    Folder
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Folder_Model_DbTable_Attachments extends Engine_Db_Table
{
  protected $_rowClass = 'Folder_Model_Attachment';
  
  public function getAttachmentSelect(array $params)
  {
    $select = $this->select();
    
    if (!empty($params['folder']))
    {
    	$folder_id = $params['folder'] instanceof Folder_Model_Folder ? $params['folder']->getIdentity() : $params['folder'];
    	$select->where('folder_id = ?', $folder_id);
    }

    if( !isset($params['order']) ) {
      $select->order('order ASC');
    } else if( is_string($params['order']) ) {
      $select->order($params['order']);
    }
    
    return $select;
  }
  
  public function getAttachmentPaginator(array $params)
  {
    $paginator = Zend_Paginator::factory($this->getAttachmentSelect($params));
    if( !empty($params['page']) )
    {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) )
    {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;    
  }
}
