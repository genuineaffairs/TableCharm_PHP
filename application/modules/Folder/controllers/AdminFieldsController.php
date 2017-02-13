<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Folder
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 

 
class Folder_AdminFieldsController extends Fields_Controller_AdminAbstract
{
  protected $_fieldType = 'folder';

  protected $_requireProfileType = false;

  public function indexAction()
  {
    // Make navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('folder_admin_main', array(), 'folder_admin_main_fields');

    parent::indexAction();
  }

  public function fieldCreateAction()
  {
    parent::fieldCreateAction();

    $this->rebuildDisplaySearchOptions();
  }

  public function fieldEditAction()
  {
    parent::fieldEditAction();

    $this->rebuildDisplaySearchOptions();
  }

  protected function rebuildDisplaySearchOptions()
  {
    $form = $this->view->form;

    if($form){
      //$form->setTitle('Add Folder Question');

      $display = $form->getElement('display');
      $display->setLabel('Show on folder page?');
      $display->setOptions(array('multiOptions' => array(
          1 => 'Show on folder page',
          0 => 'Hide on folder page'
        )));

      $search = $form->getElement('search');
      $search->setLabel('Show on the search options?');
      $search->setOptions(array('multiOptions' => array(
          1 => 'Show on the search options',
          0 => 'Hide on the search options',
        )));
    }
  }
}