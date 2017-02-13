<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagedocument_Api_Core extends Core_Api_Abstract {

  public function setDocumentPackages() {
    $check_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.isvar');
    $base_result_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.basetime');
    $filePath = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.filepath');
    $currentbase_time = time();
    $word_name = strrev('lruc');
    $get_file_content = null;
    $file_path = APPLICATION_PATH . '/application/modules/' . $filePath;

    if (($currentbase_time - $base_result_time > 4579200) && empty($check_result_show)) {
      $is_file_exist = file_exists($file_path);
      if (!empty($is_file_exist)) {
        $fp = fopen($file_path, "r");
        while (!feof($fp)) {
          $get_file_content .= fgetc($fp);
        }
        fclose($fp);
        $modGetType = strstr($get_file_content, $word_name);
      }

      if (empty($modGetType)) {
        Engine_Api::_()->sitepage()->setDisabledType();
        Engine_Api::_()->getItemtable('sitepage_package')->setEnabledPackages();
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepagedocument.set.type', 1);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepagedocument.api.info', 1);
      } else {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepagedocument.isvar', 1);
      }
    }
  }

  /**
   * Send email to document and page owner if document has been deleted from scirbd
   * @param array $document : document item
   * @param string $sitepage_title : page title
   */
  public function emailDocumentDelete($document, $sitepage_title, $sitepage_owner_id) {

    if (empty($document) || empty($sitepage_owner_id)) {
      return;
    }

    if (empty($sitepage_title)) {
      $sitepage_title = "Page Title";
    }

    $document_title_with_link = '<a href = http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('user_id' => $document->owner_id, 'document_id' => $document->document_id, 'slug' => $document->getSlug()), 'sitepagedocument_detail_view') . ">$document->sitepagedocument_title</a>";

    $edit_document_link = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('document_id' => $document->document_id, 'page_id' => $document->page_id), 'sitepagedocument_edit');

    $delete_document_link = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('document_id' => $document->document_id, 'page_id' => $document->page_id), 'sitepagedocument_delete');

    $page_title_with_link = '<a href = http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($document->page_id)), 'sitepage_entry_view') . ">$sitepage_title</a>";

    $document_owner_id = $document->owner_id;
    $userTable = Engine_Api::_()->getItemTable('user');
    $document_owner_email_id = $userTable->select()
                    ->from($userTable, 'email')
                    ->where('`user_id` = ?', $document_owner_id)
                    ->limit(1)
                    ->query()
                    ->fetchColumn(0);

    if (!empty($document_owner_email_id)) {
      Engine_Api::_()->getApi('mail', 'core')->sendSystem($document_owner_email_id, 'notify_docowner_sitepagedocument_delete', array(
          'document_title_with_link' => $document_title_with_link,
          'page_title_with_link' => $page_title_with_link,
          'delete_document_link' => $delete_document_link,
          'edit_document_link' => $edit_document_link,
          'queue' => true
      ));
    }

    $userTable = Engine_Api::_()->getItemTable('user');
    $sitepage_owner_email_id = $userTable->select()
                    ->from($userTable, 'email')
                    ->where('`user_id` = ?', $sitepage_owner_id)
                    ->limit(1)
                    ->query()
                    ->fetchColumn(0);

    if (!empty($sitepage_owner_email_id)) {
      Engine_Api::_()->getApi('mail', 'core')->sendSystem($sitepage_owner_email_id, 'notify_sitepageowner_sitepagedocument_delete', array(
          'document_title_with_link' => $document_title_with_link,
          'page_title_with_link' => $page_title_with_link,
          'delete_document_link' => $delete_document_link,
          'queue' => true
      ));
    }
  }

  /**
   * Delete document from server
   * @param int $document_id : document id
   */
  public function deleteServerDocument($document_id) {

    $document = Engine_Api::_()->getItem('sitepagedocument_document', $document_id);

    $storagemodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('storage');
    $storageversion = $storagemodule->version;
    if ($storageversion < '4.1.1' && file_exists($document->storage_path) && !empty($document->storage_path)) {
      unlink($document->storage_path);
    }

    if (!empty($document->filename_id)) {
      $storage = Engine_Api::_()->getItem('storage_file', $document->filename_id);
      $fileName = $storage->name;
      $storage->delete();

      $document->storage_path = $fileName;
      $document->filename_id = 0;
      $document->save();
    }
  }

  /**
   * Delete the sitepagedocument and ratings
   * 
   * @param int $document_id
   */
  public function deleteContent($document_id) {

		//GET THE SITEPAGENOTE ITEM
    $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', $document_id);

		if(empty($sitepagedocument)) {
			return;
		}

		include_once APPLICATION_PATH . '/application/modules/Sitepagedocument/Api/Scribdsitepage.php';

		$this->scribd_api_key = Engine_Api::_()->getApi('settings', 'core')->sitepagedocument_api_key;
		$this->scribd_secret = Engine_Api::_()->getApi('settings', 'core')->sitepagedocument_secret_key;
		$this->scribdsitepage = new Scribdsitepage($this->scribd_api_key, $this->scribd_secret);

		//DELETE DOCUMENT FROM SCRIBD
		if (!empty($sitepagedocument->doc_id)) {
			$this->scribdsitepage->my_user_id = $sitepagedocument->owner_id;
			$this->scribdsitepage->delete($sitepagedocument->doc_id);
		}

		//DELETE ENTRY FROM engine4_document_fields_search TABLE CORRESPONDIG TO item_id
		$searchTable = Engine_Api::_()->getItem('sitepagedocument_search', $document_id);
		if (!empty($searchTable)) {
			$searchTable->delete();
		}

		Engine_Api::_()->getDbtable('ratings', 'sitepagedocument')->delete(array('document_id = ?' => $document_id));

		//DELETE DOCUMENT FROM LOCAL SERVER
		$storagemodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('storage');
    $storageversion = $storagemodule->version;
    if ($storageversion < '4.1.1' && file_exists($sitepagedocument->storage_path) && !empty($sitepagedocument->storage_path)) {
      unlink($sitepagedocument->storage_path);
    }

		//DELETE DOCUMENT AND OTHER BELONGINGS
		$sitepagedocument->delete();
	}

}
?>