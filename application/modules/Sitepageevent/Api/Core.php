<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_Api_Core extends Core_Api_Abstract {

  public function setEventPackages() {

    $check_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepageevent.isvar');
    $base_result_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepageevent.basetime');
    $filePath = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepageevent.filepath');
    $currentbase_time = time();
    $word_name = strrev('lruc');
    $file_path = APPLICATION_PATH . '/application/modules/' . $filePath;

    if (($currentbase_time - $base_result_time > 4492800) && empty($check_result_show)) {
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
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepageevent.set.type', 1);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepageevent.keyword', 1);
        return;
      } else {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepageevent.isvar', 1);
      }
    }
  }

  /**
   * Delete the sitepageevent album and photos
   * 
   * @param int $event_id
   */
  public function deleteContent($event_id) {

    $sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', $event_id);

    if (empty($sitepageevent)) {
      return;
    }

    $tableEventPhoto = Engine_Api::_()->getItemTable('sitepageevent_photo');
    $select = $tableEventPhoto->select()->where('event_id = ?', $event_id);
    $rows = $tableEventPhoto->fetchAll($select);
    if (!empty($rows)) {
      foreach ($rows as $photo) {
        $photo->delete();
      }
    }

    $tableEventAlbum = Engine_Api::_()->getItemTable('sitepageevent_album');
    $select = $tableEventAlbum->select()->where('event_id = ?', $event_id);
    $rows = $tableEventAlbum->fetchAll($select);
    if (!empty($rows)) {
      foreach ($rows as $album) {
        $album->delete();
      }
    }

    $sitepageevent->delete();
  }
  
  /**
   * Get page event select query
   *
   * @param array $params
   * @param array $customParams
   * @return string $select;
   */
  public function getSitepageEventsSelect($params = array()) {

    $table = Engine_Api::_()->getDbtable('events', 'sitepageevent');
    $rName = $table->info('name');

    $locationTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
    $locationName = $locationTable->info('name');
    
		$pagePackagesTable = Engine_Api::_()->getDbtable('packages', 'sitepage');
		$pagePackageTableName = $pagePackagesTable->info('name');
		
		$tablePage = Engine_Api::_()->getDbtable('pages', 'sitepage');
		$tablePageName = $tablePage->info('name');
		
    $select = $table->select();
    $select = $select
            ->setIntegrityCheck(false)
            ->from($rName);

    $select = $select
                ->where($rName . '.search = ?', '1');
		$select->join($tablePageName, "$tablePageName.page_id = $rName.page_id", array('page_id', 'title AS page_title', 'closed', 'approved', 'declined', 'draft', 'expiration_date', 'owner_id', 'photo_id as page_photo_id'))
			->join($pagePackageTableName, "$pagePackageTableName.package_id = $tablePageName.package_id",array('package_id', 'price'));
			
    if ((isset($params['sitepage_location']) && !empty($params['sitepage_location'])) ) {
      if (isset($params['locationmiles']) && (!empty($params['locationmiles']))) {
        $longitude = 0;
        $latitude = 0;
        
        //check for zip code in location search.
        if(empty($params['Latitude']) && empty($params['Longitude'])) {
          $selectLocQuery = $locationTable->select()->where('location = ?', $params['sitepage_location']);
          $locationValue = $locationTable->fetchRow($selectLocQuery);
          $enableSocialengineaddon = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('seaocore');
          if (empty($locationValue)) {
            $getSEALocation = array();
            if (!empty($enableSocialengineaddon)) {
              $getSEALocation = Engine_Api::_()->getDbtable('locations', 'seaocore')->getLocation(array('location' => $params['sitepage_location']));
            }
            if (empty($getSEALocation)) {
              //   $locationLocal =  $params['sitepage_location'];
              $urladdress = str_replace(" ", "+", $params['sitepage_location']);
              //Initialize delay in geocode speed
              $delay = 0;
              //Iterate through the rows, geocoding each address
              $geocode_pending = true;
              while ($geocode_pending) {
                $key = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
                if (!empty($key)) {
                    $request_url = "https://maps.googleapis.com/maps/api/place/textsearch/json?query=$urladdress&sensor=true&key=$key";
                } else {
                    $request_url = "https://maps.googleapis.com/maps/api/geocode/json?address=$urladdress&sensor=true";
                }

                $ch = @curl_init();
                $timeout = 5;
                curl_setopt($ch, CURLOPT_URL, $request_url);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                ob_start();
                curl_exec($ch);
                curl_close($ch);
                $json_resopnse = Zend_Json::decode(ob_get_contents());
                ob_end_clean();
                $status = $json_resopnse['status'];
                if (strcmp($status, "OK") == 0) {
                  //Successful geocode
                  $geocode_pending = false;
                  $result = $json_resopnse['results'];

                  //Format: Longitude, Latitude, Altitude
                  $latitude = (float) $result[0]['geometry']['location']['lat'];
                  $longitude = (float) $result[0]['geometry']['location']['lng'];
                }
              }
            } else {
              $latitude = (float) $getSEALocation->latitude;
              $longitude = (float) $getSEALocation->longitude;
            }
          } else {
            $latitude = (float) $locationValue->latitude;
            $longitude = (float) $locationValue->longitude;
          }
        } else {
          $latitude = (float) $params['Latitude'];
          $longitude = (float) $params['Longitude'];
        }

        $radius = $params['locationmiles']; //in miles

        $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.proximity.search.kilometer', 0);
        if (!empty($flage)) {
          $radius = $radius * (0.621371192);
        }
        $latitudeRadians = deg2rad($latitude);
        $latitudeSin = sin($latitudeRadians);
        $latitudeCos = cos($latitudeRadians);
        $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id   ", array("latitude", "longitude", "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance"));
        $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
        $sqlstring .= ")";
        $select->where($sqlstring);
        $select->order("distance");
      } else {
        $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id");
        $select->where("`{$locationName}`.formatted_address LIKE ? or `{$locationName}`.location LIKE ? or `{$locationName}`.city LIKE ? or `{$locationName}`.state LIKE ?", "%" . urldecode($params['sitepage_location']) . "%");
        //}
      }
    } elseif (!empty($params['latitude']) && !empty($params['longitude'])) {
      $radius = Engine_Api::_()->getApi('settings', 'core')->getSetting('sgl.geolocation.range', 100); // in miles
      $latitude = $params['latitude'];
      $longitude = $params['longitude'];
      $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.proximity.search.kilometer', 0);
      if (!empty($flage)) {
        $radius = $radius * (0.621371192);
      }
      $latitudeRadians = deg2rad($latitude);
      $latitudeSin = sin($latitudeRadians);
      $latitudeCos = cos($latitudeRadians);
      $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id", array("(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance"));
      $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
      $sqlstring .= ")";
      $select->where($sqlstring);
      $select->order("distance");
    } else {
      $select->joinLeft($locationName, "$rName.seao_locationid = $locationName.locationitem_id");
    }
    
			if (!empty($params['title'])) {
				$select->where($tablePageName . ".title LIKE ? ", '%' . $params['title'] . '%');
			}

			if (!empty($params['search_event'])) {
				$select->where($rName . ".title LIKE ?" , '%' . $params['search_event'] . '%');
			}

// 			if (!empty($params['category'])) {
// 				$select->where($tablePageName . '.category_id = ?', $params['category']);
// 			}

			if (!empty($params['category_id'])) {
				$select->where($tablePageName . '.category_id = ?', $params['category_id']);
			}

// 			if (!empty($params['subcategory'])) {
// 				$select->where($tablePageName . '.subcategory_id = ?', $params['subcategory']);
// 			}

			if (!empty($params['subcategory_id'])) {
				$select->where($tablePageName . '.subcategory_id = ?', $params['subcategory_id']);
			}

// 			if (!empty($params['subsubcategory'])) {
// 				$select->where($tablePageName . '.subsubcategory_id = ?', $params['subsubcategory']);
// 			}

			if (!empty($params['subsubcategory_id'])) {
				$select->where($tablePageName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
			}
			
			if (isset($params['orderby']) && $params['orderby'] == 'view_count') {
					$select = $select
													->order($rName .'.view_count DESC')
													->order($rName .'.creation_date DESC');
			} elseif (isset($params['orderby']) && $params['orderby'] == 'member_count') {
				$select = $select
												->order($rName .'.member_count DESC');
			} elseif (isset($params['orderby']) && $params['orderby'] == 'creation_date') {
				$select = $select
												->order($rName .'.creation_date DESC');
			} elseif (isset($params['orderby']) && $params['orderby'] == 'starttime') {
				$select = $select
												->order(!empty($params['orderby']) ? $params['orderby'] . ' ASC' : $rName .'.starttime ASC');
			}
			
		  $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
			if(isset($params['show']) && $params['show'] == 'my_event') {
				$select->where($rName . '.user_id = ?', $viewer_id);
			}
			elseif(isset($params['show']) && $params['show'] == 'past_event') {
				$select->where("$rName.endtime < FROM_UNIXTIME(?)", time());
			}
			elseif((isset($params['show']) && $params['show'] == 'upcoming_event')) {
				$select->where("$rName.endtime > FROM_UNIXTIME(?)", time());
				$select->order($rName . '.starttime ASC');
			}
			elseif ((isset($params['show']) && $params['show'] == 'sponsored_event')) {
					
					$select->where($pagePackageTableName . '.price != ?', '0.00');
					$select->order($pagePackageTableName . '.price' . ' DESC');
			}
			elseif (isset($params['show']) && $params['show'] == 'Networks') {
					$select = $tablePage->getNetworkBaseSql($select, array('browse_network' => 1));

			}
			elseif((isset($params['show']) && $params['show'] == 'featured')) {
				$select = $select
												->where($rName . '.featured = ?', 1)
												->order($rName .'.creation_date DESC');
			}
			elseif (isset($params['show']) && $params['show'] == 'my_like') {
				$likeTableName = Engine_Api::_()->getDbtable('likes', 'core')->info('name');
				$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
				$select
								->join($likeTableName, "$likeTableName.resource_id = $tablePageName.page_id")
								->where($likeTableName . '.poster_type = ?', 'user')
								->where($likeTableName . '.poster_id = ?', $viewer_id)
								->where($likeTableName . '.resource_type = ?', 'sitepage_page');
			}
			
		  $select = $select
											->where($tablePageName . '.search = ?', '1')
											->where($tablePageName . '.closed = ?', '0')
											->where($tablePageName . '.approved = ?', '1')
											->where($tablePageName . '.declined = ?', '0')
											->where($tablePageName . '.draft = ?', '1');
			if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
				$select->where($tablePageName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
			}

// 		if (isset($params['order_by']) && !empty($params['order_by'])) {
// 			// Endtime
// 			if($params['order_by'] == 1 ) {
// 				$select->where("endtime <= FROM_UNIXTIME(?)", time());
// 			} elseif($params['order_by'] == 2 ) {
// 				$select->where("endtime > FROM_UNIXTIME(?)", time());
// 			}
// 		}

    // Convert times
//     $viewer = Engine_Api::_()->user()->getViewer();
//     $oldTz = date_default_timezone_get();
//     date_default_timezone_set($viewer->timezone); 
//     $start = strtotime($params['starttime']['date']);
//     $end = strtotime($params['endtime']['date']);
//     date_default_timezone_set($oldTz);
//     $startTime = date('Y-d-m', $start);
//     $endTime = date('Y-d-m', $end);



		if(!empty($params['starttime']['date'])) {
		  $startTime = date("Y-m-d", strtotime($params['starttime']));
			$select->where($rName . '.starttime >= ?', $startTime);
   	}

		if(!empty($params['endtime']['date'])) {
		 $endTime = date("Y-m-d", strtotime($params['endtime']));
     $select->where($rName . '.endtime <= ?', $endTime);
   	}
		return $select;
	}
	
	public function sendInviteEmail($object, $actionObject = null, $notificationType = null, $emailType = null, $params = null, $memberIdsArray = array()) {

		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
		$sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
		$notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
		
		$manageAdminTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
		
    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		$page_id = $object->page_id;

		$subject = Engine_Api::_()->getItem('sitepage_page', $page_id);
		$owner = $subject->getOwner();

		//previous notification is delete.
		//$notificationsTable->delete(array('type =?' => "$notificationType", 'object_type = ?' => "sitepage_page", 'object_id = ?' => $page_id, 'subject_id = ?' => $viewer_id));

		//GET PAGE TITLE AND PAGE TITLE WITH LINK.
		$pagetitle = $subject->title;
		$page_url = Engine_Api::_()->sitepage()->getPageUrl($subject->page_id);
		$page_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_url' => $page_url), 'sitepage_entry_view', true);
		$page_title_link = '<a href="' . $page_baseurl . '"  >' . $pagetitle . ' </a>';

    //ITEM TITLE AND TILTE WITH LINK.
		$item_title = $object->title;
		$item_title_url = $object->getHref();
		$item_title_baseurl = 'http://' . $_SERVER['HTTP_HOST']. $item_title_url;
		$item_title_link = "<a href='$item_title_baseurl'  >" . $item_title . " </a>";

    //POSTER TITLE AND PHOTO WITH LINK
		$poster_title = $viewer->getTitle();
		$poster_url = $viewer->getHref();
		$poster_baseurl = 'http://' . $_SERVER['HTTP_HOST']. $poster_url;
		$poster_title_link = "<a href='$poster_baseurl'  >" . $poster_title . " </a>";
		if($viewer->photo_id) {
			$photo = 'http://' . $_SERVER['HTTP_HOST'] . $viewer->getPhotoUrl('thumb.icon');
		}
		else {
			$photo = 'http://' . $_SERVER['HTTP_HOST'] . $view->baseUrl() .  '/application/modules/Sitepage/externals/images/nophoto_sitepage_thumb_icon.png';
		}
		$image = "<img src='$photo' />";
		$posterphoto_link = "<a href='$poster_baseurl'  >" . $image . " </a>";

		//MEASSGE WITH LINK.
		if(!empty($actionObject) && isset($actionObject)) {
			$post_baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . $actionObject->getHref();
			$post = $poster_title . ' Posted in ' . $pagetitle;
			$post_link = "<a href='$post_baseUrl'  >" . $post . " </a>";
		}


		//FETCH DATA
		if ($params == 'InviteMembers') {
			foreach ($memberIdsArray as $value) {
				$user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
				if(!empty($value['email'])) {
					Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, "SITEPAGEEVENT_INVITE_EMAIL", array(
							'page_title' => $pagetitle,
							'item_title_with_link' => $item_title_link,
							'item_title' => $item_title,
							'viewertitle_link' => $poster_title_link,
							'viewerphoto_link' => $posterphoto_link,
					));
				}
			}
		} 
		elseif($params == 'Pageevent Invite') {
		  $pageMembers = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinMembers($page_id);
			foreach ($groupMembers as $value) {
				$user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
				if(!empty($value['email'])) {
					Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, "SITEPAGEEVENT_INVITE_EMAIL", array(
							'page_title' => $pagetitle,
							'item_title_with_link' => $item_title_link,
							'item_title' => $item_title,
							'viewertitle_link' => $poster_title_link,
							'viewerphoto_link' => $posterphoto_link,
					));
				}
			}
		}
		else {
			$manageAdminsIds = $manageAdminTable->getManageAdmin($page_id, $viewer_id);
			foreach ($manageAdminsIds as $value) {
				$user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
				if(!empty($value['email'])) {
					Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, "SITEPAGEEVENT_INVITE_EMAIL", array(
							'page_title' => $pagetitle,
							'item_title_with_link' => $item_title_link,
							'item_title' => $item_title,
							'viewertitle_link' => $poster_title_link,
							'viewerphoto_link' => $posterphoto_link,
					));
				}
			}
		}
		
		$object_type = $object->getType();
		$object_id = $object->getIdentity();
		$subject_type = $viewer->getType();
		$subject_id = $viewer->getIdentity();
		
		if (!empty($sitepagememberEnabled)) {
			$db = Zend_Db_Table_Abstract::getDefaultAdapter();
			if ($params == 'InviteMembers') {
				$friendId = $memberIdsArray;
			} else {
				$friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
			}
			if (!empty($friendId)) {
				$db->query("INSERT IGNORE INTO `engine4_activity_notifications` (`user_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`,`params`, `date`) SELECT `engine4_sitepage_membership`.`user_id` as `user_id` ,	'". $subject_type ."' as `subject_type`, " . $subject_id . " as `subject_id`, '" . $object_type . "' as `object_type`, " . $object_id . " as `object_id`, '".$notificationType."' as `type`, 'null' as `params`, '" . date('Y-m-d H:i:s') . "' as ` date `  FROM `engine4_sitepage_membership` WHERE (engine4_sitepage_membership.page_id = ".$subject->page_id.") AND (engine4_sitepage_membership.user_id <> ".$viewer->getIdentity().") AND (engine4_sitepage_membership.notification = 1 or (engine4_sitepage_membership.notification = 2 and (engine4_sitepage_membership .user_id IN (".join(",",$friendId)."))))");
			} else {
				$db->query("INSERT IGNORE INTO `engine4_activity_notifications` (`user_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`,`params`, `date`) SELECT `engine4_sitepage_membership`.`user_id` as `user_id` ,	'". $subject_type ."' as `subject_type`, " . $subject_id . " as `subject_id`, '" . $object_type . "' as `object_type`, " . $object_id . " as `object_id`, '".$notificationType."' as `type`, 'null' as `params`, '" . date('Y-m-d H:i:s') . "' as ` date `  FROM `engine4_sitepage_membership` WHERE (engine4_sitepage_membership.page_id = ".$subject->page_id.") AND (engine4_sitepage_membership.user_id <> ".$viewer->getIdentity().") AND (engine4_sitepage_membership.notification = 1)");
			}
		}
  }
  
  public function getCircleCalendarEventsData() {
    if(!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }
    
    // GET SITE PAGE
    $sitepage_subject = Engine_Api::_()->core()->getSubject('sitepage_page');
    // GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    
    //GET PAGE ID
    $page_id = $sitepage_subject->page_id;
    // GET VIEWER ID
    $viewer_id = $viewer->getIdentity();
    
    $pageEvents = Engine_Api::_()->getDbTable('events', 'sitepageevent');
    
    $values = array();
    $values['orderby'] = 'starttime';
    $values['page_id'] = $page_id;

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    if ($can_edit) {
      $values['show_event'] = 0;
    } else {
      $values['show_event'] = 1;
      $values['event_owner_id'] = $viewer_id;
    }

    $select = $pageEvents->getSitepageeventsSelect($values);
    $events = $pageEvents->fetchAll($select);
    
    return $this->_convertEventsToCalendarData($events);
  }
  
  public function getPersonalCalendarCircleEventsData() {
    // GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $pageEvents = Engine_Api::_()->getDbTable('events', 'sitepageevent');

    $request = Zend_Controller_Front::getInstance()->getRequest();
    
    $membership = Engine_Api::_()->getDbtable('membership', 'sitepageevent');
    $select = $membership->getMembershipsOfSelect($viewer);
    //$select = $events->select();
    $select->where('starttime >= ?', $request->getParam("startDate"));
    $select->where('starttime <= ?', $request->getParam("endDate"));
    $select->where('endtime >= ?', $request->getParam("startDate"));
    $select->order('starttime ASC');

    $events = $pageEvents->fetchAll($select);
    
    return $this->_convertEventsToCalendarData($events);
  }
  
  protected function _convertEventsToCalendarData($events) {
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    if(!$view) {
      return false;
    }
    
    $membership = Engine_Api::_()->getDbtable('membership', 'sitepageevent');
    
    $data = array();

    foreach ($events as $event) {
      $tmpBody = strip_tags($event->description);
      $desc = ( Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody );
      
      $categoryTable = Engine_Api::_()->getDbtable('categories', 'sitepageevent');
      $category = $categoryTable->find($event->category_id)->current();

      $catName = "";
      if (isset($category)) {
        $catName = $category->title;
      }

      $href = $event->getHref();
      
      $not = 0;
      $waiting = 0;
      $maybe = 0;
      $attend = 0;

      $select = $membership->select()->from($membership, array('rsvp', 'count(*) as cnt'))->where('resource_id = ?', $event->event_id)->group('rsvp');
      $attendings = $membership->fetchAll($select);

      foreach ($attendings as $attending) {
        if ($attending->rsvp == "2") {
          $attend = $attending->cnt;
        } elseif ($attending->rsvp == "1") {
          $maybe = $attending->cnt;
        } elseif ($attending->rsvp == "3") {
          $waiting = $attending->cnt;
        } else {
          $not = $attending->cnt;
        }
      }

      $data[] = array(
          'title' => $event->title,
          'start' => $view->locale()->toDateTime($event->starttime),
          'end' => $view->locale()->toDateTime($event->endtime),
          'location' => $event->location,
          'description' => $desc,
          'host' => $event->host,
          'href' => $href,
          'category' => $catName == null ? '' : $catName,
          'attending' => $attend == null ? '0' : $attend,
          'maybe' => $maybe == null ? '0' : $maybe,
          'notattend' => $not == null ? '0' : $not,
          'waiting' => $waiting == null ? '0' : $waiting
      );
    }
    
    return $data;
  }
}