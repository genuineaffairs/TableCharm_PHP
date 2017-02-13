<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
class Zulu_AdminFieldsController extends Zulu_Controller_Fields_AdminAbstract {

  protected $_fieldType = 'zulu';
  protected $_requireProfileType = false;
  protected $_formTitle = 'Edit Clinical Question';

  public function indexAction() {
    parent::indexAction();
  }
  
  public function init() {
    parent::init();
    Zend_Registry::get('Zend_View')->getPluginLoader('helper')->removePrefixPath('Fields_View_Helper_');
  }

  public function fieldCreateAction() {
    parent::fieldCreateAction();

    $this->rebuildDisplaySearchOptions();

    $this->_addCustomFields();
    if ($this->getRequest()->isPost() && isset($this->view->field) /* && $this->getRequest()->getPost('group') */) {
      $this->view->group = $this->getRequest()->getPost('group');
//            $field = Engine_Api::_()->fields()->getFieldsMeta($this->_fieldType)->getRowMatching('field_id', $this->view->field['field_id']);
//            Engine_Api::_()->zulu()->changeFieldOrder($field, $this->getRequest()->getPost('group'));
      
      // Re-render all maps that have this field as a parent or child
      $maps = array_merge(
        Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $this->view->field['field_id']),
        Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $this->view->field['field_id'])
      );
      $html = $this->view->htmlArr;
      foreach ($maps as $map) {
        if ($map->getChild()->type == 'grid') {
          $html[$map->getKey()] = $this->view->zuluAdminGridFieldMeta($map);
        }
      }
      $this->view->htmlArr = $html;
      $this->_cleanMetadataCache();
    }
  }

  public function fieldEditAction() {
    parent::fieldEditAction();

    $this->rebuildDisplaySearchOptions();

    $this->_addCustomFields();
    if ($this->getRequest()->isPost() && isset($this->view->field) /* && $this->getRequest()->getPost('group') */) {
      $this->view->group = $this->getRequest()->getPost('group');
//            $field = Engine_Api::_()->fields()->getFieldsMeta($this->_fieldType)->getRowMatching('field_id', $this->view->field['field_id']);
//            Engine_Api::_()->zulu()->changeFieldOrder($field, $this->getRequest()->getPost('group'));
      
      // Re-render all maps that have this field as a parent or child
      $maps = array_merge(
        Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $this->view->field['field_id']),
        Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $this->view->field['field_id'])
      );
      $html = $this->view->htmlArr;
      foreach ($maps as $map) {
        if ($map->getChild()->type == 'grid') {
          $html[$map->getKey()] = $this->view->zuluAdminGridFieldMeta($map);
        }
      }
      $this->view->htmlArr = $html;
      $this->_cleanMetadataCache();
    }
  }
  
  public function headingEditAction() {
    parent::headingEditAction();
    
    $this->_addCustomFields();
    if ($this->getRequest()->isPost() && isset($this->view->field) /* && $this->getRequest()->getPost('group') */) {
      $this->view->group = $this->getRequest()->getPost('group');
//            $field = Engine_Api::_()->fields()->getFieldsMeta($this->_fieldType)->getRowMatching('field_id', $this->view->field['field_id']);
//            Engine_Api::_()->zulu()->changeFieldOrder($field, $this->getRequest()->getPost('group'));
      
      // Re-render all maps that have this field as a parent or child
      $maps = array_merge(
        Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $this->view->field['field_id']),
        Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $this->view->field['field_id'])
      );
      $html = $this->view->htmlArr;
      foreach ($maps as $map) {
        if ($map->getChild()->type == 'grid') {
          $html[$map->getKey()] = $this->view->zuluAdminGridFieldMeta($map);
        }
      }
      $this->view->htmlArr = $html;
      $this->_cleanMetadataCache();
    }
  }

  public function mapDeleteAction() {
    parent::mapDeleteAction();

    $field_id = $this->_getParam('child_id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->delete('engine4_zulu_fields_xhtml', array(
        'field_id = ?' => $field_id
    ));
  }

}
