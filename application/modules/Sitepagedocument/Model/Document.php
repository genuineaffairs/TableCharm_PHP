<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Document.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagedocument_Model_Document extends Core_Model_Item_Abstract {

  protected $_owner_type = 'user';
  protected $_parent_type = 'user';
  protected $_searchColumns = array('sitepagedocument_title', 'sitepagedocument_description', 'fulltext'); //GLOBAL SEARCH
  protected $_parent_is_owner = true;

	public function getMediaType() {
		return 'document';
	}
	
  /**
   * Return page object
   *
   * @return page object
   * */
  public function getParent($recurseType = null) {
    
    if($recurseType == null) $recurseType = 'sitepage_page';
    
    return Engine_Api::_()->getItem($recurseType, $this->page_id);
  }
  
  /**
   * Create a document file and make entries in storage table
   *
   * @param array file_pass
   * @return document file info array
   */
  public function setFile($file_pass) {
    if ($file_pass instanceof Zend_Form_Element_File) {
      $file = $file_pass->getFileName();
    } else if (is_array($file_pass) && !empty($file_pass['tmp_name'])) {
      $file = $file_pass['tmp_name'];
    } else if (is_string($file_pass) && file_exists($file_pass)) {
      $file = $file_pass;
    } else {
      throw new Sitepagedocument_Model_Exception('invalid argument passed to setFile');
    }
    $params = array(
        'parent_type' => 'sitepagedocument',
        'parent_id' => $this->getIdentity()
    );

    try {
      $sitepagedocument_return = Engine_Api::_()->storage()->create($file, $params);
    } catch (Exception $e) {
      $msg = $e->getMessage();
      return $msg;
    }

    if (!empty($sitepagedocument_return->file_id)) {

      //UPDATE FILE INFORMATION INTO DATABASE
      $this->modified_date = new Zend_Db_Expr('NOW()');
      $this->filename_id = $sitepagedocument_return->file_id;
      $this->storage_path = APPLICATION_PATH . '/' . $sitepagedocument_return->storage_path;
      $this->save();

      $storagemodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('storage');
      $storageversion = $storagemodule->version;
      if ($storageversion < '4.1.1') {
        $file_path = $sitepagedocument_return->storage_path;
      } else {
        $file_path = $sitepagedocument_return->map();
      }

      $sitepagedocument_info = array('sitepagedocument' => $this,
          'file_path' => $file_path);
      return $sitepagedocument_info;
    }
  }

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array()) {
    if (Zend_Controller_Front::getInstance()->getRequest()->getParam('document_id')) {
      $document_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('document_id');
      $tableDocument = Engine_Api::_()->getDbtable('documents', 'sitepagedocument');
      $select = $tableDocument->select()
                      ->where('document_id = ?', $document_id)
                      ->limit(1);

      $row = $tableDocument->fetchRow($select);
      if ($row !== null) {
        $pageid = $row->page_id;
      }
    } else {
      $pageid = $this->page_id;
    }
    
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);

		if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
			$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagedocument.sitemobile-profile-sitepagedocuments', $pageid, $layout);
		} else {
			$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagedocument.profile-sitepagedocuments', $pageid, $layout);
		}

    $params = array_merge(array(
                'route' => 'sitepagedocument_detail_view',
                'reset' => true,
                'user_id' => $this->owner_id,
                'document_id' => $this->document_id,
                'slug' => $this->getSlug(),
                'tab' => $tab_id
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
    return $this->sitepagedocument_title;
  }

	/**
   * Return a truncate text
   *
   * @param int limit 
   * @param text text 
   * @return truncate text
   * */
  public function truncateText($text, $limit) {
    $tmpBody = strip_tags($text);
    return ( Engine_String::strlen($tmpBody) > $limit ? Engine_String::substr($tmpBody, 0, $limit) . '..' : $tmpBody );
  }

	/**
   * Return a file name of document
   *
   * @return file name of document
   * */
	public function getDocumentFileName() {
		//FILENAME FROM STORAGE TABLE
    $tableStorage = Engine_Api::_()->getDbtable('files', 'storage');
    $tableStorageName = $tableStorage->info('name');
    $tableDocumentName = Engine_Api::_()->getDbtable('documents', 'sitepagedocument')->info('name');

    $file_name = $tableStorage->select()
                    ->setIntegrityCheck(false)
                    ->from($tableDocumentName, array(''))
                    ->join($tableStorageName, "$tableStorageName.file_id = $this->filename_id", array('name'))
                    ->where($tableStorageName . '.parent_type = ?', 'sitepagedocument')
										->query()
										->fetchColumn();
    return $file_name;
	}
  
  public function categoryName() {
    $categoryTable = Engine_Api::_()->getDbtable('categories', 'sitepagedocument');
    return $categoryTable->select()
                    ->from($categoryTable, 'title')
                    ->where('category_id = ?', $this->category_id)
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
  }  

  /**
   * Delete create activity feed of document before delete document 
   *
   */
  protected function _delete() {
    
    Engine_Api::_()->getApi('subCore', 'sitepage')->deleteCreateActivityOfExtensionsItem($this, array('sitepagedocument_new', 'sitepagedocument_admin_new'));
    parent::_delete();
  }

  /**
   * Return a document trunacte description
   *
   * @return truncate description
   * */
  public function getDescription() {
    // @todo decide how we want to handle multibyte string functions
    $tmpBody = strip_tags($this->sitepagedocument_description);
    return ( Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody );
  }

  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   * */
  public function comments() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   * */
  public function likes() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

}
?>