<?php

class Ynevent_Form_Filter_Browse extends Engine_Form {

	public function init() {
		$this
		->addPrefixPath('Ynevent_Form_Decorator', APPLICATION_PATH . '/application/modules/Ynevent/Form/Decorator', 'decorator')
		->addPrefixPath('Ynevent_Form_Element', APPLICATION_PATH . '/application/modules/Ynevent/Form/Element', 'element')
		->addElementPrefixPath('Ynevent_Form_Decorator', APPLICATION_PATH . '/application/modules/Ynevent/Form/Decorator', 'decorator');
		
		$this -> setAttrib('id', 'ynevent_form_browse_filter');
		 
		$this->clearDecorators()
		->addDecorators(array(
                    'FormElements',
		array('HtmlTag', array('tag' => 'dl')),
                    'Form',
		))
		->setMethod('get')
		->setAttrib('class', 'global_form_box')
		;


		// Category
		$this->addElement('MultiLevel2', 'category_id', array(
	      	'label' => 'Event Category',
	      	 'required'=>false,
	         'model'=>'Ynevent_Model_DbTable_Categories',
	         //'isSearch'=>0,
	         'module'=>'ynevent',
			 
		));

		$this->addElement('Select', 'view', array(
            'label' => 'View:',
            'multiOptions' => array(
                '' => 'Everyone\'s Events',
                '5' => 'My Friends\'s Events',
                '2' => 'Events I\'m Attending',
                '1' => 'Events I may Attending',
                '3' => 'Events I\'m Invited',
                '4' => 'Events I\'m Following',
		),

            //'onchange' => '$(this).getParent("form").submit();',
		));

		$this->addElement('Select', 'order', array(
            'label' => 'List By:',
            'multiOptions' => array(
                'starttime ASC' => 'Start Time',
                'creation_date DESC' => 'Recently Created',
                'member_count DESC' => 'Most Popular',
			),

            'value' => 'creation_date DESC',
            //'onchange' => '$(this).getParent("form").submit();',
		));
		
		// Start time
        $start = new Ynevent_Form_Element_YnCalendarSimple('start_date');
        $start -> setLabel("Start Time");
        $start -> setAllowEmpty(true);
        $this -> addElement($start);

        // End time
        $end = new Ynevent_Form_Element_YnCalendarSimple('end_date');
        $end -> setLabel("End Time");
        $end -> setAllowEmpty(true);
        $this -> addElement($end);
        
        $this->addElement('Text', 'keyword', array(
				'label' => 'Keyword',
				'maxlength' => '60',
				'required' => false,
		));

		$this->addElement('Text', 'address', array(
				'label' => 'Address',
				'maxlength' => '60',
				'required' => false,
		));
		
		$this->addElement ( 'Select', 'country', array (
				'label' => 'Country',
				'multiOptions' => Ynevent_Model_DbTable_Countries::getMapMultiOptions(),
				'value' => '',
				'style' => "width:169px;",
		) );
		
		$this->addElement('Text', 'city', array(
				'label' => 'City',
				'RegisterInArrayValidator' => false,
				'required' => false,
		));

		$this->addElement('Text', 'mile_of', array(
				'label' => 'Mile(s) from Zip/Postal Code',
				'maxlength' => '60',
				'required' => false,
				//'description' => 'mile(s)'
				'validators' => array(
                	array('Int', true),
                	new Engine_Validate_AtLeast(0),
            	),
		));

		//$this->getElement('mile_of')->getDecorator("Description")->setOption("placement", "append");

		$this->addElement('Text', 'zipcode', array(
				'label' => 'Zip/Postal Code',
				'maxlength' => '60',
				'required' => false,
				'validators' => array(
                	array('Int', true),
                	new Engine_Validate_AtLeast(1),
            	),
		));

        // Buttons
		$this->addElement('Button', 'Search', array(
				'label' => 'Search',
				'type' => 'submit',
				//'onclick' => "\$('ynevent_form_browse_filter').submit()",
		));
		
	}

	public function isValid($data) {
		$isValid = parent::isValid($data);
		if ($isValid) {
			if (array_key_exists('start_date', $data)) {
				$startDate = $data['start_date'];
			}
			if (array_key_exists('end_date', $data)) {
				$endDate = $data['end_date'];
			}
			if (!empty($startDate) && !empty($endDate)) {
				$startDate = strtotime($startDate);
				$endDate = strtotime($endDate);
				if ($startDate > $endDate) {
					$isValid = false;
				}
			}
		}
		 
		return $isValid;
	}
}