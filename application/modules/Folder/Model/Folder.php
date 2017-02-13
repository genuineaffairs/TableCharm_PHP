<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Folder
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Folder_Model_Folder extends Core_Model_Item_Abstract implements Countable
{
  // Properties
  protected $_owner_type = 'user';

  protected $_searchTriggers = array('search', 'title', 'description');

  protected $_modifiedTriggers = array('search', 'title', 'description');
  
  protected $category;
  
  
  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array())
  {
    $slug = $this->getSlug();
    
    $params = array_merge(array(
      'route' => 'folder_profile',
      'reset' => true,
      'folder_id' => $this->folder_id,
      'slug' => $slug,
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function getActionHref($action, $params = array())
  {
    $params = array_merge(array(
      'route' => 'folder_specific',
      'reset' => true,
      'folder_id' => $this->folder_id,
      'action' => $action
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function getParentFoldersHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'folder_general',
      'reset' => true,
      'parent' => $this->getParent()->getGuid(),
      'action' => 'browse',
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }
  
  public function getParentTypeHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'folder_general',
      'reset' => true,
      'parent_type' => $this->parent_type,
      'action' => 'browse',
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }
  
  public function getCategory()
  {
    if (!($this->category instanceof Folder_Model_Category) || $this->category->getIdentity() != $this->category_id)
    {
      $category = Engine_Api::_()->folder()->getCategory($this->category_id);
      if (!($category instanceof Folder_Model_Category))
      {
        $category = new Folder_Model_Category(array());
      }
      $this->category = $category;
    }

    return $this->category;
  }
  
  
  // Interfaces
  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   **/
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   **/
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  /**
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   **/
  public function tags()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }
  
  
  public function setPhoto($photo)
  {  
    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
    } else if( $photo instanceof Storage_Model_File ) {
      $file = $photo->temporary();
    } else if( $photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id) ) {
      $file = Engine_Api::_()->getItem('storage_file', $photo->file_id)->temporary();
    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
    } else if( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
    } else {
      throw new Folder_Model_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => $this->getType(),
      'parent_id' => $this->getIdentity()
    );

    // Save
    $storage = Engine_Api::_()->storage();

    // Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($path.'/m_'.$name)
      ->destroy();

    // Resize image (profile)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(200, 400)
      ->write($path.'/p_'.$name)
      ->destroy();

    // Resize image (normal)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(140, 160)
      ->write($path.'/in_'.$name)
      ->destroy();

    // Resize image (icon)
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 48, 48)
      ->write($path.'/is_'.$name)
      ->destroy();
      
    // Store
    $iMain = $storage->create($path.'/m_'.$name, $params);
    $iProfile = $storage->create($path.'/p_'.$name, $params);
    $iIconNormal = $storage->create($path.'/in_'.$name, $params);
    $iSquare = $storage->create($path.'/is_'.$name, $params);
    
    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare, 'thumb.icon');
    
    // Remove temp files
    @unlink($path.'/p_'.$name);
    @unlink($path.'/m_'.$name);
    @unlink($path.'/in_'.$name);
    @unlink($path.'/is_'.$name);
    
    // Update row
    $this->photo_id = $iMain->file_id;
    $this->save();
    
    return $this;  
  }
  
  public function removePhoto()
  {
    if (empty($this->photo_id))
    {
      return;
    }
    
    $types = array(null, 'thumb.profile', 'thumb.normal', 'thumb.icon');
    foreach ($types as $type)
    {
      $file = Engine_Api::_()->getApi('storage', 'storage')->get($this->photo_id, $type);
      if ($file)
      {
        $file->remove();
      } 
    }
    
    $this->photo_id = 0;
  }
  
  
  protected function _delete()
  {
    if( $this->_disableHooks ) return;

    // Delete all field values
    $values = Engine_Api::_()->fields()->getFieldsValues($this);
    foreach ($values as $value)
    {
      $value->delete();
    }
    
    // Delete search row
    $search = Engine_Api::_()->fields()->getFieldsSearch($this);
    if ($search)
    {
      $search->delete();
    }

    parent::_delete();
  }

  protected function _postDelete()
  {
    $this->removePhoto();
    
    $attachments = $this->getAttachmentPaginator();
    foreach ($attachments as $attachment) {
      $attachment->delete();
    }
    
    parent::_postDelete();
  }
  
  public function getAttachmentPaginator($params = array())
  {
    $params = array_merge($params, array('folder' => $this));
    $attachmentTable = Engine_Api::_()->getItemTable('folder_attachment');
    $paginator = $attachmentTable->getAttachmentPaginator($params);
    return $paginator;
  }
  
  public function count()
  {
    $attachmentTable = Engine_Api::_()->getItemTable('folder_attachment');
    return $attachmentTable->select()
        ->from($attachmentTable, new Zend_Db_Expr('COUNT(attachment_id)'))
        ->where('folder_id = ?', $this->getIdentity())
        ->limit(1)
        ->query()
        ->fetchColumn();
  }  
  
  public function getParentTypeText()
  {
    $view = Zend_Registry::get('Zend_View');
    $type = 'ITEM_TYPE_'.strtoupper($this->parent_type);
    $text = $view->translate($type);
    if( $type == $text ) {
      $text = ucwords(str_replace('_', ' ',$this->parent_type));
    }
    
    return $text;
  }
}