<?php

  $db = Zend_Db_Table_Abstract::getDefaultAdapter();
  $isSitereviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitereview");
  if( !empty($isSitereviewEnabled) ) {
    $getListingType = $db->query("SELECT * FROM `engine4_sitereview_listingtypes` LIMIT 0 , 30")->fetchAll();
    if( !empty($getListingType) ) {
      foreach($getListingType as $listingType) {
        $communityadModuleTable = Engine_Api::_()->getDbTable('modules', 'communityad');
        $communityadModuleTableName = $communityadModuleTable->info('name');
        $temTableName = "sitereview_listing_" . $listingType["listingtype_id"];

        $isAdsExist = $db->query("SELECT * FROM `engine4_communityad_modules` WHERE `table_name` LIKE '" . $temTableName . "' LIMIT 1")->fetch();
        if( empty($isAdsExist) ) {
          $row = $communityadModuleTable->createRow();
          $row->module_name = "sitereview";
          $row->module_title = $listingType["title_singular"];
          $row->table_name = $temTableName;
          $row->title_field = "title";
          $row->body_field = "body";
          $row->owner_field = "owner_id";
          $row->displayable = "7";
          $row->is_delete = "1";
          $row->save();
        }
      }
    }
  }

	$check_table = Engine_Api::_()->getDbtable('menuItems', 'core');
        $package_table = Engine_Api::_()->getItemtable('package');
        $enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
        $defaultModules = Engine_Api::_()->getDbtable('modules', 'communityad')->freePackageModule();
        $queary_info = array_intersect($enabledModuleNames, $defaultModules);

        $urloption = 'website';
        if (!empty($queary_info)) {
          foreach ($queary_info as $module) {
            $urloption .= ',' . $module;
          }
        }
        if (!empty($urloption)) {
          $page_item = $package_table->createRow();
          $page_item->title = 'Free Ad Package';
          $page_item->desc = 'This is a free ad package. An advertiser does not need to pay for creating an ad of this package.';
          $page_item->price = 0;
          $page_item->sponsored = 0;
          $page_item->featured = 0;
          $page_item->urloption = $urloption;
          $page_item->enabled = 1;
          $page_item->network = 1;
          $page_item->public = 1;
          $page_item->price_model = 'Pay/click';
          $page_item->model_detail = -1;
          $page_item->creation_date = date('Y-m-d H:i:s');
          $page_item->renew = 0;
          $page_item->renew_before = 0;
          $page_item->auto_aprove = 1;
		  $page_item->type = 'default';
          $page_item->save();

		  $story_item = $package_table->createRow();
          $story_item->title = 'Free Sponsored Story Package';
          $story_item->desc = 'This is a free ad package. An advertiser does not need to pay for creating an ad of this package.';
          $story_item->price = 0;
          $story_item->sponsored = 0;
          $story_item->featured = 0;
          $story_item->urloption = $urloption;
          $story_item->enabled = 1;
          $story_item->network = 1;
          $story_item->public = 1;
          $story_item->price_model = 'Pay/click';
          $story_item->model_detail = -1;
          $story_item->creation_date = date('Y-m-d H:i:s');
          $story_item->renew = 0;
          $story_item->renew_before = 0;
          $story_item->auto_aprove = 1;
		  $story_item->type = 'sponsored_stories';
          $story_item->save();
        }

        $check_name = $check_table->info('name');
        $select = $check_table->select()
                ->setIntegrityCheck(false)
                ->from($check_name, array('id'))
                ->where('name = ?', 'core_main_communityad');
        $queary_info = $select->query()->fetchAll();
        if (empty($queary_info)) {
          $menu_item = $check_table->createRow();
          $menu_item->name = 'core_main_communityad';
          $menu_item->module = 'communityad';
          $menu_item->label = 'Advertising';
          $menu_item->plugin = 'Communityad_Plugin_Menus::canViewAdvertiesment';
          $menu_item->params = '{"route":"communityad_display"}';
          $menu_item->menu = 'core_main';
          $menu_item->submenu = '';
          $menu_item->order = 2;
          $menu_item->save();
        }

//        $check_name = $check_table->info('name');
//        $select = $check_table->select()
//                ->setIntegrityCheck(false)
//                ->from($check_name, array('id'))
//                ->where('name = ?', 'communityad_admin_widget_setting');
//        $queary_info = $select->query()->fetchAll();
//        if (empty($queary_info)) {
//          $menu_item = $check_table->createRow();
//          $menu_item->name = 'communityad_admin_widget_setting';
//          $menu_item->module = 'communityad';
//          $menu_item->label = 'Manage Ad Blocks';
//          $menu_item->plugin = '';
//          $menu_item->params = '{"route":"admin_default","module":"communityad","controller":"widgets","action":"manage"}';
//          $menu_item->menu = 'communityad_admin_main';
//          $menu_item->submenu = '';
//          $menu_item->order = 2;
//          $menu_item->save();
//        }

        $check_name = $check_table->info('name');
        $select = $check_table->select()
                ->setIntegrityCheck(false)
                ->from($check_name, array('id'))
                ->where('name = ?', 'communityad_admin_main_packagelist');
        $queary_info = $select->query()->fetchAll();
        if (empty($queary_info)) {
          $menu_item = $check_table->createRow();
          $menu_item->name = 'communityad_admin_main_packagelist';
          $menu_item->module = 'communityad';
          $menu_item->label = 'Manage Ad Packages';
          $menu_item->plugin = '';
          $menu_item->params = '{"route":"admin_default","module":"communityad","controller":"packagelist"}';
          $menu_item->menu = 'communityad_admin_main';
          $menu_item->submenu = '';
          $menu_item->order = 3;
          $menu_item->save();
        }

        $check_name = $check_table->info('name');
        $select = $check_table->select()
                ->setIntegrityCheck(false)
                ->from($check_name, array('id'))
                ->where('name = ?', 'communityad_admin_admodule_manage');
        $queary_info = $select->query()->fetchAll();
        if (empty($queary_info)) {
          $menu_item = $check_table->createRow();
          $menu_item->name = 'communityad_admin_admodule_manage';
          $menu_item->module = 'communityad';
          $menu_item->label = 'Manage Modules';
          $menu_item->plugin = '';
          $menu_item->params = '{"route":"admin_default","module":"communityad","controller":"widgets","action":"admodule-manage"}';
          $menu_item->menu = 'communityad_admin_main';
          $menu_item->submenu = '';
          $menu_item->order = 4;
          $menu_item->save();
        }

        $check_name = $check_table->info('name');
        $select = $check_table->select()
                ->setIntegrityCheck(false)
                ->from($check_name, array('id'))
                ->where('name = ?', 'communityad_admin_level_settings');
        $queary_info = $select->query()->fetchAll();
        if (empty($queary_info)) {
          $menu_item = $check_table->createRow();
          $menu_item->name = 'communityad_admin_level_settings';
          $menu_item->module = 'communityad';
          $menu_item->label = 'Member Level Settings';
          $menu_item->plugin = '';
          $menu_item->params = '{"route":"admin_default","module":"communityad","controller":"level"}';
          $menu_item->menu = 'communityad_admin_main';
          $menu_item->submenu = '';
          $menu_item->order = 5;
          $menu_item->save();
        }

        $check_name = $check_table->info('name');
        $select = $check_table->select()
                ->setIntegrityCheck(false)
                ->from($check_name, array('id'))
                ->where('name = ?', 'communityad_admin_target_settings');
        $queary_info = $select->query()->fetchAll();
        if (empty($queary_info)) {
          $menu_item = $check_table->createRow();
          $menu_item->name = 'communityad_admin_target_settings';
          $menu_item->module = 'communityad';
          $menu_item->label = 'Targeting Settings';
          $menu_item->plugin = '';
          $menu_item->params = '{"route":"admin_default","module":"communityad","controller":"settings","action":"target"}';
          $menu_item->menu = 'communityad_admin_main';
          $menu_item->submenu = '';
          $menu_item->order = 6;
          $menu_item->save();
        }

        $check_name = $check_table->info('name');
        $select = $check_table->select()
                ->setIntegrityCheck(false)
                ->from($check_name, array('id'))
                ->where('name = ?', 'communityad_admin_graph');
        $queary_info = $select->query()->fetchAll();
        if (empty($queary_info)) {
          $menu_item = $check_table->createRow();
          $menu_item->name = 'communityad_admin_graph';
          $menu_item->module = 'communityad';
          $menu_item->label = 'Graphs Settings';
          $menu_item->plugin = '';
          $menu_item->params = '{"route":"admin_default","module":"communityad","controller":"settings","action":"graph"}';
          $menu_item->menu = 'communityad_admin_main';
          $menu_item->submenu = '';
          $menu_item->order = 7;
          $menu_item->save();
        }

        $check_name = $check_table->info('name');
        $select = $check_table->select()
                ->setIntegrityCheck(false)
                ->from($check_name, array('id'))
                ->where('name = ?', 'communityad_admin_view_advertisment');
        $queary_info = $select->query()->fetchAll();
        if (empty($queary_info)) {
          $menu_item = $check_table->createRow();
          $menu_item->name = 'communityad_admin_view_advertisment';
          $menu_item->module = 'communityad';
          $menu_item->label = 'Manage Advertisements';
          $menu_item->plugin = '';
          $menu_item->params = '{"route":"admin_default","module":"communityad","controller":"viewad","action":"index"}';
          $menu_item->menu = 'communityad_admin_main';
          $menu_item->submenu = '';
          $menu_item->order = 8;
          $menu_item->save();
        }

        $check_name = $check_table->info('name');
        $select = $check_table->select()
                ->setIntegrityCheck(false)
                ->from($check_name, array('id'))
                ->where('name = ?', 'communityad_admin_main_statistics');
        $queary_info = $select->query()->fetchAll();
        if (empty($queary_info)) {
          $menu_item = $check_table->createRow();
          $menu_item->name = 'communityad_admin_main_statistics';
          $menu_item->module = 'communityad';
          $menu_item->label = 'Ad Reports';
          $menu_item->plugin = '';
          $menu_item->params = '{"route":"admin_default","module":"communityad","controller":"statistics","action":"export-report"}';
          $menu_item->menu = 'communityad_admin_main';
          $menu_item->submenu = '';
          $menu_item->order = 9;
          $menu_item->save();
        }

        $check_name = $check_table->info('name');
        $select = $check_table->select()
                ->setIntegrityCheck(false)
                ->from($check_name, array('id'))
                ->where('name = ?', 'communityad_admin_payment_history');
        $queary_info = $select->query()->fetchAll();
        if (empty($queary_info)) {
          $menu_item = $check_table->createRow();
          $menu_item->name = 'communityad_admin_payment_history';
          $menu_item->module = 'communityad';
          $menu_item->label = 'Transactions';
          $menu_item->plugin = '';
          $menu_item->params = '{"route":"admin_default","module":"communityad","controller":"payment","action":"index"}';
          $menu_item->menu = 'communityad_admin_main';
          $menu_item->submenu = '';
          $menu_item->order = 10;
          $menu_item->save();
        }

        $check_name = $check_table->info('name');
        $select = $check_table->select()
                ->setIntegrityCheck(false)
                ->from($check_name, array('id'))
                ->where('name = ?', 'communityad_admin_user_manage');
        $queary_info = $select->query()->fetchAll();
        if (empty($queary_info)) {
          $menu_item = $check_table->createRow();
          $menu_item->name = 'communityad_admin_user_manage';
          $menu_item->module = 'communityad';
          $menu_item->label = 'Manage Help & Learn More';
          $menu_item->plugin = '';
          $menu_item->params = '{"route":"admin_default","module":"communityad","controller":"helps","action":"help-and-learnmore"}';
          $menu_item->menu = 'communityad_admin_main';
          $menu_item->submenu = '';
          $menu_item->order = 11;
          $menu_item->save();
        }

        $check_name = $check_table->info('name');
        $select = $check_table->select()
                ->setIntegrityCheck(false)
                ->from($check_name, array('id'))
                ->where('name = ?', 'communityad_admin_adreports');
        $queary_info = $select->query()->fetchAll();
        if (empty($queary_info)) {
          $menu_item = $check_table->createRow();
          $menu_item->name = 'communityad_admin_adreports';
          $menu_item->module = 'communityad';
          $menu_item->label = 'Abuse Reports';
          $menu_item->plugin = '';
          $menu_item->params = '{"route":"admin_default","module":"communityad","controller":"widgets","action":"adreports"}';
          $menu_item->menu = 'communityad_admin_main';
          $menu_item->submenu = '';
          $menu_item->order = 12;
          $menu_item->save();
        }


        
        $contentTable = Engine_Api::_()->getDbtable('content', 'core');
        $contentTableName = $contentTable->info('name');
        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $pageTableName = $pageTable->info('name');
        $selectPage = $pageTable->select()
                ->from($pageTableName, array('page_id'))
                ->where('name =?', 'user_index_home')
                ->limit(1);
        $fetchPageId = $selectPage->query()->fetchAll();
        if (!empty($fetchPageId)) {
          $pageId = $fetchPageId[0]['page_id'];
          $selectMainContentId = $contentTable->select()
                  ->from($contentTableName, array('content_id'))
                  ->where('page_id =?', $pageId)
                  ->where('type = ?', 'container')
                  ->where('name =?', 'main')
                  ->limit(1);
          $fetchMainContentId = $selectMainContentId->query()->fetchAll();
          if (!empty($fetchMainContentId)) {
            $mainContentId = $fetchMainContentId[0]['content_id'];
            $selectRightContentId = $contentTable->select()
                    ->from($contentTableName, array('content_id'))
                    ->where('page_id =?', $pageId)
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'right')
                    ->where('parent_content_id = ?', $mainContentId)
                    ->limit(1);
            $fetchRightContentId = $selectRightContentId->query()->fetchAll();
            if (!empty($fetchRightContentId)) {
              $rightContentId = $fetchRightContentId[0]['content_id'];
              $selectWidgetId = $contentTable->select()
                      ->from($contentTableName, array('content_id'))
                      ->where('page_id =?', $pageId)
                      ->where('type = ?', 'widget')
                      ->where('name = ?', 'communityad.ads')
                      ->where('parent_content_id = ?', $rightContentId)
                      ->limit(1);
              $fetchRightContentId = $selectWidgetId->query()->fetchAll();
              if (empty($fetchRightContentId)) {
                $contentWidget = $contentTable->createRow();
                $contentWidget->page_id = $pageId;
                $contentWidget->type = 'widget';
                $contentWidget->name = 'communityad.ads';
                $contentWidget->params='{"loaded_by_ajax":"1","show_type":"sponsored","itemCount":"3"}';
                $contentWidget->parent_content_id = $rightContentId;
                $contentWidget->order = 1;
                $contentWidget->save();
 
                $contentWidget = $contentTable->createRow();
                $contentWidget->page_id = $pageId;
                $contentWidget->type = 'widget';
                $contentWidget->name = 'communityad.ads';
                $contentWidget->params='{"loaded_by_ajax":"1","show_type":"featured","itemCount":"3"}';
                $contentWidget->parent_content_id = $rightContentId;
                $contentWidget->order = 3;
                $contentWidget->save();
                
                $contentWidget = $contentTable->createRow();
                $contentWidget->page_id = $pageId;
                $contentWidget->type = 'widget';
                $contentWidget->name = 'communityad.ads';
                $contentWidget->params='{"loaded_by_ajax":"1","show_type":"all","itemCount":"3"}';
                $contentWidget->parent_content_id = $rightContentId;
                $contentWidget->order = 999;
                $contentWidget->save();
                $contentId = $contentWidget->content_id;
              }

              $selectWidgetId = $contentTable->select()
                      ->from($contentTableName, array('content_id'))
                      ->where('page_id =?', $pageId)
                      ->where('type = ?', 'widget')
                      ->where('name = ?', 'communityad.create-ad')
                      ->where('parent_content_id = ?', $rightContentId)
                      ->limit(1);
              $fetchRightContentId = $selectWidgetId->query()->fetchAll();
              if (empty($fetchRightContentId)) {
                $contentWidget = $contentTable->createRow();
                $contentWidget->page_id = $pageId;
                $contentWidget->type = 'widget';
                $contentWidget->name = 'communityad.create-ad';
                $contentWidget->parent_content_id = $rightContentId;
                $contentWidget->order = 4;
                $contentWidget->params = '{"title":"Want more Customers?","titleCount":"true"}';
                $contentWidget->save();
              }


            }
          }
        }


        $selectPage = $pageTable->select()
                ->from($pageTableName, array('page_id'))
                ->where('name =?', 'core_index_index')
                ->limit(1);
        $fetchPageId = $selectPage->query()->fetchAll();
        if (!empty($fetchPageId)) {
          $pageId = $fetchPageId[0]['page_id'];
          $selectMainContentId = $contentTable->select()
                  ->from($contentTableName, array('content_id'))
                  ->where('page_id =?', $pageId)
                  ->where('type = ?', 'container')
                  ->where('name =?', 'main')
                  ->limit(1);
          $fetchMainContentId = $selectMainContentId->query()->fetchAll();
          if (!empty($fetchMainContentId)) {
            $mainContentId = $fetchMainContentId[0]['content_id'];
            $selectLeftContentId = $contentTable->select()
                    ->from($contentTableName, array('content_id'))
                    ->where('page_id =?', $pageId)
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'right')
                    ->where('parent_content_id = ?', $mainContentId)
                    ->limit(1);
            $fetchLeftContentId = $selectLeftContentId->query()->fetchAll();
            if (!empty($fetchLeftContentId)) {
              $leftContentId = $fetchLeftContentId[0]['content_id'];

              $selectWidgetId = $contentTable->select()
                      ->from($contentTableName, array('content_id'))
                      ->where('page_id =?', $pageId)
                      ->where('type = ?', 'widget')
                      ->where('name = ?', 'communityad.ads')
                      ->where('parent_content_id = ?', $leftContentId)
                      ->limit(1);
              $fetchRightContentId = $selectWidgetId->query()->fetchAll();
              if (empty($fetchRightContentId)) {
                $contentWidget = $contentTable->createRow();
                $contentWidget->page_id = $pageId;
                $contentWidget->type = 'widget';
                $contentWidget->name = 'communityad.ads';
                $contentWidget->parent_content_id = $leftContentId;
                $contentWidget->order = 1;
                $contentWidget->save();
                $contentId = $contentWidget->content_id;

              }
            }
          }
        }


        $selectPage = $pageTable->select()
                ->from($pageTableName, array('page_id'))
                ->where('name =?', 'user_profile_index')
                ->limit(1);
        $fetchPageId = $selectPage->query()->fetchAll();
        if (!empty($fetchPageId)) {
          $pageId = $fetchPageId[0]['page_id'];
          $selectMainContentId = $contentTable->select()
                  ->from($contentTableName, array('content_id'))
                  ->where('page_id =?', $pageId)
                  ->where('type = ?', 'container')
                  ->where('name =?', 'main')
                  ->limit(1);
          $fetchMainContentId = $selectMainContentId->query()->fetchAll();
          if (!empty($fetchMainContentId)) {
            $mainContentId = $fetchMainContentId[0]['content_id'];
            $selectLeftContentId = $contentTable->select()
                    ->from($contentTableName, array('content_id'))
                    ->where('page_id =?', $pageId)
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'left')
                    ->where('parent_content_id = ?', $mainContentId)
                    ->limit(1);
            $fetchLeftContentId = $selectLeftContentId->query()->fetchAll();
            if (!empty($fetchLeftContentId)) {
              $leftContentId = $fetchLeftContentId[0]['content_id'];

              $selectWidgetId = $contentTable->select()
                      ->from($contentTableName, array('content_id'))
                      ->where('page_id =?', $pageId)
                      ->where('type = ?', 'widget')
                      ->where('name = ?', 'communityad.ads')
                      ->where('parent_content_id = ?', $leftContentId)
                      ->limit(1);
              $fetchRightContentId = $selectWidgetId->query()->fetchAll();
              if (empty($fetchRightContentId)) {
                $contentWidget = $contentTable->createRow();
                $contentWidget->page_id = $pageId;
                $contentWidget->type = 'widget';
                $contentWidget->name = 'communityad.ads';
                $contentWidget->parent_content_id = $leftContentId;
                $contentWidget->order = 999;
                $contentWidget->save();
                $contentId = $contentWidget->content_id;
              }
            }
          }
        }



// Add widgets when plugin will activate.
//
//	$contentTable = Engine_Api::_()->getDbtable('content', 'core');
//	$contentTableName = $contentTable->info('name');
//	$pageTable = Engine_Api::_()->getDbtable('pages', 'core');
//	$pageTableName = $pageTable->info('name');
//	$selectPage = $pageTable->select()
//					->from($pageTableName, array('page_id'))
//					->where('name =?', 'user_index_home')
//					->limit(1);
//	$fetchPageId = $selectPage->query()->fetchAll();
//	if (!empty($fetchPageId)) {
//		$pageId = $fetchPageId[0]['page_id'];
//		$selectMainContentId = $contentTable->select()
//						->from($contentTableName, array('content_id'))
//						->where('page_id =?', $pageId)
//						->where('type = ?', 'container')
//						->where('name =?', 'main')
//						->limit(1);
//		$fetchMainContentId = $selectMainContentId->query()->fetchAll();
//		if (!empty($fetchMainContentId)) {
//			$mainContentId = $fetchMainContentId[0]['content_id'];
//			$selectRightContentId = $contentTable->select()
//							->from($contentTableName, array('content_id'))
//							->where('page_id =?', $pageId)
//							->where('type = ?', 'container')
//							->where('name = ?', 'right')
//							->where('parent_content_id = ?', $mainContentId)
//							->limit(1);
//			$fetchRightContentId = $selectRightContentId->query()->fetchAll();
//			if (!empty($fetchRightContentId)) {
//				$rightContentId = $fetchRightContentId[0]['content_id'];
//				$selectWidgetId = $contentTable->select()
//								->from($contentTableName, array('content_id'))
//								->where('page_id =?', $pageId)
//								->where('type = ?', 'widget')
//								->where('name = ?', 'communityad.sponsored-stories')
//								->where('parent_content_id = ?', $rightContentId)
//								->limit(1);
//				$fetchRightContentId = $selectWidgetId->query()->fetchAll();
//				if (empty($fetchRightContentId)) {
//					$contentWidget = $contentTable->createRow();
//					$contentWidget->page_id = $pageId;
//					$contentWidget->type = 'widget';
//					$contentWidget->name = 'communityad.sponsored-stories';
//					$contentWidget->parent_content_id = $rightContentId;
//					$contentWidget->order = 999;
//					$contentWidget->save();
//				}
//			}
//		}
//	}