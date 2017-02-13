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
class Zulu_AdminUserImportController extends Zulu_Controller_Fields_AdminAbstract
{

  protected $_fieldType = 'zulu';

  protected $_requireProfileType = false;

  protected $_formTitle = 'Edit Clinical Question';

  public function indexAction()
  {
    $struct = Engine_Api::_()->fields()->getFieldsStructureFull('user', 1, 1);
    $processingFields = include_once APPLICATION_PATH .
      '/application/modules/Zulu/settings/user-import-fields.php';

    if ($this->getRequest()->isPost() &&
      is_uploaded_file($_FILES["import_file"]['tmp_name']) &&
      pathinfo($_FILES["import_file"]['name'], PATHINFO_EXTENSION) == 'csv'
    ) {

      // Open the CSV file
      $file = fopen($_FILES["import_file"]['tmp_name'], 'r');
      // Initialize field values table
      $fieldValuesTable = Engine_Api::_()->fields()->getTable('user', 'values');
      // Fetch country list
      $territories = Zend_Locale::getTranslationList('territory', 'en', 2);
      asort($territories);

      $isFirstLine = true;
      while (($data = fgetcsv($file, 1000, ',')) !== false) {

        // Skip the first line
        if ($isFirstLine) {
          $isFirstLine = false;
          continue;
        }

        // Check if the user already existed
        $email = array_shift($data);
        $user = Engine_Api::_()->user()->getUser($email);
        if ($user->getIdentity()) {
          continue;
        }
        unset($user);

        // Get other information
        $password = array_shift($data);
        $timezone = array_shift($data);

        // Create user
        $user = Engine_Api::_()->getDbtable('users', 'user')->createRow();
        $user->email = $email;
        $user->password = $password;
        $user->timezone = $timezone;
        $user->save();

        /* @var $map Fields_Model_Map */
        /* @var $field Fields_Model_Meta */
        foreach ($processingFields as $alias => $fskey) {
          if (array_key_exists($fskey, $struct)) {
            $map = $struct[$fskey];

            $parts = explode('_', $fskey);
            if (count($parts) != 3) {
              continue;
            }
            $field = $map->getChild();
            $params = $field->getElementParams($user);

            $multiOptions = array();
            // Get field options
            if (array_key_exists('multiOptions', $params['options'])) {
              $multiOptions = $params['options']['multiOptions'];
            } elseif ($field->type == 'country') {
              $multiOptions = $territories;
            }

            $value = array_shift($data);
            // Parse text inputs to values
            if (!empty($multiOptions)) {
              $value = $this->_parseInput($multiOptions, $value);
            } // Process date string
            elseif ($alias == 'birthdate') {
              $value = date_format(date_create($value), 'Y-m-d');
            } elseif ($alias == 'first_name') {
              $first_name = $value;
            } elseif ($alias == 'last_name') {
              $last_name = $value;
            }
            list ($parent_id, $option_id, $field_id) = $parts;

            $valueRow = $fieldValuesTable->createRow();
            $valueRow->field_id = $field_id;
            $valueRow->item_id = $user->getIdentity();

            $valueRow->value = htmlspecialchars($value);
            $valueRow->privacy = 'everyone';
            $valueRow->save();
          }
        }
        // Update user
        $user->displayname = $first_name . ' ' . $last_name;
        $user->enabled = 1;
        $user->verified = 1;
        $user->save();
      }
    }
  }

  protected function _parseInput(array $options, $input)
  {
    $options = array_flip($options);
    return $options[$input];
  }

  public function init()
  {
    parent::init();
    Zend_Registry::get('Zend_View')->getPluginLoader('helper')->removePrefixPath(
      'Fields_View_Helper_');
  }
}
