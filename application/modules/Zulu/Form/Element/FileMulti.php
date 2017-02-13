<?php

class Zulu_Form_Element_FileMulti extends Zend_Form_Element_Xhtml {

  // Protected
  protected $_arrOldValues = array();
  protected $_arrDeletePaths = array();
  protected $_nameFiles = '';
  protected $_type = 'fileMulti';
  protected $_tmpDir;
  protected $_tmpFile;
  protected $_skipValidation = false;
  protected $_storageService;
  protected $_pathMap = array();

  /**
   * @var array Private validation rules
   */
  protected $_privateValidators = array('Extension' => '', 'Size' => '');
  // Public
  public $helper = 'formFileMulti';

  public function __construct($spec, $options = null)
  {
    if (Engine_Api::_()->zulu()->isModEnabled('storage') &&
            ($service = Engine_Api::_()->getDbtable('services', 'storage')->getService()) instanceof Storage_Service_S3) {
      $this->_storageService = $service;
    }
    parent::__construct($spec, $options);

    $this->_tmpDir = APPLICATION_PATH_TMP . '/zulu';
    if (!is_dir($this->_tmpDir)) {
      mkdir($this->_tmpDir);
    }
  }

  public function setName($name)
  {
    parent::setName($name);
    $this->_nameFiles = $this->_name . '_files';
    return $this;
  }

  public function loadDefaultDecorators()
  {
    if ($this->loadDefaultDecoratorsIsDisabled()) {
      return;
    }

    $decorators = $this->getDecorators();
    if (empty($decorators)) {
      $this->addDecorator('ViewHelper');
      Engine_Form::addDefaultDecorators($this);
    }
    $this->addDecorator('HtmlTag3', array('tag' => 'div', 'class' => 'multifile-form-element'));
  }

  public function isValid($value, $context = null)
  {
    if ($this->_skipValidation) {
      return true;
    }
    return $this->isFileUploadValid() && parent::isValid($value, $context);
  }

  /**
   * Verify if the uploaded file is valid
   * 
   * @return boolean
   */
  public function isFileUploadValid()
  {
    // Get multiple files uploaded
    $files = $_FILES[$this->_nameFiles];
    $files['tmp_name'] = array_filter($files['tmp_name']);
    $file_count = count($files['tmp_name']);
    $allow_extentions = array_filter(array_map('trim', explode(',', $this->_privateValidators['Extension'])));

    for ($i = 0; $i < $file_count; $i++) {
      foreach ($this->_privateValidators as $key => $option) {
        $file_name = $files['name'][$i];
        $file_size = $files['size'][$i];

        if ($option && $file_name) {
          switch ($key) {
            case 'Extension':
              if (!in_array(pathinfo($file_name, PATHINFO_EXTENSION), $allow_extentions)) {
                $error_message = Zend_Registry::get('Zend_Translate')->_("The file '{$file_name}' has a false extension");
                $this->_messages[] = $error_message;
                return false;
              }
              break;
            case 'Size':
              if ($file_size > $this->_privateValidators[$key]) {
                $allow_size = round(($this->_privateValidators[$key] / 1024), 2) . ' KB';
                $file_size = round(($file_size / 2014), 2) . ' KB';
                $error_message = Zend_Registry::get('Zend_Translate')->_("Maximum allowed size for file '{$file_name}' "
                        . "is '{$allow_size}' but '{$file_size}' detected");
                $this->_messages[] = $error_message;
                return false;
              }
              break;
          }
        }
      }
    }
    return true;
  }

  public function skipValidation($value = true)
  {
    $this->_skipValidation = $value;
  }

  /**
   * In case we do not store the uploaded file right away.
   * Serve in case of multiple steps signup
   */
  public function makeTmpFileFromUpload()
  {
    if (is_uploaded_file($_FILES [$this->_nameFiles] ['tmp_name'])) {
      $this->_tmpFile = $this->_tmpDir . DS . date("YmdHis") . $this->_nameFiles . uniqid() . $_FILES[$this->_nameFiles]['name'];
      move_uploaded_file($_FILES[$this->
              _nameFiles]['tmp_name'], $this->_tmpFile);
    } else {
      return false;
    }
  }

  public function setTmpFile($tmpFile)
  {
    $this->_tmpFile = $tmpFile;
  }

  public function getTmpFile()
  {
    return $this->_tmpFile;
  }

  public function removeTmpFile()
  {

    if (file_exists($this->_tmpFile)) {
      unlink($this->_tmpFile);
    }
  }

  public function setValue($value)
  {
    if (empty($this->_arrOldValues)) {
      $this->_arrOldValues = explode(',', $value);
    }
    if ($_POST[$this->_nameFiles . '_delete']) {
      $this->_arrDeletePaths = array_intersect(explode(',', $_POST[$this->_nameFiles . '_delete']), $this->_arrOldValues);
    }
    // Remove deleted paths
    if (!empty($this->_arrDeletePaths)) {
      $value = array_diff($this->_arrOldValues, $this->_arrDeletePaths);
    } else {
      $value = $this->_arrOldValues;
    }
    if ($this->isFileUploadValid()) {
      return parent::setValue($this->_getRemoteStoredPaths(implode(',', $value)));
    } else {
      return parent::setValue(implode(',', $this->_arrOldValues));
    }
  }

  public function addValidator($validator, $breakChainOnFailure = false, $options = array())
  {
    if (array_key_exists($validator, $this->_privateValidators)) {
      $this->_privateValidators[$validator] = $options;
      return $this;
    }
    return parent::addValidator($validator, $breakChainOnFailure, $options);
  }

  /**
   * Generate S3 paths if files are uploaded, otherwise return the saved paths from db
   */
  protected function _getRemoteStoredPaths($value = null)
  {
    $arrVal = explode(',', $value);
    if ($this->_storageService &&
            is_array($_FILES[$this->_nameFiles]['tmp_name']) &&
            count(array_filter($_FILES[$this->_nameFiles]['tmp_name']))) {
      $arrVal = array_unique(array_merge($arrVal, $this->_generatePaths()));
    }
    return implode(',', array_filter($arrVal));
  }

  /**
   * Generate S3 paths for newly uploaded files
   * 
   * @return array
   * @throws Engine_Application_Exception
   */
  protected function _generatePaths()
  {
    $paths = array();
    $fileCount = count($_FILES[$this->_nameFiles]['tmp_name']);

    for ($i = 0; $i < $fileCount; $i++) {
      $file_path = $_FILES[$this->_nameFiles]['tmp_name'][$i];
      // Check if file exists
      if (is_uploaded_file($file_path)) {
        $filename = $_FILES[$this->_nameFiles]['name'][$i];
      } else {
        continue;
      }
      // Check if file is attached to a specific user
      if (Engine_Api::_()->core()->hasSubject()) {
        $user_id = Engine_Api::_()->core()->getSubject('user')->user_id;
      } else {
        continue;
      }
      $subdir = sprintf("%04x", $user_id) . '/' . sprintf("%04x", $this->{'data-field-id'});
      // Everything is fine, let's add the file to path
      $remote_path = "public/zulu/{$subdir}/{$filename}";
      $paths[] = $remote_path;
      $this->_pathMap[$remote_path] = $file_path;
    }
    return $paths;
  }

  public function store()
  {
    if ($this->_storageService) {
      $this->removeFieldFiles();

      foreach ($this->_pathMap as $remote => $local) {
        $reflectionClass = new ReflectionClass('Storage_Service_S3');

        // Get internal service
        $internalServiceProperty = $reflectionClass->getProperty('_internalService');
        $internalServiceProperty->setAccessible(true);
        /* @var $zendServiceAmazonS3 Zend_Service_Amazon_S3 */
        $zendServiceAmazonS3 = $internalServiceProperty->getValue($this->_storageService);

        // Get S3 bucket
        $bucketProperty = $reflectionClass->getProperty('_bucket');
        $bucketProperty->setAccessible(true);
        $bucket = $bucketProperty->getValue($this->_storageService);

        $zendServiceAmazonS3->putFile($local, $bucket . '/' . $remote, array(
            Zend_Service_Amazon_S3::S3_ACL_HEADER => Zend_Service_Amazon_S3::S3_ACL_PUBLIC_READ,
            'Cache-Control' => 'max-age=864000, public',
        ));
      }
    }
    // Files are successfully stored
    return true;
  }

  public function removeFieldFiles()
  {
    foreach ($this->_arrDeletePaths as $path) {
      $service = Engine_Api::_()->getDbtable('services', 'storage')->getService();
      $service->removeFile($path);
    }
  }

}
