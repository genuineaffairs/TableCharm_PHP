<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_Widget_TagcloudSitepagenoteController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //CONSTRUCTING TAG CLOUD
    $tag_array = array();
		$noteTable = Engine_Api::_()->getDbtable('notes', 'sitepagenote');
		$this->view->count_only = $noteTable->getTagCloud(20, 1);
		if($this->view->count_only <= 0) {
			return $this->setNotRender();
		}
    $tag_cloud_array = $noteTable->getTagCloud(20, 0);

    foreach ($tag_cloud_array as $vales) {
      $tag_array[$vales['text']] = $vales['Frequency'];
      $tag_id_array[$vales['text']] = $vales['tag_id'];
    }

    if (!empty($tag_array)) {
      $max_font_size = 18;
      $min_font_size = 12;
      $max_frequency = max(array_values($tag_array));
      $min_frequency = min(array_values($tag_array));
      $spread = $max_frequency - $min_frequency;
      if ($spread == 0) {
        $spread = 1;
      }
      $step = ($max_font_size - $min_font_size) / ($spread);

      $tag_data = array('min_font_size' => $min_font_size, 'max_font_size' => $max_font_size, 'max_frequency' => $max_frequency, 'min_frequency' => $min_frequency, 'step' => $step);

      $this->view->tag_data = $tag_data;
      $this->view->tag_id_array = $tag_id_array;
    }
    $this->view->tag_array = $tag_array;

		if(empty($this->view->tag_array)) {
			return $this->setNoRender();
		}
    
    if(isset($_GET['tag'])) {
      $this->view->tag = $_GET['tag'];
    }
    
    if (!empty($_GET['tag']) || Zend_Controller_Front::getInstance()->getRequest()->getParam('tag', null)) {      
      if( !empty($_GET['tag']) ) {
        $this->view->tag = $_GET['tag'];
      } else {
        $this->view->tag = Zend_Controller_Front::getInstance()->getRequest()->getParam('tag', null); 
      }
    }
  }

}
?>