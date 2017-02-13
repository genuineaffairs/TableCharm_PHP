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
 
 
 
class Resume_Form_Admin_Section_Edit extends Resume_Form_Admin_Section_Create
{

  public function init()
  {
    parent::init();
    $this->setTitle('Edit Section')
         ->setDescription('Please fill out the form below to update section.');

    $this->submit->setLabel('Save Changes');
  }
  
}