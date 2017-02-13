<?php

abstract class Zulu_Form_Common_ProfileFields_Abstract extends Fields_Form_Standard {

  protected $_fieldType = 'user';
  // This is used to mark header name for breaking Field List into several steps
  protected $_nextStopField = null;
  protected $_startField = null;
  protected $_editUserHelper = null;
  protected $_arrParticipationLevel = array('Player', 'Coach', 'Referee');
  protected $_responsiveBlockClass = 'col-xs-12 col-md-6';
//  protected $_arrHiddenSectionHeadings = array('Work and Education', 'Personal Details', 'Sporting Details');
  protected $_arrHiddenSectionHeadings = array();

  public static $_mgslData = null;

  public function __construct($options = null) {
    if (is_null(self::$_mgslData)) {
      self::$_mgslData = include_once APPLICATION_PATH . '/application/modules/Zulu/settings/mgsl.php';
    }
    parent::__construct($options);
  }

  /**
   * Get all special fields of profile form
   * 
   * @return array
   * label => alias
   */
  public static final function getSpecialFields() {
    return array_merge(self::getSouthAfricanFields(), self::getValkeFields());
  }

  public static final function getSouthAfricanFields() {
    return self::$_mgslData['south_africa'];
  }

  public static final function getValkeFields() {
    return self::$_mgslData['valke'];
  }

  public function init() {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    if ($view !== null) {
      if (!Engine_Api::_()->zulu()->isMobileMode()) {
        $view->headScript()->appendScript("var sa_participation_list = ['" . implode("','", $this->_arrParticipationLevel) . "'];");
        $view->headScript()->appendFile($view->layout()->staticBaseUrl . 'application/modules/Zulu/externals/js/profile-fields.js');
      } else {
        $view->sa_participation_list = "var sa_participation_list = ['" . implode("','", $this->_arrParticipationLevel) . "'];";
      }
    }
    // Init Edit User Helper
    $this->_editUserHelper = Zend_Controller_Action_HelperBroker::getExistingHelper('editUser');
    $this->addPrefixPath('Zulu_Form_Element', APPLICATION_PATH . '/application/modules/Zulu/Form/Element', 'element');
    parent::init();
  }

  public function generate() {

    $orderIndex = 0;

    $responsiveClass = $this->_responsiveBlockClass;

    // Custom HTML element
    $noteName = 'custom_element' . $orderIndex;
    $note = new Zulu_Form_Element_Note(
            $noteName, array(
        'value' => '<div class="row profile_form"><div class="' . $responsiveClass . '"><div class="inner_wrapper">',
        'order' => $orderIndex++,
    ));
    $this->addElement($note)->getElement($noteName)->removeDecorator('Label')->removeDecorator('HtmlTag');

    // Photo Upload -->
    $this->addSubForm($this->_editUserHelper->getForm(), 'EditPhoto', $orderIndex++);
    // <-- Photo Upload

    $struct = $this->getFieldStructure();

    $startPoint = false;

    $blockCount = 0;

    foreach ($struct as $fskey => $map) {
      $field = $map->getChild();

      // Skip fields hidden on signup
      if (isset($field->show) && !$field->show && $this->_isCreation) {
        continue;
      }
      
      if ($field->isHeading()) {
        // Get current heading for simple trick to hide some sections
        $current_heading = $field->label;
      }
      
      // Skip predefined hidden sections
      if(in_array(trim($current_heading), $this->_arrHiddenSectionHeadings)) {
        continue;
      }

      // Open special hidden wrapper
      if ($field->label === 'Do you have a South African ID Number?' || $field->alias === 'have SAID') {
        // Custom HTML element
        $noteName = 'custom_element' . $orderIndex;
        $note = new Zulu_Form_Element_Note(
                $noteName, array(
            'value' => '<div class="special_hidden_fields">',
            'order' => $orderIndex++,
        ));
        $this->addElement($note)->getElement($noteName)->removeDecorator('Label')->removeDecorator('HtmlTag');
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

      // Close special hidden wrapper
      if ($field->label === 'List of clubs') {
        // Custom HTML element
        $noteName = 'custom_element' . $orderIndex;
        $note = new Zulu_Form_Element_Note(
                $noteName, array(
            'value' => '</div>',
            'order' => $orderIndex++,
        ));
        $this->addElement($note)->getElement($noteName)->removeDecorator('Label')->removeDecorator('HtmlTag');
      }

      $element = $this->getElement($key);

      if ($field->alias) {
        $element->alias = $field->alias;
      }

      $this->_addCustomValidators($element);

      if (method_exists($element, 'setFieldMeta')) {
        $element->setFieldMeta($field);
      }

      // @todo Create special class selection for javascript
      $special_field_class = '';
      $arrSpecialFields = array_values(self::getSpecialFields());

      if (preg_grep('/' . $field->label . '/i', $arrSpecialFields)) {
        $special_field_class = $this->_convertAliasToVarName($field->label) . ' ';
      } elseif (preg_grep('/' . $field->alias . '/i', $arrSpecialFields)) {
        $special_field_class = $this->_convertAliasToVarName($field->alias) . ' ';
      }

      // Set attributes for hiding/showing fields using javscript
      $classes = $special_field_class . 'field_container field_' . $map->child_id . ' option_' . $map->option_id . ' parent_' . $map->field_id;
      $element->setAttrib('class', $classes);

      //
      if ($field->canHaveDependents()) {
        $element->setAttrib('onchange', 'changeFields(this)');
      }

      // Set custom error message
      if ($field->error) {
        $element->addErrorMessage($field->error);
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
                ->getDecorator('HtmlTag2')->setOption('class', 'form-wrapper-heading');

        // Custom HTML element
        $noteName = 'custom_element' . $orderIndex;
        $note = new Zulu_Form_Element_Note(
                $noteName, array(
            'value' => '</div></div><div class="' . $responsiveClass . '"><div class="inner_wrapper">',
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

    // Custom HTML element
    $noteName = 'custom_element' . $orderIndex;
    $note = new Zulu_Form_Element_Note(
            $noteName, array(
        'value' => '</div></div></div>',
        'order' => $orderIndex++,
    ));
    $this->addElement($note)->getElement($noteName)->removeDecorator('Label')->removeDecorator('HtmlTag');
  }

  protected function _convertAliasToVarName($alias) {
    return str_replace(' ', '_', strtolower($alias));
  }

  protected function _checkAllFieldsDefined($fields, $definedVars) {
    $procesedFields = array_map(function($var) {
      if (is_string($var)) {
        return $this->_convertAliasToVarName($var);
      }
    }, array_values($fields));

    return (count(array_diff($procesedFields, $definedVars)) == 0);
  }

  public function isValid($data) {

    $south_africa = true;
    // MGSL specific code, used to skip empty validation for some special fields in specific conditions
    foreach ($this->getElements() as $key => $element) {
      if (in_array($element->alias, array_values(self::getSpecialFields()))) {
        ${$this->_convertAliasToVarName($element->alias)} = $element;
      }

      if (isset($data[$key])) {
        $element->setValue($data[$key]);
      }
    }

    $fieldOptions = Engine_Api::_()->fields()->getFieldsOptions($this->_fieldType);

    $definedVars = array_keys(get_defined_vars());

    // If all neccessary fields for South African process are defined
    if ($this->_checkAllFieldsDefined(self::getSouthAfricanFields(), $definedVars)) {
      // South African fields
      $selectedParticipation = array();

      // Get all selected participation
      foreach ($participation_level->getValue() as $option_id) {
        $selectedParticipation[] = $fieldOptions->getRowMatching('option_id', $option_id)->label;
      }

      if ($country_of_residence->getValue() !== 'ZA' || $primary_sport->getValue() !== 'Rugby_Union' || !array_intersect($selectedParticipation, $this->_arrParticipationLevel)) {
        $sa_provinces->setRequired(false)->setAllowEmpty(true);
        $have_said->setRequired(false)->setAllowEmpty(true);
        $said->setRequired(false)->setAllowEmpty(true);
        $passport->setRequired(false)->setAllowEmpty(true);
        $club_or_school->setRequired(false)->setAllowEmpty(true);
        $south_africa = false;

        $data[$sa_provinces->getName()] = '';
        $data[$have_said->getName()] = '';
        $data[$said->getName()] = '';
        $data[$passport->getName()] = '';
        $data[$club_or_school->getName()] = '';
        $data[$list_of_clubs->getName()] = '';
        $data[$list_of_schools->getName()] = '';
      }
    }

    $parent_valid = parent::isValid($data);

    $custom_valid = true;

    // If all neccessary fields for Valke process are defined
    if ($this->_checkAllFieldsDefined(self::getValkeFields(), $definedVars)) {
      // Valke fields
      if ($south_africa && $fieldOptions->getRowMatching('option_id', $club_or_school->getValue())->label === 'Both') {
        $custom_valid = (bool) ($list_of_clubs->isValid($list_of_clubs->getValue(), $data) & $list_of_schools->isValid($list_of_schools->getValue(), $data));
        $list_of_clubs->setIgnore(false);
        $list_of_schools->setIgnore(false);
      }
    }

    return ($parent_valid && $custom_valid);
  }

  protected function _addCustomValidators($element) {
    if ($element->alias === 'SAID') {
      $element->addValidator(new Engine_Validate_Callback(array($this, 'validateSAID')));
    }
  }

  public function validateSAID($id) {

    if (!is_numeric($id)) {
      return false;
    }

    $d = -1;
    $a = 0;
    for ($i = 0; $i < 6; $i++)
      $a += substr($id, $i * 2, 1);
    for ($i = 0; $i < 6; $i++)
      $b = $b * 10 + substr($id, (2 * $i) + 1, 1);
    $b *= 2;
    $c = 0;

    while ($b > 0) {
      $c += $b % 10;
      $b = $b / 10;
    }
    $c += $a;
    $d = 10 - ($c % 10);
    if ($d == 10)
      $d = 0;
    if ($d == substr($id, strlen($id) - 1, 1)) {
      return true;
    } else {
      return false;
    }
  }

}
