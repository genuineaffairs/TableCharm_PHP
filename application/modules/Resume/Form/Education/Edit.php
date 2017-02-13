<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Education
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Resume_Form_Education_Edit extends Resume_Form_Education_Create
{
  public $_error = array();

  public function init()
  {
    parent::init();
    
    $this->setTitle('Edit Education')
      ->setDescription('Edit your education below, then click "Save Changes" to save your education.');
  
      
    $this->submit->setLabel('Save Changes');
  }
  
  
}