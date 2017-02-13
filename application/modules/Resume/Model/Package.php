<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @package   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Resume_Model_Package extends Core_Model_Item_Abstract
{
  // Resumes
  protected $_searchTriggers = array();

  // General
/*
      'day' => 'Day(s)',
      'week' => 'Week(s)',
      'month' => 'Month(s)',
      'year' => 'Year(s)',
      'forever' => 'Forever',
 */  
  
  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'resume_package_profile',
      'reset' => true,
      'package_id' => $this->package_id,
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }
	
  public function getTable()
  {
    if( is_null($this->_table) )
    {
      $this->_table = Engine_Api::_()->getDbtable('packages', 'resume');
    }

    return $this->_table;
  }

  public function getResumeCount($params = array()) 
  {
    $params = array_merge($params, array('package' => $this));  
    return Engine_Api::_()->resume()->countResumes($params);
    /*
    $table  = Engine_Api::_()->getDbTable('resumes', 'resume');
    $rName = $table->info('name');
    $select = $table->select()
                    ->from($rName)
                    ->where($rName.'.package_id = ?', $this->package_id);
    $row = $table->fetchAll($select);
    $total = count($row);
    return $total;
    */
  }

  public function getEpaymentCount($params = array())
  {
    $params = array_merge($params, array(
      'resource_type' => 'resume',
      'package_id' => $this->package_id
    ));
    
    return Engine_Api::_()->epayment()->countEpayments($params);
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

    // Resize image (mini)
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 16, 16)
      ->write($path.'/imn_'.$name)
      ->destroy();
      
    // Store
    $iMain = $storage->create($path.'/m_'.$name, $params);
    $iProfile = $storage->create($path.'/p_'.$name, $params);
    $iIconNormal = $storage->create($path.'/in_'.$name, $params);
    $iSquare = $storage->create($path.'/is_'.$name, $params);
    $iMini = $storage->create($path.'/imn_'.$name, $params);
    
    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare, 'thumb.icon');
    $iMain->bridge($iMini, 'thumb.mini');
    
    // Remove temp files
    @unlink($path.'/p_'.$name);
    @unlink($path.'/m_'.$name);
    @unlink($path.'/in_'.$name);
    @unlink($path.'/is_'.$name);
    @unlink($path.'/imn_'.$name);
    
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
    
    $types = array(null, 'thumb.profile', 'thumb.normal', 'thumb.icon', 'thumb.mini');
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
  
  public function getTerm()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('epayment.currency', 'USD');
    $view = Zend_Registry::get('Zend_View');
    
    $str = $view->locale()->toCurrency($this->price, $currency);
    
   
    // Plan is free
    if( $this->isFree() ) {
      $str = $translate->translate('Free');
    }

    // Add duration, if not forever
    if( !$this->isForever() ) {
      $typeStr = $translate->translate(array($this->duration_type, $this->duration_type . 's', $this->duration), $this->duration);
      $str = sprintf($translate->translate('%1$s for %2$s %3$s'), $str, $this->duration, $typeStr);
    }
    else {
      $str = sprintf($translate->translate('%1$s for forever'), $str);
    }

    return $str;
  }
  
  public function isFree()
  {
    return $this->price == 0;
  }
  
  public function isForever()
  {
    return $this->duration == 0 || $this->duration_type == 'forever';
  }
  
  /**
	 * @return Zend_Date
   */
  public function calculateExpiresDate($start_date = null, $part = null)
  {	
  	if ($start_date !== null)
  	{
  	  $date = new Zend_Date($start_date, $part);
  	}
  	else 
  	{
  	  $date = new Zend_Date();
  	}
  	
  	switch ($this->duration_type)
  	{
  		case 'day':
  			$type = Zend_Date::DAY;
  			break;
  			
  		case 'week':
  			$type = Zend_Date::WEEK;
  			break;
  			
  		case 'month':
  			$type = Zend_Date::MONTH;
  			break;
  		
  		case 'year':
  			$type = Zend_Date::YEAR;
  			break;
  	}
  	
  	if ($type)
  	{
  	  $date->add($this->duration, $type);
  	}
  	//echo " start_date=$start_date type=$type duration={$this->duration}";
  	//echo " date=" . $date;
  	//echo " date=" . $date->toString('yyyy-MM-dd HH:mm:ss');
  	return $date;
  }
  
  protected function _delete()
  {
    $this->removePhoto();

    if( $this->_disableHooks ) return;
    parent::_delete();
  }  
}