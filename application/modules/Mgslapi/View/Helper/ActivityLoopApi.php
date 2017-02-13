<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: ActivityLoop.php 9801 2012-10-19 22:05:13Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Mgslapi_View_Helper_ActivityLoopApi extends Zend_View_Helper_Abstract
{
  public function activityLoopApi($actions = null, array $data = array(), $scrolling = 0, $device = null)
  {
    if( null == $actions || (!is_array($actions) && !($actions instanceof Zend_Db_Table_Rowset_Abstract)) ) {
      return '';
    }
    $form = new Activity_Form_Comment();
    
    $viewer = $data['viewer'];
    $activity_moderate = "";
    try
    {
		$group = false;
		$group_owner = false;
		if(Engine_Api::_()->core()->hasSubject()){
		    $group = Engine_Api::_()->core()->getSubject('group');    
		}
    }
    catch( Exception $e){      
    }
    if ($group) {
    $table = Engine_Api::_()->getDbtable('groups', 'group');
    $select = $table->select()
         ->where('group_id = ?', $group->getIdentity())
         ->limit(1);

    $row = $table->fetchRow($select);
    $group_owner = $row['user_id'];
    }
    if($viewer->getIdentity()){
      $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');
    }
    $data = array_merge($data, array(
      'actions' => $actions,
      'commentForm' => $form,
      'user_limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userlength'),
      'allow_delete' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userdelete'),
      'activity_group' =>$group_owner,
      'activity_moderate' =>$activity_moderate,
    ));
    if($device == 'android')
    {
        if(!$scrolling)
        {
            return $this->view->partial(
                'android/_activityTextApi.tpl',
                'mgslapi',
                $data
              );
        }
        elseif($scrolling == 1)
        {
            return $this->view->partial(
              'android/_dumyactivityTextApi.tpl',
              'mgslapi',
              $data
            );
        }
        elseif($scrolling == 2)
        {
            return $this->view->partial(
              'android/_latestactivityTextApi.tpl',
              'mgslapi',
              $data
            );
        }
    }
    else
    {
        if(!$scrolling)
        {
            return $this->view->partial(
                '_activityTextApi.tpl',
                'mgslapi',
                $data
              );
        }
        elseif($scrolling == 1)
        {
            return $this->view->partial(
              '_dumyactivityTextApi.tpl',
              'mgslapi',
              $data
            );
        }
        elseif($scrolling == 2)
        {
            return $this->view->partial(
              '_latestactivityTextApi.tpl',
              'mgslapi',
              $data
            );
        }
    }    
  }
}