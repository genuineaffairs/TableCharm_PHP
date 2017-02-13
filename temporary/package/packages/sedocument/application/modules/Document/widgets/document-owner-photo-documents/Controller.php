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

class Document_Widget_DocumentOwnerPhotoDocumentsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  { 
		//DON'T RENDER IF SUBJECT IS NOT SET
		if(!Engine_Api::_()->core()->hasSubject()) {
			return $this->setNoRender();
		}

		//GET SUBJECT
		$document = Engine_Api::_()->core()->getSubject();

		//GET DOCUMENT OWNER
		$this->view->owner = $document->getOwner();
	}  		
}
?>