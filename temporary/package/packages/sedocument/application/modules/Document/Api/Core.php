<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Document_Api_Core extends Core_Api_Abstract {

	/**
	* Delete document from server
	* @param int $document_id : document id
	*/
	public function deleteServerDocument($document_id) {

		//GET DOCUMENT OBJECT
		$document = Engine_Api::_()->getItem('document', $document_id);

		//RETURN IF DOCUMENT OBJECT IS EMPTY
		if(empty($document)) {
			return;
		}

		//UNLINK THE DOCUMENT FILE
		$storagemodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('storage');
		$storageversion = $storagemodule->version;
		if($storageversion < '4.1.1' && file_exists($document->storage_path) && !empty($document->storage_path)) {
			unlink($document->storage_path);
		}

		//DELETE STORAGE TABLE ENTRIES
		if(!empty($document->filename_id)) {
			$storage = Engine_Api::_()->getItem('storage_file', $document->filename_id);
			$fileName = $storage->name;
			$storage->delete();

			$document->storage_path = $fileName;
			$document->filename_id = 0;
			$document->save();
		}
	}

  /**
   * Return parse string
   *
   * @param string $content
   * @return parse string
   */
  public function parseString($content) {
		
		//RETURN PARSED STRING
    return str_replace("'", "\'", trim($content));
  }

  /**
   * Delete the document and belongings
   * 
   * @param int $document_id
   */
  public function deleteContent($document_id) {

    //GET THE DOCUMENT OBJECT
    $document = Engine_Api::_()->getItem('document', $document_id);

		//RETURN IF DOCUMENT ID IS EMPTY
    if (empty($document)) {
      return;
    }

		//INCLUDE SCRIBD FILE
    include_once APPLICATION_PATH . '/application/modules/Document/Api/Scribd.php';

		//GET SCRIBD OBJECT
    $scribd_api_key = Engine_Api::_()->getApi('settings', 'core')->document_api_key;
    $scribd_secret = Engine_Api::_()->getApi('settings', 'core')->document_secret_key;
    $scribd = new Scribd($scribd_api_key, $scribd_secret);

    //DELETE DOCUMENT FROM SCRIBD
    if (!empty($document->doc_id)) {
      $scribd->my_user_id = $document->owner_id;
      $scribd->delete($document->doc_id);
    }

		//DELETE ALL MAPPING VALUES FROM FIELD TABLES
		Engine_Api::_()->fields()->getTable('document', 'values')->delete(array('item_id = ?' => $document_id));
		Engine_Api::_()->fields()->getTable('document', 'search')->delete(array('item_id = ?' => $document_id));
	
		//DELETE RATING VALUES
    Engine_Api::_()->getDbtable('ratings', 'document')->delete(array('document_id = ?' => $document_id));

		//DELETE DOCUMENT OF THE DAY DATA
		Engine_Api::_()->getDbtable('itemofthedays', 'document')->delete(array('document_id = ?' => $document_id));

    //DELETE DOCUMENT FROM LOCAL SERVER
    $storagemodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('storage');
    $storageversion = $storagemodule->version;
    if ($storageversion < '4.1.1' && file_exists($document->storage_path) && !empty($document->storage_path)) {
      unlink($document->storage_path);
    }

    //DELETE DOCUMENT AND OTHER BELONGINGS
    $document->delete();
  }

  /**
   * Send email to document and page owner if document has been deleted from scirbd
   * @param array $document : document item
   */
	public function emailDocumentDelete($document) {

		//RETURN IF DOCUMENT OBJECT IS EMPTY
		if(empty($document)) {
			return;
		}

		//GET DOCUMENT OWNER EMAIL ID
		$document_owner_id = $document->owner_id;
		$userTable = Engine_Api::_()->getItemTable('user');
		$document_owner_email_id = $userTable->select()
										->from($userTable, 'email')
										->where('user_id = ?', $document_owner_id)
										->limit(1)
										->query()
										->fetchColumn(0);

		//SEND THE EMAIL IF OWNER ID IS NOT EMPTY
		if(!empty($document_owner_email_id)) {

			$slug = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($document->document_title))), '-');

			$Zend_router = Zend_Controller_Front::getInstance()->getRouter();

			$document_title_with_link = '<a href = http://' . $_SERVER['HTTP_HOST'] . $Zend_router->assemble(array( 'user_id' => $document->owner_id, 'document_id' => $document->document_id, 'slug' => $slug), 'document_detail_view').">$document->document_title</a>";

			$edit_document_link = 'http://' . $_SERVER['HTTP_HOST'] . $Zend_router->assemble(array('document_id' => $document->document_id), 'document_edit');

			$delete_document_link = 'http://' . $_SERVER['HTTP_HOST'] . $Zend_router->assemble(array('document_id' => $document->document_id), 'document_delete');

			Engine_Api::_()->getApi('mail', 'core')->sendSystem($document_owner_email_id, 'notify_document_delete', array(
				'document_title_with_link' => $document_title_with_link,
				'edit_document_link' => $edit_document_link,
				'delete_document_link' => $delete_document_link,
				'queue' => true
			));
		}
	}

  /**
   * Get document tags created by users
   * @param int $owner_id : document owner id
	 * @param int $total_tags : number tags to show
   */
	public function getTags($owner_id = 0, $total_tags = 100, $count_only = 0) {

		//GET TAGMAP TABLE NAME
    $tableTagmaps = 'engine4_core_tagmaps';

		//GET TAG TABLE NAME
    $tableTags = 'engine4_core_tags';

		//GET DOCUMENT TABLE
    $tableDocumentName = Engine_Api::_()->getDbtable('documents', 'document');
    $tableDocuments = $tableDocumentName->info('name');

		//MAKE QUERY
    $select = $tableDocumentName->select()
                    ->setIntegrityCheck(false)
                    ->from($tableDocuments, array(''))
                    ->joinInner($tableTagmaps, "$tableDocuments.document_id = $tableTagmaps.resource_id", array('COUNT(resource_id) AS Frequency'))
                    ->joinInner($tableTags, "$tableTags.tag_id = $tableTagmaps.tag_id",array('text', 'tag_id'));

		if(!empty($owner_id)) {
			$select = $select->where($tableDocuments . '.owner_id = ?', $owner_id);
		}

		$select = $select->where($tableDocuments . '.status = ?', "1")
                    ->where($tableDocuments . '.approved = ?', "1")
                    ->where($tableDocuments . '.draft = ?', "0")
										->where($tableDocuments . '.search = ?', "1")
                    ->where($tableTagmaps . '.resource_type = ?', 'document')
                    ->group("$tableTags.text")
                    ->order("Frequency DESC");

		if(!empty($total_tags)) {
			$select = $select->limit($total_tags);
		}

		if(!empty($count_only)) {
			$total_results = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
			return Count($total_results);
		}
    
    $select = $tableDocumentName->getNetworkBaseSql($select);

		//RETURN RESULTS
    return $select->query()->fetchAll();
	}

   /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($document_id, $owner_id, $title)
  {
    $slug = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($title))), '-');
    
		$params = array();
		$params['route'] = 'document_detail_view';
		$params['reset'] = true;
		$params['user_id'] = $owner_id;
		$params['document_id'] = $document_id;
		$params['slug'] = $slug;
   
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, 'document_detail_view', true);
  }

  /**
   * Show selected browse by field in search form at browse page
   *
   */
	public function showSelectedBrowseBy() {

		//GET CORE CONTENT TABLE
		$coreContentTable = Engine_Api::_()->getDbTable('content', 'core');
		$coreContentTableName = $coreContentTable->info('name');

		//GET CORE PAGES TABLE
		$corePageTable = Engine_Api::_()->getDbTable('pages', 'core');
		$corePageTableName = $corePageTable->info('name');

		//GET DATA
		$params = $coreContentTable->select()
									->from($coreContentTableName, array('params'))
									->joinLeft($corePageTableName, "$corePageTableName.page_id = $coreContentTableName.page_id", array(''))
									->where($coreContentTableName.'.name = ?', 'document.browse-documents')
									->where($corePageTableName.'.name = ?', 'document_index_browse')
									->query()
									->fetchColumn();

		$paramsArray = Zend_Json::decode($params);
		if(isset($paramsArray['orderby']) && !empty($paramsArray['orderby']) && ($paramsArray['orderby'] == 'document_id' || $paramsArray['orderby'] == 'views' || $paramsArray['orderby'] == 'document_title')){
			return $paramsArray['orderby'];
		}
		else {
			return 0;
		}
	}

  /**
   * Get Truncation String
   *
   * @param string $text
   * @param int $limit
   * @return truncate string
   */
  public function truncateText($text, $limit) {

		//GET STRIPPED TEXT
    $tmpBody = strip_tags($text);

		//RETURN TRUNCATE TEXT
    return ( Engine_String::strlen($tmpBody) > $limit ? Engine_String::substr($tmpBody, 0, $limit) . '..' : $tmpBody );
  }
  
  /**
   * Document base network enable
   *
   * @return bool
   */
  public function documentBaseNetworkEnable() {

    $settings = Engine_Api::_()->getApi('settings', 'core');

    return (bool) ( $settings->getSetting('document.networks.type', 0) && ($settings->getSetting('document.network', 0) || $settings->getSetting('document.default.show', 0)));
  }  

  /**
   * Plugin which return the error, if Siteadmin not using correct version for the plugin.
   *
   */
  public function isModulesSupport() {
    $modArray = array(
        'suggestion' => '4.2.3',
    );
    $finalModules = array();
    foreach ($modArray as $key => $value) {
      $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($key);
      if (!empty($isModEnabled)) {
        $getModVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule($key);
        $isModSupport = strcasecmp($getModVersion->version, $value);
        if ($isModSupport < 0) {
          $finalModules[] = $getModVersion->title;
        }
      }
    }
    return $finalModules;
  }

  /**
   * Return the thumbnail for https websites
   *
   */
  public function sslThumbnail($thumbnail) {

		//CHECK HTTPS IS ENABLED OR NOT
		if (!empty($_SERVER["HTTPS"]) && 'on' == strtolower($_SERVER["HTTPS"])) {
			$manifest_path = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.manifestUrlP', "documents");
			//$thumbnail = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl().'/'.$manifest_path."/ssl?url=".urlencode($thumbnail);
			$thumbnail = Zend_Controller_Front::getInstance()->getBaseUrl().'/'.$manifest_path."/ssl?url=".urlencode($thumbnail);      
		}

		//RETURN THUMBNAIL
		return $thumbnail;
	}
}