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
 
 
 
class Folder_Form_Filter_Manage extends Folder_Form_Search
{
  public function init()
  {
    parent::init();

    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'manage'),'folder_general',true));
  }
}