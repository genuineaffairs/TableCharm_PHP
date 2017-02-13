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
 
 
 
class Folder_Widget_PopularTagsController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
  	
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $max = $this->_getParam('max', 100);
    $order = $this->_getParam('order', 'text');
    
    $this->view->tags = $tags = Engine_Api::_()->folder()->getPopularTags(array('limit' => $max, 'order' => $order));
    
    if (empty($tags))
    {
      return $this->setNoRender();
    } 
    
    $this->view->showlinkall = $this->_getParam('showlinkall');
  }

}