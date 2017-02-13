<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Form_Video extends Engine_Form {

    protected $_parent_type;
    protected $_parent_id;

    public function setParent_type($value) {
        $this->_parent_type = $value;
    }

    public function setParent_id($value) {
        $this->_parent_id = $value;
    }

    protected $_roles;

    public function init() {
        // Init form
        $this
            ->setAttrib('id', 'form-upload')
            ->setAttrib('name', 'video_create')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
        ;
        
        
        
        $user = Engine_Api::_()->user()->getViewer();

        // Init name
        $this->addElement('Text', 'title', array(
            'label' => 'Video Title',
            'maxlength' => '100',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                //new Engine_Filter_HtmlSpecialChars(),
//                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '100')),
            )
        ));

        // init tag
        $this->addElement('Text', 'tags', array(
            'label' => 'Tags (Keywords)',
            'description' => 'Separate tags with commas.',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));
        $this->tags->getDecorator("Description")->setOption("placement", "append");

        // Init descriptions
        $this->addElement('Textarea', 'description', array(
            'label' => 'Video Description',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                //new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
            ),
        ));

        $this->addElement('Select', 'category_id', array(
            'label' => 'Category',
            'onchange' => "updateSubCategories(this.value)",
        ));

        $this->addElement('Select', 'subcategory_id', array('label' => 'Sub Category'));


        // Init search
        $this->addElement('Checkbox', 'search', array(
            'label' => "Show this video in search results",
            'value' => 0,
        ));

        // View

        if ($this->_parent_type == 'group') {
            $this->_roles = array(
                'parent_member' => 'Circle Members',
                'owner' => 'Just Me',
                'registered' => 'All Registered Members',
                'everyone' => 'Everyone',
            );
        } else {
              $this->_roles = array(
                'owner' => 'Just Me',
                'owner_member' => 'Friends Only',
                'owner_member_member' => 'Friends of Friends',
                'owner_network' => 'Friends and Networks',
                'registered' => 'All Registered Members',
                'everyone' => 'Everyone',
            );
        }
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $user, 'auth_view');
        $viewOptions = array_intersect_key($this->_roles, array_flip($viewOptions));

        if (!empty($viewOptions) && count($viewOptions) >= 1) {
            // Make a hidden field
            if (count($viewOptions) == 1) {
                $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_view', array(
                    'label' => 'Privacy',
                    'description' => 'Who may see this video?',
                    'multiOptions' => $viewOptions,
                    'value' => key($viewOptions),
                ));
                $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        // Comment
        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $user, 'auth_comment');
        $commentOptions = array_intersect_key($this->_roles, array_flip($commentOptions));

        if (!empty($commentOptions) && count($commentOptions) >= 1) {
            // Make a hidden field
            if (count($commentOptions) == 1) {
                $this->addElement('hidden', 'auth_comment', array('value' => key($commentOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_comment', array(
                    'label' => 'Comment Privacy',
                    'description' => 'Who may post comments on this video?',
                    'multiOptions' => $commentOptions,
                    'value' => key($commentOptions),
                ));
                $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        $this->addAdditionalElements();

        // Init submit
        $this->addElement('Button', 'upload', array(
            'label' => 'Save Video',
            'type' => 'submit',
        ));

        $this->initValueForElements();
    }

    protected function initValueForElements() {
        
    }

    protected function addAdditionalElements() {
        // Init video
        $this->addElement('Select', 'type', array(
            'label' => 'Video Source',
            'multiOptions' => array('0' => ' '),
            'onchange' => "updateTextFields()",
        ));

        $video_options = Ynvideo_Plugin_Factory::getAllSupportTypes();

        $user = Engine_Api::_()->user()->getViewer();
        //My Computer
        $allowed_upload = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $user, 'upload');
        $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->ynvideo_ffmpeg_path;
        if (!(!empty($ffmpeg_path) && $allowed_upload)) {
            unset($video_options[Ynvideo_Plugin_Factory::getUploadedType()]);
        }
        $this->type->addMultiOptions($video_options);

        //ADD AUTH STUFF HERE
        // Init url
        $this->addElement('Text', 'url', array(
            'label' => 'Video Link (URL)',
            'description' => 'Paste the web address of the video here.',
            'maxlength' => '150'
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

        // Init file
        $fancyUpload = new Engine_Form_Element_FancyUpload('file');
        $fancyUpload->clearDecorators()
            ->addDecorator('FormFancyUpload')
            ->addDecorator('viewScript', array(
                'viewScript' => '_FancyUpload.tpl',
                'placement' => '',
            ));
        Engine_Form::addDefaultDecorators($fancyUpload);
        $this->addElement($fancyUpload);
    }

    public function saveValues() {
        $set_cover = False;
        $values = $this->getValues();
        $params = Array();
        if ((empty($values['owner_type'])) || (empty($values['owner_id']))) {
            $params['owner_id'] = Engine_Api::_()->user()->getViewer()->user_id;
            $params['owner_type'] = 'user';
        } else {
            $params['owner_id'] = $values['owner_id'];
            $params['owner_type'] = $values['owner_type'];
            throw new Zend_Exception("Non-user album owners not yet implemented");
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

        // Add action and attachments
        $api = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $album, 'album_photo_new', null, array('count' => count($values['file'])));

        // Do other stuff
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