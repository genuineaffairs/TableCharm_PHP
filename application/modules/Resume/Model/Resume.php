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
 
 
 
class Resume_Model_Resume extends SharedResources_Model_Item_Abstract
{
  // Resumes
  
  const STATUS_PENDING = 'pending';
  const STATUS_REVIEWING = 'reviewing';
  const STATUS_APPROVED = 'approved';
  const STATUS_REJECTED = 'rejected';
  const STATUS_CANCELLED = 'cancelled';
  const STATUS_REFUNDED = 'refunded';
  const STATUS_QUEUED = 'queued';

  
  protected $_parent_type = 'user';
  
  protected $_owner_type = 'user';

  protected $_searchTriggers = array('search', 'title', 'description');

  protected $_modifiedTriggers = array('search', 'title', 'description', 'status');
  
  protected $_parent_is_owner = true;

  protected $category;
    
  protected $package;
  
  protected $_location = null;
  
  public function save()
  {
    $where = $this->_getWhereQuery();
    $row = $this->_getTable()->fetchRow($where);

    // Set site id for resume after created
    if (null === $row) {
      $this->site_id = Engine_Api::_()->getApi('core', 'sharedResources')->getSiteId();
    }
    return parent::save();
  }

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array())
  {
    $slug = $this->getSlug();
    
    $params = array_merge(array(
      'route' => 'resume_profile',
      'reset' => true,
      'resume_id' => $this->resume_id,
      'slug' => $slug,
      'tab' => Engine_Api::_()->resume()->getDetailTabId(),
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function getActionHref($action, $params = array())
  {
    $params = array_merge(array(
      'route' => 'resume_specific',
      'reset' => true,
      'resume_id' => $this->resume_id,
      'action' => $action
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }
  
  
  public function getEditHref($params = array())
  {
    return $this->getActionHref('edit', $params);
  }
  
  public function getDeleteHref($params = array())
  {
    return $this->getActionHref('delete', $params);
  }  

  
  public function getCheckoutHref($params = array())
  {
    return $this->getActionHref('checkout', $params);
  }

  public function getCategory()
  {
    if (!($this->category instanceof Resume_Model_Category) || $this->category->getIdentity() != $this->category_id)
    {
      $category = Engine_Api::_()->resume()->getCategory($this->category_id);
      if (!($category instanceof Resume_Model_Category))
      {
        $category = new Resume_Model_Category(array());
      }
      $this->category = $category;
    }

    return $this->category;
  }

  
  public function getPackage()
  {
    if (!($this->package instanceof Resume_Model_Package) || $this->package->package_id != $this->package_id)
    {
      $package = Engine_Api::_()->resume()->getPackage($this->package_id);
      if (!($package instanceof Resume_Model_Package))
      {
        $package = new Resume_Model_Package(array());
      }
      $this->package = $package;
    }

    return $this->package;
  }  
  
  // Interfaces
  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   **/
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   **/
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  /**
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   **/
  public function tags()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }
  
  
  /**
   * Gets a proxy object for the tags handler
   *
   * @return Epayment_Model_DbTable_Epayments Engine_ProxyObject
   **/
  public function epayments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('epayments', 'epayment'));
  }

  
  public function setPhoto($photo)
  {  
    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
    } else if( $photo instanceof Storage_Model_File ) {
      $file = $photo->temporary();
    } else if( $photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id) ) {
      $file = Engine_Api::_()->getItem('storage_file', $photo->file_id)->temporary();
    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
    } else if( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
    } else {
      throw new Resume_Model_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => $this->getType(),
      'parent_id' => $this->getIdentity()
    );

    // Save
    $storage = Engine_Api::_()->storage();

    // Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($path.'/m_'.$name)
      ->destroy();

    // Resize image (profile)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(200, 400)
      ->write($path.'/p_'.$name)
      ->destroy();

    // Resize image (normal)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(140, 160)
      ->write($path.'/in_'.$name)
      ->destroy();

    // Resize image (icon)
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 48, 48)
      ->write($path.'/is_'.$name)
      ->destroy();
      
    // Store
    $iMain = $storage->create($path.'/m_'.$name, $params);
    $iProfile = $storage->create($path.'/p_'.$name, $params);
    $iIconNormal = $storage->create($path.'/in_'.$name, $params);
    $iSquare = $storage->create($path.'/is_'.$name, $params);
    
    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare, 'thumb.icon');
    
    // Remove temp files
    @unlink($path.'/p_'.$name);
    @unlink($path.'/m_'.$name);
    @unlink($path.'/in_'.$name);
    @unlink($path.'/is_'.$name);
    
    // Update row
    $this->photo_id = $iMain->file_id;
    $this->save();
    
    return $this;  
  }
  
  public function removePhoto()
  {
    if (empty($this->photo_id))
    {
      return;
    }
    
    $types = array(null, 'thumb.profile', 'thumb.normal', 'thumb.icon');
    foreach ($types as $type)
    {
      $file = Engine_Api::_()->getApi('storage', 'storage')->get($this->photo_id, $type);
      if ($file)
      {
        $file->remove();
      } 
    }
    
    $this->photo_id = 0;
  }
  
  
  protected function _delete()
  {
    if( $this->_disableHooks ) return;

    // Delete all field values
    $values = Engine_Api::_()->fields()->getFieldsValues($this);
    foreach ($values as $value)
    {
      $value->delete();
    }
    
    // Delete search row
    $search = Engine_Api::_()->fields()->getFieldsSearch($this);
    if ($search)
    {
      $search->delete();
    }

    
    // Delete location
    $location = $this->getLocation();
    if ($location)
    {
      $location->delete();
    }    
    
    // Delete epayments
    $epayments = $this->epayments()->getEpaymentPaginator();
    foreach ($epayments as $epayment)
    {
      $epayment->delete();
    }
    
    // Delete all albums
    $albumTable = Engine_Api::_()->getItemTable('resume_album');
    $albumSelect = $albumTable->select()->where('resume_id = ?', $this->getIdentity());
    foreach( $albumTable->fetchAll($albumSelect) as $resumeAlbum ) {
      $resumeAlbum->delete();
    }
    
    
    $this->removePhoto();
    
    $sections = $this->getSections();
    foreach ($sections as $section) {
      $section->delete();
    }
    
    parent::_delete();
  }

  protected function _insert()
  {
    if( isset($this->status_date) ) {
      $this->status_date = date('Y-m-d H:i:s');
    }    
    
    parent::_insert();
  }
  
  protected function _postInsert()
  {
    parent::_postInsert();
    $this->updateLocation();
  }
  
  protected function _postUpdate()
  {
    parent::_postUpdate();
    if (array_key_exists('location', $this->_modifiedFields)) {
      $this->updateLocation();
    }
  }  
  
  public function updateStatus($status)
  {
    if (!array_key_exists($status, $this->getStatusTypes()))
    {
      throw new Resume_Model_Exception('Invalid Resume status: '.$status);
    }
    
    $this->status = $status;
    $this->status_date = date('Y-m-d H:i:s');
  }
  
  public function updateExpirationDate($package = null, $start_date = null, $part = null)
  {
    if (!($package instanceof Resume_Model_Package))
    {
      $package = $this->getPackage();
    }
    
    if ($package->isForever())
    {
      $this->expiration_settings = 0;
      $this->expiration_date = '0000-00-00 00:00:00';
    }
    else
    {
      $expires_date = $package->calculateExpiresDate($start_date, $part);
      $this->expiration_settings = 1;
      $this->expiration_date = $expires_date->toString('yyyy-MM-dd HH:mm:ss');
    }
    
  }
  
  public function hasExpirationDate()
  {
    return $this->expiration_settings ? true : false;  
  }
  
  public function isExpired()
  {
    return $this->hasExpirationDate() && time() > strtotime($this->expiration_date);
  }
  
  public function isPublished()
  {
    return $this->published == 1;
  }
  
  public function isLive()
  {
    return $this->isPublished() && $this->isApprovedStatus() && !$this->isExpired();  
  }
  
  public function isQueuedStatus()
  {
    return $this->isStatus(self::STATUS_QUEUED);  
  }  
  
  public function isPendingStatus()
  {
    return $this->isStatus(self::STATUS_PENDING);  
  }
  
  public function isApprovedStatus()
  {
    return $this->isStatus(self::STATUS_APPROVED);  
  }
  
  public function isRejectedStatus()
  {
    return $this->isStatus(self::STATUS_REJECTED);  
  }
  
  
  public function isStatus($status)
  {
    return $this->status == $status;
  }

  public function getStatusText()
  {
    return Zend_Registry::get('Zend_Translate')->translate(self::getStatusTypes($this->status));
  }
  
  static public function getStatusTypes($key=null)
  {
    $types = array(
      self::STATUS_APPROVED => 'Approved',
      self::STATUS_REJECTED => 'Rejected',
      
      self::STATUS_CANCELLED => 'Cancelled',
      self::STATUS_REFUNDED => 'Refunded',
      
      self::STATUS_REVIEWING => 'Reviewing',
      
      self::STATUS_PENDING => 'Pending',
      self::STATUS_QUEUED => 'Queued',
    );
    
    if ($key !== null) {
      return (isset($types[$key])) ? $types[$key] : 'pending';
    }
    
    return $types;
  }
  
  
  public function getLocation()
  {
    if ($this->location)
    {
      if ($this->_location === null)
      {
        $this->_location = Engine_Api::_()->getApi('location', 'radcodes')->getSingletonLocation($this);
      }
      return $this->_location;
    }
  }
  
  public function updateLocation()
  {
    Engine_Api::_()->getApi('location', 'radcodes')->updateMappableLocation($this, $this->location);
  }  
  
  
  public function getSingletonAlbum()
  {
    $table = Engine_Api::_()->getItemTable('resume_album');
    $select = $table->select()
      ->where('resume_id = ?', $this->getIdentity())
      ->order('album_id ASC')
      ->limit(1);

    $album = $table->fetchRow($select);

    if( null === $album )
   {
      $album = $table->createRow();
      $album->setFromArray(array(
        'resume_id' => $this->getIdentity()
      ));
      $album->save();
    }

    return $album;
  }
  

  public function processIpnData($values)
  {
    // do validation
    $user = Engine_Api::_()->getItem('user', $values['user_id']);
    if (!($user instanceof User_Model_User))
    {
      throw new Resume_Model_Exception("Could not find user with id=".$values['user_id']);
    }
    
    $package = Engine_Api::_()->getItem('resume_package', $values['package_id']);
    if (!($package instanceof Resume_Model_Package))
    {
      throw new Resume_Model_Exception("Could not find resume package with id=".$values['package_id']);
    }

    //$epayment_count = $this->epayments()->getEpaymentCount();
    
    $options = array();
    $base_start = 'skip';
    
    if ($values['status'] == Epayment_Model_Epayment::STATUS_COMPLETED)
    {
      if ($this->isApprovedStatus() && $this->getPackage()->isSelf($package))
      {
        $base_start = 'expiration_date';
      }
      else 
      {
        $base_start = 'current_date';
      }    
    }
    $options['base_start_expiration_date'] = $base_start;

    $values['processed'] = 0;
    $epayment = $this->addEpayment($values);

    if ($package->auto_process)
    {
      Engine_Api::_()->resume()->processEpayment($epayment, $options);
    }

    return $epayment;
  }
  
  public function addEpayment($values)
  {
    // need to check for dup
    $epayment = $this->epayments()->getEpayment(array('transaction_code' => $values['transaction_code'], 'resource' => $this));
    
    if ($epayment instanceof Epayment_Model_Epayment) 
    {
      unset($values['user_id']);
      $epayment->setFromArray($values);
      $epayment->save();
    }
    else 
    {
      $epayment = $this->epayments()->addEpayment($values);
    }
    
    return $epayment;
  }
  
  
  public function requiresEpayment()
  {
    return !$this->getPackage()->isFree();
  }
  
  public function getRecentEpayment()
  {
    return $this->epayments()->getRecentEpayment();
  }
  
  public function getPrice()
  {
    $currency = Engine_Api::_()->resume()->getPriceUnit();
    $view = Zend_Registry::get('Zend_View');
    
    $str = $view->locale()->toCurrency($this->price, $currency);

    return $str;
  }
  
  ///////
  
  public function getSections()
  {
    $table = Engine_Api::_()->getItemTable('resume_section');
    $sections = $table->getSections(array('resume' => $this->getIdentity()));
    return $sections;
  }
  
  public function getFieldValueString($fieldLabel) {
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    if (!$view) {
      return null;
    }
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    
    $metaData = Engine_Api::_()->fields()->getFieldsMeta('resume');

    $field = $metaData->getRowMatching(array('label' => $fieldLabel));

    $value = $field->getValue($this);

    $helperName = Engine_Api::_()->fields()->getFieldInfo($field->type, 'helper');

    $helper = $view->getHelper($helperName);

    return $helper->$helperName($this, $field, $value);
  }

}