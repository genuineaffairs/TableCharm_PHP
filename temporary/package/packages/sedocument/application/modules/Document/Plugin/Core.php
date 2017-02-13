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
class Document_Plugin_Core extends Zend_Controller_Plugin_Abstract 
{
	//MOBILE PAGES WORK
  public function routeShutdown(Zend_Controller_Request_Abstract $request) {

		//IF MOBILE MODULE IS NOT ENABLED THEN RETURN
    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('mobi'))
      return;

    //CHECK IF ADMIN
    if (substr($request->getPathInfo(), 1, 5) == "admin") {
      return;
    }

    $mobile = $request->getParam("mobile");
    $session = new Zend_Session_Namespace('mobile');

    if ($mobile == "1") {
      $mobile = true;
      $session->mobile = true;
    } elseif ($mobile == "0") {
      $mobile = false;
      $session->mobile = false;
    } else {
      if (isset($session->mobile)) {
        $mobile = $session->mobile;
      } else {
        //CHECK TO SEE IF MOBILE
        if (Engine_Api::_()->mobi()->isMobile()) {
          $mobile = true;
          $session->mobile = true;
        } else {
          $mobile = false;
          $session->mobile = false;
        }
      }
    }

    if (!$mobile) {
      return;
    }
    $module = $request->getModuleName();
    $controller = $request->getControllerName();
    $action = $request->getActionName();

		//MOBILE BROWSE DOCUMENT PAGE
		if ($module == "document" && $controller == "index" && $action == "browse") {		
			$request->setActionName('mobi-browse');
		}
	
		//MOBILE DOCUMENT HOME PAGE
		if ($module == "document" && $controller == "index" && $action == "home") {		
			$request->setActionName('mobi-home');
		}

		//MOBILE DOCUMENT HOME PAGE
		if ($module == "document" && $controller == "index" && $action == "view") {		
			$request->setActionName('mobi-view');
		}

    //CREATE LAYOUT
    $layout = Zend_Layout::startMvc();

    //SET OPTIONS
    $layout->setViewBasePath(APPLICATION_PATH . "/application/modules/Mobi/layouts", 'Core_Layout_View')
            ->setViewSuffix('tpl')
            ->setLayout(null);
  }

	//SHOW DETAILS ON STATISTICS
  public function onStatistics($event)
  {
		//FETCH NUMBER OF DOCUMENTS
    $total_rows = Engine_Api::_()->getDbTable('documents', 'document')->onStatisticsData();

		if($total_rows == 1)
			$event->addResponse($total_rows, 'document');
		else
			$event->addResponse($total_rows, 'documents');
  }

  //DELETE USERS BELONGINGS BEFORE THAT USER DELETION
  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    
    if( $payload instanceof User_Model_User ) {

			//FETCH OWNER DOCUMETNS
			$owner_id = $payload->getIdentity();
			$documents = Engine_Api::_()->getDbtable('documents', 'document')->getOwnerDocuments($owner_id);

      foreach($documents as $document ) {

				//DELETE DOCUMENT BELONGINGS
				Engine_Api::_()->document()->deleteContent($document->document_id);
      }
    }
  }
}