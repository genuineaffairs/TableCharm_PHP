<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagelikebox
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Filter.php 2011-10-10 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagelikebox_Form_Admin_Manage_Filter extends Engine_Form {

  public function init() {

    $this->setAttribs(array(
			'id' => 'filter_form',
			'class' => 'global_form_box',
		));

		//ADD FOR BORDER COLOR.
    $this->addElement('Text', 'sitepagelikebox_color', array(
			'decorators' => array(array('ViewScript', array(
				'viewScript' => '_formColorCode.tpl',
				'class' => 'form element'
			)))
    ));

    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
  }
}
?>