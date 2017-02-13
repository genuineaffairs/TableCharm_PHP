<?php

abstract class Zulu_Form_Common_ClinicalFields_Abstract extends Fields_Form_Standard {

  protected $_fieldType = 'zulu';
  // This is used to mark header name for breaking Field List into several steps
  protected $_nextStopField = null;
  protected $_startField = null;

//  protected $_emergencyFields = array(
//      'international travel insurance',
//      'next of kin',
//      'blood type',
//      'medications',
//      'allergies',
//      'overseas travel',
//      'immunisations'
//  );

  public function init()
  {
    // Init form
    $this->setTitle('Medical Record');
    $this->addPrefixPath('Zulu_Form_Element', APPLICATION_PATH . '/application/modules/Zulu/Form/Element', 'element');

    if (!$this->_item) {
      $this->setItem(new Zulu_Model_Zulu(array()));
    }

    parent::init();
  }

  public function generate()
  {

    /**
     * Remove all fields before adding (in case generate function run twice)
     */
    $this->clearElements();

    $struct = $this->getFieldStructure();

    $orderIndex = 1;

    $responsiveClass = 'col-md-12';

    // Custom HTML element
    $noteName = 'custom_element' . $orderIndex;
    $note = new Zulu_Form_Element_Note(
            $noteName, array(
        'value' => '<div class="row profile_form">',
        'order' => $orderIndex++,
    ));
    $this->addElement($note)->getElement($noteName)->removeDecorator('Label')->removeDecorator('HtmlTag');

    $startPoint = false;

    $blockCount = 0;

    $latest_top_field_id = (int) $this->_topLevelId;

    foreach ($struct as $fskey => $map) {

      // Top level condition
      $isTopLevel = $map->field_id === (int) $this->_topLevelId && $map->option_id === (int) $this->_topLevelValue;

      // If fields are not on top level, open child fields wrapper
      if (!$isTopLevel && $latest_top_field_id !== $map->field_id) {
        $noteName = 'custom_element' . $orderIndex;
        $note = new Zulu_Form_Element_Note(
                $noteName, array(
            'value' => '<div class="zulu_child_fields_wrapper">',
            'order' => $orderIndex++,
        ));
        $this->addElement($note)->getElement($noteName)->removeDecorator('Label')->removeDecorator('HtmlTag');

        // Store the latest top field id
        $latest_top_field_id = $map->field_id;
      }

      // Close wrapper field
      if ($latest_top_field_id !== (int) $this->_topLevelId && $latest_top_field_id !== $map->field_id) {
        $noteName = 'custom_element' . $orderIndex;
        $note = new Zulu_Form_Element_Note(
                $noteName, array(
            'value' => '</div>',
            'order' => $orderIndex++,
        ));
        $this->addElement($note)->getElement($noteName)->removeDecorator('Label')->removeDecorator('HtmlTag');

        $latest_top_field_id = (int) $this->_topLevelId;
      }

      $field = $map->getChild();
      $add_class = '';

      if ($field->type === 'grid') {
        $add_class = 'zulu-grid-table grid-edit-table ';

        $db = Engine_Db_Table::getDefaultAdapter();

        $data = $db->select()
                        ->from('engine4_zulu_fields_xhtml')
                        ->where('field_id = ?', $field->field_id)
                        ->query()->fetch();

        if ($data === false) {
          continue;
        }
      }

      // Skip fields hidden on signup
      if (isset($field->show) && !$field->show && $this->_isCreation) {
        continue;
      }

      // Add field and load options if necessary
      $params = $field->getElementParams($this->getItem());

      //$key = 'field_' . $field->field_id;
      $key = $map->getKey();

      // If value set in processed values, set in element
      if (!empty($this->_processedValues[$field->field_id])) {
        $params['options']['value'] = $this->_processedValues[$field->field_id];
      }

      if (!@is_array($params['options']['attribs'])) {
        $params['options']['attribs'] = array();
      }

      // Heading
      if ($params['type'] == 'Heading') {
        $params['options']['value'] = Zend_Registry::get('Zend_Translate')->_($params['options']['label']);
        unset($params['options']['label']);
      }

      // Order
      // @todo this might cause problems, however it will prevent multiple orders causing elements to not show up
      if ($field->isHeading()) {
        // Get value of plus first, in order to reserve place for Block-Break-HTML
        $params['options']['order'] = ++$orderIndex;
      } else {
        $params['options']['order'] = $orderIndex++;
      }

      $inflectedType = Engine_Api::_()->fields()->inflectFieldType($params['type']);
      unset($params['options']['alias']);
      unset($params['options']['publish']);

      // @todo skip fields until reaching start point
      if ($startPoint === false) {
        if (!is_null($this->_startField) && !preg_match('/^' . $this->_startField . '/i', $params['options']['value'])) {
          continue;
        } else {
          $startPoint = true;
        }
      }

      // @todo stop displaying remain parts when reaching stop point
      if (!is_null($this->_nextStopField) && preg_match('/^' . $this->_nextStopField . '/i', $params['options']['value'])) {
        break;
      }

      $this->addElement($inflectedType, $key, $params['options']);

      $element = $this->getElement($key);

      // Remove element label if field label equals '_' character
      if ($field->label === '_') {
        $element->removeDecorator('Label');
      }

      if (method_exists($element, 'setFieldMeta')) {
        $element->setFieldMeta($field);
      }

      // Set attributes for hiding/showing fields using javscript
      $classes = $add_class . 'field_container field_' . $map->child_id . ' option_' . $map->option_id . ' parent_' . $map->field_id;
      $element->setAttrib('class', $classes);

      //
      if ($field->canHaveDependents()) {
        $element->setAttrib('onchange', 'changeFields(this)');
      }

      // Set custom error message
      if ($field->error) {
        $element->addErrorMessage($field->error);
      }

      if ($field->type === 'file') {
        $field_id = $field->field_id;

        $this->$key->setItem($this->getItem());

        $description = $this->$key->getDescription();

        if ($description) {
          $this->$key->getDecorator('Description')->setEscape(false);
          $description .= "<br />";
        }

        $filesize = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.filesize', 2048);
        $description .= Zend_Registry::get('Zend_Translate')->_('Browse and choose a file for your document. Maximum permissible size: %s KB and allowed file types: pdf, txt, ps, rtf, epub, odt, odp, ods, odg, odf, sxw, sxc, sxi, sxd, doc, ppt, pps, xls, docx, pptx, ppsx, xlsx, tif, tiff');
        $description = sprintf($description, $filesize);

        /* @var $field Zulu_Form_Element_File */
        $this->$key
                ->addValidator('Extension', false, 'pdf, txt, ps, rtf, epub, odt, odp, ods, odg, odf, sxw, sxc, sxi, sxd, doc, ppt, pps, xls, docx, pptx, ppsx, xlsx, tif, tiff')
                ->addValidator('Size', false, 2 * 1048576) // 2 MB
                ->setDescription($description)
                ->getDecorator('HtmlTag2')->setOption('class', 'form-wrapper zulu-file-wrapper');

        $fileUrl = $this->getItem()->getFileUrl($field_id);

        if ($fileUrl !== null) {
          $noteName = $this->$key->getName() . '_link';
          $note = new Zulu_Form_Element_Note(
                  $noteName, array(
              'value' => '<a href="' . $fileUrl . '">' . str_replace('/', '', strrchr($fileUrl, '/')) . '</a>',
              'order' => $orderIndex++,
          ));
          $this->addElement($note);
          $this->$noteName->removeDecorator('Label');

          $this->addElement('Cancel', $this->$key->getName() . '_remove', array(
              'label' => 'remove file',
              'link' => true,
              'onclick' => "javascript:removeFile('{$this->$key->getName()}')",
              'prependText' => ' or ',
              'decorators' => array(
                  'ViewHelper'
              ),
              'order' => $orderIndex++
          ));
          $this->{$this->$key->getName() . '_remove'}->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'remove-file-button'));
        }
      }

      if ($field->type === 'fileMulti') {
        $description = $this->$key->getDescription();

        if ($description) {
          $this->$key->getDecorator('Description')->setEscape(false);
          $description .= "<br />";
        }

        $filesize = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.filesize', 2048);
        $description .= Zend_Registry::get('Zend_Translate')->_('Browse and choose a file for your document. Maximum permissible size: %s KB and allowed file types: pdf, txt, ps, rtf, epub, odt, odp, ods, odg, odf, sxw, sxc, sxi, sxd, doc, ppt, pps, xls, docx, pptx, ppsx, xlsx, tif, tiff');
        $description = sprintf($description, $filesize);

        $this->$key
                ->addValidator('Extension', false, 'pdf, txt, ps, rtf, epub, odt, odp, ods, odg, odf, sxw, sxc, sxi, sxd, doc, ppt, pps, xls, docx, pptx, ppsx, xlsx, tif, tiff')
                ->addValidator('Size', false, 2 * 1048576) // 2 MB
                ->setDescription($description)
                ->getDecorator('HtmlTag2')->setOption('class', 'form-wrapper zulu-file-wrapper');
      }

      if ($field->isHeading()) {
        $element->removeDecorator('Label')
                ->removeDecorator('HtmlTag')
                ->getDecorator('HtmlTag2')->setOption('class', 'form-wrapper-heading mainheading-form-element');

        if ($field->label === '_') {
          $element->setValue('')->removeDecorator('HtmlTag2');
        }

//        if(in_array(strtolower($field->label), $this->_emergencyFields)) {
//          $element->setValue($field->label . ' <b class="emergency_fields_asterisk">*</b>');
//        }
        if ($field->display == 1) {
          $element->setValue($field->label . ' <b class="emergency_fields_asterisk">*</b>');
        }

        // Custom HTML element
        $noteName = 'custom_element' . $orderIndex;

        $element->alias = $field->alias;

        if ($blockCount == 0) {
          // Do not close block when not open yet
          $div_close = '';
        } else {
          $div_close = '</div></div>';
        }

        $wrapper_label = strtolower($field->label);
        $note = new Zulu_Form_Element_Note(
                $noteName, array(
            'value' => $div_close . '<div class="' . $responsiveClass . '"><div class="inner_wrapper clinical_blocks ' . $wrapper_label . '">',
            // Block-Break-HTML will appear in previous reserved place
            'order' => $orderIndex - 1,
        ));
        $this->addElement($note)->getElement($noteName)->removeDecorator('Label')->removeDecorator('HtmlTag');

        // Increase number of blocks
        $blockCount++;

        // Increase index which is used by Block-Break-HTML
        $orderIndex++;
      }
    }

    $this->addElement('Hidden', 'remove_fieldfile', array(
        'order' => $orderIndex++
    ));

    // Custom HTML element
    $noteName = 'custom_element' . $orderIndex;
    $note = new Zulu_Form_Element_Note(
            $noteName, array(
        'value' => '</div>',
        'order' => $orderIndex++,
    ));
    $this->addElement($note);
    $this->getElement($noteName)->removeDecorator('Label')->removeDecorator('HtmlTag');

//        $this->addElement('Button', 'back', array(
//            'label' => 'Back',
//            'type' => 'submit',
//            'order' => 10000,
//        ));
  }

  public function setItem(Core_Model_Item_Abstract $item)
  {
    parent::setItem($item);

    foreach ($this->getElements() as $element) {
      if ($element->getType() === 'file') {
        $element->setItem($item);
      }
    }
  }

  public function saveValues()
  {
    // Store file field
    foreach ($this->getElements() as $element) {
      if ($element->getType() === 'file') {
        $element->store();
      }
      // Clear grid field's value if hidden
      if ($element->getType() === 'Zulu_Form_Element_Grid') {
        if ($element->getIgnore()) {
          $element->setValue('');
          $element->setIgnore(false);
        }
      }
    }
    $this->updateHasConcussionTest();

    parent::saveValues();
  }

  /**
   * Update the flag to indicate whether a medical record contains concussion test or not
   * If user has uploaded at least one concussion test document, this flag is true
   */
  public function updateHasConcussionTest()
  {
    $lastHeading = '';
    $has_concussion_test = false;
    foreach ($this->getElements() as $element) {
      if (preg_match('/Heading/', $element->getType())) {
        $lastHeading = $element->alias;
      } elseif (!$has_concussion_test && $lastHeading == 'CONCUSSION REPORTS') {
        if (preg_match('/file/', $element->getType()) && $element->getValue() != '') {
          $has_concussion_test = true;
          break;
        }
      }
    }
    $zulu = $this->getItem();
    $zulu->has_concussion_test = (int) $has_concussion_test;
    $zulu->save();
  }

}
