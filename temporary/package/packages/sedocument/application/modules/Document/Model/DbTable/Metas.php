<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Metas.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Document_Model_DbTable_Metas extends Engine_Db_Table {

  protected $_name = 'document_fields_meta';
  protected $_rowClass = 'Document_Model_Meta';


  /**
   * Get Default Profile Id
	 *
   */
	public function defaultProfileId() {

		//GET DEFAULT PROFILE ID
		$defaultProfileId = $this->select()
										->from($this->info('name'), array('field_id'))
										->where('type = ?', 'profile_type')
										->where('alias = ?', 'profile_type')
										->query()
										->fetchColumn();

		//RETURN DEFAULT PROFILE ID
		return $defaultProfileId;
	}
}