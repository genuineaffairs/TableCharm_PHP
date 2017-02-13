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

class Resume_Form_Admin_Epayment_Create extends Epayment_Form_Admin_Manage_Create
{
  
  protected $_type = 'resume';
  
  public function init()
  {
    parent::init();
    
    $this->resource_id->setLabel('Resume ID');
  }
}