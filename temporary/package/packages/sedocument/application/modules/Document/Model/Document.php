<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Document.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Document_Model_Document extends Core_Model_Item_Abstract
{
  protected $_owner_type = 'user';
  
  protected $_parent_type = 'user';

  protected $_searchColumns = array('document_title', 'document_description', 'fulltext');

  protected $_parent_is_owner = true;
  
	public function getMediaType() {
		return 'document';
	}

	/**
   * Create document
   *
   * @param array file_pass 
   * @return create document and return info
   * */	
  public function setFile($file_pass)
  {
    if( $file_pass instanceof Zend_Form_Element_File ) {
      $file = $file_pass->getFileName();
    } else if( is_array($file_pass) && !empty($file_pass['tmp_name']) ) {  
      $file = $file_pass['tmp_name'];
    } else if( is_string($file_pass) && file_exists($file_pass) ) {
      $file = $file_pass;
    } else {
      throw new Document_Model_Exception('invalid argument passed to setFile');
    }
    $params = array(
      'parent_type' => 'document',
      'parent_id' => $this->getIdentity()
    );

    try {
  		$document_return = Engine_Api::_()->storage()->create($file, $params);
    } catch (Exception $e) {
     	$msg = $e->getMessage();
     	return $msg;
    }
    
    if (!empty($document_return->file_id)) { 
    
      //UPDATE FILE INFORMATION INTO DATABASE
	    $this->modified_date = new Zend_Db_Expr('NOW()');
	    $this->filename_id = $document_return->file_id;
			$this->storage_path = APPLICATION_PATH.'/'.$document_return->storage_path;
	    $this->save();

			$storagemodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('storage');
			$storageversion = $storagemodule->version;
			if($storageversion < '4.1.1') {
				$file_path = $document_return->storage_path;
			}
			else {
				$file_path = $document_return->map();
			}

	    $document_info = array('document' => $this, 'file_path' => $file_path);

	    return $document_info;
    } 
  }

	/**
   * Create document thumbnail
   *
   * */
  public function setPhoto()
  {
		if(empty($this->thumbnail)) {
			return;
		}

		//MAKE DIRECTORY IN PUBLIC FOLDER
		@mkdir(APPLICATION_PATH."/temporary/documents", 0777, true);

		//COPY THE ICONS IN NEWLY CREATED FOLDER
		$temp_name = "documentThumb".$this->document_id.".jpeg";
		@copy($this->thumbnail, APPLICATION_PATH."/temporary/documents/$temp_name");
		@chmod(APPLICATION_PATH . '/temporary/documents', 0777);

		//UPLOAD DEFAULT ICONS
		$file = array();
		$file['tmp_name'] =  APPLICATION_PATH . "/temporary/documents/$temp_name";
		$fileName = $file['name'] = $temp_name;
    
    if( !$fileName ) {
      $fileName = basename($file['tmp_name']);
    }
    
    $extension = ltrim(strrchr(basename($fileName), '.'), '.');
    $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    
    $params = array(
      'parent_type' => 'document',
      'parent_id' => $this->document_id,
      'user_id' => $this->owner_id,
      'name' => $fileName,
    );

    // Save
    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

    // Resize image (normal)
    $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file['tmp_name'])
      ->resample(0, 0, $image->width, $image->height, $image->width, $image->height)
      ->write($normalPath)
      ->destroy();

    // Resize image (icon)
    $squarePath = $path . DIRECTORY_SEPARATOR . $base . '_is.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file['tmp_name']);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 48, 48)
      ->write($squarePath)
      ->destroy();

    // Store
    $iIconNormal = $filesTable->createFile($normalPath, $params);
    $iSquare = $filesTable->createFile($squarePath, $params);

    $iIconNormal->bridge($iSquare, 'thumb.icon');

    // Remove temp files
    @unlink($normalPath);
    @unlink($squarePath);

		//REMOVE THE COPIED IMAGE
		$is_exist = file_exists(APPLICATION_PATH . "/temporary/documents/$temp_name");
		if($is_exist) {
			@unlink(APPLICATION_PATH . "/temporary/documents/$temp_name");
		}

		return $iIconNormal->file_id;
  }

	/**
   * Return keywords
   *
   * @param char separator 
   * @return keywords
   * */	
  public function getKeywords($separator = ' ')
  {
    $keywords = array();
    foreach( $this->tags()->getTagMaps() as $tagmap ) {
      $tag = $tagmap->getTag();
      $keywords[] = $tag->getTitle();
    }

    if( null === $separator ) {
      return $keywords;
    }

    return join($separator, $keywords);
  }
  
  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array())
  {
    $slug = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($this->getTitle()))), '-');
    
    $params = array_merge(array(
      'route' => 'document_detail_view',
      'reset' => true,
      'user_id' => $this->owner_id,
      'document_id' => $this->document_id,
      'slug' => $slug,
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }
  
  /**
   * Return a document title
   *
   * @return title
   * */
  public function getTitle() {
    return $this->document_title;
  }
  
  /**
   * Return a file name of document
   *
   * @return file name of document
   * */
  public function getDocumentFileName() {

    //GET STORAGE TABLE
    $tableStorage = Engine_Api::_()->getDbtable('files', 'storage');
    $tableStorageName = $tableStorage->info('name');

		//GET DOCUMENT TABLE NAME
    $tableDocumentName = Engine_Api::_()->getDbtable('documents', 'document')->info('name');

		//MAKE QUERY
    $file_name = $tableStorage->select()
                    ->setIntegrityCheck(false)
                    ->from($tableDocumentName, array(''))
                    ->join($tableStorageName, "$tableStorageName.file_id = $this->filename_id", array('name'))
                    ->where($tableStorageName . '.parent_type = ?', 'document')
                    ->query()
                    ->fetchColumn();

		//RETURN RESULTS
    return $file_name;
  }

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
}