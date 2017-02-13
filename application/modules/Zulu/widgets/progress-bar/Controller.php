<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Zulu_Widget_ProgressBarController extends Engine_Content_Widget_Abstract {
    
    public function indexAction() {
        $this->_appendStylesheet();
        $params = $this->_getAllParams();
        $this->view->steps = $params['steps'];
    }

    protected function _appendStylesheet() {
        $class_parts = explode('_', get_class($this));
        $module_name = $class_parts[0];
        $this->view->css = $this->view->layout()->staticBaseUrl . 'application/modules/' . ucfirst($module_name)
                    . '/widgets/progress-bar/style.css';
    }

}
