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
class Resume_Form_Resume_Edit extends Resume_Form_Resume_Create
{
  public $_error = array();
  protected $_item;

  public function getItem()
  {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item)
  {
    $this->_item = $item;
    return $this;
  }
  
  public function init()
  {
    parent::init();
    $this->setTitle('Edit Resume')
         ->setDescription('Edit your resume below, then click "Save Changes" to save your resume.');

    $user = Engine_Api::_()->user()->getViewer();       

    if (!$this->_item->isQueuedStatus())
    {
      $order = $this->package_id->getOrder();
      $this->removeElement('package_id');
      $this->addElement('Dummy', 'package', array(
        'label' => 'Package',
        'content' => $this->_item->getPackage()->__toString(),
        'order' => $order,
      ));
    }
    
    $this->submit->setLabel('Save Changes');

    $this->cancel->setLabel('view');
    $this->cancel->setAttrib('href', $this->_item->getHref());
  }
}