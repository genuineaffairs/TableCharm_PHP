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

class Resume_Widget_CategoriesController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {   
    $this->view->showphoto = $this->_getParam('showphoto', 1);
    $this->view->descriptionlength = $this->_getParam('descriptionlength', 68);
    $this->view->display_style = $this->_getParam('display_style', 'narrow');
    
    $this->view->categories = $categories = Engine_Api::_()->getItemTable('resume_category')->getParentChildrenAssoc();
    
    if (empty($categories))
    {
      return $this->setNoRender();
    }
  }

}