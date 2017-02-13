<?php
class Advgroup_Form_Create extends Engine_Form
{
  public function init()
  {
    $user = Engine_Api::_()->user()->getViewer();

    $this
      ->setTitle('Create New Group');

    $this->addElement('Text', 'title', array(
      'label' => 'Group Name',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 64)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));

    $this->addElement('Text', 'tags',array(
          'label'=>'Tags (Keywords)',
          'autocomplete' => 'off',
          'description' => 'Separate tags with commas.',
          'filters' => array(
            new Engine_Filter_Censor(),
          ),
        ));
    $this->tags->getDecorator("Description")->setOption("placement", "append");

    $allowed_html = 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, object , param, iframe';
    $this->addElement('Textarea', 'description', array(
      'label' => 'Description',
      'editorOptions' => array(
         'bbcode' => 1,
          'html'   => 1,
          'theme_advanced_buttons1' => array(
              'undo', 'redo', 'cleanup', 'removeformat', 'pasteword', '|',
              'media', 'image','link', 'unlink', 'fullscreen', 'preview', 'emotions'
          ),
          'theme_advanced_buttons2' => array(
              'fontselect', 'fontsizeselect', 'bold', 'italic', 'underline',
              'strikethrough', 'forecolor', 'backcolor', '|', 'justifyleft',
              'justifycenter', 'justifyright', 'justifyfull', '|', 'outdent', 'indent', 'blockquote',
          ),
        ),
      'required'   => true,
      'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_Html(array('AllowedTags'=>$allowed_html)))
    ));

    $this->addElement('File', 'photo', array(
      'label' => 'Profile Photo'
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');

    $this->addElement('Select', 'category_id', array(
      'label' => 'Category',
      'multiOptions' => array(
        '0' => ' '
      ),
    ));

    $this->addElement('Radio', 'search', array(
      'label' => 'Include in search results?',
      'multiOptions' => array(
        '1' => 'Yes, include in search results.',
        '0' => 'No, hide from search results.',
      ),
      'value' => '1',
    ));

    $this->addElement('Radio', 'auth_invite', array(
      'label' => 'Let members invite others?',
      'multiOptions' => array(
        'member' => 'Yes, members can invite other people.',
        'officer' => 'No, only officers can invite other people.',
      ),
      'value' => 'member',
    ));

    $this->addElement('Radio', 'approval', array(
      'label' => 'Approve members?',
      'description' => ' When people try to join this group, should they be allowed '.
        'to join immediately, or should they be forced to wait for approval?',
      'multiOptions' => array(
        '0' => 'New members can join immediately.',
        '1' => 'New members must be approved.',
      ),
      'value' => '1',
    ));

     // Add subforms
    if( !$this->_item ) {
      $customFields = new Advgroup_Form_Custom_Fields();
    } else {
      $customFields = new Advgroup_Form_Custom_Fields(array(
        'item' => $this->getItem()
      ));
    }
    if( get_class($this) == 'Advgroup_Form_Create' ) {
      $customFields->setIsCreation(true);
    }

    $this->addSubForms(array(
      'fields' => $customFields
    ));

    // Privacy
    $availableLabels = array(
      'everyone' => 'Everyone',
      'member' => 'All Group Members',
      'registered' => 'Registered Members',
      'officer' => 'Officers and Owner Only',
      'owner' => 'Owner Only',
    );


    // View
    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_view');
    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));

    if( !empty($viewOptions) && count($viewOptions) >= 1 ) {
      // Make a hidden field
      if(count($viewOptions) == 1) {
        $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_view', array(
            'label' => 'View Privacy',
            'description' => 'Who may see this group?',
            'multiOptions' => $viewOptions,
            'value' => key($viewOptions),
        ));
        $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    // Comment
    $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_comment');
    $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

    if( !empty($commentOptions) && count($commentOptions) >= 1 ) {
      // Make a hidden field
      if(count($commentOptions) == 1) {
        $this->addElement('hidden', 'auth_comment', array('value' => key($commentOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_comment', array(
            'label' => 'Comment Privacy',
            'description' => 'Who may post on this group\'s wall?',
            'multiOptions' => $commentOptions,
            'value' => key($commentOptions),
        ));
        $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    // Photo
    $photoOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_photo');
    $photoOptions = array_intersect_key($availableLabels, array_flip($photoOptions));

    if( !empty($photoOptions) && count($photoOptions) >= 1 ) {
      // Make a hidden field
      if(count($photoOptions) == 1) {
        $this->addElement('hidden', 'auth_photo', array('value' => key($photoOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_photo', array(
            'label' => 'Photo - Album Creation',
            'description' => 'Who may upload photos/albums to this group?',
            'multiOptions' => $photoOptions,
            'value' => key($photoOptions),
        ));
        $this->auth_photo->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    // Event
    $eventOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_event');
    $eventOptions = array_intersect_key($availableLabels, array_flip($eventOptions));

    if( !empty($eventOptions) && count($eventOptions) >= 1 ) {
      // Make a hidden field
      if(count($eventOptions) == 1) {
        $this->addElement('hidden', 'auth_event', array('value' => key($eventOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_event', array(
            'label' => 'Event Creation',
           'description' => 'Who may create events for this group?',
            'multiOptions' => $eventOptions,
            'value' => key($eventOptions),
        ));
        $this->auth_event->getDecorator('Description')->setOption('placement', 'append');
      }
    }
    //Poll
    $pollOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_poll');
    $pollOptions = array_intersect_key($availableLabels, array_flip($pollOptions));

    if( !empty($pollOptions) && count($pollOptions) >= 1 ) {
      // Make a hidden field
      if(count($pollOptions) == 1) {
        $this->addElement('hidden', 'auth_poll', array('value' => key($pollOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_poll', array(
            'label' => 'Poll Creation',
           'description' => 'Who may create polls for this group?',
            'multiOptions' => $pollOptions,
            'value' => key($pollOptions),
        ));
        $this->auth_poll->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    //Sub-Group
    $subGroupOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_sub_group');
    $subGroupOptions = array_intersect_key($availableLabels, array_flip($subGroupOptions));

    if( !empty($subGroupOptions) && count($subGroupOptions) >= 1 ) {
      // Make a hidden field
      if(count($subGroupOptions) == 1) {
        $this->addElement('hidden', 'auth_sub_group', array('value' => key($subGroupOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_sub_group', array(
            'label' => 'Sub Group Creation',
           'description' => 'Who may create sub group for this group?',
            'multiOptions' => $subGroupOptions,
            'value' => key($subGroupOptions),
        ));
        $this->auth_sub_group->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    //Video
    $videoOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_video');
    $videoOptions = array_intersect_key($availableLabels, array_flip($videoOptions));

    if( !empty($videoOptions) && count($videoOptions) >= 1 ) {
      // Make a hidden field
      if(count($videoOptions) == 1) {
        $this->addElement('hidden', 'auth_video', array('value' => key($videoOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_video', array(
            'label' => 'Video Creation',
            'description' => 'Who may create videos for this group?',
            'multiOptions' => $videoOptions,
            'value' => key($videoOptions),
        ));
        $this->auth_video->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    //Wiki
    $wikiOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_wiki');
    $wikiOptions = array_intersect_key($availableLabels, array_flip($wikiOptions));

    if( !empty($wikiOptions) && count($wikiOptions) >= 1 ) {
      // Make a hidden field
      if(count($wikiOptions) == 1) {
        $this->addElement('hidden', 'auth_wiki', array('value' => key($wikiOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_wiki', array(
            'label' => 'Wiki Page Creation',
           'description' => 'Who may create wiki pages for this group?',
            'multiOptions' => $wikiOptions,
            'value' => key($wikiOptions),
        ));
        $this->auth_wiki->getDecorator('Description')->setOption('placement', 'append');
      }
    }
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'onclick' => 'removeSubmit()',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}