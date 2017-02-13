<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Resume_Form_Sportinghistory_Edit extends Resume_Form_Sportinghistory_Create
{
  public $_error = array();

  public function init()
  {
    parent::init();
    
    $this->setTitle('Edit History')
      ->setDescription('Edit your history below, then click "Save Changes" to save your history.');
  
      
    $this->submit->setLabel('Save Changes');
  }
  
  
}