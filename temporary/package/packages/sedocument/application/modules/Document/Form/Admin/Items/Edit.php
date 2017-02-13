<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Document_Form_Admin_Items_Edit extends Document_Form_Admin_Items_Item 
{

  public function init() {

    parent::init();

    $this->setTitle('Edit Dates')
         ->setDescription('Edit the start date and end date below.');

    $this->submit->setLabel('Save Changes');
  }
}