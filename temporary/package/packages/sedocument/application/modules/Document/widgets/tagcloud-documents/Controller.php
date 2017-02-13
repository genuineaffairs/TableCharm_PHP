<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Document_Widget_TagcloudDocumentsController extends Engine_Content_Widget_Abstract
{ 
  public function indexAction()
  { 
		//GET ACTION DETAILS
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$action = '';
		if (!empty($request)) {
			$module = $request->getModuleName();
			$controller = $request->getControllerName();
			$action = $request->getActionName();
		}

		if($module == 'document' && $controller == 'index' && $action == 'view') {

			//DON'T RENDER IF SUBJECT IS NOT SET
			if(!Engine_Api::_()->core()->hasSubject()) {
				return $this->setNoRender();
			}
			
			//GET SUBJECT
			$document = Engine_Api::_()->core()->getSubject();
	
			//GET OWNER INFORMATION
			$this->view->owner_id = $owner_id = $document->owner_id;
			$this->view->owner = $document->getOwner();
		}
		elseif($module == 'document' && $controller == 'index' && ($action == 'browse' || $action == 'home')) {
			$this->view->owner_id = $owner_id = 0;
		}	
		else {
			return $this->setNoRender();
		}

		//HOW MANY TAGS WE HAVE TO SHOW
		$total_tags = $this->_getParam('itemCount', 100);

  	//CONSTRUCTING TAG CLOUD
		$tag_array = array();
		$this->view->count_only = Engine_Api::_()->document()->getTags($owner_id, 0, 1);
		if($this->view->count_only <= 0) {
			return $this->setNoRender();
		}

		//FETCH TAGS
		$tag_cloud_array = Engine_Api::_()->document()->getTags($owner_id, $total_tags);
		
		foreach($tag_cloud_array as $vales)	{
			$tag_array[$vales['text']] = $vales['Frequency'];		
			$tag_id_array[$vales['text']] = $vales['tag_id'];	
		}
    
		if(!empty($tag_array)) {
			$max_font_size = 18;
			$min_font_size = 12;
			$max_frequency = max(array_values($tag_array));
			$min_frequency = min(array_values($tag_array));
			$spread = $max_frequency - $min_frequency;
			if($spread == 0) {
				$spread = 1;
			}
			$step = ($max_font_size - $min_font_size) / ($spread);
			
			$tag_data = array('min_font_size' => $min_font_size, 'max_font_size' => $max_font_size, 'max_frequency' => $max_frequency, 'min_frequency' => $min_frequency, 'step' => $step);
			$this->view->tag_data = $tag_data;
			$this->view->tag_id_array = $tag_id_array;
		}
		$this->view->tag_array = $tag_array;

		if(empty($this->view->tag_array)) {
			return $this->setNoRender();
		}
  }
}
?>