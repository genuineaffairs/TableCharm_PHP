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

class Communityad_Widget_GetconnectionLinkController extends Engine_Content_Widget_Abstract 
{
  public function indexAction() 
  {
		if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

		$enable_ads = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
		if(!$enable_ads) {
			return $this->setNoRender();
		}

		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();
		
		$module_subject = Engine_Api::_()->core()->getSubject();

		if(empty($module_subject)) {
			return $this->setNoRender();
		}
		$item_type = $module_subject->getType();
		if($item_type == 'album_photo') {
			return $this->setNoRender();
		}

		$module_name = $module_subject->getModuleName();
	  $module_type = strtolower($module_name);
    
		$module_type_id = $module_subject->getIdentity();

		$ismoduleads_enabled = Engine_Api::_()->getDbtable('modules', 'communityad')->ismoduleads_enabled($module_type);
		if(!$ismoduleads_enabled) {
			return $this->setNoRender();
		}
    
    if(!empty($module_type) && ($module_type === "sitereview")){
      $tableObj = Engine_Api::_()->getDbtable('modules', 'communityad');
      $tableName = $tableObj->info('name');
      
      $select = $tableObj->select()->from($tableName, array('module_id'))->where('table_name =?', 'sitereview_listing_' . $module_subject->listing_id);
      $moduleId = $select->query()->fetchColumn();
      if(!empty($moduleId))
        $module_type = 'sitereview_' . $moduleId;
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
		$this->view->info = $info;
		$this->view->module_type = $module_type;
		$this->view->module_type_id = $module_type_id;
	}
}
?>