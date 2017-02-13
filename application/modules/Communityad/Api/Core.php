<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Api_Core extends Core_Api_Abstract {

// GET FIELDS STARUCTURE OF PROFILE FIELDS
  public function getFieldsStructureSearch($spec, $parent_field_id = null, $parent_option_id = null, $showGlobal = true) {
    $type = Engine_Api::_()->getApi('core', 'fields')->getFieldType($spec);
    $structure = array();

    foreach (Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps($type)->getRowsMatching('field_id', (int) $parent_field_id) as $map) {
// Skip maps that don't match parent_option_id (if provided)
      if (null !== $parent_option_id && $map->option_id != $parent_option_id) {
        continue;
      }
// Get child field
      $field = Engine_Api::_()->getApi('core', 'fields')->getFieldsMeta($type)->getRowMatching('field_id', $map->child_id);
      if (empty($field)) {
        continue;
      }
      $structure[$map->getKey()] = $map;

// Get children
      if ($field->canHaveDependents()) {
        $structure += $this->getFieldsStructureSearch($spec, $map->child_id, null, $showGlobal);
      }
    }

    return $structure;
  }

// GET USER PROFILE_ID
  public function getUserProfileId($viewer_id = null) {
    if (empty($viewer_id)) {
      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    }
    return Engine_Api::_()->getDbtable('metas', 'communityad')->getUserProfileId($viewer_id);
  }

//SET ENABLE TARGET FIELDS
  public function preFieldPkgTargetData() {

    $targetFields = Engine_Api::_()->getItemTable('target')->getFields();
    $targetFields = $targetFields->toarray();
    $checkElementName = array();
    foreach ($targetFields as $targetField) {
      $checkElementName[$targetField['mp_id'] . 'check' . $targetField['field_id']] = 1;
    }

    return $checkElementName;
  }

  /**
   * CREATE NEW AND EDIT ADVERTISEMENT
   * @param int $param : Parameter of ad
   * @return  save ad information
   */
  public function saveUserAd($param) {
// ADVERTISIMENT TABLE
    $adTable = Engine_Api::_()->getItemTable('userads');
// PACKAGE TABLE
    $package = Engine_Api::_()->getItem('package', $param['package_id']);
    $adCampaignTable = Engine_Api::_()->getItemTable('adcampaign');
    $public = 0;
    if (isset($param['public'])) {
      $public = $param['public'];
    }
    if (empty($param['campaign_id'])) {
      $adCampaignRow = $adCampaignTable->createRow();
      $adCampaignData = array(
          'name' => $param['campaign_name'],
          'owner_id' => $param['owner_id'],
      );
      $adCampaignRow->setFromArray($adCampaignData);
      $adCampaignRow->save();
      $adCampaignId = $adCampaignRow['adcampaign_id'];
    } else {
      $adCampaignId = $param['campaign_id'];
    }
    $viewer = Engine_Api::_()->user()->getViewer();

    $oldTz = date_default_timezone_get();
    date_default_timezone_set($viewer->timezone);
    $start_date = strtotime($param['cads_start_date']);
    date_default_timezone_set($oldTz);
    $param['cads_start_date'] = date('Y-m-d H:i:s', $start_date);
// End date unset
    if (!empty($param['enable_end_date'])) {
      unset($param['cads_end_date']);
    } else {
      $oldTz = date_default_timezone_get();
      date_default_timezone_set($viewer->timezone);
      $end_date = strtotime($param['cads_end_date']);
      date_default_timezone_set($oldTz);
      $param['cads_end_date'] = date('Y-m-d H:i:s', $end_date);
    }


    if (isset($param['userad_id']) && !empty($param['userad_id'])) {
      $createFlage = false;
      $adRow = Engine_Api::_()->getItem('userads', $param['userad_id']);
      if (!empty($param['enable_end_date'])) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = "UPDATE  `engine4_communityad_userads` SET  `cads_end_date` = NULL WHERE  `engine4_communityad_userads`.`userad_id` =" . $param['userad_id'];
        $db->query($sql);
      }
    } else {
      $createFlage = true;
      $adRow = $adTable->createRow();
      $param['create_date'] = date('Y-m-d H:i:s');
    }

    $adRow->setFromArray($param);
    $adRow->package_id = $package['package_id'];
    $adRow->campaign_id = $adCampaignId;
    if (isset($param['name']))
      $adRow->cads_title = $param['name'];

    $adRow->save();


    $pathName = $param['photoPath'];
// Uploaded image save into storage table
    if (!empty($pathName) && @is_file($pathName)) {
      $storage = Engine_Api::_()->storage();
      $paramsPhtoo = array(
          'parent_type' => 'communityad',
          'parent_id' => $adRow->getIdentity()
      );
      $storage = Engine_Api::_()->storage();
      @chmod($pathName, 0777);
      $adsPhoto = $storage->create($pathName, $paramsPhtoo);
      $adRow->photo_id = $adsPhoto->getIdentity();
      if (isset($adRow->photo_url))
        $adRow->photo_url = NULL;
      @unlink($pathName);
    }

    if (empty($pathName) && !empty($param['photo_id_filepath']) && !empty($param['like'])) {

      $storage = Engine_Api::_()->storage();
      $paramsPhtoo = array(
          'parent_type' => 'communityad',
          'parent_id' => $adRow->getIdentity()
      );
      $storage = Engine_Api::_()->storage();
      @chmod($pathName, 0777);

      $getCDN = Engine_Api::_()->seaocore()->isCdn();

      if (!empty($getCDN) || ($param['resource_type'] == 'document')) {
        $file = $param['photo_id_filepath'];
      } else {
        $file = APPLICATION_PATH . '/' . $param['photo_id_filepath'];

        if (strstr($file, "//")) {
          if (strstr($param['photo_id_filepath'], "/public")) {
            $tempExplode = explode("public", $param['photo_id_filepath']);
            $tempPath = trim($tempExplode[1], "/");
            $uploaded_image_path = "public/" . $tempPath;
          }
          $param['photo_id_filepath'] = $uploaded_image_path;
          $file = APPLICATION_PATH . '/' . $uploaded_image_path;
        }
      }

//MINIMUM HIGHT AND WIDTH OF CREATE IMAGE
      $min = 60;
// SET WIDTH AND HIGHT OF IMAGE
      $maxW = $createWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.image.width', 120);
      $maxH = $createHight = Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.image.hight', 90);

// Recreate image
      $image = Engine_Image::factory();
      $image->open($file);
//IMAGE WIDTH
      $dstW = $image->width;
// IMAGE HIGHT
      $dstH = $image->height;

// SET THE IMAGE AND WIDTH BASE ON IMAGE
      $multiplier = min($maxW / $dstW, $maxH / $dstH);
      if ($multiplier > 1) {
        $dstH *= $multiplier;
        $dstW *= $multiplier;
      }

      if (($delta = $maxW / $dstW) < 1) {
        $dstH = round($dstH * $delta);
        $dstW = round($dstW * $delta);
      }
      if (($delta = $maxH / $dstH) < 1) {
        $dstH = round($dstH * $delta);
        $dstW = round($dstW * $delta);
      }

      $createHight = $dstH;
      $createWidth = $dstW;

      if ($createWidth < $min)
        $createWidth = $min;

      if ($createHight < $min)
        $createHight = $min;

      $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/communityad/temporary';
      $file1 = str_replace('/', '_', $param['photo_id_filepath']);

// Image Resize
      $image = Engine_Image::factory();

      if (!empty($getCDN) || ($param['resource_type'] == 'document')) {
        $image->open($file)
                ->resize($createWidth, $createHight)
                ->write($path . '/' . $file1);
        $photoName = $path . '/' . $file1;
      } else {
        $image->open($file)
                ->resize($createWidth, $createHight)
                ->write($param['photo_id_filepath']);
        $photoName = $param['photo_id_filepath'];
      }
      $adsPhoto = $storage->create($photoName, $paramsPhtoo); // Save the image in storage files.
      $path .=$file1;
      if (is_file($path)) {
        @chmod($path, 0777);
        @unlink($path);
      }
      $adRow->photo_id = $adsPhoto->getIdentity();
    }

    $adRow->save();

    if ($createFlage) {
      if (!empty($param['approved'])) {
// SEND ACTIVE MAIL HERE
        Engine_Api::_()->communityad()->sendMail("ACTIVE", $adRow->userad_id);
      } else {
// SEND APPROVAL_PENDING MAIL HERE
        Engine_Api::_()->communityad()->sendMail("APPROVAL_PENDING", $adRow->userad_id);
      }
    }

    if (empty($adRow->approved)) {
      Engine_Api::_()->communityad()->sendAdminMail("DISAPPROVED_NOTIFICATION", $adRow->userad_id);
    }

    return $adRow;
  }

  /**
   * Check availablity in like table of selected content.
   * @param int $adId : advertisment id.
   * @return  int or array()
   */
  public function check_availability($adId) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $sub_status_table = Engine_Api::_()->getItemTable('communityad_like');
    $sub_status_name = $sub_status_table->info('name');
    $sub_status_select = $sub_status_table->select()
            ->from($sub_status_name, array('like_id'))
            ->where('poster_id = ?', $viewer->getIdentity())
            ->where('ad_id = ?', $adId)
            ->limit(1);
    $fetchRecord = $sub_status_select->query()->fetchAll();
    return $fetchRecord;
  }

  /**
   * Turncat the string
   * @param int $string : string which turncate
   * @param int $length : length of string after it turncate default 16
   * @return  turncate string
   */
  public function truncation($string, $length = null) {
    if (empty($length)) {
      $length = 16;
    }
    $string = strip_tags($string);
    return Engine_String::strlen($string) > $length ? Engine_String::substr($string, 0, ($length - 3)) . '...' : $string;
  }

  /**
   * @param string $resource_type : resource type.
   * @return  string [ subtitle ].
   */
  public function viewType($resource_type, $call_from = 0) {
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $module_info = Engine_Api::_()->getDbTable('modules', 'communityad')->getModuleInfo($resource_type);
    if (!empty($module_info)) {
      $module_title = $module_info['module_title'];
      switch ($resource_type) {
        case 'music':
          $sub_title = $view->translate("Listen to %s", $module_title);
          break;
        case 'poll':
          $sub_title = $view->translate('Vote on %s', $module_title);
          break;

        default :
          // Finding the 'Title' from database for other modules.
          $module_title = ucfirst($module_title);
          $getView = $view->translate('View');
          $title = $getView . ' ' . $module_title;
          $sub_title = $title;
          break;
      }
      $sub_title = $view->translate($sub_title);
      return $sub_title;
    }
    return null;
  }

  /**
   * This function calling from 'widgets' where this function return the 'Dynamic link' of the content and 'Sub title'
   * @param string $resourceType : content type.
   * @param int $resourceId : content id.
   * @return  string [ subtitle ].
   */
  public function resourceUrl($resourceType, $resourceId) {
    global $communityad_resource_type;

    $fun_call_flag = 0;
    $viewer = Engine_Api::_()->user()->getViewer();
    if (strstr($resourceType, "sitepage_page")) {
      $resourceType = "sitepage";
    }
    $module_info = Engine_Api::_()->getDbTable('modules', 'communityad')->getModuleInfo($resourceType);
    $is_module_enabled = $this->isModuleEnabled($module_info['module_name']);
    if (empty($is_module_enabled)) {
      return;
    }

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    if (strstr($resourceType, "sitereview")) {
      $resource_type = $resourceType;
    } else {
      $resource_type = $module_info['module_name'];
    }
    $resource_item = $module_info['table_name'];
    $resourceObject = Engine_Api::_()->getItem($resource_item, $resourceId);
    if (empty($resourceObject)) {
      return;
    }
    $is_like = Engine_Api::_()->getDbtable('likes', 'core')->isLike($resourceObject, $viewer);
    if (( $resourceType == 'group' ) || ( $resourceType == 'event' )) {
      $logdden_user_identity = $viewer->getIdentity();
      if (!$resourceObject->membership()->isMember($viewer, null) && !empty($logdden_user_identity)) {
        if ($resourceType == 'group') {
          $module_title = $view->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'join', 'group_id' => $resourceObject->getIdentity()), $view->translate('Join Group'), array('class' => 'smoothbox'));
        } else if ($resourceType == 'event') {
          $module_title = $view->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'join', 'event_id' => $resourceObject->getIdentity()), $view->translate('Join Event'), array('class' => 'smoothbox'));
        }
      } else {
        $fun_call_flag = 1;
      }
    } else {
      $fun_call_flag = 1;
    }

    if (!empty($fun_call_flag)) {
      $module_title = $this->viewType($resource_type, 1);
    }

    $resourceArray['link'] = $resourceObject->getHref();
    $resourceArray['title'] = $module_title;
    $resourceArray['status'] = $fun_call_flag;
    $resourceArray['like'] = $is_like;

    return $resourceArray;
  }

  /**
   * When view the advertisment on the site.
   * @param int $adId : advertisment id.
   * @return  int
   */
  public function adViewCount($adId) {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $userad = Engine_Api::_()->getItem('userads', $adId);
    $ad_ownerid = $userad->owner_id;
    if ($ad_ownerid == $viewer_id) {
      return;
    }
    if (preg_match('/bot|crawl|spider/i', $_SERVER['HTTP_USER_AGENT'])) {
      return false;
    }

    $date = new Zend_Date(time());
    $date->setTimezone(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));
    $current_date = gmdate('Y-m-d', $date->getTimestamp());
    $displayed_ad_url = '';
    global $communityad_view_count;

    $campaignId = $userad->campaign_id;
    $ip_address = $this->getRealIpAddr();

    if (!empty($userad->resource_type) && !empty($userad->resource_id)) {
      $resource_url = Engine_Api::_()->communityad()->resourceUrl($userad->resource_type, $userad->resource_id);
      if (!empty($resource_url)) {
        $displayed_ad_url = $resource_url['link'];
      }
    } else {
      $displayed_ad_url = $userad->cads_url;
    }
    $sub_status_table = Engine_Api::_()->getDbTable('adstatistics', 'communityad');
    $sub_status_name = $sub_status_table->info('name');
    $adstatisticscache_table = Engine_Api::_()->getDbTable('adstatisticscache', 'communityad');

    $fetchView = $adstatisticscache_table->getStatisticsCache(array('userad_id' => $adId, 'viewer_id' => $viewer_id, 'response_date' => $current_date));
// Condition: In the current date if user view ad again then update the row else create a new row.
    if (empty($fetchView)) {
      $widgetDisplay = $sub_status_table->createRow();
      $widgetDisplay->userad_id = $adId;
      $widgetDisplay->adcampaign_id = $campaignId;
      $widgetDisplay->viewer_id = $viewer_id;
      $widgetDisplay->hostname = $ip_address;
      $widgetDisplay->user_agent = $_SERVER['HTTP_USER_AGENT'];
      $widgetDisplay->url = $displayed_ad_url;
      $widgetDisplay->response_date = gmdate('Y-m-d H:i:s', $date->getTimestamp());
      $widgetDisplay->value_click = 0;
      $widgetDisplay->value_view = 1;
      $widgetDisplay->value_like = 0;
      $widgetDisplay->save();
      $adstatisticscache_table->setStatisticsCache($widgetDisplay->toArray());
    } else {
      $sub_status_table->update(array('value_view' => $fetchView[0]['value_view'] + 1), array('adstatistic_id =?' => $fetchView[0]['adstatistic_id']));
      $adstatisticscache_table->updateStatisticsCache(array('value_view' => $fetchView[0]['value_view'] + 1), array('adstatistic_id =?' => $fetchView[0]['adstatistic_id']));
    }
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
//$userad = Engine_Api::_()->getItem('userads', $adId);
      $userad->count_view = $userad->count_view + 1;
      if ($userad->limit_view > 0) {
        $userad->limit_view = $userad->limit_view - 1;
      }
      if ($userad->limit_view == 0 && $userad->price_model == 'Pay/view') {
        if ($userad->payment_status == 'active')
          $userad->payment_status = "expired";
        if ($userad->status <= 2) {
          $userad->approved = 0;
          $userad->enable = 0;
          $userad->status = 3;
          Engine_Api::_()->communityad()->sendMail("EXPIRED", $userad->userad_id);
        }
      }
      $userad->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  /**
   * Campaign list of user
   * @param int $owner_id : owner id  whose campaign list
   * @return  Campagin list
   */
  public function getUserCampaigns($owner_id = null) {
    if (empty($owner_id)) {
      $owner_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    }
    return Engine_Api::_()->getItemTable('adcampaign')->getUserCampaigns($owner_id);
  }

  /**
   * List of cancel ad by user
   * @param int $viewer_id : viewr id who view
   * @return  list
   */
  public function getAdCancelIds($viewer_id = null, $ad_type = 'default') {

    if (empty($viewer_id)) {
      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    }
    $communityad_table = Engine_Api::_()->getItemTable('userads');
    $communityad_table_name = $communityad_table->info('name');
    $communityadAdcancelTable = Engine_Api::_()->getItemTable('communityad_adcancel');
    $communityadAdcancelName = $communityadAdcancelTable->info('name');
    $communityadAdcancelSelect = $communityadAdcancelTable->select()
            ->setIntegrityCheck(false)
            ->from($communityadAdcancelName, array('ad_id'))
            ->join($communityad_table_name, $communityad_table_name . '.userad_id =' . $communityadAdcancelName . '.ad_id', array())
            ->where('user_id = ?', $viewer_id)
            ->where('ad_type = ?', $ad_type);
    return $communityadAdcancel_ids = $communityadAdcancelTable->fetchAll($communityadAdcancelSelect);
  }

  /**
   * Functions will add the targetting in this SQL.
   * @param $communityad_select : Object of SQL querys.
   * @return  Object
   */
  public function getTargettingSQL($communityad_select) {
    $communityad_table = Engine_Api::_()->getItemTable('userads');
    $communityad_table_name = $communityad_table->info('name');
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    if (empty($viewer_id)) {
      $communityad_select->where($communityad_table_name . '.public = 1');
      $optionsProfile = Engine_Api::_()->getDBTable('options', 'communityad')->getAllProfileTypes();
      $count_profile = @count($optionsProfile);
      if ($count_profile > 1) {
        $communityad_select->where($communityad_table_name . '.profile = 0');
      }
    } else {
      $profile_id = $this->getUserProfileId($viewer_id);
      if (!empty($profile_id)) {
        $communityad_select->where($communityad_table_name . '.profile = 0 or ' . $communityad_table_name . '.profile = ?', $profile_id);
      }
    }

    $removeSubject = null;
    if (!Engine_Api::_()->core()->hasSubject()) {
      Engine_Api::_()->core()->setSubject(Engine_Api::_()->user()->getViewer());
    } else {
      $removeSubject = Engine_Api::_()->core()->getSubject();
      Engine_Api::_()->core()->clearSubject();
      Engine_Api::_()->core()->setSubject(Engine_Api::_()->user()->getViewer());
    }
    $subject = Engine_Api::_()->core()->getSubject();
    $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($subject);

    // Values
    $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($subject);

    $userValue = array();
    $userBirthDate = '';
    $gender_vale = null;
    foreach ($fieldStructure as $map) {
      // Get field meta object
      $field = $map->getChild();
      $value = $field->getValue($subject);

      if ($field->type == 'gender') {
        // Get params
        $values = $field->getElementParams('user', array('required' => false));
        if (!empty($value->value) && isset($values['options']['multiOptions'][$value->value]))
          $gender_vale = $values['options']['multiOptions'][$value->value];
      }
      if ($field->type == "date" || $field->type == "int" || $field->type == "float")
        continue;
      if (!empty($field->alias)) {
        $userValue[$field->field_id]['alias'] = $field->alias;
      } else {
        $userValue[$field->field_id]['alias'] = sprintf('field_%d', $field->field_id);
      }

      $userValue[$field->field_id]['type'] = $field->type;
      if (!empty($value->value)) {
        if ($field->type != 'gender') {
          $userValue[$field->field_id]['value'] = $value->value;
        } else {
          $userValue[$field->field_id]['value'] = $gender_vale;
        }
      }

      if (in_array($field->type, array("partner_gender", "looking_for", 'ethnicity', 'multiselect', 'multi_checkbox', 'radio'))) {
        $vals = array();
        if (!empty($value) && is_array($value)) {
          foreach ($value as $singleValue) {
            if (is_string($singleValue)) {
              $vals[] = $singleValue;
            } else if (is_object($singleValue)) {
              $vals[] = $singleValue->value;
            }
          }
        }
        if (!empty($vals))
          $userValue[$field->field_id]['value'] = $vals;
      } elseif ($field->type == "interests") {
        if (!empty($value->value)) {
          $interestsValue = explode(",", $value->value);
          $interestsValue1 = explode(", ", $value->value);
          $interestsValue = array_merge($interestsValue, $interestsValue1);
        } else {
          $interestsValue = null;
        }
        $userValue[$field->field_id]['value'] = $interestsValue;
      }

      if ($field->type == 'birthdate') {
        if (!empty($value->value))
          $userBirthDate = $value->value;
      }
    }
    $targetFields = Engine_Api::_()->getItemTable('target')->getFields();
    $adtargetsName = Engine_Api::_()->getDbtable('adtargets', 'communityad')->info('name');

    $targetFieldIds = array();
    $targetMapIds = array();
    foreach ($targetFields as $targetField) {
      $targetFieldIds[] = $targetField->field_id;
    }

    $communityad_select->joinLeft($adtargetsName, $adtargetsName . '.userad_id =' . $communityad_table_name . '.userad_id', array());


    $birthday_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('target.birthday', 0);
    if (!empty($birthday_enable)) {
      if (!empty($userBirthDate)) {
        $dayMonth = date('m-d', strtotime($userBirthDate));
        $currentDayMonth = date('m-d');
        if ($currentDayMonth !== $dayMonth) {
          $communityad_select->where($adtargetsName . '.birthday_enable = ?', 0);
        }
      } else {
        $communityad_select->where($adtargetsName . '.birthday_enable = ?', 0);
      }
      $communityad_select->order($adtargetsName . '.birthday_enable DESC');
    }

    $targetColumns = $this->getTargetColumns();
    unset($targetColumns['0']);
    unset($targetColumns['1']);
    unset($targetColumns['2']);

    $db = Engine_Db_Table::getDefaultAdapter();
    $export = Engine_Db_Export::factory($db);
    $connection = $export->getAdapter()->getConnection();

    $network_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('community.target.network', 0);

    if (!empty($network_enable) && Engine_Api::_()->communityad()->hasNetworkOnSite()) {
      $viewerNetwork = Engine_Api::_()->getDbtable('membership', 'network')->getMembershipsOfInfo($viewer);
      if (!empty($viewerNetwork)) {
        $str = array();
        foreach ($viewerNetwork as $networkvalue) {
          $network_id = $networkvalue->resource_id;
          $str[] = $start_one = "'" . $network_id . "'";
          $str[] = $start = "'" . $network_id . ",%'";
          $str[] = $middile = "'%," . $network_id . ",%'";
          $str[] = $end = "'%," . $network_id . "'";
        }
        $columnName = $adtargetsName . '.' . 'networks';
        if (!empty($str)) {
          $likeVale = (string) ( join(" or $columnName  LIKE ", $str) );
          $communityad_select->where($columnName . ' LIKE ' . $likeVale . ' or ' . $columnName . " IS NULL");
        } else {
          $communityad_select->where($columnName . " IS NULL");
        }
      }
      $keysUnset = array_search('networks', $targetColumns);
      unset($targetColumns[$keysUnset]);
    }



    $targetColumns['0'] = 'birthdate';

    foreach ($userValue as $field_id => $value) {

      if (in_array($value['alias'], $targetColumns) && !empty($value['value'])) {

        $keysUnset = @array_search($value['alias'], $targetColumns);
        unset($targetColumns[$keysUnset]);

        $notType = array("birthdate", "gender", "partner_gender", "ethnicity", "looking_for", "interests", "multiselect", "multi_checkbox", "radio");
        if (!in_array($value['type'], $notType)) {
          if (method_exists($connection, 'real_escape_string'))
            $convert_string = $connection->real_escape_string($value['value']);
          else
            $convert_string = str_replace("'", "\'", $value['value']);
          $start_one = "'" . $convert_string . "%'";
          $end_one = "'%" . $convert_string . "'";
          $start = "'" . $convert_string . ",%'";
          $middile = "'%," . $convert_string . ",%'";
          $end = "'%," . $convert_string . "'";
          $columnName = $adtargetsName . '.' . $value['alias'];
          $communityad_select->where('(' . $columnName . ' LIKE ' . $start_one . ' and ' . $columnName . ' LIKE ' . $end_one . ') or ' . $columnName . ' LIKE ' . $start . ' or ' . $columnName . ' LIKE ' . $middile . ' or ' . $columnName . ' LIKE ' . $end . ' or ' . $columnName . " IS NULL");
        } elseif (($value['type'] === 'partner_gender' || $value['type'] === 'looking_for' || $value['type'] === 'ethnicity' || $value['type'] === 'interests' || $value['type'] === 'multiselect' || $value['type'] === 'multi_checkbox' || $value['type'] === 'radio' ) && !empty($value['value'])) {
          $str = array();
          if (is_array($value['value'])) {
            foreach ($value['value'] as $valueAD) {
              if (method_exists($connection, 'real_escape_string'))
                $valueAD = $connection->real_escape_string($valueAD);
              else
                $valueAD = str_replace("'", "\'", $valueAD);

              $str[] = $start_one = "'" . $valueAD . "'";
              $str[] = $start = "'" . $valueAD . ",%'";
              $str[] = $middile = "'%," . $valueAD . ",%'";
              $str[] = $end = "'%," . $valueAD . "'";
            }
          }
          $columnName = $adtargetsName . '.' . $value['alias'];
          if (!empty($str)) {
            $likeVale = (string) ( join(" or $columnName  LIKE ", $str) );
            $communityad_select->where($columnName . ' LIKE ' . $likeVale . ' or ' . $columnName . " IS NULL");
          } else {
            $communityad_select->where($columnName . " IS NULL");
          }
        } elseif ($value['type'] == 'gender') {
          $columnName = $adtargetsName . '.' . $value['alias'];
          $communityad_select->where($columnName . '= ? or ' . $columnName . " IS NULL", $value['value']);
        } elseif ($value['type'] == 'birthdate' && !empty($value['value'])) {

          unset($targetColumns['3']);
          unset($targetColumns['4']);

          $date_array = explode("-", $value['value']);
          $age = date("Y", time()) - $date_array[0];

          if ($date_array[1] > $date_Month = date("m", time())) {
            $age = $age - 1;
          } elseif ($date_array[1] == $date_Month) {
            if ($date_array[2] > date("d", time())) {
              $age = $age - 1;
            }
          }

          $columnName = $adtargetsName . '.age_min';
          $communityad_select->where($columnName . '<= ? or ' . $columnName . " IS NULL", $age);
          $columnName = $adtargetsName . '.age_max';
          $communityad_select->where($columnName . '>= ? or ' . $columnName . " IS NULL", $age);
        }
      }
    }
    unset($targetColumns['0']);
    foreach ($targetColumns as $column) {
      $columnName = $adtargetsName . '.' . $column;
      $communityad_select->where($columnName . " IS NULL");
    }



    if (Engine_Api::_()->core()->hasSubject()) {
      Engine_Api::_()->core()->clearSubject();
      if (!empty($removeSubject))
        Engine_Api::_()->core()->setSubject($removeSubject);
    }
    return $communityad_select;
  }

  /**
   * Functions will add the comman conditions in this SQL.
   * @param $communityad_select : Object of SQL querys.
   * @return  Object
   */
  public function getCommanConditionsForSQL($communityad_select, $ad_type, $communityad_table_name, $totalAd = 0) {
    $limitCancelAD = 100;
    $strAdCancel = null;
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    if ($viewer_id && $totalAd >= 0 && $totalAd <= $limitCancelAD) {
      $communityadAdcancel_ids = $this->getAdCancelIds($viewer_id, $ad_type);
      $adClancelIds = array();
      foreach ($communityadAdcancel_ids as $id)
        $adClancelIds[] = $id->ad_id;

      if (!empty($adClancelIds))
        $communityad_select->where($communityad_table_name . '.userad_id 	 not in (?)', new Zend_Db_Expr((string) ( "'" . join("', '", $adClancelIds) . "'" )));
    }

    $communityad_select
            ->where($communityad_table_name . '.approved = 1 ')
            ->where($communityad_table_name . '.enable = 1 ')
            ->where($communityad_table_name . '.cads_start_date <= ? ', date('Y-m-d H:i:s'))
            ->where($communityad_table_name . '.cads_end_date >= ? or ' . $communityad_table_name . '.cads_end_date Is NULL ', date('Y-m-d H:i:s'))
            ->where($communityad_table_name . '.status <> 4')
            ->where($communityad_table_name . '.status <> 5')
            ->where("case when price_model='Pay/click'  then  limit_click  > 0 or limit_click  =-1 when price_model='Pay/view' then   limit_view  > 0 or limit_view  =-1 when price_model='Pay/period' then  expiry_date > " . date('Y-m-d') . " END")
            ->where("min_ctr  <=  case when count_view <> 0 and  count_click <>0  then  (count_click / count_view)  when count_view <> 0 and  count_click = 0  then (1 / count_view) when count_view = 0 and  count_click = 0 then 1 end")
            ->order($communityad_table_name . '.weight DESC')
            ->order('RAND()');

    return $communityad_select;
  }

  /**
   * List of ad which are display base on all targeting
   * @param array $params : requirments
   * @return  list
   */
  public function getAdvertisement($params = array()) {
    $limit = 25;

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    if (!empty($viewer_id)) {
      $user_level = $viewer->level_id;
    } else {
      $user_level = $this->getPublicUserLevel();
    }

    $can_view = Engine_Api::_()->authorization()->getPermission($user_level, 'communityad', 'view');

    if (!$can_view) {
      return;
    }
    $ad_type = 'default';
    $communityad_table = Engine_Api::_()->getItemTable('userads');
    $totalAd = $communityad_table->fetchAll(array('ad_type' => $ad_type))->count();
    if (empty($totalAd)) {
      return;
    }

    $communityad_table_name = $communityad_table->info('name');

    $communityad_select = $communityad_table->select()
            ->setIntegrityCheck(false)
            ->from($communityad_table_name, array($communityad_table_name . '.*'))
            ->where($communityad_table_name . '.ad_type =?', $ad_type);


    $communityad_select = $this->getCommanConditionsForSQL($communityad_select, $ad_type, $communityad_table_name, $totalAd);

    if (isset($params["packageIds"]) && !empty($params["packageIds"])) {
      $communityad_select->where($communityad_table_name . '.package_id IN(?)', (array) $params["packageIds"]);
    }
    $communityad_select = $this->getTargettingSQL($communityad_select);

    if (!empty($params['featured'])) {
      $communityad_select->where($communityad_table_name . '.featured = ?', 1);
    }

    if (!empty($params['sponsored'])) {
      $communityad_select->where($communityad_table_name . '.sponsored = ?', 1);
    }

    if (!empty($params['lim'])) {
      $communityad_select->limit($params['lim']);
    } else {
      $communityad_select->limit($limit);
    }

    $result = $communityad_table->fetchAll($communityad_select);
    $counter = count($result);
    if (!empty($counter)) {
      return $result;
    } else {
      return null;
    }
  }

  public function getTargetColumns() {
    return Engine_Api::_()->getDbtable('adtargets', 'communityad')->info('cols');
  }

  /**
   * Give Expiry dat base on passing days
   * @param int $days : number of days increment,
   * @return  expiry date
   */
  public function getExpiryDate($days) {
    $date = time();
    if ($days <= 0)
      $days = 0;
    $incrmnt_date = date('d', $date) + ($days);
    return date('Y-m-d', mktime(0, 0, 0, date("m"), $incrmnt_date));
  }

  /**
   * Calling from 'Create & Edit advertisment form' this function return the array of enabled modules
   * @param int $package_id : package id.
   * @return  array
   */
  public function enabled_module_content($package_id) {
    $is_customAd = 0;
    $is_module = 0;
    $adModules = array();
    $getModules_Core = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames(); // Get: Enabled modules from core table.
    $getModules_Communityad = Engine_Api::_()->getDbtable('modules', 'communityad')->getModuleName(); // Get: Module which are selected by admin.
    $enabledModules = array_intersect($getModules_Core, $getModules_Communityad);

    $enabledModules[] = 'website';
    // Get: modules string from package ( Which module allow by package )
    $getDisplayedModule = Engine_Api::_()->getItem('package', $package_id)->urloption;
    if (!empty($getDisplayedModule)) {
      $adValue = explode(',', $getDisplayedModule);
    }

    if (strstr($getDisplayedModule, "sitereview")) {
      foreach ($adValue as $tempModName) {
        if (strstr($tempModName, "sitereview")) {
          $tempSitereviewArray[] = $tempModName;
        }
      }
    }

    $adValue = array_intersect($adValue, $enabledModules);
    if (!empty($tempSitereviewArray)) {
      $adValue = array_merge($adValue, $tempSitereviewArray);
    }
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $adModules[] = $view->translate('-- Select --');
    foreach ($adValue as $value) {
      if (strstr($value, "sitereview")) {
        $is_module = 1;
        $sitereviewExplode = explode("_", $value);
        $getAdsMod = Engine_Api::_()->getItem("communityad_module", $sitereviewExplode[1]);
        $modTemTitle = strtolower($getAdsMod->module_title);
        $modTemTitle = ucfirst($modTemTitle);
        $adModules[$value] = $modTemTitle;
      } else {
        if ($value == 'website') {
          $is_customAd = 1;
        } else {
          $is_module = 1;
          $getInfo = Engine_Api::_()->getDbtable('modules', 'communityad')->getModuleInfo($value);
          if (!empty($getInfo)) {
            if (strstr($value, "ynevent")) {
              $value = 'ynevent_event';
            }
            $adModules[$value] = $getInfo['module_title'];
          }
        }
      }
    }
    $returnValues[0] = $is_customAd; // int: 0 if custom ad is not allow & 1 if custom ad is not allow.
    $returnValues[1] = $adModules; // array: which modules are enabled and availabled in package.
    $returnValues[2] = $is_module; // int: 0 if module ad is not allow & 1 if module ad is not allow.
    return $returnValues;
  }

  /**
   * Calling from 'Create & Edit advertisment form' when select any content then this function return the all item of this content which created by loggden user.
   * @param string $content_type : module type.
   * @param string $calling_from : From where this function is calling.
   * @return  string or array
   */
  public function resource_content($content_type, $story_type, $calling_from = null, $resource_id = null, $getOwner_id = null) {
    if (strstr($content_type, "sitereview")) {
      $sitereviewExplode = explode("_", $content_type);
      $tempAdModId = $sitereviewExplode[1];
      $content_type = $sitereviewExplode[0];
    }

//    $is_module_enabled = $this->isModuleEnabled($content_type);
//    if (empty($is_module_enabled)) {
//      return;
//    }

    if (!empty($tempAdModId)) {
      $module_info = Engine_Api::_()->getItem("communityad_module", $tempAdModId);
      $sitereviewTableName = explode("sitereview_listing_", $module_info->table_name);
      $listingtypeId = $sitereviewTableName[1];
      $content_table = "sitereview_listing";
      $owner_field = $module_info->owner_field;
      $title_field = $module_info->title_field;
    } else {
      $module_info = Engine_Api::_()->getDbtable('modules', 'communityad')->getModuleInfo($content_type);
      $content_table = $module_info['table_name'];
      $owner_field = $module_info['owner_field'];
      $title_field = $module_info['title_field'];
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getItemTable($content_table);

    if ($content_type == 'siteevent') {
      $SiteEventOccuretable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
      $siteeventOccurTableName = $SiteEventOccuretable->info('name');
    }

    if ($calling_from == 'edit') {
      $data = array();
      if (empty($getOwner_id)) {
        $getOwner_id = $viewer->getIdentity();
      }



      if ($content_type == 'siteevent') {
        $select = $table->select();
        $siteeventTableName = $table->info('name');
        $select
                ->setIntegrityCheck(false)
                ->from($siteeventTableName)
                ->join($siteeventOccurTableName, "$siteeventTableName.event_id = $siteeventOccurTableName.event_id", null)
                ->where($siteeventTableName . '.closed = ?', '0')
                ->where($siteeventTableName . '.approved = ?', '1')
                ->where($siteeventTableName . '.draft = ?', '0')
                ->where($siteeventTableName . ".search = ?", 1)
                ->where("$siteeventOccurTableName.endtime > NOW()")
                ->group("$siteeventTableName.event_id")
        ;

        $select = Engine_Api::_()->getDbTable('events', 'siteevent')->getAllEventSql($select, array('user_id' => $viewer->getIdentity(), 'rsvp' => -1, 'viewtype' => 'upcoming'));
      } else {
        $select = $table->select()->where($owner_field . '=?', $getOwner_id);
        $select->order($title_field);
      }

      if (!empty($tempAdModId)) {
        $select = $select->where('listingtype_id =?', $listingtypeId);
      }

      foreach ($table->fetchAll($select) as $content_data) {

        if (!empty($story_type)) {
          // We need this function because we are not using userad table above and we need to check that selected content "Sponcerd Story" is created or not. If sponcerd story already created then we will not show that content in drop down.
//           $isStory = Engine_Api::_()->getItemTable('userads')->isStory($content_table, $content_data->getIdentity(), $story_type);
//           if (($resource_id != $content_data->getIdentity()) && !empty($isStory)) {
//             continue;
//           }

          $content_array['id'] = $content_data->getIdentity();
          $content_array['title'] = $content_data->getTitle();
          $data[] = $content_array;
        } else {
          $data[$content_data->getIdentity()] = $content_data->getTitle();
        }
      }
    } else {// For double dimention array.
      $data = array();


      if ($content_type == 'siteevent') {
        $select = $table->select();
        $siteeventTableName = $table->info('name');
        $select
                ->setIntegrityCheck(false)
                ->from($siteeventTableName)
                ->join($siteeventOccurTableName, "$siteeventTableName.event_id = $siteeventOccurTableName.event_id", null)
                ->where($siteeventTableName . '.closed = ?', '0')
                ->where($siteeventTableName . '.approved = ?', '1')
                ->where($siteeventTableName . '.draft = ?', '0')
                ->where($siteeventTableName . ".search = ?", 1)
                ->where("$siteeventOccurTableName.endtime > NOW()")
                ->group("$siteeventTableName.event_id")
        ;

        $select = Engine_Api::_()->getDbTable('events', 'siteevent')->getAllEventSql($select, array('user_id' => $viewer->getIdentity(), 'rsvp' => -1, 'viewtype' => 'upcoming'));
      } else {
        $select = $table->select()->where($owner_field . '=?', $viewer->getIdentity());
        $select->order($title_field);
      }

      if (!empty($tempAdModId)) {
        $select = $select->where('listingtype_id =?', $listingtypeId);
      }

      foreach ($table->fetchAll($select) as $content_data) {

        if (!empty($story_type)) {
          // We need this function because we are not using userad table above and we need to check that selected content "Sponcerd Story" is created or not. If sponcerd story already created then we will not show that content in drop down.
//           $isStory = Engine_Api::_()->getItemTable('userads')->isStory($content_table, $content_data->getIdentity(), $story_type);
//           if (!empty($isStory)) {
//             continue;
//           }
        }

        $content_array['id'] = $content_data->getIdentity();
        $content_array['title'] = $content_data->getTitle();
        $data[] = $content_array;
      }
    }
    return $data;
  }

// to show the preview of the ad
  public function adpreview($advertismantId) {
    if (!empty($advertismantId)) {
      $communityad_table = Engine_Api::_()->getItemTable('userads');
      $communityad_table_name = $communityad_table->info('name');
      $communityad_select = $communityad_table->select()
              ->setIntegrityCheck(false)
              ->from($communityad_table_name, array('userad_id', 'cads_url', 'cads_title', 'cads_body', 'photo_id', 'like', 'owner_id', 'resource_id', 'resource_type', 'story_type'))
              ->where("userad_id =?", $advertismantId)
              ->limit(1);
      $fetch_community_ads = $communityad_table->fetchRow($communityad_select);
      return $fetch_community_ads;
    }
  }

// SEND ADMIN NOTIFICATION MAIL
  public function sendAdminMail($type, $userad_id) {
    if (empty($type) || empty($userad_id))
      return;
    $userad = Engine_Api::_()->getItem('userads', $userad_id);
    if (!empty($userad)) {
      $package = Engine_Api::_()->getItem('package', $userad->package_id);
      if (!empty($package->auto_aprove) && !empty($userad->approved))
        return;
      $owner = Engine_Api::_()->user()->getUser($userad->owner_id);
      switch ($type) {
        case "DISAPPROVED_NOTIFICATION":
          $mail_template = 'communityad_notify_admindisapproved';
          break;
      }
      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
      $sender_email = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.from', 'email@domain.com');
      Engine_Api::_()->getApi('mail', 'core')->sendSystem($sender_email, $mail_template, array(
          'host' => $_SERVER['HTTP_HOST'],
          'userad_title' => ucfirst($userad->cads_title),
          'userad_description' => ucfirst($userad->cads_body),
          'userad_owner' => $view->htmlLink($userad->getOwner()->getHref(), $userad->getOwner()->getTitle()),
          'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
          Zend_Controller_Front::getInstance()->getRouter()->assemble(array('ad_id' => $userad->userad_id), 'communityad_userad', true),
          'sender_email' => $sender_email,
          'queue' => true
      ));
    }
  }

  /**
   * Send emails for perticuler advertiesment
   * @params $type : which mail send
   * $params $userad_id : Id of advertiesment
   * */
  public function sendMail($type, $userad_id) {
    if (empty($type) || empty($userad_id))
      return;
    $userad = Engine_Api::_()->getItem('userads', $userad_id);
    if (!empty($userad)) {
//   $package = Engine_Api::_()->getItem('package', $userad->package_id);
      $owner = Engine_Api::_()->user()->getUser($userad->owner_id);
      switch ($type) {
        case "APPROVAL_PENDING":
          $mail_template = 'communityad_userad_approval_pending';
          break;
        case "EXPIRED":
          $mail_template = 'communityad_userad_expired';
          break;
        case "OVERDUE":
          $mail_template = 'communityad_userad_overdue';
          break;
        case "CANCELLED":
          $mail_template = 'communityad_userad_cancelled';
          break;
        case "ACTIVE":
          $mail_template = 'communityad_userad_active';
          break;
        case "PENDING":
          $mail_template = 'communityad_userad_pending';
          break;
        case "REFUNDED":
          $mail_template = 'communityad_userad_refunded';
          break;
        case "APPROVED":
          $mail_template = 'communityad_userad_approved';
          break;
        case "DISAPPROVED":
          $mail_template = 'communityad_userad_disapproved';
          break;
        case "DECLINED":
          $mail_template = 'communityad_userad_declined';
          break;
      }
      Engine_Api::_()->getApi('mail', 'core')->sendSystem($owner, $mail_template, array(
          'userad_title' => ucfirst($userad->cads_title),
          'userad_description' => ucfirst($userad->cads_body),
          'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
          Zend_Controller_Front::getInstance()->getRouter()->assemble(array('ad_id' => $userad->userad_id), 'communityad_userad', true),
      ));
    }
  }

// TOTAL SPEND ON  ADVERTIESMENT
  public function paymentSpend($params = array()) {

    $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'payment');
    $transactionsName = $transactionsTable->info('name');
    $ordersName = Engine_Api::_()->getDbtable('orders', 'payment')->info("name");
    $transactionSelect = $transactionsTable->select();

    $transactionSelect
            ->from($transactionsName, array("sum(amount) as totalamount"))
            ->join($ordersName, $ordersName . '.order_id=' . $transactionsName . '.order_id', null);
// FOR PAYMENT SOURCE TYPE
    if (isset($params['source_type']) && !empty($params['source_type'])) {
      $transactionSelect->where($ordersName . '.source_type = ?', $params['source_type'])
              ->group($ordersName . '.source_type');
    }
// FOR PAYMENT PERTICULER SOURCE, FOR IT ALSO REQUIRED TO SOURCE TYPE
    if (isset($params['source_id']) && !empty($params['source_id'])) {
      $transactionSelect->where($ordersName . '.source_id = ?', $params['source_id'])
              ->group($ordersName . '.source_id');
    }

    $result = $transactionsTable->fetchRow($transactionSelect);
    if (!empty($result))
      return $result->totalamount;
    else
      return $totalAmount = 0;
  }

  public function getPublicUserLevel() {
    return Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
  }

// CONVERT THE DECODE STRING INTO ENCODE
  public function getDecodeToEncode($string = null) {
    set_time_limit(0);
    $encodeString = '';

    if (!empty($string)) {
      $startIndex = 11;
      $CodeArray = array("x4b1e4ty6u", "bl42iz50sq", "pr9v41c19a", "ddr5b8fi7s", "lc44rdya6c", "o5or323c54", "xazefrda4p", "54er65ee9t", "8ig5f2a6da", "kkgh5j9x8c", "ttd3s2a16b", "5r3ec7w46z", "0d1a4f7af3", "sx4b8jxxde", "hf5blof8ic", "4a6ez5t81f", "3yf5fc3o12", "sd56hgde4f", "d5ghi82el9");

      $time = time();
      $timeLn = Engine_String::strlen($time);
      $last2DigtTime = substr($time, $timeLn - 2, 2);
      $sI1 = (int) ($last2DigtTime / 10);
      $sI2 = $last2DigtTime % 10;
      $Index = $sI1 + $sI2;

      $codeString = $CodeArray[$Index];
      $startIndex+=$Index % 10;
      $lenght = Engine_String::strlen($string);
      for ($i = 0; $i < $lenght; $i++) {
        $code = uniqid(rand(), true);
        $encodeString.= substr($code, 0, $startIndex);
        $encodeString.=$string{$i};
        $startIndex++;
      }
      $code = uniqid(rand(), true);
      $appendEnd = substr($code, 5, $startIndex);
      $prepandStart = substr($code, 20, 10);
      $encodeString = $prepandStart . $codeString . $encodeString . $appendEnd;
    }

    return $encodeString;
  }

// CONVERT THE ENCODE STRING INTO DECODE
  public function getEncodeToDecode($string) {
    $decodeString = '';

    if (!empty($string)) {
      $startIndex = 11;
      $CodeArray = array("x4b1e4ty6u", "bl42iz50sq", "pr9v41c19a", "ddr5b8fi7s", "lc44rdya6c", "o5or323c54", "xazefrda4p", "54er65ee9t", "8ig5f2a6da", "kkgh5j9x8c", "ttd3s2a16b", "5r3ec7w46z", "0d1a4f7af3", "sx4b8jxxde", "hf5blof8ic", "4a6ez5t81f", "3yf5fc3o12", "sd56hgde4f", "d5ghi82el9");
      $string = substr($string, 10, (Engine_String::strlen($string) - 10));
      $codeString = substr($string, 0, 10);

      $Index = array_search($codeString, $CodeArray);
      $string = substr($string, 10, Engine_String::strlen($string) - 10);
      $startIndex+=$Index % 10;

      $string = substr($string, 0, (Engine_String::strlen($string) - $startIndex));

      $lenght = Engine_String::strlen($string);
      $j = 1;
      for ($i = $startIndex; $i < $lenght;
      ) {
        $j++;
        $decodeString.= $string{$i};
        $i = $i + $startIndex + $j;
      }
    }
    return $decodeString;
  }

  public function likeAdInfo($resourceId, $resourceType, $adId, $is_like) {
    $is_module_enabled = $this->isModuleEnabled($resourceType);
    if (empty($is_module_enabled)) {
      return;
    }
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $friend_like_limit = 1;
    if (empty($is_like)) {
      $friend_like_limit = 2;
    }
    $resource_info = Engine_Api::_()->getDbtable('modules', 'communityad')->getModuleInfo($resourceType);
    if (!empty($resource_info)) {
      $resourceType = $resource_info['table_name'];
    }

    $resource_object = Engine_Api::_()->getItem($resourceType, $resourceId);
    if (empty($resource_object)) {
      return;
    }
    $friend_title_str = '';
    $total_likes = Engine_Api::_()->getDbtable('likes', 'communityad')->getLikeCount($adId);

    $friendLike = $this->user_friend_likes($resourceType, $resourceId, $friend_like_limit, $adId);
    $sub_limit = 0;
    if (!empty($friendLike)) {
      $sub_limit = count($friendLike);

      if (($sub_limit == 1 && $total_likes == 1)) {
        $friend_title_str = $view->translate('%s like this. ', $friendLike[0]);
      } else if (($sub_limit == 2 && $total_likes == 2)) {
        $friend_title_str = $view->translate('%s and %s likes this. ', $friendLike[0], $friendLike[1]);
      } else {
        foreach ($friendLike as $friend_title) {
          $friend_title_str .= ', ' . $friend_title;
        }
        $friend_title_str = $view->translate('%s and ', $friend_title_str);
      }
      $friend_title_str = trim($friend_title_str, ',');

      if (!empty($is_like)) {
        $sub_limit = $sub_limit + 1;
        $total_limit = $total_likes - $sub_limit;
        if ($total_limit != 0) {
          $friend_title_str = $view->translate('You, %s', $friend_title_str);
        } else {
          $friend_title_str = trim($friend_title_str, ' and ');
          $friend_title_str = $view->translate('You and %s likes this.', $friend_title_str);
        }
      }
    } else {
      if (!empty($is_like)) {
        $sub_limit = $sub_limit + 1;
      }
    }
    $other_like = $total_likes - $sub_limit;

    if (empty($friendLike) && empty($is_like)) {
      $likeAdInfo['is_like'] = 0;
      $likeAdInfo['friend_like'] = 0;
      $likeAdInfo['total_like'] = $total_likes;
      return $likeAdInfo;
    } else if (empty($friendLike) && !empty($is_like)) {
      if (!empty($other_like)) {
        $likeAdInfo['is_like'] = 1;
        $likeAdInfo['friend_like'] = 0;
        $likeAdInfo['total_like'] = $total_likes - 1;
        return $likeAdInfo;
      } else {
        return;
      }
    } else if (empty($is_like) && !empty($friendLike)) {
      $likeAdInfo['is_like'] = 0;
      $likeAdInfo['friend_like'] = $friend_title_str;
      $likeAdInfo['total_like'] = $other_like;
      return $likeAdInfo;
    } else if (!empty($is_like) && !empty($friendLike)) {
      $likeAdInfo['is_like'] = 1;
      $likeAdInfo['friend_like'] = $friend_title_str;
      $likeAdInfo['total_like'] = $other_like;
      return $likeAdInfo;
    }
    return $likeAdInfo;
  }

  public function user_friend_likes($resource_type, $resource_id, $limit, $ad_id = null) {
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $friend_likes_obj = NULL;

    $like_table = Engine_Api::_()->getItemTable('communityad_like');
    $membership_table = Engine_Api::_()->getDbtable('membership', 'user');
    $like_name = $like_table->info('name');
    $member_name = $membership_table->info('name');
    $like_select = $like_table->select()
            ->from($like_name, array('poster_id'))
            ->joinInner($member_name, "$member_name . user_id = $like_name . poster_id", NULL)
            ->where($member_name . '.resource_id = ?', $user_id)
            ->where($member_name . '.active = ?', 1)
            ->where($like_name . '.ad_id = ?', $ad_id)
            ->where($like_name . '.poster_id != ?', $user_id)
            ->where($like_name . '.poster_id != ?', 0)
            ->order($like_name . '.like_id DESC')
            ->limit($limit);
    $like_fetch_record = $like_select->query()->fetchAll();
    $display_friend_str = '';
    if (!empty($like_fetch_record)) {
      foreach ($like_fetch_record as $fetch_friend_id) {
        $friendObj = Engine_Api::_()->getItem('user', $fetch_friend_id['poster_id']);
        $friendTitle = $view->htmlLink($friendObj->getHref(), $friendObj->getTitle());
        $friend_likes_obj[] = $friendTitle;
        $display_friend_str .= $friendObj->getIdentity() . ',';
      }
    }
    return $friend_likes_obj;
  }

  public function adSubTitle($adurl) {
    global $communityad_adhost;
//     if (empty($communityad_adhost)) {
//       exit();
//     }
    if (strstr($adurl, 'http://www.')) {
      $ad_url = str_replace('http://www.', '', $adurl);
    } else if (strstr($adurl, 'http://')) {
      $ad_url = str_replace('http://', '', $adurl);
    } else {
      $ad_url = $adurl;
    }
    $ad_url = explode('/', $ad_url);
    $ad_url = $ad_url[0];
    return $ad_url;
  }

  public function friend_number_of_like($ad_id) {
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $sub_status_table = Engine_Api::_()->getItemTable('communityad_like');
    $sub_status_name = $sub_status_table->info('name');

    $membership_table = Engine_Api::_()->getDbtable('membership', 'user');
    $member_name = $membership_table->info('name');

    $user_table = Engine_Api::_()->getItemTable('user');
    $user_Name = $user_table->info('name');

    $count_select = $sub_status_table->select()
            ->from($sub_status_name, array('COUNT(' . $sub_status_name . '.ad_id) AS like_count'))
            ->joinInner($member_name, "$member_name . user_id = $sub_status_name . poster_id", NULL)
            ->joinInner($user_Name, "$user_Name . user_id = $sub_status_name . poster_id", null)
            ->where($member_name . '.resource_id = ?', $user_id)
            ->where($member_name . '.active = ?', 1)
            ->where($sub_status_name . '.ad_id = ?', $ad_id)
            ->where($sub_status_name . '.poster_id != ?', $user_id)
            ->where($sub_status_name . '.poster_id != ?', 0);
    $fetch_count = $count_select->query()->fetchAll();
    if (!empty($fetch_count)) {
      return $fetch_count[0]['like_count'];
    } else {
      return 0;
    }
  }

  /**
   * Update click counter then redirect host to ad's target URL.
   */
  public function ad_clickcount($adId, $is_redirect = true) {
    $date = new Zend_Date(time());
    $date->setTimezone(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));
    $current_date = gmdate('Y-m-d', $date->getTimestamp());

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $userad = Engine_Api::_()->getItem('userads', $adId);
    if (empty($userad)) {
      return;
    }
    $displayed_ad_url = '';
    $campaignId = $userad->campaign_id;
    $ip_address = $this->getRealIpAddr();
    if (!empty($userad->resource_type) && !empty($userad->resource_id)) {
      $resource_url = Engine_Api::_()->communityad()->resourceUrl($userad->resource_type, $userad->resource_id);
      if (!empty($resource_url)) {
        $displayed_ad_url = $resource_url['link'];
      }
    } else {
      $displayed_ad_url = $userad->cads_url;
    }

    $status = $this->click_valid($adId);
    if (!empty($status)) {
      $sub_status_table = Engine_Api::_()->getDbTable('adstatistics', 'communityad');
      $sub_status_name = $sub_status_table->info('name');
      $sub_status_select = $sub_status_table->select()
              ->from($sub_status_name, array('adstatistic_id', 'value_click'))
              ->where('userad_id = ?', $adId)
              ->where("DATE_FORMAT(" . $sub_status_name . " .response_date, '%Y-%m-%d') = ?", $current_date)
              ->where("viewer_id = ? OR hostname = '$ip_address'", $viewer_id)
              ->limit(1);
      $fetchResult = $sub_status_select->query()->fetchAll();
      if (empty($fetchResult)) {
        $row = $sub_status_table->createRow();
        $row->userad_id = $adId;
        $row->adcampaign_id = $campaignId;
        $row->viewer_id = $viewer_id;
        $row->hostname = $ip_address;
        $row->user_agent = $_SERVER['HTTP_USER_AGENT'];
        $row->url = $displayed_ad_url;
        $row->response_date = gmdate('Y-m-d H:i:s', $date->getTimestamp());
        $row->value_click = 1;
        $row->value_view = 0;
        $row->value_like = 0;
        $row->save();
      } else {
        $sub_status_table->update(array('value_click' => $fetchResult[0]['value_click'] + 1, 'response_date' => date('Y-m-d H:i:s')), array('adstatistic_id =?' => $fetchResult[0]['adstatistic_id']));
      }
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $userad->count_click = $userad->count_click + 1;
        if ($userad->limit_click > 0) {
          $userad->limit_click = $userad->limit_click - 1;
        }
        if ($userad->limit_click == 0 && $userad->price_model == 'Pay/click') {
          if ($userad->payment_status != "free")
            $userad->payment_status = "expired";

          if ($userad->status <= 2) {
            $userad->approved = 0;
            $userad->enable = 0;
            $userad->status = 3;

            Engine_Api::_()->communityad()->sendMail("EXPIRED", $userad->userad_id);
          }
        }
        $userad->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }

    if (empty($is_redirect)) {
      return;
    }

// Allow source url to be passed in.
    if (isset($displayed_ad_url)) {
      if (!empty($displayed_ad_url)) {
        header('Location: ' . $displayed_ad_url);
      } else {
        return 'false';
      }
    } else {
      return 'false';
    }
  }

  /**
   * Perform on-the-fly click filtering.
   */
  public function click_valid($adId) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $ip_address = $this->getRealIpAddr();

    if (preg_match('/bot|crawl|spider/i', $_SERVER['HTTP_USER_AGENT'])) {
      return false;
    }

// See if the click came from a duplicate user or ip address.
    if ($viewer_id) {
      $userad = Engine_Api::_()->getItem('userads', $adId);
      $ad_ownerid = $userad->owner_id;

// See if the click came from owner of the ad
      if ($ad_ownerid == $viewer_id) {
        return false;
      }

// time before 2 hours from current_time
      $time_stmp = time() - 7200;
      $time = date('Y-m-d H:i:s', $time_stmp);
      $sub_status_table = Engine_Api::_()->getDbTable('adstatistics', 'communityad');
      $sub_status_name = $sub_status_table->info('name');
      $sub_status_select = $sub_status_table->select()
              ->from($sub_status_name, array('adstatistic_id', 'value_click'))
              ->where('userad_id = ?', $adId)
              ->where('response_date >= ?', $time)
              ->where("viewer_id = ? OR hostname = '$ip_address'", $viewer_id)
              ->limit(1);
      $duplicate_click = $sub_status_select->query()->fetchAll();
    } else {
      $sub_status_table = Engine_Api::_()->getDbTable('adstatistics', 'communityad');
      $sub_status_name = $sub_status_table->info('name');
      $sub_status_select = $sub_status_table->select()
              ->from($sub_status_name, array('adstatistic_id', 'value_click'))
              ->where('userad_id = ?', $adId)
              ->where('hostname = ?', $ip_address)
              ->limit(1);
      $duplicate_click = $sub_status_select->query()->fetchAll();
    }
    if (!empty($duplicate_click[0]['value_click'])) {
      return false;
    }
    return true;
  }

  public function getRealIpAddr() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
  }

  public function enableCreateLink() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $create_visitor = Engine_Api::_()->getApi('settings', 'core')->getSetting('adblock.create.link', 1);
    if (empty($viewer_id)) {
      if (!empty($create_visitor)) {
        return true;
      }
      else
        return false;
    }
    elseif (Engine_Api::_()->authorization()->isAllowed('communityad', $viewer, 'create')) {
      return true;
    }
    else
      return false;
  }

  public function hasNetworkOnSite() {
    $table = Engine_Api::_()->getDbtable('networks', 'network');
    $select = $table->select()
            ->where('hide = ?', 0)
            ->limit(1);
    $lists = $table->fetchRow($select);
    if (!empty($lists))
      return true;
    else
      return false;
  }

// Function for finding that module type is in package or not.
  public function is_packagesupport($package_id, $type) {
    $urloption = Engine_Api::_()->getItem('package', $package_id)->urloption;
    $check_module = strstr($urloption, $type);
    if (empty($check_module)) {
      return null;
    } else {
      return 1;
    }
  }

  public function getCommunityadTitle() {
    return Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.title', 'Communityad');
  }

  public function getResourceObj($resource_type, $resource_id) {
    return Engine_Api::_()->getItem($resource_type, $resource_id);
  }

  public function hideCustomUrl() {
    return Engine_Api::_()->getApi('settings', 'core')->getSetting('custom.ad.url', 0);
  }

  public function isModuleEnabled($moduleName) {
    if (strstr($moduleName, "sitereview")) {
      $moduleName = "sitereview";
    }
    return Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($moduleName);
  }

  public function getAdStaticsLimitDate() {
    $adStaticsLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.statistics.limit', 3) * 31536000;
    if ($adStaticsLimit) {
      $time = time() - $adStaticsLimit;
      return date('Y-m-d h:i:s', $time);
    }
  }

}

?>
