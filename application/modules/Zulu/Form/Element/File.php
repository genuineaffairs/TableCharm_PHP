<?php

class Zulu_Form_Element_File extends Engine_Form_Element_File {

  protected $_type = 'file';
  protected $_item;
  protected $_tmpDir;
  protected $_tmpFile;
  protected $_skipValidation = false;

  public function __construct($spec, $options = null) {
    parent::__construct($spec, $options);

    $this->_tmpDir = APPLICATION_PATH_TMP . '/zulu';
    if (!is_dir($this->_tmpDir)) {
      mkdir($this->_tmpDir);
    }
  }
  
  public function loadDefaultDecorators() {
    parent::loadDefaultDecorators();
    $this->addDecorator('HtmlTag3', array('tag' => 'div', 'class' => 'file-form-element'));
  }

  public function isValid($value, $context = null) {
    if ($this->_skipValidation) {
      return true;
    }
    return parent::isValid($value, $context);
  }

  public function skipValidation($value = true) {
    $this->_skipValidation = $value;
  }

  /**
   * In case we do not store the uploaded file right away.
   * Serve in case of multiple steps signup
   */
  public function makeTmpFileFromUpload() {
    if (is_uploaded_file($_FILES[$this->_name]['tmp_name'])) {
      $this->_tmpFile = $this->_tmpDir . DS . date("YmdHis") . $this->_name . uniqid() . $_FILES[$this->_name]['name'];
      move_uploaded_file($_FILES[$this->_name]['tmp_name'], $this->_tmpFile);
    } else {
      return false;
    }
  }

  public function setTmpFile($tmpFile) {
    $this->_tmpFile = $tmpFile;
  }

  public function getTmpFile() {
    return $this->_tmpFile;
  }

  public function removeTmpFile() {
    if (file_exists($this->_tmpFile)) {
      unlink($this->_tmpFile);
    }
  }

  public function setItem($item) {
    $this->_item = $item;
  }

  public function setValue($value) {

    $value = $this->_getRemoteStoredPath();
    return parent::setValue($value);
  }

  public function getValue() {
    return $this->_getRemoteStoredPath();
  }

  /**
   * Used for S3 storage
   */
  protected function _getRemoteStoredPath() {
    $value = null;

    if (is_uploaded_file($_FILES[$this->_name]['tmp_name']) || file_exists($this->_tmpFile)) {

      $isStorageModEnabled = Engine_Api::_()->zulu()->isModEnabled('storage');

      if ($isStorageModEnabled &&
              ($service = Engine_Api::_()->getDbtable('services', 'storage')->getService()) instanceof Storage_Service_S3) {

        $path = $this->_generatePath();

        $value = $path;
      }
    } else {
      if (($item = $this->_item) instanceof Core_Model_Item_Abstract) {
        $values = Engine_Api::_()->fields()->getFieldsValues($item);
        $field_id = $this->{'data-field-id'};

        $valueRow = $values->getRowMatching(array(
            'field_id' => $field_id,
            'item_id' => $item->getIdentity(),
            'index' => 0
        ));

        if ($valueRow) {
          $value = $valueRow->value;
        }
      }
    }

    return $value;
  }

  /**
   * Generate unique path
   * 
   * @return string
   * @throws Engine_Application_Exception
   */
  protected function _generatePath() {

    $path = '';

    if (is_uploaded_file($_FILES[$this->_name]['tmp_name'])) {
      $file_name = $_FILES[$this->_name]['name'];
      $file_tmp_name = $_FILES[$this->_name]['tmp_name'];
    } elseif (file_exists($this->_tmpFile)) {
      $file_name = $file_tmp_name = $this->_tmpFile;
    } else {
      return $path;
    }

    if (Engine_Api::_()->core()->hasSubject()) {
      $user_id = Engine_Api::_()->core()->getSubject('user')->user_id;
    } elseif (isset($this->_item->user_id)) {
      $user_id = $this->_item->user_id;
    } else {
      return $path;
    }

    $data = @file_get_contents($file_tmp_name);
    if ($data !== false) {
      $subdir = sprintf("%04x", $user_id) . '/' . sprintf("%04x", $this->{'data-field-id'});
      $md5 = substr(md5($file_name . $data), 4, 8);
      $ext = pathinfo($file_name, PATHINFO_EXTENSION);

      $path = "public/zulu/{$subdir}/{$file_name}";
    } else {
      throw new Engine_Application_Exception('Cannot get content of uploaded files');
    }

    return $path;
  }

  public function store($item = null) {

    $path = '';

    if (is_null($item)) {
      $item = $this->_item;
    }

    if (is_uploaded_file($_FILES[$this->_name]['tmp_name'])) {
      $file = $_FILES[$this->_name]['tmp_name'];
    } elseif (file_exists($this->_tmpFile)) {
      $file = $this->_tmpFile;
    } else {
      return false;
    }

    $isStorageModEnabled = Engine_Api::_()->zulu()->isModEnabled('storage');

    if ($isStorageModEnabled &&
            ($service = Engine_Api::_()->getDbtable('services', 'storage')->getService()) instanceof Storage_Service_S3) {

      $this->removeFieldFiles($item);

      $path = $this->_generatePath();

      if ($path) {
        $reflectionClass = new ReflectionClass('Storage_Service_S3');

        // Get internal service
        $internalServiceProperty = $reflectionClass->getProperty('_internalService');
        $internalServiceProperty->setAccessible(true);
        /* @var $zendServiceAmazonS3 Zend_Service_Amazon_S3 */
        $zendServiceAmazonS3 = $internalServiceProperty->getValue($service);

        // Get S3 bucket
        $bucketProperty = $reflectionClass->getProperty('_bucket');
        $bucketProperty->setAccessible(true);
        $bucket = $bucketProperty->getValue($service);

        $zendServiceAmazonS3->putFile($file, $bucket . '/' . $path, array(
            Zend_Service_Amazon_S3::S3_ACL_HEADER => Zend_Service_Amazon_S3::S3_ACL_PUBLIC_READ,
            'Cache-Control' => 'max-age=864000, public',
        ));
      }
    }

    return $path;
  }

  public function removeFieldFiles($item = null) {

    if ($item === null) {
      $item = $this->_item;
    }

    if ($item instanceof Core_Model_Item_Abstract) {
      $values = Engine_Api::_()->fields()->getFieldsValues($item);
      $field_id = $this->{'data-field-id'};

      $valueRow = $values->getRowMatching(array(
          'field_id' => $field_id,
          'item_id' => $item->getIdentity(),
          'index' => 0
      ));

      if ($valueRow) {
        $path = $valueRow->value;
        $service = Engine_Api::_()->getDbtable('services', 'storage')->getService();
        $service->removeFile($path);

        $valueRow->delete();

        return true;
      }
    }
    return false;
  }

}
