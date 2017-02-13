<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagelikebox
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-10-10 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagelikebox_IndexController extends Core_Controller_Action_Standard {

  public function indexAction() {

    $this->_helper->layout->disableLayout();

    //GET THE SETTINGS FROM THE CORE TABLE.
    $apiSettings = Engine_Api::_()->getApi('settings', 'core');

    //CHECK FOR PACKAGE ENABLE OR NOT.
    $hasPackageEnable = Engine_Api::_()->sitepage()->hasPackageEnable();

    $this->view->coreModuleVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version;

    //HOW MANY CONTENT SHOW IN THE IFRAME
    $this->view->likebox_contentshow = $likebox_contentshow = $apiSettings->getSetting('likebox.contentshow');
    $this->view->pagelayout = $apiSettings->getSetting('sitepage.layoutcreate', 0);

    //GET THE VALUE OF LOGO PHOTO AND NAME OF THE PHOTO.
    $logoPhoto = $apiSettings->getSetting('logo.photo');
    if (!empty($logoPhoto)) {
      $this->view->photo_name = $this->view->baseUrl() . '/public/sitepagelikebox/logo/' . $logoPhoto;
    }

    $href = null;
    if (isset($_GET['edit']) && !empty($_GET['edit'])) {
      $this->view->edit = 1;
    }
    if (isset($_GET['href']) && !empty($_GET['href'])) {
      $href = urldecode($_GET['href']);
      $breakUrl = explode('/', $href);
      $pageUrl = end($breakUrl);
      if ($pageUrl) {
        $page_id = Engine_Api::_()->sitepage()->getPageId($pageUrl);
      }

      if ($page_id) {
        $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      }
      if (empty($sitepage)) {
        return;
      }

      //VIEW PERMISSION.
      if (!Engine_Api::_()->sitepage()->canViewPage($sitepage)) {
        return;
      }

      $this->view->enableLikeBox = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'likebox');
//      if ( !$this->view->enableLikeBox )
//        return $this->_forward( 'requireauth' , 'error' , 'core' ) ;
    }
    Engine_Api::_()->core()->setSubject($sitepage);

    //CHECK FOR PRIVACY.
    // $this->view->display = false ;
    $auth = Engine_Api::_()->authorization()->context;
    $authView = $auth->isAllowed($sitepage, 'everyone', 'view');
    //TIP SHOULD BE SHOW.
    $displayFlag = false;
    if (!empty($authView)) {
      //FORM SHOULD BE SHOW.
      $displayFlag = true;
    }
    $this->view->display = $displayFlag;

    //START BADGE WORK.
    //CHECK BADGE PLUGIN IS ENABLE OR NOT AND FOR THIS PAGE BADGE IS ASSIGN OR NOT.
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge') && ($apiSettings->getSetting('sitepagebadge.isActivate', 0))) {
      if (!empty($sitepage->badge_id)) {
        $this->view->sitepagebadges_value = $apiSettings->sitepagebadge_badgeprofile_widgets;
        $this->view->sitepagebadge = Engine_Api::_()->getItem('sitepagebadge_badge', $sitepage->badge_id);
      }
    }
    //END BADGE WORK.
    $value = array();
    $value = array('width' => "300", 'height' => "660", 'titleturncation' => "50", 'border_color' => "", 'map' => "", 'faces' => "true", 'stream' => "true", 'streamupdatefeed' => "true", 'streaminfo' => "true", 'streammap' => "true",
        'streamreview' => "true", 'streamdiscussion' => "true", 'streamalbum' => "true", 'streamevent' => "true", 'streamnote' => "true", 'streampoll' => "true", 'streamoffer' => "true", 'streamvideo' => "true", 'streamdocument' => "true", 'streammusic' => "true", 'header' => "true", 'colorscheme' => 'light');

    foreach ($_GET as $key => $val)
      $value[$key] = $val;
    $this->view->value = $value;

    /*
      $this->view->width = $value['width'] = "300" ;
      $this->view->height = $value['height'] = "660" ;
      $this->view->titleturncation = $value['titleturncation'] = "50" ;
      $this->view->border_color = $value['border_color'] = "" ;
      $this->view->map = $value['map'] = "" ;
      $this->view->faces = $value['faces'] = "true" ;
      $this->view->stream = $value['stream'] = "true" ;
      $this->view->streamupdatefeed = $value['streamupdatefeed'] = "true" ;
      $this->view->streaminfo = $value['streaminfo'] = "true" ;
      $this->view->streammap = $value['streammap'] = "true" ;
      $this->view->streamreview = $value['streamreview'] = "true" ;
      $this->view->streamdiscussion = $value['streamdiscussion'] = "true" ;
      $this->view->streamalbum = $value['streamalbum'] = "true" ;
      $this->view->streamevent = $value['streamevent'] = "true" ;
      $this->view->streamnote = $value['streamnote'] = "true" ;
      $this->view->streampoll = $value['streampoll'] = "true" ;
      $this->view->streamoffer = $value['streamoffer'] = "true" ;
      $this->view->streamvideo = $value['streamvideo'] = "true" ;
      $this->view->streamdocument = $value['streamdocument'] = "true" ;
      $this->view->streammusic = $value['streammusic'] = "true" ;
      $this->view->header = $value['header'] = "true" ;
      $this->view->colorscheme = $value['colorscheme'] = 'light' ; */




//     if ( isset( $_GET['width'] ) && !empty( $_GET['width'] ) )
//       $this->view->width = $value['width'] = $_GET['width'] ;
//     if ( isset( $_GET['height'] ) && !empty( $_GET['height'] ) )
//       $this->view->height = $value['height'] = $_GET['height'] ;
//     if ( isset( $_GET['titleturncation'] ) && !empty( $_GET['titleturncation'] ) )
//       $this->view->titleturncation = $value['titleturncation'] = $_GET['titleturncation'] ;
//     if ( isset( $_GET['border_color'] ) && !empty( $_GET['border_color'] ) )
//       $this->view->border_color = $value['border_color'] = $_GET['border_color'] ;
//     if ( isset( $_GET['colorscheme'] ) )
//       $this->view->colorscheme = $value['colorscheme'] = $_GET['colorscheme'] ;
//     if ( isset( $_GET['faces'] ) )
//       $this->view->faces = $value['faces'] = $_GET['faces'] ;
//     if ( isset( $_GET['stream'] ) )
//       $this->view->stream = $value['stream'] = $_GET['stream'] ;
//     if ( isset( $_GET['streamupdatefeed'] ) )
//       $this->view->streamupdatefeed = $value['streamupdatefeed'] = $_GET['streamupdatefeed'] ;
//     if ( isset( $_GET['streaminfo'] ) && !empty( $_GET['streaminfo'] ) )
//       $this->view->streaminfo = $value['streaminfo'] = $_GET['streaminfo'] ;
//     if ( isset( $_GET['streammap'] ) && !empty( $_GET['streammap'] ) )
//       $this->view->streammap = $value['streammap'] = $_GET['streammap'] ;
//     if ( isset( $_GET['streamreview'] ) )
//       $this->view->streamreview = $value['streamreview'] = $_GET['streamreview'] ;
//     if ( isset( $_GET['streamdiscussion'] ) )
//       $this->view->streamdiscussion = $value['streamdiscussion'] = $_GET['streamdiscussion'] ;
//     if ( isset( $_GET['streamalbum'] ) )
//       $this->view->streamalbum = $value['streamalbum'] = $_GET['streamalbum'] ;
//     if ( isset( $_GET['streampoll'] ) )
//       $this->view->streampoll = $value['streampoll'] = $_GET['streampoll'] ;
//     if ( isset( $_GET['streamoffer'] ) )
//       $this->view->streamoffer = $value['streamoffer'] = $_GET['streamoffer'] ;
//     if ( isset( $_GET['streamevent'] ) )
//       $this->view->streamevent = $value['streamevent'] = $_GET['streamevent'] ;
//     if ( isset( $_GET['streamnote'] ) )
//       $this->view->streamnote = $value['streamnote'] = $_GET['streamnote'] ;
//     if ( isset( $_GET['streamvideo'] ) )
//       $this->view->streamvideo = $value['streamvideo'] = $_GET['streamvideo'] ;
//     if ( isset( $_GET['streamdocument'] ) )
//       $this->view->streamdocument = $value['streamdocument'] = $_GET['streamdocument'] ;
//     if ( isset( $_GET['streammusic'] ) )
//       $this->view->streammusic = $value['streammusic'] = $_GET['streammusic'] ;
//     if ( isset( $_GET['header'] ) )
//       $this->view->header = $value['header'] = $_GET['header'] ;


    if (isset($_GET['faces']))
      $this->view->userLikes = $userLikes = $sitepage->likes()->getAllLikesUsers();


    //DEFINE CURRENT ACTIVE TAB AND CURRENT ACTIVE ID.
    $current_activetab = '';
    $current_active = '';
    $likebox_result = 'likebox_result';
    $likebox_active = 'likebox_active';

    if ($value['stream'] == "true") {
      if ($value['streamupdatefeed'] == "true" && empty($current_activetab)) {
        $current_activetab = 'updatefeed_' . $likebox_result;
        $current_active = 'updatefeed_' . $likebox_active;
      }
      if ($value['streaminfo'] == "true" && empty($current_activetab)) {
        $current_activetab = 'info_' . $likebox_result;
        $current_active = 'info_' . $likebox_active;
      }
      if ($value['streammap'] == "true" && empty($current_activetab)) {
        $this->view->location = $sitepage->location;
        if (!empty($sitepage->location)) {
          $current_activetab = 'map_' . $likebox_result;
          $current_active = 'map_' . $likebox_active;
        }
      }

      //CHECK AND GET THE CONTENT OF DISCUSSION.
      if ($value['streamdiscussion'] == "true" && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion') && Engine_Api::_()->sitepagelikebox()->allowModule($sitepage, 'sitepagediscussion', $hasPackageEnable)) {
        $this->view->discussionresult = $topicsresult = $this->totalResults($page_id, 'topics', 'sitepage');
        $this->view->discussionTotalResult = $this->itemCount($page_id, 'topics', 'sitepage');

        if (!empty($topicsresult) && empty($current_activetab)) {
          $current_activetab = 'discussion_' . $likebox_result;
          $current_active = 'discussion_' . $likebox_active;
        }
      }

      //CHECK AND GET THE CONTENT OF ALBUMS.
      if ($value['streamalbum'] == "true" && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum') && Engine_Api::_()->sitepagelikebox()->allowModule($sitepage, 'sitepagealbum', $hasPackageEnable)) {

        $this->view->albumresult = $albumresult = $this->totalResults($page_id, 'albums', 'sitepage');
        $this->view->albumTotalResult = $this->itemCount($page_id, 'albums', 'sitepage');

        if (!empty($albumresult) && empty($current_activetab)) {
          $current_activetab = 'album_' . $likebox_result;
          $current_active = 'album_' . $likebox_active;
        }
      }

      //CHECK AND GET THE CONTENT OF EVENTS.
      if ($value['streamevent'] == "true" && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent') && Engine_Api::_()->sitepagelikebox()->allowModule($sitepage, 'sitepageevent', $hasPackageEnable)) {

        $this->view->eventresult = $eventesult = $this->totalResults($page_id, 'events', 'sitepageevent');
        $this->view->eventTotalResult = $this->itemCount($page_id, 'events', 'sitepageevent');

        if (!empty($eventesult) && empty($current_activetab)) {
          $current_activetab = 'event_' . $likebox_result;
          $current_active = 'event_' . $likebox_active;
        }
      }

      //CHECK AND GET THE CONTENT OF POLLS.
      if ($value['streampoll'] == "true" && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll') && Engine_Api::_()->sitepagelikebox()->allowModule($sitepage, 'sitepagepoll', $hasPackageEnable)) {

        $this->view->pollresult = $pollesult = $this->totalResults($page_id, 'polls', 'sitepagepoll');
        $this->view->pollTotalResult = $this->itemCount($page_id, 'polls', 'sitepagepoll');

        if (!empty($pollesult) && empty($current_activetab)) {
          $current_activetab = 'poll_' . $likebox_result;
          $current_active = 'poll_' . $likebox_active;
        }
      }

      //CHECK AND GET THE CONTENT OF NOTES.
      if ($value['streamnote'] == "true" && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote') && Engine_Api::_()->sitepagelikebox()->allowModule($sitepage, 'sitepagenote', $hasPackageEnable)) {

        $this->view->notesresult = $notesresult = $this->totalResults($page_id, 'notes', 'sitepagenote');
        $this->view->notesTotalResult = $this->itemCount($page_id, 'notes', 'sitepagenote');

        if (!empty($notesresult) && empty($current_activetab)) {
          $current_activetab = 'note_' . $likebox_result;
          $current_active = 'note_' . $likebox_active;
        }
      }

      //CHECK AND GET THE CONTENT OF OFFERS.
      if ($value['streamoffer'] == "true" && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer') && Engine_Api::_()->sitepagelikebox()->allowModule($sitepage, 'sitepageoffer', $hasPackageEnable)) {

        $this->view->offersresult = $offersresult = $this->totalResults($page_id, 'offers', 'sitepageoffer');
        $this->view->offersTotalResult = $this->itemCount($page_id, 'offers', 'sitepageoffer');

        if (!empty($offersresult) && empty($current_activetab)) {
          $current_activetab = 'offer_' . $likebox_result;
          $current_active = 'offer_' . $likebox_active;
        }
      }

      //CHECK AND GET THE CONTENT OF VIDEOS.
      if ($value['streamvideo'] == "true" && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo') && Engine_Api::_()->sitepagelikebox()->allowModule($sitepage, 'sitepagevideo', $hasPackageEnable)) {

        $this->view->videosresult = $videosresult = $this->totalResults($page_id, 'videos', 'sitepagevideo');
        $this->view->videosTotalResult = $this->itemCount($page_id, 'videos', 'sitepagevideo');

        if (!empty($videosresult) && empty($current_activetab)) {
          $current_activetab = 'video_' . $likebox_result;
          $current_active = 'video_' . $likebox_active;
        }
      }

      //CHECK AND GET THE CONTENT OF MUSIC.
      if ($value['streammusic'] == "true" && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic') && Engine_Api::_()->sitepagelikebox()->allowModule($sitepage, 'sitepagemusic', $hasPackageEnable)) {

        $this->view->playlistsresult = $playlistsresult = $this->totalResults($page_id, 'playlists', 'sitepagemusic');
        $this->view->playlistsTotalResult = $this->itemCount($page_id, 'playlists', 'sitepagemusic');

        if (!empty($playlistsresult) && empty($current_activetab)) {
          $current_activetab = 'music_' . $likebox_result;
          $current_active = 'music_' . $likebox_active;
        }
      }

      //CHECK AND GET THE CONTENT OF DOCUMENTS.
      if ($value['streamreview'] == "true" && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {

        //START REVIEW WORK
        //GET REVIEW TABLE
        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitepagereview');

        //START TOP SECTION FOR OVERALL RATING AND IT'S PARAMETER
        $noReviewCheck = $reviewTable->getAvgRecommendation($sitepage->page_id);
        if (!empty($noReviewCheck)) {
          $this->view->noReviewCheck = $noReviewCheck->toArray();
          $recommend_percentage = round($noReviewCheck[0]['avg_recommend'] * 100, 3);
          $this->view->recommend_percentage = $recommend_percentage;
        }

        $this->view->ratingDataTopbox = Engine_Api::_()->getDbtable('ratings', 'sitepagereview')->ratingbyCategory($sitepage->page_id);
        //END TOP SECTION FOR OVERALL RATING AND IT'S PARAMETER

        $this->view->paginator = $paginator = $reviewTable->pageReviews($sitepage->page_id);
        $paginator->setItemCountPerPage(1);
        $this->view->totalReviews = $paginator->getTotalItemCount();
        //END REVIEW WORK

        if (!empty($this->view->totalReviews) && empty($current_activetab)) {
          $current_activetab = 'review_' . $likebox_result;
          $current_active = 'review_' . $likebox_active;
        }
      }

      //CHECK AND GET THE CONTENT OF DOCUMENTS.
      if ($value['streamdocument'] == "true" && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument') && Engine_Api::_()->sitepagelikebox()->allowModule($sitepage, 'sitepagedocument', $hasPackageEnable)) {

        $this->view->documentsresult = $documentsresult = $this->totalResults($page_id, 'documents', 'sitepagedocument');
        $this->view->documentTotalResult = $this->itemCount($page_id, 'documents', 'sitepagedocument');

        if (!empty($documentsresult) && empty($current_activetab)) {
          $current_activetab = 'document_' . $likebox_result;
          $current_active = 'document_' . $likebox_active;
        }
      }

      //PASS THE CURRENT ACTIVE ID AND TAB IN TO .TPL FILE.
      $this->view->current_activetab = $current_activetab;
      $this->view->current_active = $current_active;

      $contactPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'contact');

      //PROFILE TYPE PRIVACY
      $profileTypePrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'profile');
      if (!empty($profileTypePrivacy)) {
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitepage/View/Helper', 'Sitepage_View_Helper');
        $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitepage);
      }
      $this->view->profileTypePrivacy = $profileTypePrivacy;
      $this->view->contactPrivacy = $contactPrivacy;
    }
    
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->hasLogin = 0;
    //CHECK USER ID
    if (!empty($viewer_id))
      $this->view->hasLogin = 1;
  }

  //FUNCTION FOR LIKE BOX SHOW.
  public function likeBoxAction() {

    //CHECK FOR VIEW PREMISSION.
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET THE VALUE OF PAGE ID.
    $this->view->page_id = $page_id = $this->_getParam('page_id');
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    if (empty($sitepage)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    if (!Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'likebox')) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $this->view->sitepages_view_menu = 20;

    //GET NAVIGATION.
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

    $this->view->display = false;
    $auth = Engine_Api::_()->authorization()->context;
    $authView = $auth->isAllowed($sitepage, 'everyone', 'view');
    //TIP SHOULD BE SHOW.
    $displayFlag = false;
    if (!empty($authView)) {
      //FORM SHOULD BE SHOW.
      $displayFlag = true;
    }
    $this->view->display = $displayFlag;

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //PASS THE URL.
    $this->view->url = "http://" . $_SERVER['HTTP_HOST'] . $this->view->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id)), 'sitepage_entry_view', true);

    $this->view->form = new Sitepagelikebox_Form_LikeBox(array('item' => $sitepage));

    $this->view->likebox_type = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagelikebox.type', null);
  }

  //FUNCTION FOR GET THE LIKE BOX CODE.
  public function getLikeCodeAction() {

    //GET THE SETTINGS FROM THE CORE TABLE.
    $apiSettings = Engine_Api::_()->getApi('settings', 'core');

    $value = array();
    $value['height'] = null;
    $value['width'] = null;

    if (isset($_GET['href']) && !empty($_GET['href']))
      $value['href'] = $href = $_GET['href'];
    if (isset($_GET['width']) && !empty($_GET['width']))
      $this->view->width = $value['width'] = $_GET['width'];
    if (isset($_GET['height']) && !empty($_GET['height']))
      $this->view->height = $value['height'] = $_GET['height'];
    if (isset($_GET['border_color']) && !empty($_GET['border_color']))
      $value['border_color'] = $_GET['border_color'];
    if (isset($_GET['colorscheme']))
      $value['colorscheme'] = $_GET['colorscheme'];
    if (isset($_GET['faces']))
      $value['faces'] = $_GET['faces'];
    if (isset($_GET['stream']))
      $value['stream'] = $_GET['stream'];
    if (isset($_GET['titleturncation']))
      $value['titleturncation'] = $_GET['titleturncation'];
    if (isset($_GET['streamupdatefeed']))
      $value['streamupdatefeed'] = $_GET['streamupdatefeed'];
    if (isset($_GET['streaminfo']))
      $value['streaminfo'] = $_GET['streaminfo'];
    if (isset($_GET['streammap']))
      $value['streammap'] = $_GET['streammap'];
    if (isset($_GET['streamreview']))
      $value['streamreview'] = $_GET['streamreview'];
    if (isset($_GET['streamdiscussion']))
      $value['streamdiscussion'] = $_GET['streamdiscussion'];
    if (isset($_GET['streamalbum']))
      $value['streamalbum'] = $_GET['streamalbum'];
    if (isset($_GET['streampoll']))
      $value['streampoll'] = $_GET['streampoll'];
    if (isset($_GET['streamoffer']))
      $value['streamoffer'] = $_GET['streamoffer'];
    if (isset($_GET['streamevent']))
      $value['streamevent'] = $_GET['streamevent'];
    if (isset($_GET['streamnote']))
      $value['streamnote'] = $_GET['streamnote'];
    if (isset($_GET['streamvideo']))
      $value['streamvideo'] = $_GET['streamvideo'];
    if (isset($_GET['streamdocument']))
      $value['streamdocument'] = $_GET['streamdocument'];
    if (isset($_GET['streammusic']))
      $value['streammusic'] = $_GET['streammusic'];
    if (isset($_GET['header']))
      $value['header'] = $_GET['header'];
    if (isset($_GET['powerdby']))
      $value['powerdby'] = $_GET['powerdby'];

    if (empty($value['height']))
      $value['height'] = $apiSettings->getSetting('likebox.default.hight');
    if (empty($value['width']))
      $value['width'] = $apiSettings->getSetting('likebox.default.width');

    //PASS THE VALUE OF GENERATED CODE.
    $this->view->code = $code = '&lt;iframe src="' . "http://" . $_SERVER['HTTP_HOST'] . $this->view->url(array('action' => 'index'), 'sitepagelikebox_general', true) . '?href=' . urlencode($value['href']) . '&amp;amp;width=' . urlencode($value['width']) . '&amp;amp;height=' . urlencode($value['height']);

    if ($apiSettings->getSetting('likebox.bordercolor', 1))
      $code.='&amp;amp;border_color=' . urlencode($value['border_color']);

    if ($apiSettings->getSetting('likebox.colorschme', 1))
      $code.='&amp;amp;colorscheme=' . urlencode($value['colorscheme']);

    if ($apiSettings->getSetting('likebox.faces', 1))
      $code.='&amp;amp;faces=' . $value['faces'];

    if ($apiSettings->getSetting('likebox.header', 1))
      $code.='&amp;amp;  header=' . urlencode($value['header']);

    if ($apiSettings->getSetting('likebox.powred', 1))
      $code.='&amp;amp;powerdby=' . urlencode($value['powerdby']);

    if ($apiSettings->getSetting('likebox.titleturncation', 1))
      $code.='&amp;amp;titleturncation=' . urlencode($value['titleturncation']);

    if ($apiSettings->getSetting('likebox.stream', 1))
      $code.='&amp;amp;stream=' . urlencode($value['stream']);

    if ($apiSettings->getSetting('likebox.streamupdatefeed', 1))
      $code.='&amp;amp;streamupdatefeed=' . urlencode($value['streamupdatefeed']);

    if ($apiSettings->getSetting('likebox.info', 1))
      $code.='&amp;amp;streaminfo=' . urlencode($value['streaminfo']);

    if ($apiSettings->getSetting('likebox.map', 1))
      $code.='&amp;amp;streammap=' . urlencode($value['streammap']);

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview') && !empty($value['streamreview']) && ($apiSettings->getSetting('likebox.review', 1)))
      $code.= '&amp;amp;streamreview=' . urlencode($value['streamreview']);

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion') && !empty($value['streamdiscussion']))
      $code.='&amp;amp;streamdiscussion=' . urlencode($value['streamdiscussion']);

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum') && !empty($value['streamalbum']))
      $code.='&amp;amp;streamalbum=' . urlencode($value['streamalbum']);

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll') && !empty($value['streampoll']))
      $code.='&amp;amp;streampoll=' . urlencode($value['streampoll']);

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer') && !empty($value['streamoffer']))
      $code.='&amp;amp;streamoffer=' . urlencode($value['streamoffer']);

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent') && !empty($value['streamevent']))
      $code.='&amp;amp;streamevent=' . urlencode($value['streamevent']);

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote') && !empty($value['streamnote']))
      $code.='&amp;amp;streamnote=' . urlencode($value['streamnote']);

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo') && !empty($value['streamvideo']))
      $code.='&amp;amp;streamvideo=' . urlencode($value['streamvideo']);

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument') && !empty($value['streamdocument']))
      $code.= '&amp;amp;streamdocument=' . urlencode($value['streamdocument']);

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic') && !empty($value['streammusic']))
      $code.= '&amp;amp;streammusic=' . urlencode($value['streammusic']);

    $this->view->code = $code.= '" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:' . $value['width'] . 'px; height:' . $value['height'] . 'px;" allowTransparency="true"&gt; &lt;/iframe&gt;';
  }

  //CHECK USER IS LOGIN.
  public function hasLoginAction() {

    //GET THE VALUE OF USER ID.
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->hasLogin = 0;

    //CHECK USER ID
    if (!empty($viewer_id))
      $this->view->hasLogin = 1;
  }

  //FOR LOGIN .
  public function loginAction() {

    //CHECK FOR VIEW PERMISSION.
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }

    //GET THE VALUE OF FUNCTION.
    $function = $this->_getParam('function');
    if ($function == "like")
      $this->like();
    if ($function == "unlike")
      $this->unlike();
  }

  //FUNCTION FOR LIKE.
  public function likeAction() {
    $this->like();
  }

  //FUNCTION FOR UNLIKE.
  public function unlikeAction() {
    $this->unlike();
  }

  public function like() {

    //GET THE VALUES OF TYPE AND ID
    $type = $this->_getParam('type');
    $identity = $this->_getParam('id');
    $this->view->status = false;

    //CHECK FOR VIEW PERMISSION.
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }
    if (!$this->_helper->requireAuth()->setAuthParams($type, null, 'comment')->isValid()) {
      throw new Engine_Exception('This user is not allowed to unlike this page');
    }
    $subject = null;

    //CHECK FOR TYPE AND ID ID VALID OR NOT.
    if ($type && $identity) {
      $subject = Engine_Api::_()->getItem($type, $identity);
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $function = $this->_getParam('function', null);
    if (!$this->getRequest()->isPost() && empty($function)) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    //PROCESS
    if (!$subject->likes()->isLike($viewer)) {
      $db = $subject->likes()->getAdapter();
      $db->beginTransaction();

      try {

        $subject->likes()->addLike($viewer);
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike'))
          Engine_Api::_()->sitelike()->setLikeFeed($viewer, $subject);

        //ADD NOTIFICATION.
        $owner = $subject->getOwner();
        $this->view->owner = $owner->getGuid();
        if ($owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity()) {
          $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
          $notifyApi->addNotification($owner, $viewer, $subject, 'liked', array(
              'label' => $subject->getShortType()
          ));
        }

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->view->status = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Like added');
    }
  }

  public function unlike() {

    //GET THE VALUE OG TYPE AND ID.
    $type = $this->_getParam('type');
    $identity = $this->_getParam('id');
    $this->view->status = false;

    //CHECK FOR VIEW PERMISSION.
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }
    if (!$this->_helper->requireAuth()->setAuthParams($type, null, 'comment')->isValid()) {
      throw new Engine_Exception('This user is not allowed to unlike this page');
    }
    $subject = null;

    //CHECK FOR TYPE AND ID IS VALID OR NOT.
    if ($type && $identity) {
      $subject = Engine_Api::_()->getItem($type, $identity);
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $function = $this->_getParam('function', null);
    if (!$this->getRequest()->isPost() && empty($function)) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    //PROCESS
    if ($subject->likes()->isLike($viewer)) {
      $db = $subject->likes()->getAdapter();
      $db->beginTransaction();

      try {
        $subject->likes()->removeLike($viewer);
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike'))
          Engine_Api::_()->sitelike()->removeLikeFeed($viewer, $subject);
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->view->status = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Like removed');
    }
  }

  //FUNCTION FOR COUNT THE ITEM.
  public function itemCount($page_id, $tableName, $moduleName) {

    $moduleTable = Engine_Api::_()->getDbtable($tableName, $moduleName);

    return $moduleTable->select()
                    ->from($moduleTable, new Zend_Db_Expr('COUNT(page_id)'))
                    ->where('page_id = ?', $page_id)
                    ->limit(1)->query()->fetchColumn();
  }

  //FUNCTION FOR GET THE TOTAL RESULTS.
  public function totalResults($page_id, $tableName, $moduleName) {

    //GET THE HOW MANY CONTENT SHOW IN THE TAB.
    $likebox_contentshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('likebox.contentshow');

    $moduleTable = Engine_Api::_()->getDbtable($tableName, $moduleName);
    $moduleTableName = $moduleTable->info('name');

    $select = $moduleTable->select()->where('page_id = ?', $page_id)->limit($likebox_contentshow)->order($moduleTableName . '.creation_date DESC');

    return $moduleTable->fetchAll($select);
  }

}