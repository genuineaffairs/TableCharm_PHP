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
 
 
 
class Resume_Form_Employment_Edit extends Resume_Form_Employment_Create
{
  public $_error = array();

  public function init()
  {
    parent::init();
    
    $this->setTitle('Edit Employment')
      ->setDescription('Edit your employment below, then click "Save Changes" to save your employment.');
  
      
    $this->submit->setLabel('Save Changes');
  }
  
  
}