<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
if (empty($this->pageHeaderTitle)) {
  $request = Zend_Controller_Front::getInstance()->getRequest();
  $pageTitleKey = 'pagetitle-' . $request->getModuleName() . '-' . $request->getActionName()
          . '-' . $request->getControllerName();
  $pageTitle = $this->translate($pageTitleKey);
  $pageTitleKey = 'mobilepagetitle-' . $request->getModuleName() . '-' . $request->getActionName()
          . '-' . $request->getControllerName();
  $pageTitle = $this->translate($pageTitleKey);
  if (($pageTitle && $pageTitle != $pageTitleKey)) {
    $title = $pageTitle;
    if (($this->subject() && $this->subject()->getIdentity()) && $this->subject()->getTitle()) {
      $title = $pageTitle . " - " . $this->subject()->getTitle();
    }

    $pageHeaderTitle = $title;
  } else {
    $pageTitle = $title = str_replace(array('<title>', '</title>'), '', $this->headTitle()->toString());
    if (empty($title)) {
      $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
      $pageTitle = $title =  $coreSettingsApi->getSetting('sitemobile.site.title', $coreSettingsApi->getSetting('core_general_site_title'));
    }
    if ($this->subject() && $this->subject()->getIdentity() && $this->subject()->getTitle()) {
      $title = $pageTitle . " - " . $this->subject()->getTitle();
    }
    $pageHeaderTitle = $title;
  }
} else {
  $pageHeaderTitle = $this->pageHeaderTitle;
}
?>
<h2 class="ui-title"><?php echo!empty($pageHeaderTitle) ? $this->translate($pageHeaderTitle) : '' ?></h2>