<?php
class Advgroup_Form_Search extends Engine_Form
{
  public function init()
  {
     //Set Form Layout And Attributes.
    $this
      ->setAttribs(array( 'id' => 'filter_form',
                          'class' => 'global_form_box',
                           'method' => 'GET'
                    ));

      //Page Id Field.
    $this->addElement('Hidden','page',array(
        'order' => 100,
    ));

    $this->addElement('Hidden','tag',array(
        'order' => 101,
    ));
      //Search Text Field.
    $this->addElement('Text', 'text', array(
      'label' => 'Search Circles:'
    ));
    
      //Category Field.
    $categories = Engine_Api::_()->getDbtable('categories', 'advgroup')->getAllCategoriesAssoc();
    if(count($categories) >= 1 ) {
      $this->addElement('Select', 'category_id', array(
        'label' => 'Category:',
        'multiOptions' => $categories,
        'onchange' => '$(this).getParent("form").submit();',
      ));
    }
      //View Field.
    $this->addElement('Select', 'view', array(
      'label' => 'View:',
      'multiOptions' => array(
        '0' => 'Everyone\'s Circles',
        '1' => 'Only My Friends\' Circles',
      ),
      'onchange' => '$(this).getParent("form").submit();',
    ));

      //Order Field
    $this->addElement('Select', 'order', array(
      'label' => 'List By:',
      'multiOptions' => array(
        'creation_date' => 'Recently Created',
        'member_count' => 'Most Popular',
        'most_active' => 'Most Active',
      ),
      'value' => 'creation_date',
      'onchange' => '$(this).getParent("form").submit();',
    ));
    
  }
}