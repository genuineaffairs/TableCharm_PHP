<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Folder
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Attachment.php 9071 2011-07-20 23:43:30Z john $
 * @author     Sami
 */

/**
 * @category   Application_Extensions
 * @package    Folder
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Folder_Model_Attachment extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = array('title', 'description', 'search');
  
  protected $storage_file;
  
  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'folder_attachment_profile',
      'reset' => true,
      'attachment_id' => $this->getIdentity(),
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
      'route' => 'folder_attachment_specific',
      'reset' => true,
      'action' => $action,
      'attachment_id' => $this->getIdentity(),
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }
    
  public function getTitle()
  {
    $title = $this->title ? $this->title : $this->getFile()->name;
    return $title;
  }

  
  /**
   * @return Engine_Vfs_Object_Abstract
   */
  public function getFile()
  {
    if (!$this->storage_file)
    {
      $this->storage_file = Engine_Api::_()->getApi('storage', 'storage')->get($this->file_id, null);
    }
    return $this->storage_file;
  }  
  
  public function getFolder()
  {
    return Engine_Api::_()->getItem('folder', $this->folder_id);
  }

  public function getParent($type = null)
  {
    if( null === $type || $type === 'folder' ) {
      return $this->getFolder();
    } else {
      return $this->getFolder()->getParent($type);
    }
  }

  /**
   * Gets a url to the current attachment representing this item. Return null if none
   * set
   *
   * @param string The attachment type (null -> main, thumb, icon, etc);
   * @return string The attachment url
   */
  public function getAttachmentUrl($type = null)
  {
    $attachment_id = $this->file_id;
    if( !$attachment_id ) {
      return null;
    }

    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($attachment_id, $type);
    if( !$file ) {
      return null;
    }

    return $file->map();
  }

  public function isSearchable()
  {
    $folder = $this->getFolder();
    if( !($folder instanceof Core_Model_Item_Abstract) ) {
      return false;
    }
    return $folder->isSearchable();
  }

  public function getAuthorizationItem()
  {
    return $this->getFolder();
  }

  public function isOwner($user)
  {
    if( empty($this->folder_id) ) {
      return (($this->owner_id == $user->getIdentity()) && ($this->owner_type == $user->getType()));
    }
    return parent::isOwner($user);
  }

  public function setAttachment($attachment)
  {
    if( $attachment instanceof Zend_Form_Element_File ) {
      $file = $attachment->getFileName();
      $fileName = $file;
    } else if( $attachment instanceof Storage_Model_File ) {
      $file = $attachment->temporary();
      $fileName = $attachment->name;
    } else if( $attachment instanceof Core_Model_Item_Abstract && !empty($attachment->file_id) ) {
      $tmpRow = Engine_Api::_()->getItem('storage_file', $attachment->file_id);
      $file = $tmpRow->temporary();
      $fileName = $tmpRow->name;
    } else if( is_array($attachment) && !empty($attachment['tmp_name']) ) {
      $file = $attachment['tmp_name'];
      $fileName = $attachment['name'];
    } else if( is_string($attachment) && file_exists($attachment) ) {
      $file = $attachment;
      $fileName = $attachment;
    } else {
      throw new User_Model_Exception('invalid argument passed to setAttachment');
    }

    if( !$fileName ) {
      $fileName = $file;
    }

    $name = basename($file);
    $extension = ltrim(strrchr($fileName, '.'), '.');
    $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => $this->getType(),
      'parent_id' => $this->getIdentity(),
      'user_id' => $this->owner_id,
      'name' => $fileName,
    );

    //Engine_Api::_()->getApi('debug','radcodes')->log($attachment, "attachment");
    //Engine_Api::_()->getApi('debug','radcodes')->log($params, "file=$file fileName=$fileName name=$name ext=$extension base=$base path=$path params=");
    
    // Save
    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

    // Resize image (main)
    $mainPath = $path . DIRECTORY_SEPARATOR . $base . '.' . $extension;
    
    if( !move_uploaded_file($file, $mainPath) ) {    
      throw new Job_Model_Exception("Unable to move file to upload directory. From=$file Destination=$mainPath");
    }
    /*
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($mainPath)
      ->destroy();
		*/
    
    // Store
    try {
      $iMain = $filesTable->createFile($mainPath, $params);
    } catch( Exception $e ) {
      // Remove temp files
      @unlink($mainPath);
      // Throw
      if( $e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE ) {
        throw new Folder_Model_Exception($e->getMessage(), $e->getCode());
      } else {
        throw $e;
      }
    }
    
    // Remove temp files
    @unlink($mainPath);

    // Update row
    $this->modified_date = date('Y-m-d H:i:s');
    $this->file_id = $iMain->file_id;
    $this->save();

    // Delete the old file?
    try {
	    if( !empty($tmpRow) ) {
	      $tmpRow->delete();
	    }
    } catch( Exception $e ) {
    	// silence
    }
    
    return $this;
  }
  
  public function getAttachmentIndex()
  {
    return $this->getTable()
        ->select()
        ->from($this->getTable(), new Zend_Db_Expr('COUNT(attachment_id)'))
        ->where('folder_id = ?', $this->folder_id)
        ->where('`order` < ?', $this->order)
        ->order('order ASC')
        ->limit(1)
        ->query()
        ->fetchColumn();
  }
  
  public function getNextAttachment()
  {
    $table = $this->getTable();
    $select = $table->select()
        ->where('folder_id = ?', $this->folder_id)
        ->where('`order` > ?', $this->order)
        ->order('order ASC')
        ->limit(1);
    $attachment = $table->fetchRow($select);
    
    if( !$attachment ) {
      // Get first attachment instead
      $select = $table->select()
          ->where('folder_id = ?', $this->folder_id)
          ->order('order ASC')
          ->limit(1);
      $attachment = $table->fetchRow($select);
    }
    
    return $attachment;
  }
  
  public function getPreviousAttachment()
  {
    $table = $this->getTable();
    $select = $table->select()
        ->where('folder_id = ?', $this->folder_id)
        ->where('`order` < ?', $this->order)
        ->order('order DESC')
        ->limit(1);
    $attachment = $table->fetchRow($select);
    
    if( !$attachment ) {
      // Get last attachment instead
      $select = $table->select()
          ->where('folder_id = ?', $this->folder_id)
          ->order('order DESC')
          ->limit(1);
      $attachment = $table->fetchRow($select);
    }
    
    return $attachment;
  }

  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   * */
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   * */
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  /**
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   * */
  public function tags()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }

  protected function _postDelete()
  {
    $mainAttachment = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id);

    // Delete main
    if( $mainAttachment && $mainAttachment->getIdentity() ) {
      try {
        $mainAttachment->delete();
      } catch( Exception $e ) {}
    }

    parent::_postDelete();
  }
}
