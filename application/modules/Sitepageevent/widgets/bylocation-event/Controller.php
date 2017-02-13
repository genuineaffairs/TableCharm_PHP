<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:20:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_Widget_BylocationEventController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    
    // Make form
    $this->view->form = $form = new Sitepageevent_Form_Locationsearch();

		if(!empty($_POST)) {
			$this->view->is_ajax = $_POST['is_ajax'];
		}
		
	  if(empty($_POST['sitepage_location'])) {
			$this->view->locationVariable = '1';
		}

    if (empty($_POST['is_ajax'])) {
			$p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
			$form->isValid($p);
			$values = $form->getValues();
			$this->view->is_ajax = $this->_getParam( 'is_ajax', 0 );
    } else {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
			$values = $_POST;
			$form->populate($values);
      $values = array_merge($values, $form->getValues()); 
    }

    $contentTable = Engine_Api::_()->getDbtable('content', 'core');
		$content = $contentTable->select()
			->from($contentTable->info('name'), array('params'))
			->where('name =?', 'sitepageevent.bylocation-event')
			->query()
			->fetchColumn();
    $result  = Zend_Json::decode($content);
    //$values['order_by'] = 0; //$result['order_by'];

//     $viewer = Engine_Api::_()->user()->getViewer();
//     if (!empty($values['view_view']) && $values['view_view'] == 1) {
// 
//       //GET AN ARRAY OF FRIEND IDS
//       $friends = $viewer->membership()->getMembers();
// 
//       $ids = array();
//       foreach ($friends as $friend) {
//         $ids[] = $friend->user_id;
//       }
//       $values['users'] = $ids;
//     }

    unset($values['or']);
    $this->view->assign($values); 

    $this->view->current_page = $page = $this->_getParam( 'page' , 1 ) ;
    $this->view->current_totalpages = $page * 15 ;

    $result = Engine_Api::_()->sitepageevent()->getSitepageEventsSelect($values);
   	$this->view->paginator = $paginator = Zend_Paginator::factory($result);
    $paginator->setItemCountPerPage(15);
    $this->view->paginator = $paginator->setCurrentPageNumber($page);

    $this->view->mobile = Engine_Api::_()->seaocore()->isMobile();

// 		if(!empty($_POST['is_ajax'])) {
// 			//For show location marker.
// 			if (!empty($checkLocation) && $paginator->getTotalItemCount() > 0) {
// 				$ids = array();
 				//$data = array();

				
// 	  foreach ($paginator as $value) {
//       $content_array = array();
//       $content_array['latitude'] = $value->latitude;
//       $content_array['longitude'] = $value->latitude;
//       $content_array['location'] = $value->location;
//       $data[] = $content_array;
//     }
//     $this->view->locations = $data;
// 				$values['page_ids'] = $ids;
// 				$this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'seaocore')->getLocation($values);
// 				$sitepage_lat = array();
// 				$sitepage_long = array();
// 				foreach ($locations as $location) {
// 					$sitepage_lat[] = $location->latitude;
// 					$sitepage_long[] = $location->longitude;
// 				}
// 				$this->view->diff_lat = max($sitepage_long) - max($sitepage_lat);
// 
// 				foreach ($locations as $location) {
// 					if ($sitepage_temp[$location->location_id]->sponsored) {
// 						break;
// 					}
// 				}
// 				$this->view->sitepage = $sitepage_temp;
// 			}
//     }
//     if($this->_getParam('is_ajax', 0) == 0) {
// 			// Render
// 			$this->_helper->content->setEnabled();
//     }
  }
}