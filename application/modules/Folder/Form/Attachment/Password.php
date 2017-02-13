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
class Folder_Form_Attachment_Password extends Folder_Form_Folder_Password
{

  public function init()
  {
    parent::init();

    $this->setTitle('Password Protected File')
      ->setDescription("In order to download this file, please enter secret code below");

    $this->submit->setLabel('Unlock File');

  }

  
}