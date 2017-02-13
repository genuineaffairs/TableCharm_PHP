  <?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: adreview.tpl  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Communityad_Widget_PageadPreviewController extends Engine_Content_Widget_Abstract 
{
  public function indexAction() 
  {
		$enable_ads = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
		if(!$enable_ads) {
			return $this->setNoRender();
		}

		if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();
		
		$this->view->module_subject = $module_subject = Engine_Api::_()->core()->getSubject();

		if(empty($module_subject)) {
			return $this->setNoRender();
		}
		$item_type = $module_subject->getType();
		if($item_type == 'album_photo') {
			return $this->setNoRender();
		}

		$module_name = $module_subject->getModuleName();
                $temp_module_name = $module_type = strtolower($module_name);
                if( $module_type == 'sitereview' )
                    $temp_module_name = 'sitereview_' . $module_subject->listingtype_id;
                
		$module_type_id = $module_subject->getIdentity();

		$ismoduleads_enabled = Engine_Api::_()->getDbtable('modules', 'communityad')->ismoduleads_enabled($module_type);
		if(!$ismoduleads_enabled) {
			return $this->setNoRender();
		}

		$useradsTable = Engine_Api::_()->getDbtable('userads', 'communityad');
    $useradsName = $useradsTable->info('name');

		$select = $useradsTable->select();
    $select
        ->from($useradsName, array('userad_id'))
				->where('resource_type = ?', $module_type)
				->where('resource_id = ?', $module_type_id)
        ->limit(1);
		$ad_exist = $useradsTable->fetchRow($select);
		if(!empty($ad_exist)) {
			return $this->setNoRender();
		}
		$info = Engine_Api::_()->getDbtable('modules', 'communityad')->getModuleInfo($module_type);

		if($module_name == 'Sitepage') {

			//START MANAGE-ADMIN CHECK
			$isManageAdmin = Engine_Api::_()->sitepage()->isPageOwner($module_subject);
			if(!$isManageAdmin) {
				return $this->setNoRender();
			}
		}
		else {
			$owner_id = $module_subject->getOwner()->getIdentity();
			if($owner_id != $viewer_id) {
				return $this->setNoRender();
			}
		}

    $this->view->get_title = $viewer->getTitle();
    
    if (!empty($module_subject)) {
			$this->view->module_name = $module_name;
			$this->view->module_type = $temp_module_name; //$module_type;
			$this->view->module_type_id = $module_type_id;
			$this->view->info = $info;
			$this->view->createWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.image.width', 120);
			$this->view->createHeight = Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.image.hight', 90);
    }
	}
}
?>