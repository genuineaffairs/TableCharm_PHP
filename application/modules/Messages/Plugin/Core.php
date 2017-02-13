<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Core
 *
 * @author abakivn
 */
class Messages_Plugin_Core extends Zend_Controller_Plugin_Abstract {

  public function postDispatch(Zend_Controller_Request_Abstract $request) {
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    $viewer = Engine_Api::_()->user()->getViewer();
    $new_message_count = Engine_Api::_()->messages()->getUnreadMessageCount($viewer);
    if ($new_message_count) {
      $view->headScript()->prependScript('var message_count = ' . $new_message_count);
    }
  }

}
