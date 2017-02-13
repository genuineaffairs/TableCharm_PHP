<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Fields.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Zulu_Form_Edit_ClinicalFields extends Zulu_Form_Common_ClinicalFields_Abstract {

  public function init() {
    // Init form
    $this->setTitle('Clinical Information')->setAttrib('id', 'EditClinical');

    parent::init();
  }

  public function generate() {
    // Check if user has Medical Record
    $hasMedicalRecord = !empty($this->getItem()->zulu_id);

    parent::generate();

    if ($hasMedicalRecord) {
      $zulu = $this->getItem();
      $user = $zulu->getOwner();

      //-- Add user information to the top of medical record
      $orderIndex = -9998;
      $responsiveClass = 'col-md-12';
      // Custom HTML element
      // Open custom block
      $noteName = 'custom_block_open';
      $note = new Zulu_Form_Element_Note(
              $noteName, array(
          'value' => '<div class="row profile_form">'
          . '<div class="' . $responsiveClass . '"><div class="inner_wrapper clinical_blocks">',
          'order' => $orderIndex++,
      ));
      $this->addElement($note)->getElement($noteName)->removeDecorator('Label')->removeDecorator('HtmlTag');
      // Owner heading
      $noteName = 'owner_info_block_heading';
      $note = new Zulu_Form_Element_Note(
              $noteName, array(
          'value' => '<div class="form-wrapper-heading mainheading-form-element">OWNER OF MEDICAL RECORD</div>',
          'order' => $orderIndex++,
      ));
      $this->addElement($note)->getElement($noteName)->removeDecorator('Label')->removeDecorator('HtmlTag');
      // Owner info body
      $noteName = 'owner_info_block_body';
      $note = new Zulu_Form_Element_Note($noteName, array(
          'value' => '<div class="custom_block_body owner_info_body">'
          . '<p><span>Full Name</span><span>' . $user->getTitle() . '</span></p>'
          . '<p><span>Date of Birth</span><span>' . $zulu->getUserFieldValueString('birthdate') . '</span></p>'
          . '</div>',
          'order' => $orderIndex++,
      ));
      $this->addElement($note)->getElement($noteName)->removeDecorator('Label')->removeDecorator('HtmlTag');
      // Close custom block
      $noteName = 'custom_block_close';
      $note = new Zulu_Form_Element_Note(
              $noteName, array(
          'value' => '</div></div></div>',
          'order' => $orderIndex++,
      ));
      $this->addElement($note)->getElement($noteName)->removeDecorator('Label')->removeDecorator('HtmlTag');
      //-- Add user information to the top of medical record
    }

    // User's href
    $href = Engine_Api::_()->core()->getSubject('user')->getHref();

    if ($hasMedicalRecord) {
      // TOP print button
      $noteName = 'print_button_top';
      $note = new Zulu_Form_Element_Note(
              $noteName, array(
          'value' => $this->getView()->htmlLink($href . '/print', 'Print Medical Record', array(
              'class' => 'buttonlink icon_zulu_print',
              'alt' => 'Print Medical Record',
              'target' => '_blank'
          )),
          'order' => -9999,
      ));
      $this->addElement($note);
      $this->getElement($noteName)->removeDecorator('Label')->removeDecorator('HtmlTag');

      // BOTTOM print button
      $noteName = 'print_button_bottom';
      $note = new Zulu_Form_Element_Note(
              $noteName, array(
          'value' => $this->getView()->htmlLink($href . '/print', 'Print Medical Record', array(
              'class' => 'buttonlink icon_zulu_print',
              'alt' => 'Print Medical Record',
              'target' => '_blank',
          )),
          'order' => 10000,
      ));
      $this->addElement($note);
      $this->getElement($noteName)->removeDecorator('Label')->removeDecorator('HtmlTag');
    }

    $this->addElement('Button', 'save', array(
        'label' => 'Save',
        'type' => 'submit',
        'order' => 10001,
    ));
    $this->getElement('save')->removeDecorator('DivDivDivWrapper');

    if ($hasMedicalRecord) {
      // Get tab id of Medical Record in view profile page
      $db = Engine_Db_Table::getDefaultAdapter();
      $tab_id = $db->select()
              ->from('engine4_core_content', 'content_id')
              ->where('`name` = ?', 'zulu.clinical-fields')
              ->query()
              ->fetchColumn();

      // View Medical Record button
      $noteName = 'view';
      $note = new Zulu_Form_Element_Note(
              $noteName, array(
          'value' => $this->getView()->htmlLink($href . '/tab/' . $tab_id, 'View Medical Record', array(
              'class' => 'link_button',
              'alt' => 'View Medical Record',
              'target' => '_blank',
          )),
          'order' => -10000,
      ));
      $this->addElement($note);
      $this->getElement($noteName)->removeDecorator('Label')->removeDecorator('HtmlTag');

      $this->addDisplayGroup(array('view'), 'top_buttons', array(
          'order' => -10000
      ));
    }

    $this->addDisplayGroup(array('save'), 'bottom_buttons', array(
        'order' => 10001
    ));
  }

}
