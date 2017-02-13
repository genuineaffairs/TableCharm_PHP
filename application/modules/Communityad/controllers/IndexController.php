<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_IndexController extends Core_Controller_Action_Standard {

  protected $_navigation;
  protected $_viewer;
  protected $_viewer_id;
// Zend_Session_Namespace
  protected $_session;

  public function init() {

    $this->view->viewer = $this->_viewer = Engine_Api::_()->user()->getViewer();

    if (!$this->_helper->requireAuth()->setAuthParams('communityad', $this->_viewer, 'view')->isValid()) {
      return;
    }

    $this->_viewer_id = $this->_viewer->getIdentity();
    // It will show the navigation bar.
    $this->view->navigation = $this->_navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('communityad_main');

    $id = $this->_getParam('id');
    $this->_session = new Zend_Session_Namespace('Payment_Userads');
    if (!Engine_Api::_()->core()->hasSubject('user'))
      Engine_Api::_()->core()->setSubject($this->_viewer);
    $this->view->user = Engine_Api::_()->core()->getSubject();
  }

//SHOW PACKAGE LIST
  public function indexAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    if (!$this->_helper->requireAuth()->setAuthParams('communityad', null, 'create')->isValid())
      return;
    $this->view->is_ajax = $this->_getParam('is_ajax', 0);
    $this->view->adTypes = Engine_Api::_()->getItemTable('communityad_adtype')->getEnableAdType();
    if ($this->view->is_ajax || count($this->view->adTypes) == 0) {
      $user_level = $this->_viewer->level_id;

      $start_one = "'" . $user_level . "'";
      $start = "'" . $user_level . ",%'";
      $middile = "'%," . $user_level . ",%'";
      $end = "'%," . $user_level . "'";

      $table = Engine_Api::_()->getItemtable('package');
      $packages_select = $table->select()
              ->where("level_id = 0 or level_id LIKE $start_one or level_id LIKE $start or level_id LIKE $middile or level_id LIKE $end ")
              ->order('order ASC')
              ->order('creation_date DESC')
              ->where('enabled = 1');
      $this->view->package_type = $this->_getParam('package_type', 'default');
      $packages_select->where('type = ?', $this->_getParam('package_type', 'default'));
      $mod_type = $this->_getParam('type', 0);
      $mod_id = $this->_getParam('type_id', 0);
      if (!empty($mod_type) && !empty($mod_id)) {
        $packages_select->where("urloption  LIKE ?", '%' . $mod_type . '%');
      }
      $paginator = Zend_Paginator::factory($packages_select);
      $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('page'));
      $paginator->setItemCountPerPage(20);
    }

    //Start Coupon plugin work.
    $couponEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecoupon');
    if (!empty($couponEnabled)) {
      $modules_enabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('modules.enabled');
      if (!empty($modules_enabled)) {
        $this->view->modules_enabled = unserialize($modules_enabled);
      }
    }
    //End coupon plugin work.

    $this->view->type_id = $this->_getParam('type_id', null);
    $this->view->type = $this->_getParam('type', null);

    $this->view->getCommunityadTitle = Engine_Api::_()->communityad()->getCommunityadTitle();
  }

// UPLOAD IMAGE
  public function uploadAction() {

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $file = $_FILES["image"]["tmp_name"];
    $file1 = $_FILES["image"]["name"];
    $name = basename($file1);

    $maxlimit = (int) ini_get('upload_max_filesize') * 1024 * 1024;
    // ALLLOW IMAGE EXTENSION
    $allowed_ext = "jpg,jpeg,gif,png";
    $match = 0;
    $errorList = array();
    // SIZE OF IMAGE
    $filesize = $_FILES["image"]['size'];
    global $communityad_image_path;
    // PATH OF DESTINATION FOR IMAGE
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/communityad/temporary';
    $values = array();

    if (empty($communityad_image_path)) {
      return;
    }
    // CHECK FOR CORRECT IMAGE
    if ($filesize > 0) {
      $filename = strtolower($file1);
      $filename = preg_replace('/\s/', '_', $filename);
      if ($filesize < 1) {
        $errorList[] = "File size is empty.";
      }
      if ($filesize > $maxlimit) {
        $errorList[] = "File size is too big.";
      }

      if (count($errorList) < 1) {
        $file_ext = @preg_split("/\./", $filename);
        $allowed_ext = @preg_split("/\,/", $allowed_ext);
        foreach ($allowed_ext as $ext) {
          if ($ext == end($file_ext)) {
            $match = "1"; // File is allowed
          }
        }

        if (empty($match)) {
          $errorList[] = "File type isn't allowed: $filename";
        }
      }
    } else {
      $errorList[] = "NO FILE SELECTED";
    }

    @chmod($path, 0777);

    if (count($errorList) < 1 && !empty($match)) {
      //MINIMUM HIGHT AND WIDTH OF CREATE IMAGE
      $min = 60;
      $createWidth = 120;
      $createHight = 90;
      // SET WIDTH AND HIGHT OF IMAGE
      $maxW = $createWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.image.width', 120);
      $maxH = $createHight = Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.image.hight', 90);

      // Recreate image
      $image = Engine_Image::factory();
      $image->open($file);
      //IMAGE WIDTH
      $dstW = $image->width;
      // IMAGE HIGHT
      $dstH = $image->height;
      // SET THE IMAGE AND WIDTH BASE ON IMAGE
      $multiplier = min($maxW / $dstW, $maxH / $dstH);
      if ($multiplier > 1) {
        $dstH *= $multiplier;
        $dstW *= $multiplier;
      }

      if (($delta = $maxW / $dstW) < 1) {
        $dstH = round($dstH * $delta);
        $dstW = round($dstW * $delta);
      }
      if (($delta = $maxH / $dstH) < 1) {
        $dstH = round($dstH * $delta);
        $dstW = round($dstW * $delta);
      }

      $createHight = $dstH;
      $createWidth = $dstW;

      if ($createWidth < $min)
        $createWidth = $min;
      if ($createHight < $min)
        $createHight = $min;

      // Resize image
      $image = Engine_Image::factory();
      $image->open($file);
      $image->resample(0, 0, $image->width, $image->height, $createWidth, $createHight)
              ->write($path . '/' . $name)
              ->destroy();

      $photoName = $this->view->baseUrl() . '/public/communityad/temporary/' . $name;
      $currentImagePath = $path . '/' . $name;
      // IF ANY IMAGE IS CREATE, IT WILL REMOVE THERE
      if (isset($this->_session->photoName_Temp)) {
        if ($currentImagePath !== $this->_session->photoName_Temp) {
          if (is_file($this->_session->photoName_Temp)) {
            @chmod($this->_session->photoName_Temp, 0777);
            @unlink($this->_session->photoName_Temp);
          }
        }
        unset($this->_session->photoName_Temp);
      }

      if (isset($this->_session->photoName_Temp_module)) {
        if (is_file($this->_session->photoName_Temp_module)) {
          @chmod($this->_session->photoName_Temp_module, 0777);
          @unlink($this->_session->photoName_Temp_module);
        }
        unset($this->_session->photoName_Temp_module);
      }
      $this->_session->photoName_Temp = $path . '/' . $name;
      echo ' <div id="photo"><img  src="' . $photoName . '" border="0"   /> </div> ';
    } else {
      echo '<img src="' . $this->view->layout()->staticBaseUrl . '/application/modules/Communityad/externals/images/error.gif" width="16" height="16px" border="0" style="marin-bottom: -3px;" /> <span style=\"color:red; \">Error(s) Found: </span>';
      $errorStr = '';
      foreach ($errorList as $value) {
        if (empty($errorStr)) {
          $errorStr = $value;
        } else {
          $errorStr = "<br />" . $value;
        }
      }
      echo "<p>$errorStr</p>";
    }
  }

  public function createAction() {
    if (isset($this->_session->photoName_Temp)) {
      unset($this->_session->photoName_Temp);
    }
    // Check auth
    if (!$this->_helper->requireUser()->isValid())
      return;

    if (!$this->_helper->requireAuth()->setAuthParams('communityad', null, 'create')->isValid())
      return;
    // Hack navigation
    foreach ($this->_navigation->getPages() as $page) {
      if ($page->route != 'communityad_listpackage')
        continue;
      $page->active = true;
      break;
    }
    $settings = Engine_Api::_()->getApi('settings', 'core');
    // GET PACKAGE IN THIS YOU WANT TO CREATE AD
    $this->view->package = $package = Engine_Api::_()->getItem('package', $this->_getParam('id'));
    if (empty($package)) {
      return $this->_forward('notfound', 'error', 'core');
    }
    $this->view->showMarkerInDate = $this->showMarkerInDate();
    $this->view->modType = $mod_type = $this->_getParam('type', 0);
    $this->view->modId = $mod_id = $this->_getParam('type_id', 0);
    if (!empty($mod_type) && !empty($mod_id)) {
      $is_packagesupport = Engine_Api::_()->communityad()->is_packagesupport($this->_getParam('id', 0), $mod_type);
      // Package not support module type.
      if (empty($is_packagesupport)) {
        return $this->_forward('notfound', 'error', 'core');
      } else {
        $this->view->module_type = $mod_type;
        $this->view->module_id = $mod_id;
      }
    }

    $ad_type = $package->type;

    if (!($package->level_id == 0 || in_array($this->_viewer->level_id, explode(",", $package->level_id)))) {
      return $this->_forward('notfound', 'error', 'core');
    }

    $communityad_crate = Zend_Registry::get('communityad_create');
    if (empty($communityad_crate)) {
      return;
    }

    // check if design faq and target faq are enabled
    global $communityad_set_payment;
    $infopageTable = Engine_Api::_()->getItemTable('communityad_infopage');
    $this->view->target_faq = $target_faq = $infopageTable->fetchRow(array('faq = ?' => 3, 'status = ?' => 1))->status;
    if (in_array($ad_type, array('default'))) {
      $this->view->design_faq = $design_faq = $infopageTable->fetchRow(array('faq = ?' => 2, 'status = ?' => 1))->status;

      // MAKE AD CREATE FORM PART-1
      $this->view->form = $form = new Communityad_Form_Create(array('packageId' => $this->_getParam('id')));

      // GET ENABLE MODULES AND CUSTOM FOR THIS PACKAGE
      $levels_prepared = Engine_Api::_()->communityad()->enabled_module_content($this->_getParam('id'));
      if (!empty($levels_prepared)) {
        $this->view->is_customAs_enabled = $levels_prepared[0];
        $this->view->is_moduleAds_enabled = $levels_prepared[2];
      }
    } else if (in_array($ad_type, array('sponsored_stories'))) {
      $this->view->form = $form = new Communityad_Form_SponsoredStory_Create(array('packageId' => $this->_getParam('id')));
      $form->editTitle->setValue('');
      $this->view->design_faq = $design_faq = $infopageTable->fetchRow(array('infopage_id = ?' => 100))->status;
    }

    $this->view->mode = $mode = 1;
    $this->view->profileSelect_id = 0;
    // SET VALUES IN FORM PART-1
    $tempTitle = $this->view->translate($package['title']);
    $form->package_name->setDescription("<a href=\"javascript:void(0);\" onclick=\"Smoothbox.open('" . $this->view->url(array('module' => 'communityad', 'controller' => 'index', 'action' => 'packge-detail', 'id' => $package->package_id, 'onlydetails' => 1), 'default') . "')\"  >" . ucfirst($tempTitle) . "</a>");
    $form->owner_id->setValue($this->_viewer_id);
    $form->package_id->setValue($package['package_id']);
    $form->ad_type->setValue($package['type']);
    // CHECH TARGETING IN ENABLE OR NOT FOR THIS PACKAGE
    $this->view->enableTarget = $enableTarget = $package['network'];
    $this->view->communityad_package_info = Zend_Registry::get('communityad_package_info');

    // DEFAULT VALUES
    $this->view->enableCountry = 0;
    $this->view->topLevelId = $topLevelId = 0;
    $this->view->topLevelValue = $topLevelValue = null;

    // Get form PART 2 (TARGETING AND SHECUDILG)
    $this->view->formField = $formField = new Fields_Form_Standard(array(
        'item' => Engine_Api::_()->core()->getSubject(),
        'topLevelId' => $topLevelId,
        'topLevelValue' => $topLevelValue,
    ));

    $formField->setTitle($this->view->translate("Targeting"));

    // GET TARGET FIELDS WHICH ARE SELECTED FOR TARGETING
    $targetFields = Engine_Api::_()->getItemTable('target')->getFields();


    $targetFieldIds = array();
    $targetMapIds = array();
    // GET TARGETING FIELDS ID
    foreach ($targetFields as $targetField) {
      $targetFieldIds[] = $targetField->field_id;
    }
    $req_field_id = $targetFieldIds;
    // OBJECT OF USER_FIELDS_MAP
    $mapTable = Engine_Api::_()->getItemTable('map');
    $select = $mapTable->select();


    $targetFieldStr = (string) ( "'" . join("', '", $targetFieldIds) . "'");
    $select->where('child_id in (?)', new Zend_Db_Expr($targetFieldStr));
    $fieldStructure = $mapTable->fetchAll($select)->toArray();

    foreach ($fieldStructure as $key => $value) {
      $fieldStructure[$value['field_id'] . '_' . $value['option_id'] . '_' . $value['child_id']] = $value;
      unset($fieldStructure[$key]);
    }

    //Refined field structure
    $newFieldStructure = $fieldStructure;
    $type = array();


    // General form without profile type
    $newFieldKeys = array_keys($newFieldStructure);
    $elements = $formField->getElements();
    foreach ($elements as $key1 => $val1) {
      $formField->removeElement($key1);
    }
    // fields that are not includeing for targeting
    $not_addType = array('text', 'textarea', 'select', 'radio', 'checkbox', 'multiselect', 'multi_checkbox', 'integer', 'float', 'date', 'heading');

    // fields that required to change discription
    $addDiscription = array('first_name', 'last_name', 'website', 'twitter', 'facebook', 'aim', 'about_me', 'city', 'zip_code', 'location', 'interests');

    $eLabel = array();
    $listFieldValue = array();
    $fieldElements = array();

    $structure = Engine_Api::_()->getApi('core', 'communityad')->getFieldsStructureSearch('user');


    //Start create targeting fields
    $index = 0;
    if (!empty($enableTarget)) {
      foreach ($structure as $map) {
        $field = $map->getChild();
        $index++;

        if (!in_array($field->field_id, $req_field_id)) {
          continue;
        }

        if (in_array($field->type, $not_addType))
          continue;
        //Get key
        $key = null;
        if (!empty($field->alias)) {
          $key = $field->alias;
        } else {
          $key = sprintf('field_%d', $field->field_id);
        }

        //Get params
        $values = $field->getElementParams('user', array('required' => false));

        if (!@is_array($values['options']['attribs'])) {
          $values['options']['attribs'] = array();
        }

        //Remove some stuff
        unset($values['options']['required']);
        unset($values['options']['allowEmpty']);
        unset($values['options']['validators']);

        //Change order
        $values['options']['order'] = $index;

        //Get generic type
        $info = Engine_Api::_()->fields()->getFieldInfo($field->type);
        $genericType = null;
        if (!empty($info['base'])) {
          $genericType = $info['base'];
        } else {
          $genericType = $field->type;
        }
        $values['type'] = $genericType; // For now
        // change birthdate -> age
        if ($field->type == 'birthdate') {
          $values['type'] = 'Select';
          $values['options']['label'] = 'Age';
          $multiOptions = array('' => $this->view->translate('Any'));

          for ($i = 13; $i <= 100; $i++) {
            $multiOptions[$i] = $i;
          }
          $values['options']['multiOptions'] = $multiOptions;
        }

        // Populate country multiOptions
        if ($field->type == 'country') {
          $territories = Zend_Locale::getTranslationList('territory', null, 2);
          asort($territories);
          $this->view->enableCountry = 1;
          $genericType = $values['type'] = 'Text';
          $listFieldValue[$key] = $territories;
        }

        if ($field->type == 'gender') {
          $listFieldValue[$key] = $values['options']['multiOptions'];
        }
        //change into multicheckbox
        if ($field->type == 'ethnicity' || $field->type == 'looking_for' || $field->type == 'partner_gender' || $field->type == 'relationship_status' || $field->type == 'occupation' || $field->type == 'religion' || $field->type == 'zodiac' || $field->type == 'weight' || $field->type == 'political_views') {
          $genericType = $values['type'] = 'MultiCheckbox';

          if (empty($values['options']['multiOptions']['']))
            unset($values['options']['multiOptions']['']);

          $listFieldValue[$key] = $values['options']['multiOptions'];
        }

        if (in_array($field->type, $addDiscription)) {
          $values['options']['description'] = $this->view->translate("Separate multiple entries with commas.");
        }

        $eLabel[$key]['lable'] = $values['options']['label'];
        $eLabel[$key]['field_id'] = $field->field_id;
        $eLabel[$key]['type'] = $values['type'];
        if ($field->type == 'gender')
          $eLabel[$key]['type'] = $field->type;
        // Hacks
        switch ($genericType) {
          // Ranges
          case 'date':
            // Use subform
            $subform = new Zend_Form_SubForm(array(
                'description' => $values['options']['label'],
                'order' => $values['options']['order'],
                'decorators' => array(
                    'FormElements',
                    array('Description', array('placement' => 'PREPEND', 'tag' => 'div', 'class' => 'form-label')),
                    array('HtmlTag', array('tag' => 'div', 'class' => 'form-wrapper'))
                )
            ));
            Fields_Form_Standard::enableForm($subform);
            Engine_Form::enableForm($subform);

            unset($values['options']['label']);
            unset($values['options']['order']);
            $values['options']['decorators'] = array('ViewHelper', array('HtmlTag', array('tag' => 'div', 'class' => 'form-element form-element-age')));
            $subform->addElement($values['type'], 'min', $values['options']);
            $subform->addElement($values['type'], 'max', $values['options']);
            $formField->addSubForm($subform, $key);
            break;
          // Select types
          case 'select':
          case 'radio':
          case 'multiselect':
          case 'multi_checkbox':
            // Ignore if there is only one option
            if (count(@$values['options']['multiOptions']) <= 1) {
              continue;
            }
            if (count(@$values['options']['multiOptions']) <= 2 && isset($values['options']['multiOptions'][''])) {
              continue;
            }

            $listFieldValue[$key] = $values['options']['multiOptions'];
            $formField->addElement($values['type'], $key, $values['options']);
            break;
          // Normal
          default:
            $formField->addElement($values['type'], $key, $values['options']);
            break;
        }

        if (in_array($field->type, $addDiscription)) {
          $formField->$key->getDecorator("Description")->setOption("placement", "append");
        }
        // For cuntry auto suggest
        if ($field->type == 'country') {
          $formField->addElement('Hidden', 'toValues', array(
              'required' => true,
              'allowEmpty' => false,
              'order' => ++$index,
              'validators' => array(
                  'NotEmpty'
              ),
              'filters' => array(
                  'HtmlEntities'
              ),
          ));
          Engine_Form::addDefaultDecorators($formField->toValues);
        }
        $element = $formField->$key;
        $fieldElements[$key] = $element;
      }
    }

    $communityad_host = str_replace("www.", "", strtolower($_SERVER['HTTP_HOST']));
    $communityad_is_flag = Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.flag.info', 0);
    $birthday_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('target.birthday', 0);
    // Element Birthday Enable
    $this->view->birthday_enable = 0;
    if (!empty($birthday_enable) && $enableTarget) {
      $index++;
      $formField->addElement('Checkbox', 'birthday_enable', array(
          'label' => $this->view->translate('Target people having their birthday on current date.'),
          'description' => 'Birthday',
          'order' => $index,
      ));

      $this->view->birthday_enable = 1;
    }
    /* -----------------
     * Targeting for Genric Fields
     */
    $count_profile = 0;
    $profile = array();
    $profile_fields = array();
    if (!empty($enableTarget)) {
      // fields that are includeing for targeting
      $addType = array('text', 'textarea', 'select', 'radio', 'checkbox', 'multiselect', 'multi_checkbox');
      // fields that arenot includeing for targeting
      $not_addType = array('first_name', 'last_name', 'website', 'gender', 'aim', 'city', 'country', 'twitter', 'facebook', 'political_views', 'income', 'eye_color', 'currency', 'birthdate', 'integer', 'float', 'date', 'heading', 'about_me', 'location', 'zip_code', 'looking_for', 'ethnicity', 'occupation', 'education_level', 'religion', 'relationship_status', 'partner_gender', 'interests');

      $structure = Engine_Api::_()->getApi('core', 'communityad')->getFieldsStructureSearch('user');
      $options = Engine_Api::_()->getDBTable('options', 'communityad')->getAllProfileTypes();
      if (empty($options)) {
        return;
      }
      $count_profile = @count($options);
      // Start create targeting fields
      $profile_base_targeting_flage = 1;
      // ELEMENTS OF PROFILE TYPE SPECIFY
      $formField->addElement('Dummy', 'profile_base_targeting', array(
          'label' => '<b>' . $this->view->translate('Advanced Targeting Options') . '</b>',
          'decorators' => array(
              'ViewHelper',
              array('Label', array('placement' => 'PREPEND', 'escape' => false)),
              array('HtmlTag', array('tag' => 'div', 'style' => 'width:100%;margin-bottom:10px;font-size:11px;')))
      ));

      if ((boolean) Engine_Api::_()->getApi('settings', 'core')->getSetting('community.target.network', 0) && $enableTarget && Engine_Api::_()->communityad()->hasNetworkOnSite()) {
        //Add network fields
        $formField->addElement('Multiselect', 'networks', array(
            'Label' => Zend_Registry::get('Zend_Translate')->_("Select Networks"),
            'description' => Zend_Registry::get('Zend_Translate')->_('Networks based advanced targeting enables you to target your ad to users of specific networks. Enter the networks, separated by commas, to which you want your ad to be targeted, using the auto-suggest box below. To reach all networks, simply leave the box empty.'),
            'attribs' => array('style' => 'height:100px; '),
            'multiOptions' => $this->getNetworkLists()
        ));
        $eLabel['networks']['lable'] = Zend_Registry::get('Zend_Translate')->_('Networks');
        $listFieldValue['networks'] = $this->getNetworkLists();
        $listFieldValuekey['networks']['key'] = 'networks';
        $eLabel['networks']['type'] = 'Multiselect';
      }
      $index++;

      $profile_base_targeting_flage = 0;
      if ($count_profile > 1) {
        // Add field for profile
        $formField->addElement('radio', 'profile', array(
            'label' => Zend_Registry::get('Zend_Translate')->_('Select Profile Type'),
            'description' => Zend_Registry::get('Zend_Translate')->_('Profile types based advanced targeting enables you to target your ad to users of a specific profile type. Select the profile type that you want to target to, or choose "All" to reach all profile types.'),
            'onclick' => 'profileFields(this.value)',
            'order' => $index,
        ));
      }
      $formField->addElement('Dummy', 'profile_base_msg', array(
          'label' => Zend_Registry::get('Zend_Translate')->_('Refine Targeting'),
          'description' => Zend_Registry::get('Zend_Translate')->_('You can further refine targeting of your ad for users of this profile type using the below fields.'),
          'order' => $index,
      ));

      $profile = array();
      $profile_fields = array();
      foreach ($options->toarray() as $opt) {
        $profile[$opt['option_id']] = $opt['label'];
        $selectOption = Engine_Api::_()->getDBTable('metas', 'communityad')->getFields($opt['option_id']);
        // ELEMENTS OF PROFILE TYPE SPECIFY
        $profile_field_ids = array();
        foreach ($selectOption as $key => $fieldvalue) {
          if (in_array($fieldvalue['type'], $not_addType))
            continue;
          $profile_field_ids[] = $key;
        }

        $profile_targeting_ids = array_intersect($req_field_id, $profile_field_ids);

        if (!empty($profile_targeting_ids)) {

          foreach ($structure as $map) {
            $field = $map->getChild();
            $index++;

            if (!in_array($field->field_id, $profile_targeting_ids)) {
              continue;
            }

            if (in_array($field->type, $not_addType))
              continue;
            // Get key
            $key = null;
            if (!empty($field->alias)) {
              $key = $field->alias;
            } else {
              $key = sprintf('field_%d', $field->field_id);
            }

            // Get params
            $values = $field->getElementParams('user', array('required' => false));

            if (!@is_array($values['options']['attribs'])) {
              $values['options']['attribs'] = array();
            }

            // Remove some stuff
            unset($values['options']['required']);
            unset($values['options']['allowEmpty']);
            unset($values['options']['validators']);

            // Change order
            $values['options']['order'] = $index;

            // Get generic type
            $info = Engine_Api::_()->fields()->getFieldInfo($field->type);
            $genericType = null;
            if (!empty($info['base'])) {
              $genericType = $info['base'];
            } else {
              $genericType = $field->type;
            }
            $values['type'] = $genericType; // For now
            //change into multicheckbox
            if ($field->type == 'select' || $field->type == 'radio' || $field->type == 'multiselect' || $field->type == 'multi_checkbox') {
              $genericType = $values['type'] = 'MultiCheckbox';

              if (empty($values['options']['multiOptions']['']))
                unset($values['options']['multiOptions']['']);
              if (count(@$values['options']['multiOptions']) <= 0) {
                continue;
              }
              $listFieldValue[$key] = $values['options']['multiOptions'];
            }

            if (in_array($field->type, $addDiscription)) {
              $values['options']['description'] = $this->view->translate("Separate multiple entries with commas.");
            }

            $profile[$opt['option_id']] = $opt['label'];
            $profile_fields[$opt['option_id']][] = $key;
            $eLabel[$key]['lable'] = $values['options']['label'];
            $eLabel[$key]['field_id'] = $field->field_id;
            $eLabel[$key]['type'] = $values['type'];
            // Hacks
            switch ($genericType) {
              // Select types
              case 'select':
              case 'radio':
              case 'multiselect':
              case 'multi_checkbox':
                // Ignore if there is only one option
                if (count(@$values['options']['multiOptions']) <= 0) {
                  continue;
                }
                if (count(@$values['options']['multiOptions']) <= 1 && isset($values['options']['multiOptions'][''])) {
                  continue;
                }
                $listFieldValue[$key] = $values['options']['multiOptions'];
                $values['type'] = 'MultiCheckbox';
                $formField->addElement($values['type'], $key, $values['options']);

                break;
              // Normal
              default:
                $formField->addElement($values['type'], $key, $values['options']);
                break;
            }
            if (in_array($field->type, $addDiscription)) {
              $formField->$key->getDecorator("Description")->setOption("placement", "append");
            }
            $element = $formField->$key;
            $fieldElements[$key] = $element;
          }
        }
      }
      if (!empty($profile)) {
        if ($count_profile > 1) {
          $profile[0] = 'All';
          ksort($profile);
          $formField->getElement('profile')
                  ->setMultiOptions($profile)
                  ->setValue(0);
        } else {
          $this->view->profileSelect_id = key($profile);
          if (empty($profile_fields)) {
            $formField->removeElement('profile_base_msg');
            if (!((boolean) Engine_Api::_()->getApi('settings', 'core')->getSetting('community.target.network', 0) && Engine_Api::_()->communityad()->hasNetworkOnSite() && $enableTarget)) {
              $formField->removeElement('profile_base_targeting');
            }
          } else {
            $formField->getElement('profile_base_msg')
                    ->setDescription("You can further refine targeting of your ad for users using the below fields.");
          }
        }
      } else {
        $formField->removeElement('profile');
        $formField->removeElement('profile_base_msg');
        $formField->removeElement('profile_base_targeting');
      }
    }

    if (empty($communityad_is_flag)) {
      $communityad_ads_field = convert_uuencode($communityad_host);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('communityad.ads.field', $communityad_ads_field);
    }

    $get_payment_settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.payment.ad', 0);
    $target_elements = $formField->getElements();


    $formField->removeElement('submit');

    $this->view->showTargetingTitle = 0;

    $countEL = count($formField->getElements());
    $countSF = count($formField->getSubForms());

    if (!empty($countEL) || !empty($countSF))
      $this->view->showTargetingTitle = 1;

    $date = (string) date('Y-m-d');
    // Start Date
    $cads_start_date = new Engine_Form_Element_CalendarDateTime('cads_start_date');
    $cads_start_date->setLabel($this->view->translate("Start Date"));
    $cads_start_date->setAllowEmpty(false);
    $cads_start_date->setValue($date . ' 00:00:00');
    $cads_start_date->setOrder('994');
    $formField->addElement($cads_start_date);

    //Enable End Date
    $formField->addElement('Checkbox', 'enable_end_date', array(
        'label' => $this->view->translate("Run my ad continuously from starting date till it expires."),
        'value' => 1,
        'order' => 995,
    ));

    // End Date
    $cads_end_date = new Engine_Form_Element_CalendarDateTime('cads_end_date');
    $cads_end_date->setLabel($this->view->translate("End Date"));
    $cads_end_date->setValue('0000-00-00 00:00:00');
    $cads_end_date->setOrder('996');
    $formField->addElement($cads_end_date);
    $this->view->listFieldValue = $listFieldValue;

    $formField->addElement('Button', 'continue_review', array(
        'label' => $this->view->translate('Continue'),
        'order' => 999,
        'ignore' => true,
    ));

    $formField->addElement('Button', 'continue', array(
        'label' => $this->view->translate('Continue'),
        'order' => 1000,
        'ignore' => true,
    ));

    if ($count_profile <= 1 && $enableTarget) {
      $formField->addElement('hidden', 'profile', array(
          'value' => $this->view->profileSelect_id,
      ));
    }
    $this->view->profile = $profile;
    $this->view->formField = $formField;
    $this->view->eLabel = $eLabel;
    $this->view->profileField = $profile_fields;


    $this->view->photoName = '';
    // Post form

    if ($this->getRequest()->isPost()) {
      // get the forms values
      $formValues = $_POST;

      if (!empty($formValues['ad_type']) && ($formValues['ad_type'] != 'default')) {
        if (!empty($formValues['resource_type']) && !empty($formValues['resource_id'])) {
          $this->view->titleLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('story.char.title', 35);
          $this->view->rootTitleLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.title', 25);
          $this->view->storyResourceType = $formValues['resource_type'];
          $this->view->storyResourceId = $formValues['resource_id'];
          $this->view->story_type = $formValues['story_type'];
          $getModInfo = Engine_Api::_()->getDbTable('modules', 'communityad')->getModuleInfo($formValues['resource_type']);
          $this->view->getModTitle = $getModInfo['module_title'];
          $this->view->resourceObj = Engine_Api::_()->getItem($getModInfo['table_name'], $formValues['resource_id']);
          if (!$formValues['flag']) {
            $formValues['flag'] = 1;
          }
          if (!$formValues['editFlag']) {
            $formValues['editFlag'] = 1;
          }
        }
      }

      if (!$_POST['mode'])
        $mode = 0;

      if (isset($formValues['profile']) && !empty($formValues['profile'])) {

        foreach ($profile_fields as $key => $profilefield) {

          if ($key == $formValues['profile'])
            continue;
          foreach ($profilefield as $fieldUnset) {
            if (!in_array($fieldUnset, $profile_fields[$formValues['profile']]))
              unset($formValues[$fieldUnset]);
          }
        }
        $this->view->profileSelect_id = $formValues['profile'];
      } else {
        $formValues['profile'] = 0;
        foreach ($profile_fields as $key => $profilefield) {
          foreach ($profilefield as $fieldUnset) {
            unset($formValues[$fieldUnset]);
          }
        }
      }

      $formValues['owner_id'] = $form->getValue('owner_id');
      // set values in form

      if (empty($formValues['campaign_name']) && !empty($formValues['campaign_id'])) {
        return;
      }
      if (in_array($ad_type, array('default'))) {
        // check validation
        if (empty($get_payment_settings)) {
          $formValues['name'] = 0;
        }

        if (empty($formValues['cads_url'])) {
          return;
        }
        if (empty($formValues['name']) && empty($formValues['like'])) {
          return;
        } elseif (empty($formValues['content_title']) && !empty($formValues['like'])) {
          return;
        } else if (!empty($formValues['content_title']) && !empty($formValues['like'])) {
          $formValues['name'] = $formValues['content_title'];
        }

        if (empty($formValues['cads_body'])) {
          return;
        } else {
          $formValues['cads_body'] = @substr($formValues['cads_body'], 0, (Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.body', 135) + 10));
        }
        if (empty($formValues['imageenable'])) {
          return;
        }
      } elseif (in_array($ad_type, array('sponsored_stories'))) {
        if (empty($formValues['name'])) {
          return;
        }
        $this->view->titileName = $formValues['name'];
      }
      if (empty($formValues['cads_end_date']['date'])) {
        $formValues['enable_end_date'] = 1;
      }


      $form->populate($formValues);
      $formField->populate($formValues);
      if (isset($formValues['resource_id']))
        $this->view->resource_id = $formValues['resource_id'];
      if (isset($formValues['resource_type']))
        $this->view->resource_type = $formValues['resource_type'];
      if ((boolean) Engine_Api::_()->getApi('settings', 'core')->getSetting('community.target.network', 0) && Engine_Api::_()->communityad()->hasNetworkOnSite() && $enableTarget) {


        if (isset($_POST['networks'])):
          $network_Ids = (string) ( is_array($_POST['networks']) ? join(",", $_POST['networks']) : $_POST['networks'] );
        else:
          $network_Ids = new Zend_Db_Expr('NULL');
        endif;
      }

      $eLabel_Keys = array_keys($eLabel);
      $eLabel_Keys[] = 'Age';

      if (!empty($this->view->enableCountry)) {
        $formValues['country'] = $formValues['toValues'];
      }

      if (in_array($ad_type, array('default'))) {
        // check for image
        if (empty($formValues['photo_id_filepath'])) {
          if (!empty($_FILES["image"])) {
            $file = $_FILES["image"]["tmp_name"];
            $file1 = $_FILES["image"]["name"];
            $name = basename($file1);
            $pathName = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/communityad/temporary/' . $name;
          } else if (!empty($formValues['imageName'])) {
            $name = $formValues['imageName'];
            $pathName = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/communityad/temporary/' . $name;
          } else {
            $name = '';
            $pathName = '';
          }
        } else {
          $name = '';
          $pathName = '';
        }
        if ((empty($pathName) || !@is_file($pathName)) && empty($formValues['like'])) {
          return;
        }
      }

      $saveTargetValue = array();
      foreach ($formValues as $key => $value) {

        if ($key == 'Age')
          $key = 'birthdate';
        if (!empty($this->view->enableCountry)) {
          if ($key == 'country')
            $value = explode(',', $value);
        }

        if (in_array($key, $eLabel_Keys)) {
          if ($key !== 'gender' && $key != 'country' && isset($eLabel[$key]['type']) && $eLabel[$key]['type'] != 'gender') {
            $saveTargetValue[$key] = (string) ( is_array($value) ? join(",", $value) : $value );
          } else if ($key == 'country') {
            $saveTargetValue[$key] = (string) ( is_array($value) ? !empty($value['0']) ? join(",", $value) : new Zend_Db_Expr('NULL')  : $value );

            if (is_array($value) && empty($value['0'])) {
              unset($saveTargetValue[$key]);
            }
            if (isset($saveTargetValue[$key]) && !empty($saveTargetValue[$key])) {
              $locale = Zend_Registry::get('Zend_Translate')->getLocale();
              $territories = Zend_Locale::getTranslationList('territory', $locale, 2);
              asort($territories);
              $countryValue = explode(',', $saveTargetValue[$key]);
              $multi = 0;
              if (count($countryValue) > 1) {
                $multi = 1;
              }
              if ($countryValue !== null) {
                foreach ($countryValue as $ck => $cv) {
                  $dataDisplay[$ck]['key'] = $cv;
                  $dataDisplay[$ck]['value'] = $territories[$cv];
                }
                $this->view->toCountry = $dataDisplay;
                $formField->toValues->setValue($saveTargetValue[$key]);
              }
            }
          } else {
            if (isset($listFieldValue[$key][$value]))
              $saveTargetValue[$key] = (string) ($listFieldValue[$key][$value]);
          }

          if (!empty($value) && !(is_scalar($value))) {
            $range_value = null;

            $range = array();
            foreach ($value as $subKey => $subValue)
              $range[$subKey] = $subValue;
            // For Range
            if (isset($range['min']) && isset($range['max'])) {
              if (is_scalar($range['min']) && is_scalar($range['max'])) {
                if ((!empty($range['min']) && !empty($range['max']))) {
                  if ($range['max'] < $range['min']) {
                    $min = $range['max'];
                    $max = $range['min'];
                  } else {
                    $min = $range['min'];
                    $max = $range['max'];
                  }
                  $saveTargetValue['age_min'] = $min;
                  $saveTargetValue['age_max'] = $max;
                  if ($min < $max)
                    $range_value = "Between the ages of " . $min . " and " . $max . " inclusive.";
                  else
                    $range_value = 'age of ' . $min;
                }

                if ((empty($range['min']) && !empty($range['max']))) {
                  $saveTargetValue['age_max'] = $range['max'];
                  $range_value = $range['max'] . " years old and younger";
                }

                if ((!empty($range['min']) && empty($range['max']))) {
                  $saveTargetValue['age_min'] = $range['min'];
                  $range_value = "age " . $range['min'] . " and older";
                }
              } else {
                $min_date = $range['min']['month'] . " " . $range['min']['day'] . " " . $range['min']['year'];
                $max_date = $range['max']['month'] . " " . $range['max']['day'] . " " . $range['max']['year'];
                $range_value = $min_date . " to" . $max_date;
              }
            } else {
              $range_value_str = array();
              foreach ($range as $r) {
                if (isset($listFieldValue[$key][$r]))
                  $range_value_str[] = $listFieldValue[$key][$r];
              }
              $range_value = (string) join(",", $range_value_str);
            }

            $eLabel[$key]['value'] = $range_value;
          } else {
            if (isset($eLabel[$key]['type']) && ($eLabel[$key]['type'] == 'select' || $eLabel[$key]['type'] == 'multi_select' || $eLabel[$key]['type'] == 'multi_checkbox' || $eLabel[$key]['type'] == 'gender')) {

              if (!empty($value))
                $value = $listFieldValue[$key][$value];
              else
                $value = '';
            }elseif (isset($eLabel[$key]['type']) && $eLabel[$key]['type'] == 'checkbox') {
              if (!empty($value))
                $value = 'enable';
            }
            $eLabel[$key]['value'] = $value;
          }
        }
      }

      if (!$mode) {
        // when click on create/place order button
        $result = array();
        foreach ($eLabel as $key => $Values) {
          if (isset($Values['value']))
            $result[$key] = $Values['value'];
        }

        $result = array_merge($result, $formValues);
        if (in_array($ad_type, array('default'))) {
          $result['photoPath'] = $pathName;
        } elseif (in_array($ad_type, array('sponsored_stories'))) {
          if (!empty($result['story_type'])) {
            $result['story_type'] = 1;
          }

          if (@array_key_exists($values['package_name'])) {
            unset($values['package_name']);
          }
          if (@array_key_exists($values['campaign_name'])) {
            unset($values['campaign_name']);
          }
          if (@array_key_exists($values['continue_next'])) {
            unset($values['continue_next']);
          }

          $result['photoPath'] = '';
          if ($formValues['temp_resource_type']) {
            $result['resource_type'] = $formValues['temp_resource_type'];
          }
          if ($formValues['temp_resource_id']) {
            $result['resource_id'] = $formValues['temp_resource_id'];
          }
          $result['like'] = 1;
          $result['photoPath'] = 0;
          if (!empty($result['resource_type']) && !strstr($result['resource_type'], "sitereview")) {
            $getModInfo = Engine_Api::_()->getDbTable('modules', 'communityad')->getModuleInfo($result['resource_type']);
            if (!empty($getModInfo)) {
              $result['resource_type'] = $getModInfo['table_name'];
            }
          }
        }
        $result['cads_start_date'] = $formField->cads_start_date->getValue();
        $result['cads_end_date'] = $formField->cads_end_date->getValue();
        // package base value
        $result['sponsored'] = $package['sponsored'];
        $result['featured'] = $package['featured'];

        $result['public'] = $package['public'];
        $result['price_model'] = $package['price_model'];

        $approved = 0;
        if ($package->isFree())
          $approved = $package['auto_aprove'];

        $result['approved'] = $approved;
        $result['status'] = $approved;
        $result['enable'] = $approved;
        // approved and free package
        if (!empty($approved) && $package->isFree()) {
          $result['approve_date'] = date('Y-m-d H:i:s');
          if ($package['price_model'] == 'Pay/click')
            $result['limit_click'] = $package['model_detail'];

          if ($package['price_model'] == 'Pay/view')
            $result['limit_view'] = $package['model_detail'];

          if ($package['price_model'] == 'Pay/period') {
            $result['model_value'] = $package['model_detail'];
            $expiry = $result['model_value'];
            if ($expiry == '-1')
              $result['expiry_date'] = '2250-01-01';
            else
              $result['expiry_date'] = Engine_Api::_()->communityad()->getExpiryDate($expiry);
          }
        }
        if ($package->isFree())
          $result['payment_status'] = 'free';
        else
          $result['payment_status'] = 'initial';

        // save ad
        $adsSave = Engine_Api::_()->communityad()->saveUserAd($result);

        $saveTargetValue['userad_id'] = $adsSave->userad_id;

        foreach ($saveTargetValue as $rKey => $rVal) {
          if (empty($rVal))
            unset($saveTargetValue[$rKey]);
        }

        if (!empty($birthday_enable) && $enableTarget) {
          if (isset($formValues['birthday_enable']))
            $saveTargetValue['birthday_enable'] = $formValues['birthday_enable'];
          else
            $saveTargetValue['birthday_enable'] = 0;
        }

        // Save the targeting values for advertizing
        $targetFields = Engine_Api::_()->getDbtable('adtargets', 'communityad')->setUserAdTargets($saveTargetValue);
        // Ad is belong to free package then redirect to ad view ad details page
        if ($package->isFree()) {
          return $this->_helper->redirector->gotoRoute(array('ad_id' => $adsSave->userad_id, "state" => 'saved'), 'communityad_userad', true);
        } else {
          // Ad is belong to payment package then redirect to payment page
          $this->_session->userad_id = $adsSave->userad_id;
          return $this->_helper->redirector->gotoRoute(array(), 'communityad_payment', true);
        }
      } else {

        if (in_array($ad_type, array('default'))) {
          // Preview of ad display and others values
          if (!empty($_FILES["image"]) && empty($formValues['photo_id_filepath'])) {
            $this->view->photoName = $name;
            $this->view->photoDisplay = '<img  src="' . $this->view->baseUrl() . '/public/communityad/temporary/' . $name . '" border="0"   />';
          } else if (!empty($name)) {
            $name = $formValues['imageName'];
            $this->view->photoName = $name;
            $this->view->photoDisplay = '<img  src="' . $this->view->baseUrl() . '/public/communityad/temporary/' . $name . '" border="0"   /> ';
          } else {
            $this->view->photoDisplay = '';
          }
        }

        $this->view->mode = $mode = 0;
      }

      if (isset($formValues['profile']) && !empty($formValues['profile']) && $count_profile > 1) {
        $this->view->profileEnable = $profile[$formValues['profile']];
      } else {
        $this->view->profileEnable = '';
      }
    }

    $this->view->eLabel = $eLabel;
  }

// SHOW PACKAGE DETAILS
  public function packgeDetailAction() {
    $id = $this->_getParam('id');
    $onlydetails = $this->_getParam('onlydetails', 0);
    $user_level = Engine_Api::_()->communityad()->getPublicUserLevel();

    if (empty($onlydetails)) {
      if (!empty($this->_viewer_id))
        $user_level = $this->_viewer->level_id;
      else
        $user_level = Engine_Api::_()->communityad()->getPublicUserLevel();
      $this->view->can_create = Engine_Api::_()->authorization()->getPermission($user_level, 'communityad', 'create');
    }else {
      $this->view->can_create = 0;
    }
    $table = Engine_Api::_()->getDbtable('packages', 'communityad');
    $rName = $table->info('name');
    $package_select = $table->select()
            ->where('package_id = ?', $id);
    $this->view->package = $table->fetchAll($package_select);
  }

// ACTIVE/PAUSE ADVERTIESMENT
  public function enabledAction() {
    $id = $this->_getParam('id');
    $userads = Engine_Api::_()->getItem('userads', $id);


    if (!empty($this->_viewer_id))
      $user_level = $this->_viewer->level_id;
    else
      $user_level = Engine_Api::_()->communityad()->getPublicUserLevel();

    $can_edit = Engine_Api::_()->authorization()->getPermission($user_level, 'communityad', 'edit');

    if (empty($can_edit) || ($can_edit == 1 && $userads->owner_id != $this->_viewer_id)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {

      $userads->enable = !$userads->enable;
      // CHANGE STATUS
      if ($userads->enable)
        $userads->status = 1;
      else
        $userads->status = 2;
      $userads->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_helper->redirector->gotoRoute(array('adcampaign_id' => $userads->campaign_id), 'communityad_ads', true);
  }

// SOFT DELETE ADVERSTIESMENT
  public function deleteadAction() {

    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('communityad_main');
    $id = $this->_getParam('id');
    $userads = Engine_Api::_()->getItem('userads', $id);
    $this->view->camp_id = $userads->campaign_id;
    if (!Engine_Api::_()->core()->hasSubject('communityad')) {
      if (Engine_Api::_()->core()->hasSubject())
        Engine_Api::_()->core()->clearSubject();
      Engine_Api::_()->core()->setSubject($userads);
    }

    // Check auth
    if (!empty($this->_viewer_id))
      $user_level = $this->_viewer->level_id;
    else
      $user_level = Engine_Api::_()->communityad()->getPublicUserLevel();

    $can_delete = Engine_Api::_()->authorization()->getPermission($user_level, 'communityad', 'delete');

    if (empty($can_delete) || ($can_delete == 1 && $userads->owner_id != $this->_viewer_id)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    // Check post
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $userads->enable = 0;
        $userads->status = 4;
        $userads->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_helper->redirector->gotoRoute(array('adcampaign_id' => $this->view->camp_id), 'communityad_ads', true);
    }
  }

// DELETE CAMPAGIN
  public function deletecampAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('communityad_main');

    $this->view->adcampaign_id = $id = $this->_getParam('id');
    $camp = Engine_Api::_()->getItem('adcampaign', $id);
    $user_level = Engine_Api::_()->communityad()->getPublicUserLevel();
    if (!empty($this->_viewer_id))
      $user_level = $this->_viewer->level_id;

    $can_delete = Engine_Api::_()->authorization()->getPermission($user_level, 'communityad', 'delete');

    if (empty($can_delete) || ($can_delete == 1 && $camp->owner_id != $this->_viewer_id)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    // Check post
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $camp->delete();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_helper->redirector->gotoRoute(array(), 'communityad_campaigns', true);
    }
  }

// MORE CAMPAGINS DELETE
  public function deleteselectedcampAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('communityad_main');
    $this->view->ids = $ids = $this->_getParam('ids', null);
    $confirm = $this->_getParam('confirm', false);
    $this->view->count = count(explode(",", $ids));

    // Save values
    if ($this->getRequest()->isPost() && $confirm == true) {
      $ids_array = explode(",", $ids);
      foreach ($ids_array as $id) {
        $camp = Engine_Api::_()->getItem('adcampaign', $id);
        if ($camp)
          $camp->delete();
      }

      $this->_helper->redirector->gotoRoute(array(), 'communityad_campaigns', true);
    }
  }

// EDIT CAMPAGINS TITLE
  public function editcampAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('communityad_main');
    $id = $this->_getParam('id');

    $this->view->adcampaign_id = $id;
    $camp = Engine_Api::_()->getItem('adcampaign', $id);

    $this->view->camp_title = $camp->name;

    if (!empty($this->_viewer_id))
      $user_level = $this->_viewer->level_id;
    else
      $user_level = Engine_Api::_()->communityad()->getPublicUserLevel();

    global $communityad_set_payment;
    $can_edit = Engine_Api::_()->authorization()->getPermission($user_level, 'communityad', 'edit');

    if (empty($can_edit) || ($can_edit == 1 && $camp->owner_id != $this->_viewer_id)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $communityad_crate = Zend_Registry::get('communityad_create');
    if (empty($communityad_crate)) {
      return;
    }
    // Check post
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $camp->name = $_POST['name'];
        $camp->save();
        $db->commit();
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 100,
            'parentRefresh' => 10,
            'messages' => array('')
        ));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_helper->redirector->gotoRoute(array(), 'communityad_campaigns', true);
    }
  }

// EDIT ADVERTIESMENT
  public function editAction() {

    if (isset($this->_session->photoName_Temp)) {
      unset($this->_session->photoName_Temp);
    }

    // Check auth
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->userAds_id = $id = $this->_getParam('id');
    $this->view->userAds = $userads = Engine_Api::_()->getItem('userads', $id);
    if (empty($userads)) {
      return $this->_forward('notfound', 'error', 'core');
    }
    $this->view->showMarkerInDate = $this->showMarkerInDate();
    $this->view->ad_type = $ad_type = $userads->ad_type;
    if (in_array($ad_type, array('default'))) {
      $this->view->is_photo_id = $userads->photo_id;

      $subModules = array();
    }

    if ($userads->ad_type == 'sponsored_stories') {
      $this->view->editFlag = false;
      $this->view->story_type = $userads->story_type;
    }

    if (!empty($userads->resource_type)) {
      if ($userads->ad_type == 'sponsored_stories') {
        $content_array = Engine_Api::_()->communityad()->resource_content($userads->resource_type, 1, 'edit', $userads->resource_id);
      } else {
        $content_array = Engine_Api::_()->communityad()->resource_content($userads->resource_type, 1, 'edit', $userads->resource_id, $userads->owner_id);
      }
      foreach ($content_array as $module) {
        $str .= $module['title'] . '_' . $module['id'] . '::';
      }
      $str = trim($str, '::');
      $str = str_replace("'", '"', $str);

      $this->view->edit_sub_title = $str;
      $this->view->resource_id = $userads->resource_id;
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');
    // GET PACKAGE FOR ADVERTIESMENT
    $this->view->package = $package = Engine_Api::_()->getItem('package', $userads->package_id);

    if (empty($package)) {
      return $this->_forward('notfound', 'error', 'core');
    }


    if (null !== ($copy = $this->_getParam('copy'))) {
      $level_id = $this->_viewer->level_id;
    } else {
      $level_id = $userads->getOwner()->level_id;
    }

    if (!($package->level_id == 0 || in_array($level_id, explode(",", $package->level_id)))) {
      return $this->_forward('notfound', 'error', 'core');
    }
    if (in_array($ad_type, array('default'))) {
      // Copy Ad
      if (null !== ($copy = $this->_getParam('copy'))) {

        if (!$this->_helper->requireAuth()->setAuthParams('communityad', null, 'create')->isValid())
          return;
        $this->view->copy = $copy;
        if ($userads->owner_id != $this->_viewer_id) {
          return $this->_forward('requireauth', 'error', 'core');
        }

        if (empty($package->enabled)) {
          return $this->_forward('notfound', 'error', 'core');
        }
      } else { // Edit Ad
        if (!$this->_helper->requireAuth()->setAuthParams('communityad', null, 'edit')->isValid())
          return;
        $this->view->copy = $copy = null;

        if (!empty($this->_viewer_id))
          $user_level = $this->_viewer->level_id;
        else
          $user_level = Engine_Api::_()->communityad()->getPublicUserLevel();

        $can_edit = Engine_Api::_()->authorization()->getPermission($user_level, 'communityad', 'edit');

        if (empty($can_edit) || ($can_edit == 1 && $userads->owner_id != $this->_viewer_id)) {
          return $this->_forward('requireauth', 'error', 'core');
        }
      }
    } elseif (in_array($ad_type, array('sponsored_stories'))) {
      if (!$this->_helper->requireAuth()->setAuthParams('communityad', null, 'edit')->isValid())
        return;
      if (!empty($this->_viewer_id))
        $user_level = $this->_viewer->level_id;
      else
        $user_level = Engine_Api::_()->communityad()->getPublicUserLevel();

      $can_edit = Engine_Api::_()->authorization()->getPermission($user_level, 'communityad', 'edit');

      if (empty($can_edit) || ($can_edit == 1 && $userads->owner_id != $this->_viewer_id)) {
        return $this->_forward('requireauth', 'error', 'core');
      }
    }
    $this->view->communityad_package_info = Zend_Registry::get('communityad_package_info');
    // check if design faq and target faq are enabled
    $infopageTable = Engine_Api::_()->getItemTable('communityad_infopage');
    $this->view->target_faq = $target_faq = $infopageTable->fetchRow(array('faq = ?' => 3, 'status = ?' => 1))->status;
    if (in_array($ad_type, array('default'))) {

      $this->view->design_faq = $design_faq = $infopageTable->fetchRow(array('faq = ?' => 2, 'status = ?' => 1))->status;

      if ($copy) {
        // FOR COPY
        $this->view->form = $form = new Communityad_Form_Create(array(
            'item' => $userads, 'packageId' => $userads->package_id, 'copy' => '1'
        ));
      } else {
        // FOR EDIT
        $this->view->form = $form = new Communityad_Form_Create(array(
            'item' => $userads, 'packageId' => $userads->package_id
        ));
        $form->campaign_id->setAttrib('disable', 'true');
      }
      // GET ENABLE MODULES SET BY ADMIN
      $levels_prepared = Engine_Api::_()->communityad()->enabled_module_content($userads->package_id);
      if (!empty($levels_prepared)) {
        $this->view->is_customAs_enabled = $levels_prepared[0];
        $this->view->is_moduleAds_enabled = $levels_prepared[2];
      }
    } elseif (in_array($ad_type, array('sponsored_stories'))) {

      $this->view->form = $form = new Communityad_Form_SponsoredStory_Create(array('packageId' => $userads->package_id));
      $form->campaign_id->setAttrib('disable', 'true');
      $form->campaign_id->setValue($userads->campaign_id);
      $this->view->design_faq = $design_faq = $infopageTable->fetchRow(array('infopage_id = ?' => 100))->status;
      $form->editTitle->setValue($userads->cads_title);
//       $this->view->storyTitle = $userads->cads_title;
    }
    $this->view->notshowapprovedMessage = $package['auto_aprove'];
    $this->view->mode = $mode = 1;
    // SET VALUES BASE ON PACKAGE
    $form->package_name->setDescription("<a href=\"javascript:void(0);\" onclick=\"Smoothbox.open('" . $this->view->url(array('module' => 'communityad', 'controller' => 'index', 'action' => 'packge-detail', 'id' => $package->package_id, 'onlydetails' => 1), 'default') . "')\"  >" . ucfirst($package['title']) . "</a>");
    $form->owner_id->setValue($this->_viewer_id);
    $form->package_id->setValue($package['package_id']);
    $this->view->enableTarget = $enableTarget = $package['network'];

    $this->view->enableCountry = 0;
    $this->view->profileSelect_id = 0;

    $this->view->topLevelId = $topLevelId = 0;
    $this->view->topLevelValue = $topLevelValue = null;

    // Get form
    $this->view->formField = $formField = new Fields_Form_Standard(array(
        'item' => Engine_Api::_()->core()->getSubject(),
        'topLevelId' => $topLevelId,
        'topLevelValue' => $topLevelValue,
    ));
    $formField->setTitle($this->view->translate('Targeting'));

    // GET TARGET FIELDS
    $targetFields = Engine_Api::_()->getItemTable('target')->getFields();

    $targetFieldIds = array();
    $targetMapIds = array();
    // GET TARGET FIELDS ID
    foreach ($targetFields as $targetField) {
      $targetFieldIds[] = $targetField->field_id;
    }

    $req_field_id = $targetFieldIds;
    $mapTable = Engine_Api::_()->getItemTable('map');
    $select = $mapTable->select();
    $targetFieldStr = (string) ( "'" . join("', '", $targetFieldIds) . "'");
    $select->where('child_id in (?)', new Zend_Db_Expr($targetFieldStr));

    $fieldStructure = $mapTable->fetchAll($select)->toArray();

    foreach ($fieldStructure as $key => $value) {
      $fieldStructure[$value['field_id'] . '_' . $value['option_id'] . '_' . $value['child_id']] = $value;
      if (isset($fieldStructure[$key]))
        unset($fieldStructure[$key]);
    }

    //Refined field structure
    $newFieldStructure = $fieldStructure;
    $type = array();
    $gender_key = "";

    // General form without profile type
    $newFieldKeys = array_keys($newFieldStructure);
    $elements = $formField->getElements();
    foreach ($elements as $key1 => $val1) {
      $formField->removeElement($key1);
    }

    // list of not add tageting fields
    $not_addType = array('text', 'textarea', 'select', 'radio', 'checkbox', 'multiselect', 'multi_checkbox', 'integer', 'float', 'date', 'heading');
    // list of fields there discription change
    $addDiscription = array('first_name', 'last_name', 'website', 'twitter', 'facebook', 'aim', 'about_me', 'city', 'zip_code', 'location', 'interests');

    $eLabel = array();
    $structure = Engine_Api::_()->getApi('core', 'communityad')->getFieldsStructureSearch('user');

    $listFieldValue = array();
    $fieldElements = array();
    // Start trageting element
    $index = 0;
    if (!empty($enableTarget)) {
      foreach ($structure as $map) {

        $field = $map->getChild();
        $index++;

        if (!in_array($field->field_id, $req_field_id)) {
          continue;
        }

        if (in_array($field->type, $not_addType))
          continue;
        // Get target fields key
        $key = null;
        if (!empty($field->alias)) {
          $key = $field->alias;
        } else {
          $key = sprintf('field_%d', $field->field_id);
        }

        // Get params
        $values = $field->getElementParams('user', array('required' => false));

        if (!@is_array($values['options']['attribs'])) {
          $values['options']['attribs'] = array();
        }

        // Remove some stuff
        unset($values['options']['required']);
        unset($values['options']['allowEmpty']);
        unset($values['options']['validators']);

        // Change order
        $values['options']['order'] = $index;

        // Get generic type
        $info = Engine_Api::_()->fields()->getFieldInfo($field->type);
        $genericType = null;
        if (!empty($info['base'])) {
          $genericType = $info['base'];
        } else {
          $genericType = $field->type;
        }
        $values['type'] = $genericType; // For now
        // change birthdate->age
        if ($field->type == 'birthdate') {
          $values['type'] = 'Select';
          $values['options']['label'] = 'Age';
          $multiOptions = array('' => $this->view->translate('Any'));
          for ($i = 13; $i <= 100; $i++) {
            $multiOptions[$i] = $i;
          }
          $values['options']['multiOptions'] = $multiOptions;
        }

        // Populate country multiOptions
        if ($field->type == 'country') {
          $locale = Zend_Registry::get('Zend_Translate')->getLocale();
          $territories = Zend_Locale::getTranslationList('territory', $locale, 2);

          asort($territories);
          $this->view->enableCountry = 1;
          $genericType = $values['type'] = 'Text';
          $listFieldValue[$key] = $territories;
        }

        if ($field->type == 'gender') {
          $listFieldValue[$key] = $values['options']['multiOptions'];
        }

        // convert into multicheckbox
        if ($field->type == 'ethnicity' || $field->type == 'looking_for' || $field->type == 'partner_gender' || $field->type == 'relationship_status' || $field->type == 'occupation' || $field->type == 'religion' || $field->type == 'zodiac' || $field->type == 'weight' || $field->type == 'political_views') {
          $genericType = $values['type'] = 'MultiCheckbox';

          if (empty($values['options']['multiOptions']['']))
            unset($values['options']['multiOptions']['']);

          $listFieldValue[$key] = $values['options']['multiOptions'];
        }

        if (in_array($field->type, $addDiscription)) {
          $values['options']['description'] = $this->view->translate("Separate multiple entries with commas.");
        }

        $eLabel[$key]['lable'] = $values['options']['label'];
        $eLabel[$key]['field_id'] = $field->field_id;
        $eLabel[$key]['type'] = $values['type'];
        if ($field->type == 'gender') {
          $gender_key = $key;
          $eLabel[$key]['type'] = $field->type;
        }
        switch ($genericType) {
          // Ranges
          case 'date':
            // Use subform
            $subform = new Zend_Form_SubForm(array(
                'description' => $values['options']['label'],
                'order' => $values['options']['order'],
                'decorators' => array(
                    'FormElements',
                    array('Description', array('placement' => 'PREPEND', 'tag' => 'div', 'class' => 'form-label')),
                    array('HtmlTag', array('tag' => 'div', 'class' => 'form-wrapper'))
                )
            ));
            Fields_Form_Standard::enableForm($subform);
            Engine_Form::enableForm($subform);
            unset($values['options']['label']);
            unset($values['options']['order']);
            $values['options']['decorators'] = array('ViewHelper', array('HtmlTag', array('tag' => 'div', 'class' => 'form-element form-element-age')));
            $subform->addElement($values['type'], 'min', $values['options']);
            $values['options']['label'] = ' - ';
            $subform->addElement($values['type'], 'max', $values['options']);
            $formField->addSubForm($subform, $key);
            break;
          // Select types
          case 'select':
          case 'radio':
          case 'multiselect':
          case 'multi_checkbox':
            // Ignore if there is only one option
            if (count(@$values['options']['multiOptions']) <= 1) {
              continue;
            }
            if (count(@$values['options']['multiOptions']) <= 2 && isset($values['options']['multiOptions'][''])) {
              continue;
            }

            $listFieldValue[$key] = $values['options']['multiOptions'];
            $formField->addElement($values['type'], $key, $values['options']);

            break;
          // Normal
          default:
            $formField->addElement($values['type'], $key, $values['options']);
            break;
        }

        if (in_array($field->type, $addDiscription)) {
          $formField->$key->getDecorator("Description")->setOption("placement", "append");
        }
        // for country
        if ($field->type == 'country') {
          $formField->addElement('Hidden', 'toValues', array(
              'required' => true,
              'allowEmpty' => false,
              'order' => ++$index,
              'validators' => array(
                  'NotEmpty'
              ),
              'filters' => array(
                  'HtmlEntities'
              ),
          ));
          Engine_Form::addDefaultDecorators($formField->toValues);
        }
        $element = $formField->$key;
        $fieldElements[$key] = $element;
      }
    }


    $birthday_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('target.birthday', 0);

    $this->view->birthday_enable = 0;
    if (!empty($birthday_enable) && $enableTarget) {

      $formField->addElement('Checkbox', 'birthday_enable', array(
          'label' => 'Target people having their birthday on current date.',
          'description' => 'Birthday'
      ));
      $this->view->birthday_enable = 1;
    }
    /* -----------------
     * Targeting for Genric Fields
     */
    $count_profile = 0;
    $profile = array();
    $profile_fields = array();
    if (!empty($enableTarget)) {
      // fields that are not includeing for targeting
      $addType = array('text', 'textarea', 'select', 'radio', 'checkbox', 'multiselect', 'multi_checkbox');
      $not_addType = array('first_name', 'last_name', 'website', 'gender', 'aim', 'city', 'country', 'twitter', 'facebook', 'political_views', 'income', 'eye_color', 'currency', 'birthdate', 'integer', 'float', 'date', 'heading', 'about_me', 'location', 'zip_code', 'looking_for', 'ethnicity', 'occupation', 'education_level', 'religion', 'relationship_status', 'partner_gender', 'interests');
      $structure = Engine_Api::_()->getApi('core', 'communityad')->getFieldsStructureSearch('user');


      $options = Engine_Api::_()->getDBTable('options', 'communityad')->getAllProfileTypes();
      if (empty($options)) {
        return;
      }
      $count_profile = @count($options);
      // Start create targeting fields
      $profile_base_targeting_flage = 1;
      // ELEMENTS OF PROFILE TYPE SPECIFY
      $formField->addElement('Dummy', 'profile_base_targeting', array(
          'label' => '<b>' . $this->view->translate('Advanced Targeting Options') . '</b>',
          'decorators' => array(
              'ViewHelper',
              array('Label', array('placement' => 'PREPEND', 'escape' => false)),
              array('HtmlTag', array('tag' => 'div', 'style' => 'width:100%;margin-bottom:10px;font-size:11px;')))
      ));

      if ((boolean) Engine_Api::_()->getApi('settings', 'core')->getSetting('community.target.network', 0) && Engine_Api::_()->communityad()->hasNetworkOnSite() && $enableTarget) {
        //Add network fields
        $formField->addElement('Multiselect', 'networks', array(
            'Label' => Zend_Registry::get('Zend_Translate')->_("Select Networks"),
            'description' => Zend_Registry::get('Zend_Translate')->_('Networks based advanced targeting enables you to target your ad to users of specific networks. Enter the networks, separated by commas, to which you want your ad to be targeted, using the auto-suggest box below. To reach all networks, simply leave the box empty.'),
            'attribs' => array('style' => 'height:100px; '),
            'multiOptions' => $this->getNetworkLists()
        ));
        $eLabel['networks']['lable'] = Zend_Registry::get('Zend_Translate')->_('Networks');
        $listFieldValue['networks'] = $this->getNetworkLists();
        $listFieldValuekey['networks']['key'] = 'networks';
        $eLabel['networks']['type'] = 'Multiselect';
      }
      $index++;

      $profile_base_targeting_flage = 0;
      if ($count_profile > 1) {
        $formField->addElement('radio', 'profile', array(
            'label' => Zend_Registry::get('Zend_Translate')->_('Select Profile Type'),
            'description' => Zend_Registry::get('Zend_Translate')->_('Profile types based advanced targeting enables you to target your ad to users of a specific profile type. Select the profile type that you want to target to, or choose "All" to reach all profile types.'),
            'onclick' => 'profileFields(this.value)',
            'order' => $index,
        ));
      }
      $formField->addElement('Dummy', 'profile_base_msg', array(
          'label' => Zend_Registry::get('Zend_Translate')->_('Refine Targeting'),
          'description' => Zend_Registry::get('Zend_Translate')->_('You can further refine targeting of your ad for users of this profile type using the below fields.'),
          'order' => $index,
      ));
      $profile = array();
      $profile_fields = array();
      $listFieldValuekey = array();
      foreach ($options->toarray() as $opt) {
        $selectOption = Engine_Api::_()->getDBTable('metas', 'communityad')->getFields($opt['option_id']);
        // ELEMENTS OF PROFILE TYPE SPECIFY
        $profile_field_ids = array();
        $profile[$opt['option_id']] = $opt['label'];
        foreach ($selectOption as $key => $fieldvalue) {
          if (in_array($fieldvalue['type'], $not_addType))
            continue;
          $profile_field_ids[] = $key;
        }

        $profile_targeting_ids = array_intersect($req_field_id, $profile_field_ids);
        if (!empty($profile_targeting_ids)) {

          foreach ($structure as $map) {
            $field = $map->getChild();
            $index++;

            if (!in_array($field->field_id, $profile_targeting_ids)) {
              continue;
            }

            if (in_array($field->type, $not_addType))
              continue;

            // Get key
            $key = null;
            if (!empty($field->alias)) {
              $key = $field->alias;
            } else {
              $key = sprintf('field_%d', $field->field_id);
            }

            // Get params
            $values = $field->getElementParams('user', array('required' => false));

            if (!@is_array($values['options']['attribs'])) {
              $values['options']['attribs'] = array();
            }

            // Remove some stuff
            unset($values['options']['required']);
            unset($values['options']['allowEmpty']);
            unset($values['options']['validators']);

            // Change order
            $values['options']['order'] = $index;

            // Get generic type
            $info = Engine_Api::_()->fields()->getFieldInfo($field->type);
            $genericType = null;
            if (!empty($info['base'])) {
              $genericType = $info['base'];
            } else {
              $genericType = $field->type;
            }

            $values['type'] = $genericType;
            //change into multicheckbox
            if (@in_array($field->type, array('select', 'radio', 'multiselect', 'multi_checkbox'))) {
              $genericType = $values['type'] = 'MultiCheckbox';

              if (isset($values['options']['multiOptions']['']) && empty($values['options']['multiOptions']['']))
                unset($values['options']['multiOptions']['']);

              if (count(@$values['options']['multiOptions']) <= 0) {
                continue;
              }
              $listFieldValue[$key] = $values['options']['multiOptions'];
              $listFieldValuekey[$key]['key'] = $key;
            }

            if (in_array($field->type, array('text'))) {
              $values['options']['description'] = $this->view->translate("Separate multiple entries with commas.");
            }
            $profile[$opt['option_id']] = $opt['label'];
            $profile_fields[$opt['option_id']][] = $key;
            $eLabel[$key]['lable'] = $values['options']['label'];
            $eLabel[$key]['field_id'] = $field->field_id;
            $eLabel[$key]['type'] = $values['type'];
            // Hacks
            switch ($genericType) {
              // Select types
              case 'select':
              case 'radio':
              case 'multiselect':
              case 'multi_checkbox':
                // Ignore if there is only one option
                if (count(@$values['options']['multiOptions']) <= 1) {
                  continue;
                }
                if (count(@$values['options']['multiOptions']) <= 2 && isset($values['options']['multiOptions'][''])) {
                  continue;
                }
                $listFieldValue[$key] = $values['options']['multiOptions'];
                $listFieldValuekey[$key]['key'] = $key;
                $values['type'] = 'MultiCheckbox';
                $formField->addElement($values['type'], $key, $values['options']);
                break;
              // Normal
              default:
                $formField->addElement($values['type'], $key, $values['options']);
                break;
            }
            if (in_array($field->type, array('text'))) {
              $formField->$key->getDecorator("Description")->setOption("placement", "append");
            }
            $element = $formField->$key;
            $fieldElements[$key] = $element;
          }
        }
      }

      if (!empty($profile)) {
        if ($count_profile > 1) {
          $profile[0] = 'All';
          ksort($profile);
          $formField->getElement('profile')
                  ->setMultiOptions($profile)
                  ->setValue(0);
        } else {
          $this->view->profileSelect_id = key($profile);
          if (empty($profile_fields)) {
            $formField->removeElement('profile_base_msg');
            if (!((boolean) Engine_Api::_()->getApi('settings', 'core')->getSetting('community.target.network', 0) && Engine_Api::_()->communityad()->hasNetworkOnSite() && $enableTarget)) {
              $formField->removeElement('profile_base_targeting');
            }
          } else {
            $formField->getElement('profile_base_msg')
                    ->setDescription("You can further refine targeting of your ad for users using the below fields.");
          }
        }
      } else {
        $formField->removeElement('profile');
        $formField->removeElement('profile_base_msg');
        $formField->removeElement('profile_base_targeting');
      }
    }


    $this->view->formField = $formField;
    $target_elements = $formField->getElements();
    $get_payment_settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.payment.ad', 0);
    $formField->removeElement('submit');

    $this->view->showTargetingTitle = 0;
    $countEL = count($formField->getElements());
    $countSF = count($formField->getSubForms());
    if (!empty($countEL) || !empty($countSF))
      $this->view->showTargetingTitle = 1;


    // $ad_type
    $date = (string) date('Y-m-d');
    // Start Date
    $cads_start_date = new Engine_Form_Element_CalendarDateTime('cads_start_date');
    $cads_start_date->setLabel($this->view->translate("Start Date"));
    $cads_start_date->setAllowEmpty(false);
    $cads_start_date->setValue($date . ' 00:00:00');
    $cads_start_date->setOrder('994');
    $formField->addElement($cads_start_date);

    //Enable End Date
    $formField->addElement('Checkbox', 'enable_end_date', array(
        'label' => 'Run my ad continuously from starting date till it expires.',
        'value' => 0,
        'order' => 995,
    ));

    // End Date
    $cads_end_date = new Engine_Form_Element_CalendarDateTime('cads_end_date');
    $cads_end_date->setLabel($this->view->translate("End Date"));
    $cads_end_date->setAllowEmpty(false);
    $cads_end_date->setValue('0000-00-00 00:00:00');
    $cads_end_date->setOrder('996');
    $formField->addElement($cads_end_date);
    $this->view->listFieldValue = $listFieldValue;

    $formField->addElement('Button', 'continue_review', array(
        'label' => 'Continue',
        'order' => 999,
        'ignore' => true,
    ));
    $formField->addElement('Button', 'continue', array(
        'label' => 'Continue',
        'order' => 1000,
        'ignore' => true,
    ));

    if ($count_profile <= 1 && $enableTarget) {
      $formField->addElement('hidden', 'profile', array(
          'value' => $this->view->profileSelect_id,
      ));
    }
    $this->view->profile = $profile;
    $this->view->formField = $formField;
    $this->view->eLabel = $eLabel;
    $this->view->profileField = $profile_fields;

    $this->view->photoName = '';
// set advertiesment values before edit
    if (!$this->getRequest()->isPost()) {
      $useradsArray = $userads->toarray();
      if ($count_profile > 1)
        $this->view->profileSelect_id = $useradsArray['profile'];

      if (in_array($ad_type, array('default'))) {
        $useradsArray['name'] = $useradsArray['cads_title'];

        if ($useradsArray['like']) {
          $useradsArray['content_title'] = $useradsArray['name'];
        }
      }
      $this->view->titileName = $useradsArray['cads_title'];
      $useradsArray['enable_end_date'] = 1;
      if (!empty($useradsArray['cads_end_date']))
        $useradsArray['enable_end_date'] = 0;
      else
        unset($useradsArray['cads_end_date']);
      // Convert and re-populate times
      $start = strtotime($useradsArray['cads_start_date']);
      if (isset($useradsArray['cads_end_date'])) {
        $end = strtotime($useradsArray['cads_end_date']);
      }
      $oldTz = date_default_timezone_get();
      date_default_timezone_set($this->_viewer->timezone);
      $useradsArray['cads_start_date'] = date('Y-m-d H:i:s', $start);
      if (isset($useradsArray['cads_end_date'])) {
        $useradsArray['cads_end_date'] = date('Y-m-d H:i:s', $end);
      }
      date_default_timezone_set($oldTz);
      $form->populate($useradsArray);

      unset($useradsArray['weight']);
      // get tageting for this advertisment
      $userAdTargets = Engine_Api::_()->getDbtable('adtargets', 'communityad')->getUserAdTargets($id);

      // arrange targeting value for set in form
      if (!empty($userAdTargets)) {

        $userAdTargets = $userAdTargets->toarray();

        foreach ($userAdTargets as $tKey => $tValue) {
          if (!isset($listFieldValuekey[$tKey]['key']))
            $listFieldValuekey[$tKey]['key'] = new Zend_Db_Expr('NULL');;
          if (in_array($tKey, array('ethnicity', 'looking_for', 'partner_gender', 'relationship_status', 'occupation', 'religion', 'zodiac', 'weight', 'political_views')) || $tKey == $listFieldValuekey[$tKey]['key']) {
            $userAdTargets[$tKey] = explode(',', $tValue);
          }
        }
        // for minimum age
        if (isset($userAdTargets['age_min']) && !empty($userAdTargets['age_min'])) {
          $birthdateForm = $formField->getSubForm('birthdate');
          if ($birthdateForm) {
            $age['min'] = $userAdTargets['age_min'];
            $birthdateForm->populate($age);
          }
        }
        // for maximum age
        if (isset($userAdTargets['age_max']) && !empty($userAdTargets['age_max'])) {
          $birthdateForm = $formField->getSubForm('birthdate');
          if ($birthdateForm) {
            $age['max'] = $userAdTargets['age_max'];
            $birthdateForm->populate($age);
          }
        }
        // for gender
        if (isset($userAdTargets['gender']) && !empty($userAdTargets['gender'])) {

          foreach ($listFieldValue['gender'] as $keygender => $valuegender) {
            if ($valuegender == $userAdTargets['gender'])
              break;
          }
          $userAdTargets['gender'] = $keygender;
        }else if (!empty($gender_key)) {
          if (isset($userAdTargets[$gender_key]) && !empty($userAdTargets[$gender_key])) {
            foreach ($listFieldValue[$gender_key] as $keygender => $valuegender) {
              if ($valuegender == $userAdTargets[$gender_key])
                break;
            }
            $userAdTargets[$gender_key] = $keygender;
          }
        }

        // for country
        if (isset($userAdTargets['country'])) {
          $locale = Zend_Registry::get('Zend_Translate')->getLocale();
          $territories = Zend_Locale::getTranslationList('territory', $locale, 2);
          asort($territories);
          $countryValue = explode(',', $userAdTargets['country']);
          $multi = 0;
          if (count($countryValue) > 1) {
            $multi = 1;
          }
          if ($countryValue !== null) {
            foreach ($countryValue as $ck => $cv) {
              $dataDisplay[$ck]['key'] = $cv;
              $dataDisplay[$ck]['value'] = $territories[$cv];
            }
            $this->view->toCountry = $dataDisplay;
            $formField->toValues->setValue($userAdTargets['country']);
          }
          unset($userAdTargets['country']);
        }
        $useradsArray = array_merge($useradsArray, $userAdTargets);
      }

      if ((boolean) Engine_Api::_()->getApi('settings', 'core')->getSetting('community.target.network', 0) && Engine_Api::_()->communityad()->hasNetworkOnSite() && $enableTarget) {
        if (!empty($userAdTargets['networks'])) {
          // $networkList = $this->getNetworksTitles($userAdTargets['networks']);
          //if (!empty($networkList)) {
          $useradsArray['networks'] = explode(',', $userAdTargets['networks']); //$networkList['title'];
          //}
        }
      }

      $formField->populate($useradsArray);
    }

    // post the form
    if ($this->getRequest()->isPost()) {

      if (isset($_POST['mode']) && !$_POST['mode'])
        $mode = 0;

      $formValues = $_POST;


      if (!empty($formValues['ad_type']) && ($formValues['ad_type'] != 'default')) {
        if (!empty($formValues['resource_type']) && !empty($formValues['resource_id'])) {
          $this->view->editFlag = true;
          $this->view->titleLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('story.char.title', 35);
          $this->view->rootTitleLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.title', 25);
          $this->view->storyResourceType = $formValues['resource_type'];
          $this->view->resource_id = $this->view->storyResourceId = $formValues['resource_id'];
          $this->view->story_type = $formValues['story_type'];
          $getModInfo = Engine_Api::_()->getDbTable('modules', 'communityad')->getModuleInfo($formValues['resource_type']);
          $this->view->getModTitle = $getModInfo['module_title'];
          $this->view->resourceObj = Engine_Api::_()->getItem($formValues['resource_type'], $formValues['resource_id']);
        }
      }

      if (empty($formValues['cads_end_date']['date'])) {
        $formValues['enable_end_date'] = 1;
      }
      if (isset($formValues['profile']) && !empty($formValues['profile'])) {
        foreach ($profile_fields as $key => $profilefield) {
          if ($key == $formValues['profile'])
            continue;
          foreach ($profilefield as $fieldUnset) {
            if (!in_array($fieldUnset, $profile_fields[$formValues['profile']]))
              $formValues[$fieldUnset] = new Zend_Db_Expr('NULL');
          }
        }
        $this->view->profileSelect_id = $formValues['profile'];
      } else {
        foreach ($profile_fields as $key => $profilefield) {
          foreach ($profilefield as $fieldUnset) {
            $formValues[$fieldUnset] = new Zend_Db_Expr('NULL');
          }
        }
      }

      $formValues['owner_id'] = $form->getValue('owner_id');
      $form->populate($formValues);
      $formField->populate($formValues);

      if (in_array($ad_type, array('default'))) {
        // check validation
        // Url is empty
        if (empty($formValues['cads_url']) && empty($formValues['like'])) {
          return;
        }

        if (empty($get_payment_settings)) {
          $formValues['name'] = 0;
        }
        // If Advertisment is create as copy then not select any campaign and add new campaign
        if (!empty($copy)) {
          if (empty($formValues['campaign_name']) && !empty($formValues['campaign_id'])) {
            return;
          }
        }

        // Advertiesment Tittle
        if (empty($formValues['name']) && empty($formValues['like'])) {
          return;
        } elseif (empty($formValues['content_title']) && !empty($formValues['like'])) {
          return;
        } else if (!empty($formValues['content_title']) && !empty($formValues['like'])) {
          $formValues['name'] = $formValues['content_title'];
        }

        // Advertiesment Discription
        if (empty($formValues['cads_body'])) {
          return;
        } else {
          $formValues['cads_body'] = @substr($formValues['cads_body'], 0, (Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.body', 135) + 10));
        }
        // Advertiesment Image
        if (empty($formValues['imageenable'])) {
          return;
        }
      } elseif (in_array($ad_type, array('sponsored_stories'))) {
        if (empty($formValues['name'])) {
          return;
        }
        $this->view->titileName = $formValues['name'];
      }


      $form->populate($formValues);

      if ((boolean) Engine_Api::_()->getApi('settings', 'core')->getSetting('community.target.network', 0) && Engine_Api::_()->communityad()->hasNetworkOnSite() && $enableTarget) {

        if (isset($_POST['networks'])):
          $network_Ids = (string) ( is_array($_POST['networks']) ? join(",", $_POST['networks']) : $_POST['networks'] );
        else:
          $network_Ids = new Zend_Db_Expr('NULL');
        endif;
      }

      $eLabel_Keys = array_keys($eLabel);
      $eLabel_Keys[] = 'Age';

      if (!empty($this->view->enableCountry)) {
        $formValues['country'] = $formValues['toValues'];
      }
      $saveTargetValue = array();
      // set the targeting values which are geting after post
      foreach ($formValues as $key => $value) {

        if ($key == 'Age')
          $key = 'birthdate';
        if (!empty($this->view->enableCountry)) {
          if ($key == 'country')
            $value = explode(',', $value);
        }

        if (in_array($key, $eLabel_Keys)) {
          if ($key !== 'gender' && $key != 'country' && isset($eLabel[$key]['type']) && $eLabel[$key]['type'] != 'gender') {
            $saveTargetValue[$key] = (string) ( is_array($value) ? join(",", $value) : $value );
          } else if ($key == 'country') {
            $saveTargetValue[$key] = (string) ( is_array($value) ? !empty($value['0']) ? join(",", $value) : new Zend_Db_Expr('NULL')  : $value );
            if (is_array($value) && empty($value['0'])) {
              unset($saveTargetValue[$key]);
            }
            if (isset($saveTargetValue[$key]) && !empty($saveTargetValue[$key])) {
              $locale = Zend_Registry::get('Zend_Translate')->getLocale();
              $territories = Zend_Locale::getTranslationList('territory', $locale, 2);
              asort($territories);
              $countryValue = explode(',', $saveTargetValue[$key]);
              $multi = 0;
              if (count($countryValue) > 1) {
                $multi = 1;
              }
              if ($countryValue !== null) {
                foreach ($countryValue as $ck => $cv) {
                  $dataDisplay[$ck]['key'] = $cv;
                  $dataDisplay[$ck]['value'] = $territories[$cv];
                }
                $this->view->toCountry = $dataDisplay;
                $formField->toValues->setValue($saveTargetValue[$key]);
              }
            }
          } else {
            if (isset($listFieldValue[$key][$value]))
              $saveTargetValue[$key] = (string) ($listFieldValue[$key][$value]);
          }

          if (!empty($value) && !(is_scalar($value))) {
            $range = array();
            foreach ($value as $subKey => $subValue)
              $range[$subKey] = $subValue;
            $range_value = '';
            // For Range value
            if (isset($range['min']) && isset($range['max'])) {
              if (is_scalar($range['min']) && is_scalar($range['max'])) {

                if ((!empty($range['min']) && !empty($range['max']))) {
                  if ($range['max'] < $range['min']) {
                    $min = $range['max'];
                    $max = $range['min'];
                  } else {
                    $min = $range['min'];
                    $max = $range['max'];
                  }

                  $saveTargetValue['age_min'] = $min;
                  $saveTargetValue['age_max'] = $max;
                  if ($min < $max)
                    $range_value = "Between the ages of " . $min . " and " . $max . " inclusive.";
                  else
                    $range_value = 'age of ' . $min;
                }

                if ((empty($range['min']) && !empty($range['max']))) {
                  $saveTargetValue['age_max'] = $range['max'];
                  $range_value = $range['max'] . " years old and younger";
                }

                if ((!empty($range['min']) && empty($range['max']))) {
                  $saveTargetValue['age_min'] = $range['min'];
                  $range_value = "age " . $range['min'] . " and older";
                }
              } else {
                $min_date = $range['min']['month'] . " " . $range['min']['day'] . " " . $range['min']['year'];
                $max_date = $range['max']['month'] . " " . $range['max']['day'] . " " . $range['max']['year'];
                $range_value = $min_date . " to" . $max_date;
              }
            } else {
              // For more than one value
              $range_value_str = array();
              foreach ($range as $r) {
                if (isset($listFieldValue[$key][$r]))
                  $range_value_str[] = $listFieldValue[$key][$r];
              }
              $range_value = (string) join(",", $range_value_str);
            }
            $eLabel[$key]['value'] = $range_value;
          } else {
            if (isset($eLabel[$key]['type']) && ($eLabel[$key]['type'] == 'select' || $eLabel[$key]['type'] == 'multi_checkbox' || $eLabel[$key]['type'] == 'gender' )) {

              if (!empty($value))
                $value = $listFieldValue[$key][$value];
              else
                $value = '';
            }elseif (isset($eLabel[$key]['type']) && $eLabel[$key]['type'] == 'checkbox') {
              if (!empty($value))
                $value = 'enable';
            }
            $eLabel[$key]['value'] = $value;
          }
        }
      }

      if (!empty($_FILES["image"])) {
        $file = $_FILES["image"]["tmp_name"];
        $file1 = $_FILES["image"]["name"];
        $name = basename($file1);
        $pathName = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/communityad/temporary/' . $name;
      } else if (!empty($formValues['imageName'])) {
        $name = $formValues['imageName'];
        $pathName = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/communityad/temporary/' . $name;
      } else {
        $name = null;
        $pathName = null;
      }


      if (!$mode) {
        // create ad/place order
        $result = array();

        foreach ($eLabel as $key => $Values) {
          if (isset($Values['value']))
            $result[$key] = $Values['value'];
        }

        $result = array_merge($result, $formValues);


        if (in_array($ad_type, array('default'))) {
          $result['photoPath'] = $pathName;
        } elseif (in_array($ad_type, array('sponsored_stories'))) {
          if (!empty($result['story_type'])) {
            $result['story_type'] = 1;
          }

          if (@array_key_exists($values['package_name'])) {
            unset($values['package_name']);
          }
          if (@array_key_exists($values['campaign_name'])) {
            unset($values['campaign_name']);
          }
          if (@array_key_exists($values['continue_next'])) {
            unset($values['continue_next']);
          }

          $result['photoPath'] = '';
          if ($formValues['temp_resource_type']) {
            $result['resource_type'] = $formValues['temp_resource_type'];
          }
          if ($formValues['temp_resource_id']) {
            $result['resource_id'] = $formValues['temp_resource_id'];
          }
          $result['like'] = 1;
          if (!empty($result['resource_type'])) {
            $getModInfo = Engine_Api::_()->getDbTable('modules', 'communityad')->getModuleInfo($result['resource_type']);
            if (!empty($getModInfo)) {
              $result['resource_type'] = $getModInfo['table_name'];
            }
          }
        }

        $result['cads_start_date'] = $formField->cads_start_date->getValue();
        $result['cads_end_date'] = $formField->cads_end_date->getValue();

        $result['photoPath'] = $pathName;

        // save values base on package
        $result['sponsored'] = $package['sponsored'];
        $result['featured'] = $package['featured'];

        $result['public'] = $package['public'];
        $result['price_model'] = $package['price_model'];


        if (in_array($ad_type, array('default')) && $copy) {
          // for copy ad from other ads
          $approved = 0;
          if ($package->isFree())
            $approved = $package['auto_aprove'];

          $result['enable'] = $result['status'] = $result['approved'] = $approved;


          $result['photo_id'] = $userads->photo_id;
          //For Free Package
          if (!empty($approved) && $package->isFree()) {
            $result['approve_date'] = date('Y-m-d H:i:s');

            //For Clicks Base
            if ($package['price_model'] == 'Pay/click')
              $result['limit_click'] = $package['model_detail'];

            //For Views Base
            if ($package['price_model'] == 'Pay/view')
              $result['limit_view'] = $package['model_detail'];

            //For Days Base
            if ($package['price_model'] == 'Pay/period') {
              $result['model_value'] = $package['model_detail'];
              $expiry = $result['model_value'];
              if ($expiry === '-1')
                $result['expiry_date'] = '2250-01-01';
              else
                $result['expiry_date'] = Engine_Api::_()->communityad()->getExpiryDate($expiry);
            }
          }

          if ($package->isFree())
            $result['payment_status'] = 'free';
          else
            $result['payment_status'] = 'initial';
        }else {
          // for edit
          $approved = 0;
          if ($package->isFree())
            $approved = $package['auto_aprove'];
          elseif (!$package->isFree() && $userads->approved)
            $approved = $package['auto_aprove'];

          $result['approved'] = $approved;

          $result['userad_id'] = $id;
          $result['campaign_id'] = $userads->campaign_id;

          if (!empty($approved) && $userads->status == 3 && !empty($userads->cads_end_date) && date('Y-m-d H:i:s', strtotime($userads->cads_end_date)) < date('Y-m-d H:i:s')) {
            if ($userads->enable == 1)
              $result['status'] = 1;
            else
              $result['status'] = 2;
          }

          unset($result['owner_id']);
          unset($result['create_date']);
        }


        // save ad
        $adsSave = Engine_Api::_()->communityad()->saveUserAd($result);

        $saveTargetValue['userad_id'] = $adsSave->userad_id;

        // targeting value
        foreach ($saveTargetValue as $rKey => $rVal) {
          if (empty($rVal))
            $saveTargetValue[$rKey] = new Zend_Db_Expr('NULL');;
        }

        if (!empty($birthday_enable) && isset($formValues['birthday_enable']) && isset($saveTargetValue['birthday_enable'])) {
          $saveTargetValue['birthday_enable'] = $formValues['birthday_enable'];
        }

        if (!isset($saveTargetValue['age_max'])) {
          $saveTargetValue['age_max'] = new Zend_Db_Expr('NULL');
          ;
        }
        if (!isset($saveTargetValue['age_min'])) {
          $saveTargetValue['age_min'] = new Zend_Db_Expr('NULL');
          ;
        }

        if (!isset($saveTargetValue['birthday_enable'])) {
          $saveTargetValue['birthday_enable'] = 0;
        }

        if (!isset($saveTargetValue['networks']))
          $saveTargetValue['networks'] = new Zend_Db_Expr('NULL');

        $targetFields = Engine_Api::_()->getDbtable('adtargets', 'communityad')->setUserAdTargets($saveTargetValue);

        if ($package->isFree() || (!$package->isFree() && ( $adsSave->payment_status != 'initial' && $adsSave->payment_status != 'overdue' ))) {
          if ($copy)
            $state = "saved";
          else
            $state = "edit";
          return $this->_helper->redirector->gotoRoute(array('ad_id' => $adsSave->userad_id, "state" => $state), 'communityad_userad', true);
        } else {

          $this->_session->userad_id = $adsSave->userad_id;
          return $this->_helper->redirector->gotoRoute(array(), 'communityad_payment', true);
        }
      } else {

        if (in_array($ad_type, array('default'))) {
          if (!empty($_FILES["image"]) && empty($formValues['photo_id_filepath'])) {
            $this->view->photoName = $name;
            $this->view->photoDisplay = '<img  src="' . $this->view->baseUrl() . '/public/communityad/temporary/' . $name . '" border="0"   />';
          } else if (!empty($name) && empty($formValues['photo_id_filepath'])) {
            $name = $formValues['imageName'];
            $this->view->photoName = $name;
            $this->view->photoDisplay = '<img  src="' . $this->view->baseUrl() . '/public/communityad/temporary/' . $name . '" border="0"   /> ';
          } else {
            $this->view->photoDisplay = '';
          }
        }

        $this->view->mode = $mode = 0;
      }
      if (isset($formValues['profile']) && !empty($formValues['profile']) && $count_profile > 1) {
        $this->view->profileEnable = $profile[$formValues['profile']];
      } else {
        $this->view->profileEnable = '';
      }
    }
    $this->view->eLabel = $eLabel;
  }

// SHOW TARGETING DETAILS
  public function targetDetailsAction() {

    // Check auth
    if (!$this->_helper->requireUser()->isValid())
      return;

    $id = $this->_getParam('id');
    $this->view->userAds = $userads = Engine_Api::_()->getItem('userads', $id);

    if (empty($userads)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    $userAdTargets = Engine_Api::_()->getDbtable('adtargets', 'communityad')->getUserAdTargets($id);

    $this->view->birthday_enable = $birthday_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('target.birthday', 0);
    $birthdayField = 0;
    $targetDetails = array();

    $targetFields = Engine_Api::_()->getItemTable('target')->getFields();
    $targetCount = count($targetFields);

    if (isset($userads->profile) && !empty($userads->profile)) {
      $optionsProfile = Engine_Api::_()->getDBTable('options', 'communityad')->getAllProfileTypes();
      $count_profile = @count($optionsProfile);
      if ($count_profile > 1) {
        $options = Engine_Api::_()->getDBTable('options', 'communityad')->getProfileType($userads->profile);
        $targetDetails['profile']['label'] = "Profile Type";
        $targetDetails['profile']['value'] = $options;
      }
    }
    if (!empty($userAdTargets)) {
      $userAdTargets = $userAdTargets->toarray();
    }
    if ($targetCount) {
      $this->view->topLevelId = $topLevelId = 0;
      $this->view->topLevelValue = $topLevelValue = null;

      // Get form
      $this->view->formField = $formField = new Fields_Form_Standard(array(
          'item' => Engine_Api::_()->core()->getSubject(),
          'topLevelId' => $topLevelId,
          'topLevelValue' => $topLevelValue,
      ));

      $targetFieldIds = array();
      $targetMapIds = array();
      // GET TARGETING FIELDS ID
      foreach ($targetFields as $targetField) {
        $targetFieldIds[] = $targetField->field_id;
      }
      $req_field_id = $targetFieldIds;

      // OBJECT OF USER_FIELDS_MAP
      $mapTable = Engine_Api::_()->getItemTable('map');
      $select = $mapTable->select();

      $targetFieldStr = (string) ( "'" . join("', '", $targetFieldIds) . "'");
      $select->where('child_id in (?)', new Zend_Db_Expr($targetFieldStr));

      $fieldStructure = $mapTable->fetchAll($select)->toArray();

      foreach ($fieldStructure as $key => $value) {
        $fieldStructure[$value['field_id'] . '_' . $value['option_id'] . '_' . $value['child_id']] = $value;
        unset($fieldStructure[$key]);
      }

      //Refined field structure
      $newFieldStructure = $fieldStructure;
      $type = array();

      // General form without profile type
      $newFieldKeys = array_keys($newFieldStructure);
      $elements = $formField->getElements();
      foreach ($elements as $key1 => $val1) {
        $formField->removeElement($key1);
      }

      $not_addType = array('integer', 'float', 'date');
      $structure = Engine_Api::_()->getApi('core', 'communityad')->getFieldsStructureSearch('user');
      $eLabel = array();
      $listFieldValue = array();
      $fieldElements = array();
      // Start firing away
      $index = 0;
      foreach ($structure as $map) {
        $field = $map->getChild();
        if (!in_array($field->field_id, $req_field_id)) {
          continue;
        }

        if (in_array($field->type, $not_addType))
          continue;
        // Get key
        $key = null;
        if (!empty($field->alias)) {
          $key = $field->alias;
        } else {
          $key = sprintf('field_%d', $field->field_id);
        }

        // Get params
        $values = $field->getElementParams('user', array('required' => false));
        if (!@is_array($values['options']['attribs'])) {
          $values['options']['attribs'] = array();
        }

        // Get generic type
        $info = Engine_Api::_()->fields()->getFieldInfo($field->type);
        $genericType = null;
        if (!empty($info['base'])) {
          $genericType = $info['base'];
        } else {
          $genericType = $field->type;
        }
        $values['type'] = $genericType; // For now
        $listFieldValue[$key]['label'] = $values['options']['label'];
        if ($field->type == 'birthdate') {
          $birthdayField = 1;
        }
        if ($field->type == 'gender') {
          $listFieldValue[$key]['value'] = $values['options']['multiOptions'];
        }

        if (in_array($field->type, array('ethnicity', 'looking_for', 'partner_gender', 'relationship_status', 'occupation', 'religion', 'zodiac', 'weight', 'political_views', 'education_level', 'income', 'select', 'radio', 'multiselect', 'multi_checkbox'))) {
          $genericType = $values['type'] = 'MultiCheckbox';

          if (isset($values['options']['multiOptions']['']) && empty($values['options']['multiOptions']['']))
            unset($values['options']['multiOptions']['']);

          $listFieldValue[$key]['value'] = $values['options']['multiOptions'];
          $listFieldValue[$key]['key'] = $key;
        }
        if ($field->type == 'checkbox') {
          $listFieldValue[$key]['value'] = 'checkbox';
          $listFieldValue[$key]['key'] = $key;
        }
      }

      if (!empty($userAdTargets)) {
        unset($userAdTargets['userad_id']);
        unset($userAdTargets['adtarget_id']);
        foreach ($userAdTargets as $tKey => $tValue) {
          if (!empty($tValue)) {
            if (isset($listFieldValue[$tKey]['label']))
              $targetDetails[$tKey]['label'] = $listFieldValue[$tKey]['label'];
            $targetDetails[$tKey]['value'] = $tValue;

            if (!isset($listFieldValue[$tKey]['key']))
              $listFieldValue[$tKey]['key'] = new Zend_Db_Expr('NULL');
            if (!isset($listFieldValue[$tKey]['value']))
              $listFieldValue[$tKey]['value'] = new Zend_Db_Expr('NULL');
            if ($listFieldValue[$tKey]['value'] == 'checkbox' && $tKey == $listFieldValue[$tKey]['key']) {
              if (!empty($tValue))
                $targetDetails[$tKey]['value'] = 'enable';
              else
                $targetDetails[$tKey]['value'] = new Zend_Db_Expr('NULL');
            }elseif (in_array($tKey, array('ethnicity', 'looking_for', 'partner_gender', 'relationship_status', 'occupation', 'religion', 'zodiac', 'weight', 'political_views', 'education_level', 'income')) || $tKey == $listFieldValue[$tKey]['key']) {

              $targetDetails[$tKey]['value'] = explode(',', $tValue);
              $range_value = '';
              foreach ($targetDetails[$tKey]['value'] as $r) {
                if (empty($range_value)) {
                  $range_value.= $listFieldValue[$tKey]['value'][$r];
                } else {
                  $range_value.= ', ' . $listFieldValue[$tKey]['value'][$r];
                }
              }
              $targetDetails[$tKey]['value'] = $range_value;
            }
          }
        }

        if (isset($userAdTargets['country']) && !empty($userAdTargets['country'])) {
          $locale = Zend_Registry::get('Zend_Translate')->getLocale();
          $territories = Zend_Locale::getTranslationList('territory', $locale, 2);
          asort($territories);
          $countryValue = explode(',', $userAdTargets['country']);

          if ($countryValue !== null) {
            $range_value = '';
            foreach ($countryValue as $ck => $cv) {
              if (empty($range_value)) {
                $range_value.= $territories[$cv];
              } else {
                $range_value.= ', ' . $territories[$cv];
              }
            }
            $targetDetails['country']['label'] = $listFieldValue['country']['label'];
            $targetDetails['country']['value'] = $range_value;
          }
        }
      }

      $range_value = '';
      if (!empty($birthdayField)) {
        if (isset($targetDetails['age_min']) && isset($targetDetails['age_max'])) {
          if ($targetDetails['age_min']['value'] == $targetDetails['age_max']['value']) {
            $range_value = 'age of ' . $targetDetails['age_min']['value'];
          } else {
            $range_value = "Between the ages of " . $targetDetails['age_min']['value'] . " and " . $targetDetails['age_max']['value'] . " inclusive.";
          }
        } elseif (isset($targetDetails['age_min']) && !isset($targetDetails['age_max'])) {
          $range_value = "age " . $targetDetails['age_min']['value'] . " and older";
        } elseif (!isset($targetDetails['age_min']) && isset($targetDetails['age_max'])) {
          $range_value = $targetDetails['age_max']['value'] . " years old and younger";
        }
        if (!empty($range_value)) {
          if (isset($targetDetails['age_min']))
            unset($targetDetails['age_min']);
          if (isset($targetDetails['age_max']))
            unset($targetDetails['age_max']);
          $targetDetails['age']['label'] = 'Age';
          $targetDetails['age']['value'] = $range_value;
        }
      } else {
        if (isset($targetDetails['age_min']))
          unset($targetDetails['age_min']);
        if (isset($targetDetails['age_max']))
          unset($targetDetails['age_max']);
      }
    }
    if (!empty($birthday_enable) && isset($userAdTargets['birthday_enable']) && !empty($userAdTargets['birthday_enable'])) {
      $targetDetails['birthday_enable']['label'] = 'Birthday';
      $targetDetails['birthday_enable']['value'] = "People with their birthday on current date will be targeted.";
    } else {
      if (isset($targetDetails['birthday_enable']))
        if (isset($targetDetails['birthday_enable']))
          unset($targetDetails['birthday_enable']);
    }

    if ((boolean) Engine_Api::_()->getApi('settings', 'core')->getSetting('community.target.network', 0) && Engine_Api::_()->communityad()->hasNetworkOnSite() && isset($userAdTargets['networks'])) {

      $network = array();
      if (!empty($userAdTargets['networks'])) {
        $network = $this->getNetworksTitles($userAdTargets['networks']);

        if (!empty($network)) {
          $targetDetails['networks']['label'] = 'Networks';
          $targetDetails['networks']['value'] = $network['title'];
        } else {
          unset($targetDetails['networks']);
        }
      }
    } else {
      if (isset($targetDetails['networks']))
        unset($targetDetails['networks']);
    }
    if (isset($targetDetails['adtarget_id']))
      unset($targetDetails['adtarget_id']);
    if (isset($targetDetails['userad_id']))
      unset($targetDetails['userad_id']);

    $this->view->targetDetails = $targetDetails;
  }

// RENEW FOR FREE PACKAGES
  public function renewAction() {
    $id = $this->_getParam('id');
    $this->view->userad = $userads = Engine_Api::_()->getItem('userads', $id);
    $package_id = $userads->package_id;
    $this->view->package = $package_id = $package = Engine_Api::_()->getItem('package', $package_id);
    $can_renew = 0;
    switch ($userads->price_model) {
      // FOR VIEWS
      case "Pay/view":
        if ($userads->limit_view != -1) {
          if ($package->renew_before >= $userads->limit_view) {
            $can_renew = 1;
          }
        }

        break;
      // FOR CLICKS
      case "Pay/click":
        if ($userads->limit_click != -1) {
          if ($package->renew_before >= $userads->limit_click) {
            $can_renew = 1;
          }
        }
      // FOR DAYS
      case "Pay/period":
        $diff_days = 0;
        if (!empty($userads->expiry_date) && date('Y-m-d', strtotime($userads->expiry_date)) > date('Y-m-d')) {
          $diff_days = round((strtotime($userads->expiry_date) - strtotime(date('Y-m-d'))) / 86400);
        }
        if (($userads->expiry_date !== '2250-01-01') || empty($userads->expiry_date)) {
          if ($package->renew_before >= $diff_days) {
            $can_renew = 1;
          }
        }
        break;
    }

    if (empty($can_renew)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    if ($this->getRequest()->isPost()) {

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $userad->featured = $package->featured;
        $userad->sponsored = $package->sponsored;
        switch ($userads->price_model) {
          // FOR VIEWS
          case "Pay/view":
            if ($userads->limit_view != -1) {

              if ($package->model_detail == -1)
                $userads->limit_view = $package->model_detail;
              else
                $userads->limit_view += $package->model_detail;
            }

            break;
          // FOR CLICKS
          case "Pay/click":
            if ($userads->limit_click != -1) {
              if ($package->model_detail == -1)
                $userads->limit_click = $package->model_detail;
              else
                $userads->limit_click += $package->model_detail;
              break;
            }
          // FOR DAYS
          case "Pay/period":
            $diff_days = 0;
            if (!empty($userads->expiry_date) && date('Y-m-d', strtotime($userads->expiry_date)) > date('Y-m-d')) {
              $diff_days = round((strtotime($userads->expiry_date) - strtotime(date('Y-m-d'))) / 86400);
            }

            if (($userads->expiry_date !== '2250-01-01') || empty($userads->expiry_date)) {
              if ($diff_days < 0)
                $diff_days = 0;
              if ($package->model_detail == -1) {
                $userads->expiry_date = '2250-01-01';
              } else {

                $userads->expiry_date = Engine_Api::_()->communityad()->getExpiryDate($package->model_detail + $diff_days);
              }
            }
            break;
        }
        $userads->status = 1;
        if (empty($approved)) {
          $userads->approved = $package->auto_aprove;
          $userads->enable = 1;
        }
        $userads->payment_status = 'free';
        $userads->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('')
      ));
    }
    $this->renderScript('index/renew.tpl');
  }

  public function setSessionAction() {
    $this->_session->userad_id = $_POST["ad_ids_session"];
    return $this->_helper->redirector->gotoRoute(array(), 'communityad_payment', true);
  }

// AUTO SUGGETS FOR COUNTRY
  public function countrySuggetAction() {
    $locale = Zend_Registry::get('Zend_Translate')->getLocale();
// GET COUNTRY LIST
    $territories = Zend_Locale::getTranslationList('territory', $locale, 2);
    asort($territories);

    $search = $this->_getParam('text');
    $data = array();
    $mode = $this->_getParam('struct');

    if ($mode == 'text') {
      foreach ($territories as $k => $v) {
        $f = stripos($v, $search);
        if ($f !== false) {
          $data[] = array(
              'id' => $k,
              'label' => $v
          );
        }
      }
    } else {
      foreach ($territories as $k => $v) {
        $f = stripos($v, $search);
        if ($f !== false) {
          $data[] = array(
              'id' => $k,
              'label' => $v
          );
        }
      }
    }


    if ($this->_getParam('sendNow', true)) {
      // return to the retrive records...
      return $this->_helper->json($data);
    } else {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }

// ADD STYLE FILE
  public function communityadsStyleAction() {
    include_once APPLICATION_PATH . '/application/modules/Communityad/Api/style.php';
    exit();
  }

  // GET NETWORK LIST
  public function getNetworkLists() {
    $table = Engine_Api::_()->getDbtable('networks', 'network');
    $select = $table->select()
            ->order('title ASC')
            ->where('hide = ?', 0);
    $lists = $table->fetchAll($select);
    $data = array();
    foreach ($lists as $network) {
      $data[$network->network_id] = $network->title;
    }
    return $data;
  }

// GET NETWORK ID USING NETWORK TITLE
  public function getNetworksId($netowrkTitle) {
    $table = Engine_Api::_()->getDbtable('networks', 'network');
    $netowrkTitleStr = (string) ( is_array($netowrkTitle) ? "'" . join("', '", $netowrkTitle) . "'" : $netowrkTitle );
    $select = $table->select()
            ->from($table->info('name'), array('network_id', 'title'))
            ->where('title in(?)', new Zend_Db_Expr($netowrkTitleStr));
    $result = $table->fetchAll($select);
    $network_ids = array();
    $title = array();
    foreach ($result as $value) {
      $network_ids[] = $value->network_id;
      $title[] = $value->title;
    }
    $return_ids = array();
    if (!empty($network_ids)) {
      $return_ids['ids'] = (string) ( is_array($network_ids) ? join(",", $network_ids) : $network_ids );
      $return_ids['title'] = (string) ( is_array($title) ? join(", ", $title) : $title );
    }

    return $return_ids;
  }

  // GET NETWORK TITLE USING NETWORK ID
  public function getNetworksTitles($netowrkIds) {

    $netowrkIds = preg_split('/[,]+/', $netowrkIds);
    $netowrkIds = array_filter(array_map("trim", $netowrkIds));
    $table = Engine_Api::_()->getDbtable('networks', 'network');
    $idsStr = (string) ( is_array($netowrkIds) ? join(", ", $netowrkIds) : $netowrkIds );
    $select = $table->select()
            ->from($table->info('name'), array('network_id', 'title'))
            ->where('network_id in(?)', new Zend_Db_Expr($idsStr));

    $result = $table->fetchAll($select);
    $network_ids = array();
    $title = array();
    foreach ($result as $value) {
      $network_ids[] = $value->network_id;
      $title[] = $value->title;
    }
    $return_ids = array();
    if (!empty($title)) {
      $return_ids['ids'] = (string) ( is_array($network_ids) ? join(",", $network_ids) : $network_ids );
      $return_ids['title'] = (string) ( is_array($title) ? join(", ", $title) : $title );
    }
    return $return_ids;
  }

  public function showMarkerInDate() {
    $localeObject = Zend_Registry::get('Locale');
    $dateLocaleString = $localeObject->getTranslation('long', 'Date', $localeObject);
    $dateLocaleString = preg_replace('~\'[^\']+\'~', '', $dateLocaleString);
    $dateLocaleString = strtolower($dateLocaleString);
    $dateLocaleString = preg_replace('/[^ymd]/i', '', $dateLocaleString);
    $dateLocaleString = preg_replace(array('/y+/i', '/m+/i', '/d+/i'), array('y', 'm', 'd'), $dateLocaleString);
    $dateFormat = $dateLocaleString;
    return $dateFormat == "mdy" ? 1 : 0;
  }

}

?>
