<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Sami
 */
?>

<?php 
if($this->icon){
  $icon = $this->icon;
} else{
  $icon = "arrow-r";
}
$breadcrumb = array(
	array("href"=>$this->sitepage->getHref(),"title"=>$this->sitepage->getTitle(),"icon"=>"arrow-r"),
	array("href"=>$this->sitepage->getHref(array('tab' => $this->tab_selected_id)),"title"=>"Events","icon"=>$icon),
);

if(isset($this->sitepageevent) && !$this->noShowTitle):?>
<?php  
$sitepageeventTitle = $this->sitepageevent->getTitle();
$breadcrumb = array_merge($breadcrumb,	array(array("title"=>$this->sitepageevent->getTitle(),"icon"=>"arrow-d","class" => "ui-btn-active ui-state-persist")));
?>
<?php endif; ?>

<?php 
  $this->brdObj=$breadcrumb;
  include APPLICATION_PATH . '/application/modules/Sitemobile/views/scripts/breadcrumb.tpl';
?> 