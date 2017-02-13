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
class Zulu_AdminUserFieldsController extends Zulu_Controller_Fields_AdminAbstract {

  protected $_fieldType = 'user';
  protected $_requireProfileType = true;
  protected $_topLevelId = 1;
  protected $_topOptionId = 1;

  public function init() {
    parent::init();
    Engine_Api::_()->zulu()->removeField('grid');
  }

  public function indexAction() {
    parent::indexAction();
  }

  public function fieldCreateAction() {
    parent::fieldCreateAction();

    $this->_addCustomFields();
    if ($this->getRequest()->isPost() && isset($this->view->field)) {
      $this->view->group = $this->getRequest()->getPost('group');
      $this->_cleanMetadataCache();
    }
  }

  public function fieldEditAction() {
    parent::fieldEditAction();

    $this->_addCustomFields();
    if ($this->getRequest()->isPost() && isset($this->view->field)) {
      $this->view->group = $this->getRequest()->getPost('group');
      $this->_cleanMetadataCache();
    }
  }

}
