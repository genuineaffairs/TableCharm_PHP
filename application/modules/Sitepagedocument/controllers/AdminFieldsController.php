<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminFieldsController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagedocument_AdminFieldsController extends Fields_Controller_AdminAbstract {

  protected $_fieldType = 'sitepagedocument_document';
  protected $_requireProfileType = false;

  //ACTION FOR SHOW CUSTOM FIELDS
  public function indexAction() {

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sitepagedocument_admin_main', array(), 'sitepagedocument_admin_main_fields');

    parent::indexAction();
  }

  //ACTION FOR CREATE CUSTOM FIELDS
  public function fieldCreateAction() {

    parent::fieldCreateAction();

    //REMOVE STUFF ONLY RELAVENT TO PROFILE QUESTIONS
    $form = $this->view->form;

    if ($form) {
      $form->setTitle('Add Page Document Question');

      $form->removeElement('show');

      $display = $form->getElement('display');
      $display->setLabel('Show on document page?');
      $display->setOptions(array('multiOptions' => array(
              1 => 'Show on document page',
              0 => 'Hide on document page'
              )));

      //$search = $form->getElement('search');
      $form->addElement('hidden', 'search', array(
          'label' => 'Show on the search options?',
          'multiOptions' => array(
              1 => 'Hide on the search options',
              0 => 'Show on the search options'
          ),
          'value' => 1,
      ));
    }
  }

  //ACTION FOR EDIT CUSTOM FIELDS
  public function fieldEditAction() {

    parent::fieldEditAction();

    //REMOVE STUFF ONLY RELAVENT TO PROFILE QUESTIONS
    $form = $this->view->form;

    if ($form) {
      $form->setTitle('Edit Page Document Question');

      $form->removeElement('show');

      $display = $form->getElement('display');
      $display->setLabel('Show on document page?');
      $display->setOptions(array('multiOptions' => array(
              1 => 'Show on document page',
              0 => 'Hide on document page'
              )));

      //$search = $form->getElement('search');
      $form->addElement('hidden', 'search', array(
          'label' => 'Show on the search options?',
          'multiOptions' => array(
              1 => 'Hide on the search options',
              0 => 'Show on the search options'
          ),
          'value' => 1,
      ));
    }
  }

}
?>