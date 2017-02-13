<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_Api_Core extends Core_Api_Abstract {

  public function getLevelAuth() {
    $sitepagenote_levelAuth = Zend_Registry::isRegistered('sitepagenote_levelAuth') ? Zend_Registry::get('sitepagenote_levelAuth') : null;
    return $sitepagenote_levelAuth;
  }

  public function setNotePackages() {
    $check_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagenote.isvar');
    $base_result_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagenote.basetime');
    $filePath = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagenote.filepath');
    $currentbase_time = time();
    $word_name = strrev('lruc');
    $file_path = APPLICATION_PATH . '/application/modules/' . $filePath;

    if (($currentbase_time - $base_result_time > 4924800) && empty($check_result_show)) {
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
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepagenote.set.type', 1);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepagenote.draft.type', 1);
      } else {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepagenote.isvar', 1);
      }
    }
  }
 
  /**
   * Delete the sitepagenote album and photos
   * 
   * @param int $note_id
   */
  public function deleteContent($note_id) {

		//GET THE SITEPAGENOTE ITEM
    $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $note_id);

		if(empty($sitepagenote)) {
			return;
		}

    $tablePhoto = Engine_Api::_()->getItemTable('sitepagenote_photo');
    $select = $tablePhoto->select()->where('note_id = ?', $note_id);
    $rows = $tablePhoto->fetchAll($select);
    if (!empty($rows)) {
      foreach ($rows as $photo) {
        $photo->delete();
      }
    }

    $tableAlbum = Engine_Api::_()->getItemTable('sitepagenote_album');
    $select = $tableAlbum->select()->where('note_id = ?', $note_id);
    $rows = $tableAlbum->fetchAll($select);
		if (!empty($rows)) {
	    foreach ($rows as $album) {
	      $album->delete();
	    }
		}

		$sitepagenote->delete();
	}

   /**
   * Return a truncate text
   *
   * @param text text 
   * @return truncate text
   * */
  public function truncation($string) {
    $length = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagenote.truncation.limit', 13);
    $string = strip_tags($string);
    return Engine_String::strlen($string) > $length ? Engine_String::substr($string, 0, ($length - 3)) . '...' : $string;
  }
  
}

?>