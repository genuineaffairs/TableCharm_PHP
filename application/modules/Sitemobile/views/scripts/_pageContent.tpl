<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _pageContent.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $hasIncludeHomeLink = $this->identity !== 'user-index-home' && $this->identity !== 'core-index-index' && 0;
; ?>
<?php
$coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
$sitemobileSettingsApi = Engine_Api::_()->getApi('settings', 'sitemobile');
$settingsParams= array();
$settingsParams['dafaultValues'] = array('mobile' => 'panel_reveal_list', 'tablet' => 'panel_reveal_icon', 'appmobile' => 'panel_reveal_list', 'apptablet' => 'panel_reveal_icon');
$dashboardContentType = $sitemobileSettingsApi->getSetting('sitemobile.dashboard.contentType', $settingsParams);
 $pageTitle=  $coreSettingsApi->getSetting('sitemobile.site.title', $coreSettingsApi->getSetting('core_general_site_title'));
//  if(Engine_Api::_()->sitemobile()->checkMode('mobile-mode')) {
// $dashboardContentType = $coreSettingsApi->getSetting('sitemobile.dashboard.contentType', 'panel_reveal_list');
// } else {
// $dashboardContentType = $coreSettingsApi->getSetting('sitemobile.tablet.dashboard.contentType', 'panel_reveal_icon');
// }


if (empty($this->contentType)) {
  $this->contentType = 'page';
}
//$hasIncludeMenuLink = (($this->onLoad && $dashboardContentType == 'panel' ) || $dashboardContentType !== 'panel') && $this->identity !== 'sitemobile-browse-browse';
$hasIncludeMenuLink = $this->identity !== 'sitemobile-browse-browse';
$is_cache=true;
if(stripos($this->identity,'-photo-view')):
   $is_cache = false; 
endif;
if ($hasIncludeMenuLink && in_array($this->identity, array('core-index-index','user-auth-login', 'user-signup-index','sitemobile-error-requireuser','core-error-requireuser'))) {
  $is_cache= false;
  $dashboardShowArray = $coreSettingsApi->getSetting('sitemobile.dashboard.display', array('login', 'signup'));

  $type = null;
  switch ($this->identity) {
    case 'core-index-index':
    case 'user-auth-login': 
    case 'sitemobile-error-requireuser':
    case 'core-error-requireuser':
      $type = 'login';
      break;
    case 'user-signup-index':
      $type = 'signup';
      break;
  }

  if(empty($dashboardShowArray)) 
    $dashboardShowArray = array();
  
  if (!empty($type) && !in_array($type, $dashboardShowArray)) {
    $hasIncludeMenuLink = false;
  }
}
if($this->noDomCache)
$is_cache= false;
?>
<?php if ($this->contentType == 'dialog'): ?>
  <?php if ($this->identity === 'core-utility-success'): ?>
    <div data-role="dialog" data-position-to="window" <?php echo $this->dataHtmlAttribs("dialog_success", array('data-theme' => "a", 'data-overlay-theme' => "a", "data-tolerance" => "15,15")); ?>  class="ui-content jqm_dialog_<?php echo $this->identity ?>" id="jqm_dialog_<?php echo $this->identity ?>" data-title="<?php echo $this->title ?>" data-subject="<?php echo $this->subject() ? $this->subject()->getGuid() : false;?>" >
      <div data-role="content" <?php echo $this->dataHtmlAttribs("dialog_content"); ?> >
        <?php echo $this->partial(
                'utility/success.tpl', 'sitemobile', $this->getVars()); ?>
      </div> 
      <div  <?php echo $this->dataHtmlAttribs("dialog_footer"); ?> >
      </div> 
    </div>
  <?php else: ?>
    <div data-role="dialog" <?php echo $this->dataHtmlAttribs("dialog", array('data-overlay-theme' => "a", 'data-theme' => "c", "data-tolerance" => "15,15")); ?> data-close-btn="left" id="jqm_dialog_<?php echo $this->identity ?>" data-title="<?php echo $this->title ?>" class="jqm_dialog_<?php echo $this->identity ?>" data-subject="<?php echo $this->subject() ? $this->subject()->getGuid() : false;?>" >

      <div data-role="header" <?php echo $this->dataHtmlAttribs("dialog_header"); ?> >
        <h1><?php echo!empty($this->sitemapPageHeaderTitle) ? $this->translate($this->sitemapPageHeaderTitle) : '' ?></h1>
      </div>
      <div data-role="content" <?php echo $this->dataHtmlAttribs("dialog_content"); ?> >
        <?php echo $this->content; ?>
      </div> 
      <div  <?php echo $this->dataHtmlAttribs("dialog_footer"); ?> >
      </div> 
    </div>
  <?php endif; ?>
<?php elseif ($this->contentType == 'page'): ?>
  <div <?php echo $this->dataHtmlAttribs("page", array("data-role" => "page")); ?> id="jqm_page_<?php echo $this->identity ?>" data-title="<?php echo $this->title ?>"  class="ui-responsive-panel jqm_page_<?php echo $this->identity ?> <?php if ($this->hasFixed): ?>p_fixed <?php endif; ?>" <?php if($is_cache):?> data-dom-cache="true"<?php endif; ?>  <?php if ($this->onLoad): ?> data-url="<?php echo $this->url()?>" <?php endif; ?> data-subject="<?php echo $this->subject() ? $this->subject()->getGuid() : false;?>" >
    <?php if ($this->headeContent || Zend_Registry::isRegistered('setFixedCreationForm')): ?>
    <div <?php echo $this->dataHtmlAttribs("page_header", array("data-role" => "header")); ?> style="overflow: hidden">
        <?php if(Zend_Registry::isRegistered('setFixedCreationForm')): ?>
        
        <?php //if (!$this->onLoad): ?>
        <a href='javascript://' class='ui-btn-left' data-rel='back' data-icon='arrow-l'  data-iconpos="notext"  ></a>
         <?php //endif; ?>
           <h2 ><?php echo $this->translate(Zend_Registry::get('setFixedCreationHeaderTitle')); ?></h2>
           <?php if(Zend_Registry::isRegistered('setFixedCreationHeaderSubmit')): ?>
           <a data-role="button" class="header_submit_button" data-rel="<?php echo Zend_Registry::get('setFixedCreationFormId')?>"><?php echo $this->translate(Zend_Registry::get('setFixedCreationHeaderSubmit'))  ?></a><?php endif;?>
        <?php else: ?>
        <?php if ($hasIncludeHomeLink): ?>
          <?php
          if ($this->viewer()->getIdentity()):
            $params = array('action' => 'home',);
            $route = 'user_general';
          else :
            $params = array();
            $route = 'default';
          endif;
          ?>
          <a href="<?php echo $this->url($params, $route, true); ?>" class="" <?php echo $this->dataHtmlAttribs("page_header_home_button", array("data-role" => "button", "data-icon" => "home", "data-iconpos" => "notext")); ?><?php echo $this->translate('Home') ?></a>
        <?php endif; ?>
        <?php if ($hasIncludeMenuLink): ?>
          <a href="<?php echo in_array($dashboardContentType,  array('panel_overlay_list','panel_reveal_list','panel_overlay_icon','panel_reveal_icon')) ? '#dashboardPanelMenu' : $this->url(array(), 'sitemobile_dashboard', true); ?>"  data-role="button" <?php echo $this->dataHtmlAttribs("dashboard_menu_button", array('data-icon' => "reorder")); ?> id="header-dashboard-menuButton" ><?php echo $this->translate('Menu') ?></a>
        <?php elseif (!$this->onLoad): ?>

      <!--          <a href='javascript:void(0);' class='ui-btn-left' data-rel='back' data-icon='arrow-l' <?php if (!$this->translate("SITEMOBILE_PAGE_BACK")): ?> data-iconpos="notext" <?php endif; ?> >
          <?php echo $this->translate("SITEMOBILE_PAGE_BACK") ?>
                </a>-->
        <?php endif; ?>
          <div data-role="player_pause" class="ui-icon ui-btn-right header-player-pause ui-btn-icon-notext ui-btn ui-shadow ui-btn-corner-all ui-icon-pause" style="display: none; right: 50px;"> </div>  
        <?php echo $this->headeContent; ?>
        <?php endif; ?>  
      </div>
    <?php endif; ?>
    <div <?php echo $this->dataHtmlAttribs("page_content", array("data-role" => "content")); ?> data-content="main" class="ui-content">
      <div class="connection_offlinemode" style=" background-color: #000; color: #fff;opacity: .8; padding: 5px;text-align: center; z-index: 100; position: fixed; width: 100%;display: none;">
       <span class="ui-icon ui-icon-ban-circle"></span>
        <?php echo  $this->translate("No Internet Connection"); ?>
     </div>
      <div data-role="wrapper">
          <div data-role="scroller" >
            <?php if ($this->identity === 'core-error-notfound'): ?>
              <?php echo $this->partial(
                      'error/notfound.tpl', 'sitemobile', $this->getVars()); ?>
            <?php elseif ($this->identity === 'core-utility-success'): ?>
              <?php echo $this->partial(
                      'utility/success.tpl', 'sitemobile', $this->getVars()); ?>
            <?php elseif ($this->identity === 'core-error-requireauth'): ?>
              <?php echo $this->partial(
                      'error/requireauth.tpl', 'sitemobile', $this->getVars()); ?>
            <?php elseif ($this->identity === 'core-error-error'): ?>
              <?php echo $this->partial(
                      'error/error.tpl', 'sitemobile', $this->getVars()); ?>
            <?php else: ?>
              <?php echo $this->content; ?>
            <?php endif; ?>
          </div>
        </div>
    </div> 
    <?php if ($this->footerContent && strlen($this->footerContent) > 100 && !Zend_Registry::isRegistered('setFixedCreationForm')): ?>  
      <div <?php echo $this->dataHtmlAttribs("page_footer", array("data-role" => "footer")); ?> >
        <?php echo $this->footerContent; ?>
      </div>
    <?php endif; ?>
      <?php if (in_array($dashboardContentType,  array('panel_overlay_list','panel_reveal_list','panel_overlay_icon','panel_reveal_icon')) && $hasIncludeMenuLink): ?>
       <div data-role="panel" class="ui-bar-a" <?php if (in_array($dashboardContentType,  array('panel_overlay_icon','panel_reveal_icon'))): ?> data-animate="false" <?php endif; ?> data-theme="a" data-display="<?php echo (in_array($dashboardContentType,  array('panel_reveal_list','panel_reveal_icon'))) ? 'reveal':'overlay' ?>" id="dashboardPanelMenu"   <?php echo $this->dataHtmlAttribs("dashboard_panel", array('data-dismissible' => 'true')); ?> >
          <?php echo $this->content('dashboard_panel', true); ?>
        </div>
        <?php if (in_array($dashboardContentType,  array('panel_overlay_icon','panel_reveal_icon'))): ?>
        <div class="dashboard-panel-mini-menu ui-bar-a">
          <?php echo $this->action('browse', 'browse', 'sitemobile',  array('showSearch'=>0,'fromWidgt'=>1)) ?>
        </div>
        <?php endif; ?>
      <?php endif; ?>
</div>
<?php endif; ?>