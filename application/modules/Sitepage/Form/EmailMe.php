<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: EmailMe.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_EmailMe extends Engine_Form {

  public $_error = array();

  public function init() {
  
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id); 
    $PageTitle = $sitepage->title;
    
    $this->setTitle('Email Me')
					->setDescription('Please fill the form given below to contact this Page.')
					->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
					->setAttrib('name', 'sitepages_create');
            
    $viewr_name = "";
    $viewr_email = "";
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($viewer->getIdentity() > 0) {
      $viewr_name = $viewer->getTitle();
      $viewr_email = $viewer->email;
    }
    // TITLE
    $this->addElement('Text', 'sitepage_sender_name', array(
        'label' => 'Your Name *',
        'allowEmpty' => false,
        'required' => true,
        'value' => $viewr_name,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '63')),
            )));

    //SENDER EMAIL
    $this->addElement('Text', 'sitepage_sender_email', array(
        'label' => 'Your Email *',
        'allowEmpty' => false,
        'required' => true,
        'value' => $viewr_email,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '63')),
            )));

    // RECIVER EMAILS
//     $this->addElement('Text', 'sitepage_reciver_emails', array(
//         'label' => 'To *',
//         'allowEmpty' => false,
//         'required' => true,
//         'description' => 'Separate multiple addresses with commas.',
//         'filters' => array(
//             new Engine_Filter_Censor(),
//         ),
//     ));
    
    
 		$text_value  = Zend_Registry::get('Zend_Translate')->_('Thought you would be interested in this.');
//     $this->sitepage_reciver_emails->getDecorator("Description")->setOption("placement", "append");

    $photo = 'http://' . $_SERVER['HTTP_HOST'] . $sitepage->getPhotoUrl('thumb.icon'); 
    $photo = "<img src='$photo' style='  float: left;height: 35px;margin-right: 5px;width: 35px;' /></img>";

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $title = $photo . "<a href='" . $view->url(array('page_url' => $sitepage->page_url), 'sitepage_entry_view') . "'  target='_blank'>" . ucfirst($sitepage->getTitle()) . "</a>";
    $this->addElement('Dummy', 'sitepage_reciver_emails', array(
        'label' => 'To *',
        'description' => $title,
    ));
    $this->getElement('sitepage_reciver_emails')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));



    // MESSAGE
    $this->addElement('textarea', 'sitepage_message', array(
        'label' => 'Message *',
        'required' => true,
        'allowEmpty' => false,
        'attribs' => array('rows' => 24, 'cols' => 150, 'style' => 'width:230px; max-width:400px;height:120px;'),
        'value' => $text_value,
        'description' => 'You can send a personal note in the mail.',
        'filters' => array(
            'StripTags',
            new Engine_Filter_HtmlSpecialChars(),
            new Engine_Filter_EnableLinks(),
            new Engine_Filter_Censor(),
        ),
    ));
    $this->sitepage_message->getDecorator("Description")->setOption("placement", "append");
    // SEND COPY TO ME
    $this->addElement('Checkbox', 'sitepage_send_me', array(
        'label' => "Send a copy to my email address.",
    ));
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.captcha.post', 1) && empty($viewer_id)) {
      $this->addElement('captcha', 'captcha', array(
          'description' => 'Please type the characters you see in the image.',
          'captcha' => 'image',
          'required' => true,
          'captchaOptions' => array(
              'wordLen' => 6,
              'fontSize' => '30',
              'timeout' => 300,
              'imgDir' => APPLICATION_PATH . '/public/temporary/',
              'imgUrl' => $this->getView()->baseUrl() . '/public/temporary',
              'font' => APPLICATION_PATH . '/application/modules/Core/externals/fonts/arial.ttf'
              )));
      $this->captcha->getDecorator("Description")->setOption("placement", "append");
    }

    // Element: SEND
    $this->addElement('Button', 'sitepage_send', array(
        'label' => 'Send',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));
    // Element: cancel
    $this->addElement('Cancel', 'sitepage_cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
       // 'href' => 'history(-2)'
       //'onclick' => 'history.go(-1); return false;',
       'onclick' => 'javascript:parent.Smoothbox.close()',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array(
        'sitepage_send',
        'sitepage_cancel',
            ), 'sitepage_buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));
  }

}

?>