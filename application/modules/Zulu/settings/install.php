<?php

/**
 * Description of install
 *
 * @author abakivn
 */
class Zulu_Installer extends Engine_Package_Installer_Module {

  protected $_isMGSL = true;

  public function getDb() {
    if (!Zend_Registry::isRegistered('Zend_Db')) {
      $db = parent::getDb();
    } elseif (class_exists('Engine_Db_Table') && Engine_Db_Table::getDefaultAdapter() instanceof Zend_Db_Adapter_Abstract) {
      $db = Engine_Db_Table::getDefaultAdapter();
    } else {
      throw new Engine_Package_Installer_Exception('Database not set');
    }

    return $db;
  }

  public function onInstall() {
    $this->_addProfileWidget();
    $this->_overwriteCoreFunctions();

    if ($this->_isMGSL) {
      $this->_insertSiteSpecificData();
    }

    parent::onInstall();
  }

  protected function _insertSiteSpecificData() {
    $db = $this->getDb();

    $path = $this->_operation->getPrimaryPackage()->getBasePath() . '/'
            . $this->_operation->getPrimaryPackage()->getPath() . '/'
            . 'settings';

    $files = array(
        'valke-clubs.sql' => function() {
          $db = $this->getDb();
      
          $valkeQuestion = $db->select()
                  ->from('engine4_user_fields_meta')
                  ->where('alias = ?', 'club or school')
                  ->limit(1)
                  ->query()
                  ->fetchObject();
          
          return empty($valkeQuestion);
        },
        'constant.sql' => function() {
          return true;
        }
    );
    
    $db->beginTransaction();

    foreach ($files as $file => $callback) {
      if (call_user_func($callback)) {
        $contents = file_get_contents($path . '/' . $file);
        foreach (Engine_Package_Utilities::sqlSplit($contents) as $sqlFragment) {
          try {
            $db->query($sqlFragment);
          } catch (Exception $e) {
            return $this->_error('Query failed with error: ' . $e->getMessage());
          }
        }
      }
    }
    
    $mgslData = include_once 'mgsl.php';
    // Set special fields alias
    foreach(array_merge($mgslData['south_africa'], $mgslData['valke']) as $label => $alias) {
      $db->update('engine4_user_fields_meta',
        // SET
        array('alias' => $alias),
        // WHERE
        array('`label` = ?' => $label)
      );
    }
    $db->commit();
  }

  protected function _overwriteCoreFunctions() {
    $db = $this->getDb();

    $db->update('engine4_core_menuitems',
            // SET
            array('plugin' => 'Zulu_Plugin_Menus'),
            // WHERE
            array('`name` = ?' => 'user_profile_edit')
    );
  }

  // Replace default User profile field widget with Zulu widget
  protected function _addProfileWidget() {
    $db = $this->getDb();

    $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'user_profile_index')
            ->limit(1)
            ->query()
            ->fetchColumn();

    if ($page_id) {

      $zuluProfileWidget = $db->select()
                      ->from('engine4_core_content')
                      ->where('page_id = ?', $page_id)
                      ->where('type = ?', 'widget')
                      ->where('name = ?', 'zulu.profile-fields')
                      ->order('order')
                      ->limit(1)
                      ->query()->fetchObject();

      $zuluClinicalWidget = $db->select()
                      ->from('engine4_core_content')
                      ->where('page_id = ?', $page_id)
                      ->where('type = ?', 'widget')
                      ->where('name = ?', 'zulu.clinical-fields')
                      ->order('order')
                      ->limit(1)
                      ->query()->fetchObject();

      $defaultWidget = $db->select()
                      ->from('engine4_core_content')
                      ->where('page_id = ?', $page_id)
                      ->where('type = ?', 'widget')
                      ->where('name = ?', 'user.profile-fields')
                      ->order('order')
                      ->limit(1)
                      ->query()->fetchObject();

      if (empty($zuluProfileWidget) && empty($zuluClinicalWidget)) {
        // container_id (will always be there)
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_content')
                ->where('page_id = ?', $page_id)
                ->where('type = ?', 'container')
                ->limit(1);
        $container_id = $select->query()->fetchObject()->content_id;

        // middle_id (will always be there)
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_content')
                ->where('parent_content_id = ?', $container_id)
                ->where('type = ?', 'container')
                ->where('name = ?', 'middle')
                ->limit(1);
        $middle_id = $select->query()->fetchObject()->content_id;

        // tab_id (tab container) may not always be there
        $select
                ->reset('where')
                ->where('type = ?', 'widget')
                ->where('name = ?', 'core.container-tabs')
                ->where('page_id = ?', $page_id)
                ->limit(1);
        $tab_id = $select->query()->fetchObject();
        if ($tab_id && @$tab_id->content_id) {
          $tab_id = $tab_id->content_id;
        } else {
          $tab_id = null;
        }

        // Profile tab
        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'zulu.profile-fields',
            'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
            'order' => $defaultWidget->order,
            'params' => '{"title":"About","name":"user.profile-fields"}',
        ));

        // Clinical tab
        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'zulu.clinical-fields',
            'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
            'order' => $defaultWidget->order + 1,
            'params' => '{"title":"Medical Record","name":"zulu.clinical-fields"}',
        ));

        $db->delete('engine4_core_content', array(
            'page_id = ?' => $page_id,
            'type = ?' => 'widget',
            'name = ?' => 'user.profile-fields',
            '`order` = ?' => $defaultWidget->order
        ));
      }
    }

    // Code for inserting whole new page
//        if (!$page_id) {
//            $db->insert('engine4_core_pages', array(
//                'name' => 'zulu_profile_index',
//                'displayname' => 'Zulu Member Profile',
//                'title' => 'Profile',
//                'description' => "This is a member's profile.",
//                'custom' => 0,
//                'provides' => 'subject=user',
//            ));
//            $page_id = $db->lastInsertId();
//        }
  }

  public function onEnable() {
    $this->_addProfileWidget();

    parent::onEnable();
  }

}
