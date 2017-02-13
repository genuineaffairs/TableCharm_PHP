<?php
$db = Zend_Db_Table_Abstract::getDefaultAdapter();
						$contentTable = Engine_Api::_()->getDbtable('content', 'core');
						$contentTableName = $contentTable->info('name');
						$pageTable = Engine_Api::_()->getDbtable('pages', 'core');
						$pageTableName = $pageTable->info('name');

						// Widgets: Member profile page.
						$selectPage = $pageTable->select()
							->from($pageTableName, array('page_id'))
							->where('name =?', 'user_profile_index')
							->limit(1);
						$fetchPageId = $selectPage->query()->fetchAll();
						if( !empty($fetchPageId) ) {
							$pageId = $fetchPageId[0]['page_id'];
							$selectContainerId = $contentTable->select()
								->from($contentTableName, array('content_id'))					
								->where('page_id =?', $pageId)
								->where('type = ?', 'widget')
								->where('name = ?', 'core.container-tabs')
								->limit(1);
							$fetchContainerId = $selectContainerId->query()->fetchAll();
							if ( !empty($fetchContainerId) ) {
								$containerId = $fetchContainerId[0]['content_id'];								
								$selectWidgetId = $contentTable->select()
									->from($contentTableName, array('content_id'))					
									->where('page_id =?', $pageId)
									->where('type = ?', 'widget')
									->where('name = ?', 'document.profile-documents')
									->where('parent_content_id = ?', $containerId)
									->limit(1);
								$fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
								if ( empty($fetchWidgetContentId) ) {
									$contentWidget = $contentTable->createRow();
									$contentWidget->page_id = $pageId;
									$contentWidget->type = 'widget';
									$contentWidget->name = 'document.profile-documents';
									$contentWidget->parent_content_id = $containerId;
									$contentWidget->order = 6;
									$contentWidget->params = '{"title":"Documents","titleCount":true}';
									$contentWidget->save();
								}
							}
						}


						// Make a Widgitized Page & widgets.
						$selectPage = $pageTable->select()
							->from($pageTableName, array('page_id'))
							->where('name =?', 'document_index_browse')
							->limit(1);
						$fetchPageId = $selectPage->query()->fetchAll();
						if( empty($fetchPageId) ) {
							$pageCreate = $pageTable->createRow();
							$pageCreate->name = 'document_index_browse';
							$pageCreate->displayname = 'Documents Browse Page';
							$pageCreate->title = 'Documents Browse Page';
							$pageCreate->description = 'This is the document browse page.';
							$pageCreate->custom = 0;
							$pageCreate->save();
							$page_id = $pageCreate->page_id;

							// Insert Top Container.
							$topContainer = $contentTable->createRow();
							$topContainer->page_id = $page_id;
							$topContainer->type = 'container';
							$topContainer->name = 'top';
							$topContainer->order = 1;
							$topContainer->save();

							// Insert Top-Middle Container.
							$topMiddleContainer = $contentTable->createRow();
							$topMiddleContainer->page_id = $page_id;
							$topMiddleContainer->type = 'container';
							$topMiddleContainer->name = 'middle';
							$topMiddleContainer->parent_content_id = $topContainer->content_id;
							$topMiddleContainer->order = 6;
							$topMiddleContainer->save();

							// Insert widgets: In Top-Middle Container.
							$topMiddleWidgets = $contentTable->createRow();
							$topMiddleWidgets->page_id = $page_id;
							$topMiddleWidgets->type = 'widget';
							$topMiddleWidgets->name = 'document.navigation-documents';
							$topMiddleWidgets->parent_content_id = $topMiddleContainer->content_id;
							$topMiddleWidgets->order = 1;
							$topMiddleWidgets->params = '{"title":"","titleCount":"true"}';
							$topMiddleWidgets->save();

							// Insert Main Container.
							$mainContainer = $contentTable->createRow();
							$mainContainer->page_id = $page_id;
							$mainContainer->type = 'container';
							$mainContainer->name = 'main';
							$mainContainer->order = 2;
							$mainContainer->save();

							// Insert Main-Right Container.
							$mainRightContainer = $contentTable->createRow();
							$mainRightContainer->page_id = $page_id;
							$mainRightContainer->type = 'container';
							$mainRightContainer->name = 'right';
							$mainRightContainer->parent_content_id = $mainContainer->content_id;
							$mainRightContainer->order = 5;
							$mainRightContainer->save();

							// Insert widgets: In Main Container.
							$topRightWidgets_1 = $contentTable->createRow();
							$topRightWidgets_1->page_id = $page_id;
							$topRightWidgets_1->type = 'widget';
							$topRightWidgets_1->name = 'document.sidebar-categories-documents';
							$topRightWidgets_1->parent_content_id = $mainRightContainer->content_id;
							$topRightWidgets_1->order = 4;
							$topRightWidgets_1->params = '{"title":"Categories","titleCount":"true"}';
							$topRightWidgets_1->save();

							// Insert widgets: In Main Container.
							$topRightWidgets_1 = $contentTable->createRow();
							$topRightWidgets_1->page_id = $page_id;
							$topRightWidgets_1->type = 'widget';
							$topRightWidgets_1->name = 'document.search-documents';
							$topRightWidgets_1->parent_content_id = $mainRightContainer->content_id;
							$topRightWidgets_1->order = 4;
							$topRightWidgets_1->params = '{"title":"","titleCount":"true"}';
							$topRightWidgets_1->save();

							// Insert widgets: In Main Container.
							$topRightWidgets_2 = $contentTable->createRow();
							$topRightWidgets_2->page_id = $page_id;
							$topRightWidgets_2->type = 'widget';
							$topRightWidgets_2->name = 'document.create-documents';
							$topRightWidgets_2->parent_content_id = $mainRightContainer->content_id;
							$topRightWidgets_2->order = 5;
							$topRightWidgets_2->params = '{"title":"","titleCount":"true"}';
							$topRightWidgets_2->save();

							// Insert widgets: In Main Container.
							$topRightWidgets_3 = $contentTable->createRow();
							$topRightWidgets_3->page_id = $page_id;
							$topRightWidgets_3->type = 'widget';
							$topRightWidgets_3->name = 'document.rate-documents';
							$topRightWidgets_3->parent_content_id = $mainRightContainer->content_id;
							$topRightWidgets_3->order = 6;
							$topRightWidgets_3->params = '{"title":"Top Rated Documents","titleCount":"true"}';
							$topRightWidgets_3->save();

							// Insert widgets: In Main Container.
							$topRightWidgets_4 = $contentTable->createRow();
							$topRightWidgets_4->page_id = $page_id;
							$topRightWidgets_4->type = 'widget';
							$topRightWidgets_4->name = 'document.tagcloud-documents';
							$topRightWidgets_4->parent_content_id = $mainRightContainer->content_id;
							$topRightWidgets_4->order = 7;
							$topRightWidgets_4->params = '{"title":"","titleCount":"true"}';
							$topRightWidgets_4->save();

							// Insert widgets: In Main Container.
							$topRightWidgets_5 = $contentTable->createRow();
							$topRightWidgets_5->page_id = $page_id;
							$topRightWidgets_5->type = 'widget';
							$topRightWidgets_5->name = 'document.comment-documents';
							$topRightWidgets_5->parent_content_id = $mainRightContainer->content_id;
							$topRightWidgets_5->order = 8;
							$topRightWidgets_5->params = '{"title":"Most Commented Documents","titleCount":"true"}';
							$topRightWidgets_5->save();

							// Insert widgets: In Main Container.
							$topRightWidgets_6 = $contentTable->createRow();
							$topRightWidgets_6->page_id = $page_id;
							$topRightWidgets_6->type = 'widget';
							$topRightWidgets_6->name = 'document.popular-documents';
							$topRightWidgets_6->parent_content_id = $mainRightContainer->content_id;
							$topRightWidgets_6->order = 9;
							$topRightWidgets_6->params = '{"title":"Popular Documents","titleCount":"true"}';
							$topRightWidgets_6->save();

							// Insert widgets: In Main Container.
							$topRightWidgets_7 = $contentTable->createRow();
							$topRightWidgets_7->page_id = $page_id;
							$topRightWidgets_7->type = 'widget';
							$topRightWidgets_7->name = 'document.recent-documents';
							$topRightWidgets_7->parent_content_id = $mainRightContainer->content_id;
							$topRightWidgets_7->order = 10;
							$topRightWidgets_7->params = '{"title":"Recent Documents","titleCount":"true"}';
							$topRightWidgets_7->save();

							//RECOMMENDED DOCUMENTS WIDGET IF SUGGESTION IS INSTALLED
							$select = new Zend_Db_Select($db);
							$select
								->from('engine4_core_modules')
								->where('name = ?', 'suggestion');
							$is_installed = $select->query()->fetchObject();
							if( !empty($is_installed) ) {
							  $topRightWidgets_8= $contentTable->createRow();
							  $topRightWidgets_8->page_id = $page_id;
							  $topRightWidgets_8->type = 'widget';
							  $topRightWidgets_8->name = 'suggestion.common-suggestion';
							  $topRightWidgets_8->parent_content_id = $mainRightContainer->content_id;
							  $topRightWidgets_8->order = 11;
							  $topRightWidgets_8->params = '{"title":"Recommended Document","resource_type":"document","getWidAjaxEnabled":"1","getWidLimit":"5","nomobile":"0","name":"suggestion.common-suggestion"}';
							  $topRightWidgets_8->save();
							}

							// Insert Main-Middle Container.
							$mainMiddleContainer = $contentTable->createRow();
							$mainMiddleContainer->page_id = $page_id;
							$mainMiddleContainer->type = 'container';
							$mainMiddleContainer->name = 'middle';
							$mainMiddleContainer->parent_content_id = $mainContainer->content_id;
							$mainMiddleContainer->order = 6;
							$mainMiddleContainer->save();

							// Insert widgets: In Main Container.
							$topRightWidgets_1 = $contentTable->createRow();
							$topRightWidgets_1->page_id = $page_id;
							$topRightWidgets_1->type = 'widget';
							$topRightWidgets_1->name = 'document.featured-documents';
							$topRightWidgets_1->parent_content_id = $mainMiddleContainer->content_id;
							$topRightWidgets_1->order = 2;
							$topRightWidgets_1->params = '{"title":"","titleCount":"true"}';
							$topRightWidgets_1->save();

							// Insert widgets: In Main Container.
							$topRightWidgets_2 = $contentTable->createRow();
							$topRightWidgets_2->page_id = $page_id;
							$topRightWidgets_2->type = 'widget';
							$topRightWidgets_2->name = 'document.browse-documents';
							$topRightWidgets_2->parent_content_id = $mainMiddleContainer->content_id;
							$topRightWidgets_2->order = 3;
							$topRightWidgets_2->params = '{"title":"","titleCount":"true"}';
							$topRightWidgets_2->save();
						}


    //START MOBILE DOCUMENT BROWSE PAGE WORK
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'document_index_mobi-browse')
      ->limit(1);
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {
			//CREATE PAGE IF NOT EXIST
      $db->insert('engine4_core_pages', array(
        'name' => 'document_index_mobi-browse',
        'displayname' => 'Mobile Documents Browse Page',
        'title' => 'Mobile Documents Browse Page',
        'description' => 'This is the mobile browse document page.',
        'custom' => 0,
        'layout' => 'default',
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

			//MAIN CONTAINER
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
      ));
      $main_container_id = $db->lastInsertId('engine4_core_content');

			//MIDDLE CONTAINER
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $main_container_id,
        'order' => 2,
        'params' => '',
      ));
      $middle_container_id = $db->lastInsertId('engine4_core_content');

			//MAIN NAVIGATION WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.navigation-documents',
        'parent_content_id' => $middle_container_id,
        'order' => 3,
        'params' => '',
      ));

			//SEARCH FORM WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.search-documents',
        'parent_content_id' => $middle_container_id,
        'order' => 4,
        'params' => '',
      ));

			$total_items = $db->select()
											->from('engine4_core_settings', array('value'))
											->where('name = ?', 'document.browse.widgets')
											->limit(1)
											->query()
											->fetchColumn();
			if(empty($total_items)) {
				$total_items = 10;
			}

			$params = '{"itemCount":"'.$total_items.'"}';

			//BROWSE DOCUMENTS WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.browse-documents',
        'parent_content_id' => $middle_container_id,
        'order' => 5,
        'params' => $params,
      ));
    }
		//END MOBILE DOCUMENT BROWSE PAGE WORK

    //START MOBILE DOCUMENT HOME PAGE WORK
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'document_index_mobi-home')
      ->limit(1);
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {
			//CREATE PAGE IF NOT EXIST
      $db->insert('engine4_core_pages', array(
        'name' => 'document_index_mobi-home',
        'displayname' => 'Mobile Documents Home Page',
        'title' => 'Mobile Documents Home Page',
        'description' => 'This is the mobile document home page.',
        'custom' => 0,
        'layout' => 'default',
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

			//MAIN CONTAINER
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
      ));
      $main_container_id = $db->lastInsertId('engine4_core_content');

			//MIDDLE CONTAINER
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $main_container_id,
        'order' => 2,
        'params' => '',
      ));
      $middle_container_id = $db->lastInsertId('engine4_core_content');

			//MAIN NAVIGATION WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.navigation-documents',
        'parent_content_id' => $middle_container_id,
        'order' => 3,
        'params' => '',
      ));

			//SEARCH FORM WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.search-documents',
        'parent_content_id' => $middle_container_id,
        'order' => 4,
        'params' => '',
      ));

			//HOME DOCUMENTS WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.ajax-home-documents',
        'parent_content_id' => $middle_container_id,
        'order' => 5,
        'params' => '',
      ));
    }
		//END MOBILE DOCUMENT HOME PAGE WORK

    //START MOBILE DOCUMENT VIEW PAGE WORK
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'document_index_mobi-view')
      ->limit(1);
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {
			//CREATE PAGE IF NOT EXIST
      $db->insert('engine4_core_pages', array(
        'name' => 'document_index_mobi-view',
        'displayname' => 'Mobile Document View Page',
        'title' => 'Mobile Document View Page',
        'description' => 'This is the mobile view document page.',
        'custom' => 0,
        'layout' => 'default',
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

			//MAIN CONTAINER
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
      ));
      $main_container_id = $db->lastInsertId('engine4_core_content');

			//MIDDLE CONTAINER
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $main_container_id,
        'order' => 2,
        'params' => '',
      ));
      $middle_container_id = $db->lastInsertId('engine4_core_content');

			//VIEW DOCUMENTS WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.document-view-documents',
        'parent_content_id' => $middle_container_id,
        'order' => 5,
        'params' => '',
      ));
    }
		//END MOBILE DOCUMENT VIEW PAGE WORK

    //START DOCUMENT VIEW PAGE WORK
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'document_index_view')
      ->limit(1);

    $info = $select->query()->fetch();

    if( empty($info) ) {
			//CREATE PAGE IF NOT EXIST
      $db->insert('engine4_core_pages', array(
        'name' => 'document_index_view',
        'displayname' => 'Document View Page',
        'title' => 'Document View Page',
        'description' => 'This is the document view page.',
        'custom' => 0,
        'layout' => 'default',
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

			//MAIN CONTAINER
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 2,
        'params' => '',
      ));
      $main_container_id = $db->lastInsertId('engine4_core_content');

			//RIGHT CONTAINER
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $main_container_id,
        'order' => 5,
        'params' => '',
      ));
			$right_container_id = $db->lastInsertId('engine4_core_content');

			//MAIN MIDDLE CONTAINER
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $main_container_id,
        'order' => 6,
        'params' => '',
      ));
			$main_middle_container_id = $db->lastInsertId('engine4_core_content');

			//DOCUMENT VIEWER WINDOW WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.document-view-documents',
        'parent_content_id' => $main_middle_container_id,
        'order' => 3,
        'params' => '',
      ));

			//DOCUMENT OWNER PHOTO WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.document-owner-photo-documents',
        'parent_content_id' => $right_container_id,
        'order' => 5,
        'params' => '',
      ));

			//DOCUMENT OPTIONS WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.options-documents',
        'parent_content_id' => $right_container_id,
        'order' => 6,
        'params' => '',
      ));

			//SEARCH BOX WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.search-box-documents',
        'parent_content_id' => $right_container_id,
        'order' => 7,
        'params' => '{"title":"Search Documents","titleCount":"true"}',
      ));

			//SAME USER DOCUMENTS WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.document-owner-documents',
        'parent_content_id' => $right_container_id,
        'order' => 8,
        'params' => '',
      ));

			$social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"document.socialshare-documents"}';

			//SOCIAL SHARE BUTTONS WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.socialshare-documents',
        'parent_content_id' => $right_container_id,
        'order' => 9,
				'params' => $social_share_default_code,
      ));

			//TAG CLOUD WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.tagcloud-documents',
        'parent_content_id' => $right_container_id,
        'order' => 11,
        'params' => '',
      ));

			//ARCHIVES WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.archives-documents',
        'parent_content_id' => $right_container_id,
        'order' => 12,
        'params' => '{"title":"Archives","titleCount":"true"}',
      ));

		}
		//END DOCUMENT VIEW PAGE WORK

    //START DOCUMENT HOME PAGE WORK
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'document_index_home')
      ->limit(1);

    $info = $select->query()->fetch();

    if( empty($info) ) {
			//CREATE PAGE IF NOT EXIST
      $db->insert('engine4_core_pages', array(
        'name' => 'document_index_home',
        'displayname' => 'Documents Home Page',
        'title' => 'Documents Home Page',
        'description' => 'This is the document home page.',
        'custom' => 0,
        'layout' => 'default',
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

			//TOP CONTAINER
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
      ));
      $top_container_id = $db->lastInsertId('engine4_core_content');

			//MAIN CONTAINER
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 2,
        'params' => '',
      ));
      $main_container_id = $db->lastInsertId('engine4_core_content');

			//LEFT CONTAINER
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'left',
        'parent_content_id' => $main_container_id,
        'order' => 4,
        'params' => '',
      ));
			$left_container_id = $db->lastInsertId('engine4_core_content');

			//RIGHT CONTAINER
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $main_container_id,
        'order' => 5,
        'params' => '',
      ));
			$right_container_id = $db->lastInsertId('engine4_core_content');

			//TOP MIDDLE CONTAINER
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $top_container_id,
        'order' => 6,
        'params' => '',
      ));
			$top_middle_container_id = $db->lastInsertId('engine4_core_content');

			//MAIN MIDDLE CONTAINER
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $main_container_id,
        'order' => 6,
        'params' => '',
      ));
			$main_middle_container_id = $db->lastInsertId('engine4_core_content');

			//MAIN NAVIGATION WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.navigation-documents',
        'parent_content_id' => $top_middle_container_id,
        'order' => 3,
        'params' => '',
      ));

			//DOCUMENT OF THE DAY WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.day-item-document',
        'parent_content_id' => $left_container_id,
        'order' => 6,
        'params' => '{"title":"Document of the Day","titleCount":"true"}',
      ));

			//TOP RATED DOCUMENTS WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.rate-documents',
        'parent_content_id' => $left_container_id,
        'order' => 7,
        'params' => '{"title":"Top Rated Documents","titleCount":"true"}',
      ));

			//MOST LIKED DOCUMENTS WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.like-documents',
        'parent_content_id' => $left_container_id,
        'order' => 8,
        'params' => '{"title":"Most Liked Documents","titleCount":"true"}',
      ));

			//MOST LIKED DOCUMENTS WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.popular-documents',
        'parent_content_id' => $left_container_id,
        'order' => 8,
        'params' => '{"title":"Popular Documents","titleCount":"true"}',
      ));

      //RECOMMENDED DOCUMENTS WIDGET IF SUGGESTION IS INSTALLED
      $select = new Zend_Db_Select($db);
      $select
	      ->from('engine4_core_modules')
	      ->where('name = ?', 'suggestion');
      $is_installed = $select->query()->fetchObject();
      if( !empty($is_installed) ) {
	      $db->insert('engine4_core_content', array(
		      'page_id' => $page_id,
		      'type' => 'widget',
		      'name' => 'suggestion.common-suggestion',
		      'parent_content_id' => $left_container_id,
		      'order' => 9,
		      'params' => '{"title":"Recommended Document","resource_type":"document","getWidAjaxEnabled":"1","getWidLimit":"5","nomobile":"0","name":"suggestion.common-suggestion"}',
	      ));
      }

			//ZERO DOCUMENTS MESSAGE WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.zero-documents',
        'parent_content_id' => $main_middle_container_id,
        'order' => 10,
        'params' => '',
      ));

			//FEATURED SLIDESHOW DOCUMENTS WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.slideshow-featured-documents',
        'parent_content_id' => $main_middle_container_id,
        'order' => 11,
        'params' => '{"title":"Featured Documents","titleCount":"true"}',
      ));

			//CATEGORIES, 2nd LEVEL CATEGORIES AND 3rd CATEGORIES LIST WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.middle-column-categories-documents',
        'parent_content_id' => $main_middle_container_id,
        'order' => 12,
        'params' => '{"title":"Categories","titleCount":"true"}',
      ));

			//CATEGORIZED DOCUMENTS WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.categorized-documents',
        'parent_content_id' => $main_middle_container_id,
        'order' => 13,
        'params' => '{"title":"Categorically Popular Documents","titleCount":"true"}',
      ));

			//AJAX BASED DOCUMENTS WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.ajax-home-documents',
        'parent_content_id' => $main_middle_container_id,
        'order' => 14,
        'params' => '',
      ));

			//SEARCH FORM WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.search-documents',
        'parent_content_id' => $right_container_id,
        'order' => 15,
        'params' => '',
      ));

			//SPONSORED CAROUSEL WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.sponsored-documents',
        'parent_content_id' => $right_container_id,
        'order' => 16,
        'params' => '{"title":"Sponsored Documents","titleCount":"true"}',
      ));

			//TAG CLOUD WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.tagcloud-documents',
        'parent_content_id' => $right_container_id,
        'order' => 17,
        'params' => '',
      ));

			//MOST COMMENTED DOCUMENTS WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.comment-documents',
        'parent_content_id' => $right_container_id,
        'order' => 18,
        'params' => '{"title":"Most Commented Documents","titleCount":"true"}',
      ));

			//MOST RECENT DOCUMENTS WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'document.recent-documents',
        'parent_content_id' => $right_container_id,
        'order' => 19,
        'params' => '{"title":"Recent Documents","titleCount":"true"}',
      ));
    }
    //END DOCUMENT HOME PAGE WORK

		//START PROFILE DOC WIDGET WORK
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages', array('page_id'))
      ->where('name = ?', 'user_profile_index')
      ->limit(1);
    $page = $select->query()->fetch();

		//IF USER PROFILE PAGE IS EXIST
		if(!empty($page)) {
			$page_id = $page['page_id'];
			if(!empty($page_id)) {

				$select = new Zend_Db_Select($db);
				$select
					->from('engine4_core_content', array('content_id'))
					->where('page_id = ?', $page_id)
					->where('type = ?', 'widget')
					->where('name = ?', 'core.container-tabs')
					->limit(1);
				$container = $select->query()->fetch();

				//IF DESIRABLE CONTAINER IS EXIST
				if(!empty($container)) {
					$container_id = $container['content_id'];
					if(!empty($container_id)) {
						$select = new Zend_Db_Select($db);
						$select
								->from('engine4_core_content', array('content_id'))					
								->where('page_id =?', $page_id)
								->where('type = ?', 'widget')
								->where('name = ?', 'document.profile-doc-documents')
								->where('parent_content_id = ?', $container_id)
								->limit(1);
						$profileDocWidget = $select->query()->fetch();

						//IF PROFILE DOC IS WIDGET IS NOT THERE
						if(empty($profileDocWidget)) {

								$social_share_default_code = '{"title":"Profile Document","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"document.profile-doc-documents"}';

							$db->insert('engine4_core_content', array(
								'page_id' => $page_id,
								'type' => 'widget',
								'name' => 'document.profile-doc-documents',
								'parent_content_id' => $container_id,
								'order' => 999,
								'params' => $social_share_default_code,
							));
						}
					}
				}
			}
		}
		//END PROFILE DOC WIDGET WORK