<?php

class Mgslapi_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $this->_helper->layout->setLayout('default-simple');   
  }
  
  public function iosAction()
  {
    $this->_helper->layout->setLayout('default-simple');   
  }
  
  public function androidAction()
  {
    $this->_forward('ios');
    $this->_helper->layout->setLayout('default-simple');   
  }
}
