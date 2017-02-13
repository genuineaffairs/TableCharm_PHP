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
class Resume_Form_Resume_Style extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Resume Styles')
      ->setDescription('You can change the colors, fonts, and styles of your resume by adding CSS code below. The contents of the text area below will be output between <style> tags on your resume.')
      ->setAttrib('class', 'global_form resumes_style');
    ;

    //$this->removeDecorator('FormWrapper');

    $this->addElement('Textarea', 'style', array(
      'label' => 'Custom Resume Styles',
    	'attribs' => array(
    		'cols' => 140
    	)
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit'), 'buttons');
  }
}