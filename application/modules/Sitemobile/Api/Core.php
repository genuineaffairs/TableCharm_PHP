<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Api_Core extends Core_Api_Abstract {

  const CONTROLLER_PREFIX = 'sitemobile-';
  const MODULE_PREFIX = 'sitemobile';
  const VIEW_BASE_PATH = 'views/sitemobile';
  const VIEW_BASE_PATH_SPEC = ':moduleDir/views/sitemobile';
  const APP_USER_AGENT_ID = 'SEAOSEAPP';

  protected $_appInfo = null;

  public function __construct() {
    $this->isApp();
  }

  public function translateData() {
    $data = array(
        'SITEMOBILE_LOADING_PAGE_MESSAGE', 'SITEMOBILE_ERROR_LOADING_PAGE_MESSAGE', 'SITEMOBILE_LOADING_PHOTOGALLERY_MESSAGE', 'Everyone', 'All Members', 'Friends', 'Only Me', array('% like', '% likes'), '%s likes this', '%s like this', "now", 'in a few seconds', 'a few seconds ago', '%s minute ago', 'in %s minute', '%s hour ago', 'in %s hour', '%s at %s', 'Write a comment...', 'Unlike', '% likes this', 'like', 'Cancel', 'Search', 'Search..', '% like', 'Like', 'Add ', 'cancel', 'Loading...', 'There was an error detecting your current location.<br />Please make sure location services are enabled in your browser,and this site has permission to use them. You can still search for a place, but the search will not be as accurate.', 'at', 'in', 'Add People', 'with:', 'Add Link', 'Attach', 'Choose Image:', 'Last', 'Next', 'Don\'t show an image', 'Add Video', 'Choose Source', 'YouTube', 'Vimeo', 'Sorry, the browser you are using does not support Photo uploading. You can upload the Photo from your Desktop.', 'Invalid 
Upload', 'Add Music', 'Add Photo','Choose Photo','Choose Music');

    $appInfo = Engine_Api::_()->sitemobile()->getAppInfo();
    if (!empty($appInfo) && $appInfo['appOS'] == 'android') {
      $data[] = 'share_app_android_subject';
      $data[] = 'share_app_android_body';
    }
    $data[] = 'ADDBEFOREITADDEND';
    return $data;
  }

  public function isSupportedModule($modulename) {
    if (!Zend_Registry::isRegistered('SupportedModules')) {
      $supportedModules = Engine_Api::_()->getDbtable('modules', 'sitemobile')->getEnabledModuleNames();
      Zend_Registry::set('SupportedModules', $supportedModules);
    } else {
      $supportedModules = Zend_Registry::get('SupportedModules');
    }

    $isModActivated = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.isActivate', false);
    if (empty($isModActivated))
      return;
    else
      return in_array($modulename, $supportedModules);
  }

  public function isSupportedModulePhotoGallery($modulename) {
    if (!$this->isSupportedModule($modulename))
      return;
    return in_array($modulename, array('album', 'event', 'group', 'sitepage', 'sitepagenote', 'sitepageevent', 'sitereview', 'sitebusiness', 'sitebusinessnote', 'sitebusinessevent', 'sitegroup', 'sitegroupnote', 'sitegroupevent', 'sitestore', 'sitestoreproduct', 'siteevent'));
  }

  public function getSession() {
    return new Zend_Session_Namespace('siteViewModeSM');
  }

  public function isApp($info = false) {

    // No USER_AGENT defined?
    if (!Engine_Api::_()->hasModuleBootstrap('sitemobileapp') || !isset($_SERVER['HTTP_USER_AGENT']))
      return false;

    $useragent = $_SERVER['HTTP_USER_AGENT']; //." SEAOSEAPP {\"appOS\":\"android\"}";

    if ($this->_appInfo === null) {
      $appInfo = explode(self::APP_USER_AGENT_ID, $useragent);
      if (count($appInfo) == 2) {
        $this->_appInfo = Zend_Json::decode($appInfo[1]);
      } else {
        $this->_appInfo = false;
      }
    }

    return $info ? $this->_appInfo : (!(!$this->_appInfo));
  }

  public function getAppInfo() {
    return $this->isApp(true);
  }

  public function viewData($data = array()) {
    return array_merge(array_intersect_key($data, array('redirect' => '',
        'noDomCache' => "",
        "redirectTime" => "",
        "smoothboxClose" => "",
        "parentRefresh" => "",
        "redirect" => "",
        "triggerEventsOnContentLoad" => array(),
        "viewerDetails" => array(),
        "parentRedirect" => "")), array("onloadFirstPage" => 1));
  }

  public function isMobile() {
    // No UA defined?
    if (!isset($_SERVER['HTTP_USER_AGENT'])) {
      return false;
    }

    // Windows is (generally) not a mobile OS
    if (false !== stripos($_SERVER['HTTP_USER_AGENT'], 'windows') &&
            false === stripos($_SERVER['HTTP_USER_AGENT'], 'windows phone')) {
      return false;
    }
//Mozilla/5.0 (compatible; MSIE 10.0; Windows Phone 8.0; Trident/6.0; IEMobile/10.0; ARM; Touch; NOKIA; Lumia 720)
    // Sends a WAP profile header
    if (isset($_SERVER['HTTP_PROFILE']) ||
            isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
      return true;
    }

    // Accepts WAP as a valid type
    if (isset($_SERVER['HTTP_ACCEPT']) &&
            false !== stripos($_SERVER['HTTP_ACCEPT'], 'application/vnd.wap.xhtml+xml')) {
      return true;
    }

    // Is Opera Mini
    if (isset($_SERVER['ALL_HTTP']) &&
            false !== stripos($_SERVER['ALL_HTTP'], 'OperaMini')) {
      return true;
    }

    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $appInfo = $this->getAppInfo();
    if ($appInfo && isset($appInfo['device']))
      $userAgent = $appInfo['device'];

    if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android)/i', $userAgent)) {
      return true;
    }

    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
    $mobile_agents = array(
        'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird',
        'blac', 'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric',
        'hipt', 'inno', 'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c',
        'lg-d', 'lg-g', 'lge-', 'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi',
        'mot-', 'moto', 'mwbp', 'nec-', 'newt', 'noki', 'oper', 'palm', 'pana',
        'pant', 'phil', 'play', 'port', 'prox', 'qwap', 'sage', 'sams', 'sany',
        'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar', 'sie-', 'siem', 'smal',
        'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-', 'tosh', 'tsm-',
        'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp', 'wapr',
        'webc', 'winw', 'winw', 'xda ', 'xda-'
    );

    if (in_array($mobile_ua, $mobile_agents)) {
      return true;
    }

    return false;
  }

  public function isMobiAttempt() {
    $getMobiAttemptStr = array();

    if (isset($_SERVER['HTTP_USER_AGENT'])) {
      $getMobiAttemptStr[] = $_SERVER['HTTP_USER_AGENT'];
    }

    if (false !== stripos($_SERVER['HTTP_USER_AGENT'], 'windows') &&
            false === stripos($_SERVER['HTTP_USER_AGENT'], 'windows phone')) {
      $getMobiAttemptStr[] = $_SERVER['HTTP_USER_AGENT'];
    }

    if (isset($_SERVER['HTTP_PROFILE']) ||
            isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
      $getMobiAttemptStr[] = $_SERVER['HTTP_X_WAP_PROFILE'];
    }

    if (isset($_SERVER['HTTP_ACCEPT']) &&
            false !== stripos($_SERVER['HTTP_ACCEPT'], 'application/vnd.wap.xhtml+xml')) {
      $getMobiAttemptStr[] = $_SERVER['HTTP_ACCEPT'];
    }

    if (isset($_SERVER['ALL_HTTP']) &&
            false !== stripos($_SERVER['ALL_HTTP'], 'OperaMini')) {
      $getMobiAttemptStr[] = $_SERVER['ALL_HTTP'];
    }

    if (isset($_SERVER['HTTP_HOST'])) {
      $getMobiAttemptStr[] = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
    }

    if (empty($getMobiAttemptStr) && !empty($mobileShowViewtype)) {
      $getValue = false;
    } else {
      $getValue = @end($getMobiAttemptStr);
    }

    return $getValue;
  }

  public function isSiteMobileModeEnabled() {
    return $this->checkMode('tablet-mode') || $this->checkMode('mobile-mode');
  }

  public function isTabletDivice() {
    // No UA defined?
    if (!isset($_SERVER['HTTP_USER_AGENT'])) {
      return false;
    }
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $appInfo = $this->getAppInfo();
    if ($appInfo && isset($appInfo['device']))
      $userAgent = $appInfo['device'];
//    // Windows is (generally) not a mobile OS
//    if (false !== stripos($_SERVER['HTTP_USER_AGENT'], 'windows') &&
//            false === stripos($_SERVER['HTTP_USER_AGENT'], 'windows phone os')) {
//      return false;
//    }
    if (preg_match('/' . 'iPad|Nexus|GT-P1000|SGH-T849|SHW-M180S' . '/i', $userAgent))
      return true;
    else
      return false;
  }

  public function isSiteLocal() {
    $hostType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.mobi.attempt', null);
    $hostName = @convert_uudecode($hostType);
    if ($hostName == 'localhost' || strpos($hostName, '192.168.') != false || strpos($hostName, '127.0.') != false) {
      return false;
    }

    return true;
  }

  public function enabelTablet() {
    return Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.enabel.tablet', 1);
  }

  public function checkMode($mode = 'fullsite-mode') {
    return (bool) ($this->getViewMode() === $mode);
  }

  public function getViewMode() {

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $viewRequest = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.view.request', null);
    if ($request == null) {
      $request = new Zend_Controller_Request_Http();
    }
    $module = $request->getModuleName();
    $noCheckSitemode = $request->getParam("noCheckSitemode");
    if ((substr($request->getPathInfo(), 1, 5) == "admin" || substr($request->getPathInfo(), 1, 23) == 'sitemobile/theme-roller') || empty($viewRequest) || !empty($noCheckSitemode)) {
      return 'fullsite-mode';
    }

    $session = new Zend_Session_Namespace('siteViewModeSM');
    $viewMode = $request->getParam("switch-mode");

    if ($viewMode === 'standard') {
      $viewMode = 'fullsite';
    }
    if (!$this->isApp() && in_array($viewMode, array("mobile", "tablet", "fullsite"))) {
      $session->siteViewModeSM = $viewMode;
    }

    if (!isset($session->siteViewModeSM)) {
      // CHECK TO SEE IF MOBILE
      if ($this->isTabletDivice()) {
        $session->siteViewModeSM = "tablet";
      } else if ($this->isMobile()) {
        $session->siteViewModeSM = "mobile";
      } else {
        $session->siteViewModeSM = "fullsite";
      }
    }

    if ($session->siteViewModeSM === "tablet" && !$this->enabelTablet()) {
      $session->siteViewModeSM = "fullsite";
    }

    $sessionCore = new Zend_Session_Namespace('mobile');
    if ($session->siteViewModeSM) {
      $sessionCore->unsetAll();
      $sessionCore->mobile = $session->siteViewModeSM == 'fullsite' ? 0 : 1;
    }
    if (isset($session->siteViewModeSM)) {
      $tempSession = $session->siteViewModeSM;
      $session->unsetAll();
      $session->siteViewModeSM = $tempSession;
      return $session->siteViewModeSM . '-mode';
    }
  }

//  public function getMenuItemRow($getMenuItemName, $getSelectedMenuName) {
//    $menuItemName = str_replace('mobi_', 'sitemobile_', $getMenuItemName);
//    $selectedMenuName = str_replace('mobi_', 'sitemobile_', $getSelectedMenuName);
//    if (empty($selectedMenuName)) {
//      $selectedMenuName = 'sitemobile_browse';
//    }
//
//    $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
//    $menuItemsSelect = $menuItemsTable->select()
//            ->where('menu = ?', $selectedMenuName)
//            ->where('name = ?', $menuItemName);
//    $menuItemsRow = $menuItemsSelect->query()->fetchAll();
//    return $menuItemsRow;
//  }

  public function setupRequest(Zend_Controller_Request_Abstract $request) {
    /**
     * @var $viewRenderer Zend_Controller_Action_Helper_ViewRenderer
     */
    $moduleName = $request->getModuleName();
    $controllerName = $request->getControllerName();
    $actionName = $request->getActionName();
    $response = array(
        'status' => 0,
        'return' => null
    );

    $frontController = Zend_Controller_Front::getInstance();
    $viewRenderer = $this->getViewRenderer();

    $viewRenderer->setViewBasePathSpec(':moduleDir/views');
    $mobileSetupRequest = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.setup.request', false);

    if (!$this->isSupportedModule($moduleName) || empty($mobileSetupRequest))
      return $response;
// Advance Forum Related Work
    if ($moduleName == 'forum' && $actionName == 'index' && $controllerName == 'index' && Engine_Api::_()->hasModuleBootstrap('ynforum')) {
      $moduleName = 'ynforum';
      $request->setParam("module", $moduleName);
      $request->setModuleName($moduleName);
      $response['status'] = 4;
      $viewRenderer->setViewBasePathSpec('application/modules/Sitemobile/modules/Forum/views');
      Zend_Controller_Action_HelperBroker::getExistingHelper('content')
              ->setContentName("forum_index_index");
    } else {
      if (($moduleDir = $this->isInside($moduleName, true)) && ($this->hasControllerInsideModule($moduleName, $controllerName))) {
        $moduleDir .= DIRECTORY_SEPARATOR . $frontController->getModuleControllerDirectoryName();
        $frontController->addControllerDirectory($moduleDir, $moduleName);
        $response['status'] = 1;
        return $response;
      } elseif ($this->isControllerPrefix($moduleName, $controllerName)) {
        $request->setControllerName(Sitemobile_Api_Core::CONTROLLER_PREFIX . $controllerName);
        // $viewRenderer->setViewBasePathSpec(Sitemobile_Api_Core::VIEW_BASE_PATH_SPEC);

        $response['status'] = 2;
        // $response['return'] = $viewRenderer;
        return $response;
      } elseif ($this->isViewsScriptSitemobile($moduleName, $controllerName, $actionName)) {
        $viewRenderer->setViewBasePathSpec(Sitemobile_Api_Core::VIEW_BASE_PATH_SPEC);
        $response['status'] = 3;
        $response['return'] = $viewRenderer;
        return $response;
      } elseif ($this->isSeperateModule($moduleName)) {
        $request->setModuleName(Sitemobile_Api_Core::MODULE_PREFIX . $moduleName);
        $response['status'] = 4;
      } elseif ($this->isSupportedModule($moduleName)) {
        $response['status'] = 5;
      }
    }
    return $response;
  }

  public function setModuleDirectory($moduleName) {
    if ($moduleDir = $this->isInside($moduleName, true)) {
      $frontController = Zend_Controller_Front::getInstance();
      $moduleDir .= DIRECTORY_SEPARATOR . $frontController->getModuleControllerDirectoryName();
      $frontController->addControllerDirectory($moduleDir, $moduleName);
      return true;
    }
  }

  private function getViewRenderer() {
    $viewRenderer = Zend_Controller_Action_HelperBroker::getStack()->ViewRenderer;
    if (!$viewRenderer)
      throw new Exception('ViewRenderer is out of stack');
    return $viewRenderer;
  }

  private function isInside($module, $getdir = false) {
    $path = $this->getPath($module);
    return $getdir ? (is_dir($path) ? $path : false) : is_dir($path);
  }

  private function hasControllerInsideModule($moduleName, $controllerName) {
    return $this->getControllerFileName($moduleName, $controllerName, true, true);
  }

  private function isControllerPrefix($moduleName, $controllerName) {
    return $this->getControllerFileName($moduleName, Sitemobile_Api_Core::CONTROLLER_PREFIX . $controllerName, true);
  }

  public function getPath($module, $params = array()) {
    $moduleInflected = Engine_Api::inflect($module);

    $path = APPLICATION_PATH
            . DIRECTORY_SEPARATOR
            . "application"
            . DIRECTORY_SEPARATOR
            . "modules"
            . DIRECTORY_SEPARATOR
            . 'Sitemobile'
            . DIRECTORY_SEPARATOR
            . 'modules'
            . DIRECTORY_SEPARATOR
            . $moduleInflected;

    foreach ($params as $dir) {
      $path .= DIRECTORY_SEPARATOR . $dir;
    }

    return $path;
  }

  /**
   *
   * @param string $moduleName
   * @param string $controllerName
   * @param bool $get_if_exists
   * @return bool|string
   */
  private function getControllerFileName($moduleName, $controllerName, $get_bool = false, $check_inSide = false) {
    $moduleName = Engine_Api::inflect($moduleName);
    $cname = '';
    foreach (explode('-', $controllerName) as $part) {
      $cname .= ucfirst($part);
    };
    $cname .= 'Controller.php';
    if ($check_inSide) {
      // For Error, Not Found Case
//      if ($moduleName == 'Core' && !in_array($cname, array('CommentController.php',
//                  'ConfirmController.php', 'CrossDomainController.php', 'HelpController.php','IndexController.php' ,'LinkController.php',
//                  'PagesController.php', 'ReportController.php', 'SearchController.php', 'SitemapController.php', 'TagController.php', 'WidgetController.php','UtilityController.php'))) {
//        return true;
//      }
      $moduleName = 'Sitemobile'
              . DIRECTORY_SEPARATOR
              . "modules"
              . DIRECTORY_SEPARATOR
              . $moduleName;
    }
    $path = APPLICATION_PATH
            . DIRECTORY_SEPARATOR
            . "application"
            . DIRECTORY_SEPARATOR
            . "modules"
            . DIRECTORY_SEPARATOR
            . $moduleName
            . DIRECTORY_SEPARATOR
            . "controllers"
            . DIRECTORY_SEPARATOR
            . $cname;
    return $get_bool ? (file_exists($path) ? true : false) : $path;
  }

  private function isSeperateModule($module) {
    return Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled(Sitemobile_Api_Core::MODULE_PREFIX . strtolower($module));
  }

  /**
   * @param \Zend_Controller_Request_Abstract $request
   * @return bool
   */
  private function isViewsScriptSitemobile($moduleName, $controllerName, $actionName) {
    $path = APPLICATION_PATH
            . DIRECTORY_SEPARATOR
            . 'application'
            . DIRECTORY_SEPARATOR
            . 'modules'
            . DIRECTORY_SEPARATOR
            . ucfirst($moduleName)
            . DIRECTORY_SEPARATOR
            . Sitemobile_Api_Core::VIEW_BASE_PATH;
    return is_dir($path) && file_exists($path . "/scripts/$controllerName/$actionName.tpl");
  }

  public function setContentStorage() {
    $content = Zend_Registry::get('Engine_Content');
    $mobileStorageType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.storage.type', false);
    if (empty($mobileStorageType))
      return;
    $moduleName = $this->isApp() ? 'sitemobileapp' : 'sitemobile';

    if ($this->checkMode('mobile-mode')) {
      $contentTable = Engine_Api::_()->getDbtable('pages', $moduleName);
    } else {
      $contentTable = Engine_Api::_()->getDbtable('tabletpages', $moduleName);
    }

    $content->setStorage($contentTable);
    // Save to registry
    Zend_Registry::set('Engine_Content', $content);
  }

  //CHECK IF CORROSPONDING COMPOSER IS ENABLE OR NOT:

  public function enableComposer($type, $params = array()) {

    if ($type == 'photo')
      $type = 'album';
    if ($type == 'link')
      $type = 'core';

    if ($type == 'album' || $type == 'music' || $type == 'video') {


      if (Engine_Api::_()->core()->hasSubject()) {
        $subject = Engine_Api::_()->core()->getSubject();

        if ($subject && in_array($subject->getType(), array('sitepage_page', 'sitepageevent_event', 'sitebusiness_business', 'sitebusinesseevent_event', 'sitegroup_group', 'sitegroupevent_event'))) {
          $moduleName = $subject->getModuleName();
          if (stripos('event_event', $subject->getType()) != false)
            $moduleName = str_replace('event', '', $moduleName);

          $moduleName = strtolower($moduleName . $type);

          if ($this->isSupportedModule($moduleName) && method_exists(Engine_Api::_()->getApi('core', $moduleName), 'enableComposer'))
            return Engine_Api::_()->getApi('core', $moduleName)->enableComposer();
          else
            return false;
        }
      }
    }


    if (!$this->isSupportedModule($type) || !empty($params['auth']) && !Engine_Api::_()->authorization()->isAllowed($params['auth'][0], null, $params['auth'][1])) {
      return false;
    }

    if (method_exists(Engine_Api::_()->getApi('core', $type), 'enableComposerSM'))
      return Engine_Api::_()->getApi('core', $type)->enableComposerSM();
    return true;
  }

  public function enabelTinymceditor() {
    if ($this->isApp())
      return false;
    return Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.tinymceditor', 0);
  }

  //THESE TWO FUNCTION SITEMOBILE_MODULE_NOT_SUPPORT_DESC_FOR_SOMEPAGES [setupRequestError, showNotSupportedMessage]
  public function setupRequestError() {

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $request->setControllerName('error');
    $request->setModuleName('sitemobile');
    $request->setActionName('notsupport');
    if (Engine_API::_()->seaocore()->isSiteMobileModeEnabled()) {
      Engine_Api::_()->sitemobile()->setupRequest($request);
    }
    $request->setDispatched(false);
  }

  public function showNotSupportedMessage() {

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $controllerName = $request->getParam('controller');
    $moduleName = $request->getParam('module');
    $actionName = $request->getParam('action');
    $controllerNameArray = array('index');
    $moduleNameArray = array('sitereview', 'sitepage', 'sitebusiness', 'sitegroup');
    $actionNameArray = array('create', 'edit');
    $notSupported = 0;
    if (in_array($moduleName, $moduleNameArray) && in_array($controllerName, $controllerNameArray) && in_array($actionName, $actionNameArray)) {
      $notSupported = 1;
    }

    return $notSupported;
  }

  public function autoRotationImage($file, $filePath = null) {
    if (empty($file))
      return;
    if(!function_exists('exif_read_data'))
      return;
    if ($filePath) {
      $fileTemp = $filePath;
      $fileName = $filePath;
    } else {
      $fileTemp = $file['tmp_name'];
      $fileName = $file['name'];
    }
    $extension = ltrim(strrchr(basename($fileName), '.'), '.');
    //exif only supports jpg in our supported file types
    if ($extension == "jpg" || $extension == "jpeg") {
      $exif = exif_read_data($fileTemp);
      if (isset($exif['Orientation'])) {
        switch ($exif['Orientation']) {
          case 3: // 180 rotate left
            $angle = 180;
            break;
          case 6: // 90 rotate right
            $angle = 270;
            break;
          case 8: // 90 rotate left
            $angle = 90;
            break;
          default:
            $angle = 0;
            break;
        }
        if ($angle) {
          $image = Engine_Image::factory();
          $image->open($fileTemp)
                  ->rotate($angle)
                  ->write()
                  ->destroy();
        }
      }
    }
  }

}
