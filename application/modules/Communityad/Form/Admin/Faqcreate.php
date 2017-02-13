<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Faqcreate.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Communityad_Form_Admin_Faqcreate extends Engine_Form
{
	public function init() {

		// Conditions: When click on 'edit' from the faq-manage-page for showing prefields for selected ID.
		$faqId = Zend_Controller_Front::getInstance()->getRequest()->getParam('faq_id', null);
		$questionValue = '';
		$answerValue = '';
		$faq_default = 0;
		$faq_type = 0;
		if ( !empty($faqId) ) {
			$faqObj = Engine_Api::_()->getItem('communityad_faq', $faqId);
			$questionValue = $faqObj->question;
			$answerValue = $faqObj->answer;
			$faq_default = $faqObj->faq_default;
			$faq_type = $faqObj->type;
		}

		$this->setTitle('FAQ')
			->setDescription('Write the FAQ.');

		if( empty($faq_default) ) {
			$this->addElement('Textarea', 'faq_question', array(
				'label' => 'Enter Question',
				'description' => "Please enter the 'Question' ?",
				'required' => true,
				'value' => $questionValue
			));

			$this->addElement('Textarea', 'faq_answer', array(
				'label' => 'Enter Answer',
				'description' => "Please enter the 'Answer' for the 'Question' ?",
				'required' => true,
				'value' => $answerValue
			));

			$this->addElement('Button', 'submit', array(
				'label' => 'Post FAQ',
				'type' => 'submit',
				'ignore' => true
			)); 
		}else {
			// Condition: General FAQ.
			if( $faq_type == 1 ) {
				$language_variable = '_communityad_help_generalfaq_';
			}else if( $faq_type == 2 ){
				$language_variable = '_communityad_help_designfaq_';
			}else if( $faq_type == 3 ){
				$language_variable = '_communityad_help_targetingfaq_';
			}
			$faq_qus = $language_variable . $faq_default;
			$faq_ans = $faq_default + 1;
			$message = 'For QUS: ' . $faq_qus . ' and for ANS: ' . $language_variable . $faq_ans;
			$this->addElement('Dummy', 'dummy_msg', array(
				'label' => $message
			));
		}
  }
}
?>