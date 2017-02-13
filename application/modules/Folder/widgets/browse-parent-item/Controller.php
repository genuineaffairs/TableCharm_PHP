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
 
 
 
class Folder_Widget_BrowseParentItemController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();

    if (!empty($params['parent']))
    {
      try
      {
        $p = Engine_Api::_()->getItemByGuid($params['parent']);
        if ($p instanceof Core_Model_Item_Abstract && $p->getIdentity() > 0)
        {
          $this->view->parentObject = $p;
          return;
        }
      }
      catch (Exception $e)
      {
        // silence
      }
    }
    return $this->setNoRender();
  }

}