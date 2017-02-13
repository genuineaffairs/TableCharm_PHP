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

class Folder_Widget_CategoriesController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {       
    $this->view->categories = $categories = Engine_Api::_()->folder()->getCategories();
    
    if (empty($categories))
    {
      return $this->setNoRender();
    }
    
    $this->view->showphoto = $this->_getParam('showphoto', 1);
    $this->view->showdescription = $this->_getParam('showdescription');
    $this->view->display_style = $this->_getParam('display_style', 'narrow');    
    
  }

}