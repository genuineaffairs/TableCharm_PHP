<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Resume_video
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Video.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Resume_Form_Video_Create extends Engine_Form {

  public function init() {
    
    $resume_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('resume_id', null);
    $tab_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $url = $view->item('resume', $resume_id)->getHref(array('tab'=>$tab_id));

    $this
            ->setTitle('Add New Video')
            ->setDescription('Add a new video in this profile using the form below.')
            ->setAttrib('id', 'form-upload')
            ->setAttrib('name', 'video_create')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $this->addElement('Text', 'title', array(
        'label' => 'Video Title',
        'maxlength' => '100',
        'allowEmpty' => false,
        'required' => true,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '100')),
        )
    ));

    $this->addElement('Text', 'tags', array(
        'label' => 'Tags (Keywords)',
        'autocomplete' => 'off',
        'description' => 'Separate tags with commas.',
        'filters' => array(
            new Engine_Filter_Censor(),
        )
    ));
    $this->tags->getDecorator("Description")->setOption("placement", "append");

    $this->addElement('Textarea', 'description', array(
        'label' => 'Description',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_EnableLinks(),
        ),
    ));

    $this->addElement('Checkbox', 'search', array(
        'label' => "Show this video in search results.",
        'value' => 1,
    ));

    $this->addElement('Select', 'type', array(
        'label' => 'Video Source',
        'multiOptions' => array('0' => ' '),
        'onchange' => "updateTextFields()",
    ));

    $video_options = Array();
    $video_options[1] = "YouTube";
    $video_options[2] = "Vimeo";

    $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->sitepagevideo_ffmpeg_path;
    $mobile = false;
    if (isset($_SERVER['HTTP_USER_AGENT']) &&  (preg_match('/' . 'iPad|Nexus|GT-P1000|SGH-T849|SHW-M180S|Kindle|Silk' . '/i', $_SERVER['HTTP_USER_AGENT'])||preg_match('/' . 'iPhone' . '/i', $_SERVER['HTTP_USER_AGENT'])) || Engine_Api::_()->seaocore()->isMobile()) {

    $mobile=true;
    }
    if (!empty($ffmpeg_path) && !$mobile) {
      $video_options[3] = "My Computer";
    }
    $this->type->addMultiOptions($video_options);

    $this->addElement('Text', 'url', array(
        'label' => 'Video Link (URL)',
        'description' => 'Paste the web address of the video here.',
        'maxlength' => '500'
    ));
    $this->url->getDecorator("Description")->setOption("placement", "append");

    $this->addElement('Hidden', 'code', array(
        'order' => 1
    ));
    $this->addElement('Hidden', 'id', array(
        'order' => 2
    ));
    $this->addElement('Hidden', 'ignore', array(
        'order' => 3
    ));

    $fancyUpload = new Engine_Form_Element_FancyUpload('file');
    $fancyUpload->clearDecorators()
            ->addDecorator('FormFancyUpload')
            ->addDecorator('viewScript', array(
                'viewScript' => '_FancyUploadVideo.tpl',
                'placement' => '',
            ));
    Engine_Form::addDefaultDecorators($fancyUpload);
    $this->addElement($fancyUpload);

    $this->addElement('Button', 'upload', array(
        'label' => 'Save Video',
        'type' => 'submit',
    ));
    
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'Go Back',
        'link' => true,
//        'prependText' => ' or ',
        'href' => $url,
        'decorators' => array(
            'ViewHelper',
        ),
    ));
  }

  public function clearAlbum() {
    $this->getElement('album')->setValue(0);
  }

  public function saveValues() {

    $set_cover = false;

    //GET VALUES FROM FORM
    $values = $this->getValues();

    $params = Array();
    if ((empty($values['owner_id']))) {
      $params['owner_id'] = Engine_Api::_()->user()->getViewer()->user_id;
    } else {
      $params['owner_id'] = $values['owner_id'];
      throw new Zend_Exception("Non-user album owners not yet implemented.");
    }

    if (($values['album'] == 0)) {
      $params['name'] = $values['name'];
      if (empty($params['name'])) {
        $params['name'] = "Untitled Album";
      }
      $params['description'] = $values['description'];
      $params['search'] = $values['search'];
      $album = Engine_Api::_()->getDbtable('albums', 'album')->createRow();
      $set_cover = True;
      $album->setFromArray($params);
      $album->save();
    } else {
      if (is_null($album)) {
        $album = Engine_Api::_()->getItem('album', $values['album']);
      }
    }

    //ADD ACTIVITY AND ATTACHMENTS
    $api = Engine_Api::_()->getDbtable('actions', 'activity');
    $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $album, 'album_photo_new', null, array('count' => count($values['file'])));

    $count = 0;
    foreach ($values['file'] as $photo_id) {
      $photo = Engine_Api::_()->getItem("album_photo", $photo_id);
      if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity())
        continue;

      if ($set_cover) {
        $album->photo_id = $photo_id;
        $album->save();
        $set_cover = false;
      }

      $photo->collection_id = $album->album_id;
      $photo->save();

      if ($action instanceof Activity_Model_Action && $count < 8) {
        $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
      }
      $count++;
    }
    return $album;
  }

}
?>