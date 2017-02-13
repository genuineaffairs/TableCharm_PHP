<?php

$db = Engine_Db_Table::getDefaultAdapter();
$is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_communityad_pagesettings'")->fetch();
if (!empty($is_table_exist)) {
  $select = new Zend_Db_Select($db);
  $select
          ->from('engine4_communityad_pagesettings', array('ad_widget_name', 'widget_content_id', 'value', 'ajax_enabled', 'page_id', 'ad_widget_id'))
          ->where('page_id > ?', 0)
  ;

  $blocks = $select->query()->fetchAll();
  foreach ($blocks as $block) {
    $params = '{"loaded_by_ajax":"' . $block['ajax_enabled'] . '","show_type":"' . getWidgetType($block['ad_widget_name']) . '","itemCount":"' . $block['value'] . '"}';

    $db->update('engine4_core_content', array('name' => 'communityad.ads', 'params' => $params), array('content_id = ?' => $block['widget_content_id'], 'page_id = ?' => $block['page_id'], 'name =?' => $block['ad_widget_name']));
  }
}

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_content', array('content_id', 'page_id', 'parent_content_id', 'name'))
        ->where("(name Like 'communityad.featured-%' or name Like 'communityad.sponsored%' or name Like 'communityad.sponserd-%' or name Like 'communityad.left-%' or name Like 'communityad.right-%' or name Like 'communityad.extended-%' or name Like 'communityad.fullwidth-%' or name Like 'communityad.middle-%')")
        ->where("name <> 'communityad.sponsored-stories'");
;

$blocks = $select->query()->fetchAll();

$ajax_enabled = 0;
$limit = 3;
$set_select = new Zend_Db_Select($db);
$set_select
        ->from('engine4_core_settings')
        ->where('name = ?', 'widgets.limit');
$limitObj = $set_select->query()->fetchObject();
if ($limitObj) {
  $limit = $limitObj->value;
}
$set_select = new Zend_Db_Select($db);
$set_select
        ->from('engine4_core_settings')
        ->where('name = ?', 'ad.ajax.based');
$ajaxObj = $set_select->query()->fetchObject();
if ($ajaxObj) {
  $ajax_enabled = $ajaxObj->value;
}

foreach ($blocks as $block) {
  $params = '{"loaded_by_ajax":"' . $ajax_enabled . '","show_type":"' . getWidgetType($block['name']) . '","itemCount":"' . $limit . '"}';
  $db->update('engine4_core_content', array('name' => 'communityad.ads', 'params' => $params), array('content_id = ?' => $block['content_id'], 'page_id = ?' => $block['page_id'], 'name =?' => $block['name']));
}

function getWidgetType($name) {
  $type = 'all';
  if (strpos($name, 'communityad.sponserd') === 0 || strpos($name, 'communityad.sponsored') === 0) {
    $type = 'sponsored';
  } elseif (strpos($name, 'communityad.featured') === 0) {
    $type = 'featured';
  }
  return $type;
}

?>