<?php 
class Advgroup_Widget_GroupsCategoriesController extends Engine_Content_Widget_Abstract
{
  protected $_navigation;
  public function indexAction()
  {
      $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('advgroup_main');
  }
}