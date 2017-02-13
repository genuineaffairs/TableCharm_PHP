<?php

class Mgslapi_Api_Core extends Core_Api_Abstract
{
    protected $_actionTypes = array(
          'friend_accepted' => 'You and %s are now friends.',
          'event_approve' => '<a href="%s" class="ProfileName">%s</a> has requested to join the event <a href="%s" class="ProfileName">%s</a>.',
          'commented' => '%s has commented on your %s.',
          'commented_commented' => '<a href="%s" class="ProfileName">%s</a> has commented on a <a href="%s" class="ProfileName">%s</a> you commented on.',
          'liked' => '<a href="%s" class="ProfileName">%s</a> likes your <a href="%s" class="ProfileName">%s</a>.',
          'group_discussion_reply' => '<a href="%s" class="ProfileName">%s</a> has <a href="%s" class="ProfileName">posted</a> on a <a href="%s" class="ProfileName">group topic</a> you posted on.',
          'message_new' => '<a href="%s" class="ProfileName">%s</a> has sent you a <a href="%s" class="ProfileName">message</a>.',
          'video_processed' => 'Your <a href="%s" class="ProfileName">video</a> is ready to be viewed.',
          'friend_request' => '%s has requested to be your friend.',
          'post_user' => '<a href="%s" class="ProfileName">%s</a> has posted on your<a href="%s" class="ProfileName">profile</a>.',
      );
    
    public function getRichContent($video_id, $view = false, $params = array())
    {
        $video = Engine_Api::_()->getItem('video', $video_id);
        $session = new Zend_Session_Namespace('mobile');
        $mobile = $session->mobile;
        $zendview = Zend_Registry::get('Zend_View');

        // if video type is youtube
        if ($video->type == 1)
        {
            $videoEmbedded = $video->compileYouTube($video->video_id, $video->code, $view, $mobile);
        }
        // if video type is vimeo
        if ($video->type == 2)
        {
            $videoEmbedded = $video->compileVimeo($video->video_id, $video->code, $view, $mobile);
        }

        // if video type is uploaded
        if ($video->type == 3)
        {
            $video_location = Engine_Api::_()->storage()->get($video->file_id, $video->getType())->getHref();
            $videoEmbedded = $video->compileFlowPlayer($video_location, $view);
        }

        // $view == false means that this rich content is requested from the activity feed
        if ($view == false)
        {

            // prepare the duration
            //

            // prepare the thumbnail
            $thumb = Zend_Registry::get('Zend_View')->itemPhoto($video, 'thumb.video.activity');

            if ($video->photo_id)
            {
//                $thumb = Zend_Registry::get('Zend_View')->itemPhoto($video, 'thumb.video.activity');                
//                $thumb = '<img alt="" src="'.Engine_Api::_()->mgslapi()->getItemPhotoUrl($video).'">';
                if($video->type == 1)
                {
                    $thumb = '<img alt="" src="http://img.youtube.com/vi/'.$video->code.'/0.jpg">';
                }
                elseif($video->type == 2)
                {
                    $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/{$video->code}.php"));                    
                    $src =  $hash[0]['thumbnail_large']; 
                    $thumb = '<img alt="" src="'.$src.'">';
                    
                }
                
            }
            else
            {
                $thumb = '<img alt="" src="' . Zend_Registry::get('StaticBaseUrl') . 'application/modules/Video/externals/images/video.png">';
            }

            if (!$mobile)
            {
                $type = '';
                if($video->type == 1)
                {
                    $type = 'youtube';
                }
                elseif($video->type == 2)
                {
                    $type = 'vimeo';
                    
                }
                $thumb = '<div class="play_icon"><a id="do_play_video_action" href="#code='.$video->code.'@type='.$type.'"><img title="" alt="" src="'.$zendview->serverUrl((string)$zendview->baseUrl()).'/application/modules/Mgslapi/externals/images/play_button.png"></a></div>'
                        . '<a id="do_play_video_action" href="#code='.$video->code.'@type='.$type.'">
                  ' . $thumb . '
                  </a>';
//                $thumb = '<a id="video_thumb_' . $video->video_id . '" style="" href="javascript:void(0);" onclick="$(this).hide(); $(this).next().show()">
//                  <div class="video_thumb_wrapper">' . $thumb . '</div>
//                  </a>';
            }
            else
            {
                $thumb = '<a id="video_thumb_' . $video->video_id . '" class="video_thumb" href="javascript:void(0);" onclick="javascript: $(\'videoFrame' . $video->video_id . '\').style.display=\'block\'; $(\'videoFrame' . $video->video_id . '\').src = $(\'videoFrame' . $video->video_id . '\').src; $(this).hide(); $(this).next().show()">
                  <div class="video_thumb_wrapper">' . $thumb . '</div>
                  </a>';
            }

            // prepare title and description
            $title = "<a href='" . $video->getHref($params) . "'>$video->title</a>";
            $tmpBody = strip_tags($video->description);
            $description = "<div class='video_desc'>" . (Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody) . "</div>";

            $videoEmbedded = $thumb; // . '<div id="video_object_' . $video->video_id . '" class="video_object" style="display:none">' . $videoEmbedded . '</div><div class="video_info"></div>';
        }

        return $videoEmbedded;
    }

    public function getMessagesPaginator(User_Model_User $user, $conversation, $params = array())
    {
      if( empty($conversation->store()->messages) )
      {
        if( !$conversation->hasRecipient($user) )
        {
          throw new Messages_Model_Exception('Specified user not in convo');
        }

        $table = Engine_Api::_()->getItemTable('messages_message');
        $select = $table->select()
          ->where('conversation_id = ?', $conversation->getIdentity())
          //->limit(10);
          ;

        if(!empty($params['previous_conversations_id'])){
            $select->where('message_id < ? ', $params['previous_conversations_id'])
                    ->order('message_id ASC');
        }
        
        if(!empty($params['latest_conversations_id'])){
            $select->where('message_id > ? ', $params['latest_conversations_id'])                    
                    ->order('message_id ASC');
        }
        $conversation->store()->messages = Zend_Paginator::factory($select);
      }

      return $conversation->store()->messages;
    }
    
    public function getMessages(User_Model_User $user, $conversation, $params = array())
    {
      if( empty($conversation->store()->messages) )
      {
        if( !$conversation->hasRecipient($user) )
        {
          throw new Messages_Model_Exception('Specified user not in convo');
        }

        $table = Engine_Api::_()->getItemTable('messages_message');
        $select = $table->select()
          ->where('conversation_id = ?', $conversation->getIdentity())
          //->limit(10);
          ;

        if(!empty($params['previous_conversations_id'])){
            $select->where('message_id < ? ', $params['previous_conversations_id'])
                    ->order('message_id DESC');
        }
        
        if(!empty($params['latest_conversations_id'])){
            $select->where('message_id > ? ', $params['latest_conversations_id'])                    
                    ->order('message_id DESC');
        }
        $conversation->store()->messages = $table->fetchAll($select);
      }

      return $conversation->store()->messages;
    }
    
    public function getUserByCredential($email = false, $password = false)
    {
        if($email AND $password){
            $table = Engine_Api::_()->getDbTable('users', 'user');
            $user = $table->fetchRow(array('email = ?' => $email));
            
            // check valid parent
            if(!empty($user) AND !empty($user->user_id))
            {
                // get core secret
                $secret = Engine_Api::_()
                        ->getApi('settings', 'core')->getSetting('core.secret');

                // get user salt
                $salt = $user->salt;

                // prepare password
                $curentPassword = $secret.$password.$salt;
                $curentPassword = md5($curentPassword);

                // check password validity
                if($curentPassword == $user->password){
                    return $user;
                } 
            }
        }        
        return false;
    }
            
    public function getItemPhotoUrl($item, $type = 'thumb.main')
    {
        $view = Zend_Registry::get('Zend_View');
        if($item->getType() == 'user' OR $item->getType() == 'event')
        {
            if($item->getPhotoUrl())
            {
                $thumb_icon = simplexml_load_string($view->itemPhoto($item, $type));
            }
            else
            {
                $thumb_icon = simplexml_load_string($view->itemPhoto($item, 'thumb.profile'));
            }  
        }
        else
        {
            $thumb_icon = simplexml_load_string($view->itemPhoto($item, $type));
        }
        
        $imageURL = $thumb_icon['src'];
        $strToTrunk = $view->baseUrl() . '/';
        if (substr($imageURL, 0, strlen($strToTrunk)) == $strToTrunk) {
            $imageURL = substr($imageURL, strlen($strToTrunk));
        }
        if (preg_match('/http/',$imageURL))
        {
            $imageURL = ltrim($view->baseUrl($imageURL), '/'); 
        }
        else
        {
            $imageURL = $view->serverUrl((string) $view->baseUrl($imageURL)); 
        }
        return $imageURL;
    }
    
    public function getPreviousComments($action, $params = array())
    {
      $comments = $action->comments();
      $table = $comments->getReceiver();
      $comment_count = $comments->getCommentCount();
      
      if( $comment_count <= 0 ) {
        return;
      }
      // Always just get the last three comments
      $select = $comments->getCommentSelect();
      if(!empty($params['comment_id']))
      {
          $select->where('comment_id < ?', $params['comment_id']);
      }      
      if(!empty($params['newest_comment_id']))
      {
          $select->where('comment_id > ?', $params['newest_comment_id']);
      }      
      if(!empty($params['limit']))
      {
          $select->limit($params['limit']); 
      }      
//      $select->order('comment_id DESC');   
      if( $comment_count <= 10 ) {
        $select->limit(10);      
      } else if( !$commentViewAll ) {
        $select->limit(10);
      }
      //echo $select; exit;
      return $table->fetchAll($select);
    }
    
    public function getLatestComments($action)
    {
      $comments = $action->comments();
      $table = $comments->getReceiver();
      $comment_count = $comments->getCommentCount();
      
      if( $comment_count <= 0 ) {
        return;
      }
      // Always just get the last three comments
      $select = $comments->getCommentSelect();      
//      $select->order('comment_id DESC');   
      if( $comment_count <= 10 ) {
        $select->limit(10);      
      } else if( !$commentViewAll ) {
        $select->limit(10, $comment_count - 10);
      }
      return $table->fetchAll($select);
    }
    
    public function getLatestCommentsBeforePosting($action, $params)
    {
      $comments = $action->comments();
      $table = $comments->getReceiver();
      $comment_count = $comments->getCommentCount();
      
      if( $comment_count <= 0 ) {
        return;
      }
      // Always just get the last three comments
      $select = $comments->getCommentSelect();      
//      $select->order('comment_id DESC');  
      if(!empty($params['current_comment_id']))
      {
          $select->where('comment_id >= ?', $params['current_comment_id']);
      }
      elseif(!empty($params['comment_id']))
      {
          $select->where('comment_id > ?', $params['comment_id']);
      }  
      //echo $select; exit;
      return $table->fetchAll($select);
    }
    
    public function getSuggestGooglePalces($keyword, $latitude = 0, $longitude = 0) {

    //GET API KEY
    $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();


    //GET LATITUDE
    $latitude = str_replace(',', '.', "$latitude");

    //GET LONGITUDE
    $longitude = str_replace(',', '.', "$longitude");

    //SET PARAMS
    $params = array(
        'key' => $apiKey,
        'sensor' => 'false',
        'input' => $keyword,
        'language' => $this->getGoogleMapLocale(),
    );

    //SET LOCATION
    if ($latitude != '0' && $longitude != '0') {
      $params['location'] = $latitude . ',' . $longitude;
    }

    //BUILD QUERY STRING
    $query = http_build_query($params, null, '&');
    //SET URL
    $url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?' . $query;

    //SEND CURL REQUEST
    $ch = curl_init();
    $timeout = 0;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    ob_start();
    curl_exec($ch);
    curl_close($ch);

    //GET CURL RESPONSE
    $response = Zend_Json::decode(ob_get_contents());

    //IF EMPTY REESPONSE THEN GET RESPONSE FROM FILE GET CONTENTS
    if (empty($response)) {
      $response = Zend_Json::decode(file_get_contents($url));
    }

    ob_end_clean();

    //IF STATUS IS NOT OK THE RETURN
    if (!isset($response['status']) || $response['status'] != 'OK') {
      return array();
    }

    //GET PREDICTIONS
    $results = isset($response['predictions']) ? $response['predictions'] : array();
    
    //MAKE SUGGEST ARRAY
    $suggestGooglePalces = array();
    foreach ($results as $place) {        
         
      $details = $this->getDetailsGooglePalces($place['reference']);  
      $suggestGooglePalces[] = array(
          'resource_guid' => 0,
          'google_id' => $place['id'],
          'label' => $place['description'],
          'reference' => $place['reference'],
          'name' => $details['result']['name'],
          'vicinity' => $details['result']['vicinity'],
          'latitude' => $details['result']['geometry']['location']['lat'],
          'longitude' => $details['result']['geometry']['location']['lng'],
          'icon' => $details['result']['icon'],
          'types' => $details['result']['types'],
      );
      
//      if($details['status'] === 'OK')
//      {
//          $suggestGooglePalces['name'] = $details['result']['name'] ;
//          $suggestGooglePalces['vicinity'] = $details['result']['vicinity'] ;
//          $suggestGooglePalces['latitude'] = $details['result']['geometry']['location']['lat'] ;
//          $suggestGooglePalces['longitude'] = $details['result']['geometry']['location']['lng'];
//          $suggestGooglePalces['icon'] = $details['result']['icon'];
//          $suggestGooglePalces['types'] = $details['result']['types'];
//      }
    }

    return $suggestGooglePalces;
  }
  
    public function getDetailsGooglePalces($reference) {

    //GET API KEY
    $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();

    //SET PARAMS
    $params = array(
        'key' => $apiKey,
        'sensor' => 'true',
        'reference' => $reference
    );

    //BUILD QUERY STRING
    $query = http_build_query($params, null, '&');
    
    //SET URL
    $url = 'https://maps.googleapis.com/maps/api/place/details/json?' . $query;


    //SEND CURL REQUEST
    $ch = curl_init();
    $timeout = 0;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    ob_start();
    curl_exec($ch);
    curl_close($ch);

    //GET CURL RESPONSE
    $response = Zend_Json::decode(ob_get_contents());

    //IF EMPTY REESPONSE THEN GET RESPONSE FROM FILE GET CONTENTS
    if (empty($response)) {
      $response = Zend_Json::decode(file_get_contents($url));
    }

    ob_end_clean();

    //IF STATUS IS NOT OK THE RETURN
    if (!isset($response['status']) || $response['status'] != 'OK') {
      return array();
    }

    //GET PREDICTIONS
    $results = isset($response['status']) ? $response : array();
    return $results;
  }
  
   public function getGoogleMapLocale($locale = false) {

    if (!$locale) {
      $locale = Zend_Registry::get('Zend_Translate')->getLocale();
    }

    $british_english = array('en_AU', 'en_BE', 'en_BW', 'en_BZ', 'en_GB', 'en_GU', 'en_HK', 'en_IE', 'en_IN',
        'en_MT', 'en_NA', 'en_NZ', 'en_PH', 'en_PK', 'en_SG', 'en_ZA', 'en_ZW', 'kw', 'kw_GB');

    $friulian = array('fur', 'fur_IT');

    $swiss_german = array('gsw', 'gsw_CH');

    $norwegian_bokma = array('nb', 'nb_NO');

    $portuguese = array('pt', 'pt_PT');

    $brazilian_portuguese = array('pt_BR');

    $chinese = array('zh', 'zh_CN');

    $sar_china = array('zh_HK', 'zh_MO', 'zh_SG');

    $taiwan = array('zh_TW');

    if (in_array($locale, $british_english)) {
      $locale = 'en-GB';
    } elseif (in_array($locale, $friulian)) {
      $locale = 'it';
    } elseif (in_array($locale, $swiss_german)) {
      $locale = 'de';
    } elseif (in_array($locale, $norwegian_bokma)) {
      $locale = 'no';
    } elseif (in_array($locale, $portuguese)) {
      $locale = 'pt-PT';
    } elseif (in_array($locale, $brazilian_portuguese)) {
      $locale = 'pt-BR';
    } elseif (in_array($locale, $chinese)) {
      $locale = 'zh-CN';
    } elseif (in_array($locale, $sar_china)) {
      $locale = 'zh-HK';
    } elseif (in_array($locale, $taiwan)) {
      $locale = 'zh-TW';
    } elseif ($locale) {
      $locale_arr = explode('_', $locale);
      $locale = ($locale_arr[0]) ? $locale_arr[0] : 'en';
    } else {
      $locale = 'en';
    }

    return $locale;
  }
  
  public function getContent($action)
  {
    //$model = Engine_Api::_()->getApi('core', 'activity');
    $params = array_merge(
      $action->toArray(),
      (array) $action->params,
      array(
        'subject' => $action->getSubject(),
        'object' => $action->getObject()
      )
    );
    //$content = $model->assemble($this->body, $params);
    $content = $this->assemble($action->getTypeInfo()->body, $params);
    return $content;
  }
  
  public function assemble($body, array $params = array())
  {
    // Translate body
    $body = $this->getHelper('translate')->direct($body);
    
    // Do other stuff
    preg_match_all('~\{([^{}]+)\}~', $body, $matches, PREG_SET_ORDER);
    foreach( $matches as $match )
    {
      $tag = $match[0];
      $args = explode(':', $match[1]);
      $helper = array_shift($args);

      $helperArgs = array();
      foreach( $args as $arg )
      {
        if( substr($arg, 0, 1) === '$' )
        {
          $arg = substr($arg, 1);
          $helperArgs[] = ( isset($params[$arg]) ? $params[$arg] : null );
        }
        else
        {
          $helperArgs[] = $arg;
        }
      }
      $helper = $this->getHelper($helper);
      $r = new ReflectionMethod($helper, 'direct');
      $content = $r->invokeArgs($helper, $helperArgs);
      $content = preg_replace('/\$(\d)/', '\\\\$\1', $content);
      $body = preg_replace("/" . preg_quote($tag) . "/", $content, $body, 1);
      //echo $body; exit;
    }

    return $body;
  }
  
  public function getHelper($name)
  {
    $name = $this->_normalizeHelperName($name);
    if( !isset($this->_helpers[$name]) )
    {
      $helper = $this->getPluginLoader()->load($name);
      $this->_helpers[$name] = new $helper;
    }

    return $this->_helpers[$name];
  }

  /**
   * Normalize helper name
   * 
   * @param string $name
   * @return string
   */
  protected function _normalizeHelperName($name)
  {
    $name = preg_replace('/[^A-Za-z0-9]/', '', $name);
    //$name = strtolower($name);
    $name = ucfirst($name);
    return $name;
  }
  public function getPluginLoader()
  {
    if( null === $this->_pluginLoader )
    {
      $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR 
          . 'modules' . DIRECTORY_SEPARATOR
          . 'Mgslapi';
      $this->_pluginLoader = new Zend_Loader_PluginLoader(array(
        'Mgslapi_Model_Helper_' => $path . '/Model/Helper/'
      ));
    }

    return $this->_pluginLoader;
  }
  
  public function getNotificationsHtml($notification)
  {
    $view = Zend_Registry::get('Zend_View');
    
    $notificationSubject = $notification->getSubject();
    $notificationObject = $notification->getObject();
    $params= array();
    $params['type'] = $notification->type;
//    $params['s'] = $notificationSubject->getType();
//    $params['s_id'] = $notificationSubject->getIdentity();
//    $params['o'] = $notificationObject->getType();
//    $params['o_id'] = $notificationObject->getIdentity();

    
//    switch ($notification->type)
//    {        
//      case 'friend_accepted':
//       $params['body'] = sprintf($this->_actionTypes[$notification->type], $notificationSubject->getTitle());        
//        break;
//
//      case 'event_approve':
//        $params['body'] = sprintf($this->_actionTypes[$notification->type],  $this->getProfileHrefUser($notificationSubject->getIdentity()), $notificationSubject->getTitle(), $this->getBaseUrl() . $this->getEventHref($notificationObject->getIdentity(), $notificationObject->getTitle()), $notificationObject->getTitle());
//        break;
//
//      case 'commented':
//        list($title, $url) = $this->getCommentedTitleUrl($notification);
//  
//        echo '<pre>';
//        print_r($notification->getContent()));
//        echo '</pre>';
//        exit;
//        $params['body'] = sprintf($this->_actionTypes[$notification->type],  $notificationSubject->getTitle(),  $title);
//        break;
//
//      case 'group_discussion_reply':
//        $groupTopicUrl = $this->getGroupTopicUrl($notification);
//        $params['body'] = sprintf($this->_actionTypes[$notification->type], $this->getProfileHrefUser($notificationSubject->getIdentity()), $notificationSubject->getTitle(), $groupTopicUrl, $this->getBaseUrl() . $this->getGroupHref($notificationObject->getParent()->getIdentity()), $notificationObject->getParent()->getTitle());
//        break;
//
//      case 'message_new':
//        $params['body'] = sprintf($this->_actionTypes[$notification->type],  $this->getProfileHrefUser($notificationSubject->getIdentity()), $notificationSubject->getTitle(), $this->getMessageDetailHref($notificationObject->getIdentity()));
//          break;
//
//      case 'commented_commented':
//        list($title, $url) = $this->getCommentedTitleUrl($notification);
//        $params['body'] = sprintf($this->_actionTypes[$notification->type],  $this->getProfileHrefUser($notificationSubject->getIdentity()), $notificationSubject->getTitle(), $url, $title);
//        break;
//
//      case 'video_processed':
//        list($video_location, $video_type) = $this->getVideoTypeAndLocation($notificationObject);
//        $params['body'] = sprintf($this->_actionTypes[$notification->type],  $this->getVideoHref($video_location, $video_type));
//        break;
//
//      case 'friend_request':
//        $params['body'] = sprintf($this->_actionTypes[$notification->type], $notificationSubject->getTitle());
//          break;
//
//      case 'post_user':
//        $params['body'] = sprintf($this->_actionTypes[$notification->type],  $this->getProfileHrefUser($notificationSubject->getIdentity()), $notificationSubject->getTitle(), $this->getBaseUrl() . $this->getProfileHrefUser($notificationObject->getIdentity()));
//        break;
//
//      default:
//        // Log not implemented $notification->type
//    }
    $params['body'] = strip_tags($notification->getContent());
    return $params;
  }
  public function getProfileHrefUser($identity = null)
  {
    if(!$identity)
    {
      return null;
    }
    $view = Zend_Registry::get('Zend_View');
    return 'javascript:void(0);';//$view->baseUrl(); . '/?' .  $this->buildHttpQuery(array('viewController' => 'Profile', 'id' => $identity));
  }

  
  public function getAlbumGridHref($identity = null)
  {
    if(!$identity)
    {
      return null;
    }
    $view = Zend_Registry::get('Zend_View');
    return 'javascript:void(0);';//$view->baseUrl() . '/?' .  $this->buildHttpQuery(array('viewController' => 'GridAlbum', 'id' => $identity));
  }
  
  public function getBlogHref($identity = null)
  {
    if(!$identity)
    {
      return null;
    }
    $view = Zend_Registry::get('Zend_View');
    $blog = Engine_Api::_()->getItem('blog', $identity);

    return 'javascript:void(0);';//$view->baseUrl() . '/?' .  $this->buildHttpQuery(array('viewController' => 'BlogDetails', 'id' => $identity, 'title' => $blog->getTitle()));
  }
  
  public function getAlbumHref($identity = null)
  {
    if(!$identity)
    {
      return null;
    }
    $view = Zend_Registry::get('Zend_View');
    return 'javascript:void(0);';//$view->baseUrl() . '/?' .  $this->buildHttpQuery(array('viewController' => 'Album', 'id' => $identity));
  }
  
  public function getMusicHref($type = null, $url = null)
  {
    $view = Zend_Registry::get('Zend_View');
    return 'javascript:void(0);';//$view->baseUrl() . '/?' .  $this->buildHttpQuery(array('viewController' => 'Audio', 'type' => $type, 'url' => $url));
  }
  
  public function getMusicHrefHtml($id = null)
  {
    $view = Zend_Registry::get('Zend_View');
    $musicPlaylist = Engine_Api::_()->getItem('music_playlist', $id);
    return 'javascript:void(0);';//$view->baseUrl() . '/?' .  $this->buildHttpQuery(array('viewController' => 'MusicPlayer', 'id' => $id, 'title' => $musicPlaylist->getTitle()));
  }
  
  public function getVideoHref($video_location = null, $video_type = null)
  {
    $view = Zend_Registry::get('Zend_View');
    return 'javascript:void(0);';//$view->baseUrl() . '/?' .  $this->buildHttpQuery(array('viewController' => 'Video', 'link' => $video_location, 'type' => $video_type));
  }
  
  public function getEventHref($identity = null, $title = '')
  {
    if(!$identity)
    {
      return null;
    }
    $view = Zend_Registry::get('Zend_View');
    return'javascript:void(0);';// $view->baseUrl() . '/?' .  $this->buildHttpQuery(array('viewController' => 'EventDetail', 'id' => $identity, 'title' => $title));
  }
  
  public function getMessageDetailHref($identity = null)
  {
    if(!$identity)
    {
      return null;
    }
    $view = Zend_Registry::get('Zend_View');
    $conversation = Engine_Api::_()->getItem('messages_conversation', $identity);
    return 'javascript:void(0);';//$view->baseUrl() . '/?' .  $this->buildHttpQuery(array('viewController' => 'MessageThread', 'id' => $identity, 'message_title' => $conversation->getTitle()));
  }
  
  public function getGroupHref($identity = null)
  {
    if(!$identity)
    {
      return null;
    }
    $view = Zend_Registry::get('Zend_View');
    return 'javascript:void(0);';//$view->baseUrl() . '/?' .  $this->buildHttpQuery(array('viewController' => 'GroupDetail', 'id' => $identity));
  }
  
  public function getBaseUrl($fullPath = false)
  {
    $baseUrl = (!empty($_SERVER['HTTPS'])?'https://':'http://').$_SERVER['HTTP_HOST'];
    if($fullPath)
    {
      $view = Zend_Registry::get('Zend_View');
      return $baseUrl . $view->baseUrl();
    }
    return $baseUrl;
  }
   public function _getPostSelf($data)
  {
    $view = Zend_Registry::get('Zend_View');
    $html = '';
    $actionRowCollection = $data['actionRowCollection'];
    $suff = '';
    if( $actionRowCollection->attachment_count > 0 && count($actionRowCollection->getAttachments()) > 0 ):
      if( count($actionRowCollection->getAttachments()) == 1 &&
                    null != ( $richContent = current($actionRowCollection->getAttachments())->item->getRichContent()) ):
        $currentItem = current($actionRowCollection->getAttachments())->item;
        switch($currentItem->getModuleName())
        {
          case 'Video':
            $suff = '_video';
            break;
          case 'Music':
            $suff = '_music';
            break;
        }
      endif;
    endif;
    $html = $view->partial(
      '_post_self'.$suff.'.tpl',
      $this->getModuleName(),
      $data
    );
    return $html;
  }
  
  /*
   * returns adding base url and image
   * @param html string
   */
  public function addBaseUrlImgAnc($html = null)
  {
    if(empty($html))
      return;
    
    $view = Zend_Registry::get('Zend_View');
    $zendDomQuery = new Zend_Dom_Query($html);
    
    $result = $zendDomQuery->queryXpath('//a[starts-with(@href,"'.$view->baseUrl().'")]');
    //Add baseUrl to link
    foreach ($result as $value)
    {
      $value->setAttribute('href', $this->getBaseUrl().$value->getAttribute('href'));
    }
    
    $result = $zendDomQuery->queryXpath('//img[starts-with(@src,"'.$view->baseUrl().'") or starts-with(@src,"application") or starts-with(@src,"/application")]');
    //Add baseUrl to image
    $pattern[] = '#^('.$view->baseUrl().'.*)$#i';
    $replacement[] = $this->getBaseUrl().'$1';
    $pattern[] = '#^(application.*)$#i';
    $replacement[] = $this->getBaseUrl(true).'/$1';
    $pattern[] = '#^(/application.*)$#i';
    $replacement[] = $this->getBaseUrl(true).'$1';
    foreach ($result as $value)
    {
      $src = preg_replace($pattern, $replacement, $value->getAttribute('src'));
      $value->setAttribute('src', $src);
    }
    return $result->getDocument()->saveHTML();
  }
  
  public function urlExists($url) {
    if (!$fp = curl_init($url)) return false;
    return true;
  }
  
  /*
   * This method chops a string with a desired width
   * it doesn't cut string from a word
   * ref : http://stackoverflow.com/questions/79960/how-to-truncate-a-string-in-php-to-the-word-closest-to-a-certain-number-of-chara
   * 
   *  @param String $string
   *  @param Integer $your_desired_width
   *  @return string
   */
  public function tokenTruncate($string, $your_desired_width) {
    $parts = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
    $parts_count = count($parts);

    $length = 0;
    $last_part = 0;
    for (; $last_part < $parts_count; ++$last_part) {
      $length += strlen($parts[$last_part]);
      if ($length > $your_desired_width) { break; }
    }

    return implode(array_slice($parts, 0, $last_part));
  }
  
  public function tokenTruncateAddDot($string = '', $your_desired_width = 100, $suf = '...')
  {
    if(empty($string))
      return $string;
    $string = strip_tags($string);
    $str = $this->tokenTruncate($string, $your_desired_width);
    if(!empty($str))
    {
      return $str . $suf;
    }
    return '';
  }

  public function userFriendship($user, $viewer = null)
  {
    $view = Zend_Registry::get('Zend_View');
    $anchor = '<a href="' . $this->getBaseUrl(true) . '/?' . '%s"><img src="' . $this->getBaseUrl(true) . '/public/' . $this->getModuleName() . '/static/%s" alt="" width="20" height="24" border="0" /></a>';

    if( null === $viewer ) {
      $viewer = Engine_Api::_()->user()->getViewer();
    }

    if( !$viewer || !$viewer->getIdentity() || $user->isSelf($viewer) ) {
      return '';
    }

    $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);

    // Get data
    if( !$direction ) {
       $row = $user->membership()->getRow($viewer);
    }
    else $row = $viewer->membership()->getRow($user);

    // Render

    // Check if friendship is allowed in the network
    $eligible =  (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.eligible', 2);
    if($eligible == 0){
      return '';
    }
   
    // check admin level setting if you can befriend people in your network
    else if( $eligible == 1 ) {

      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
      $networkMembershipName = $networkMembershipTable->info('name');

      $select = new Zend_Db_Select($networkMembershipTable->getAdapter());
      $select
        ->from($networkMembershipName, 'user_id')
        ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
        ->where("`{$networkMembershipName}`.user_id = ?", $viewer->getIdentity())
        ->where("`{$networkMembershipName}_2`.user_id = ?", $user->getIdentity())
        ;

      $data = $select->query()->fetch();

      if(empty($data)){
        return '';
      }
    }
    
    if( !$direction ) {
      // one-way mode
      if( null === $row ) {
        // @todo icon not done
        return sprintf($anchor, $this->buildHttpQuery(array('action' => 'add_friend', 'id' => $user->getIdentity())), 'add_friend@2x.png');
      } else if( $row->resource_approved == 0 ) {
        // @todo icon not done
        return sprintf($anchor, $this->buildHttpQuery(array('action' => 'cancel_friend', 'id' => $user->getIdentity())), 'remove_friend@2x.png');
      } else {
        // @todo icon not done
        return sprintf($anchor, $this->buildHttpQuery(array('action' => 'remove_friend', 'id' => $user->getIdentity())), 'remove_friend@2x.png');
      }

    } else {
      // two-way mode
      if( null === $row ) {
        return sprintf($anchor, $this->buildHttpQuery(array('action' => 'add_friend', 'id' => $user->getIdentity())), 'add_friend@2x.png');
      } else if( $row->user_approved == 0 ) {
        // @todo icon not done
        return sprintf($anchor, $this->buildHttpQuery(array('action' => 'cancel_friend', 'id' => $user->getIdentity())), 'remove_friend@2x.png');
      } else if( $row->resource_approved == 0 ) {
        // @todo icon not done
        return sprintf($anchor, $this->buildHttpQuery(array('action' => 'confirm_friend', 'id' => $user->getIdentity())), 'add_friend@2x.png');
      } else if( $row->active ) {
        return sprintf($anchor, $this->buildHttpQuery(array('action' => 'remove_friend', 'id' => $user->getIdentity())), 'remove_friend@2x.png');
      }
    }

    return '';
  }
  
  public function buildHttpQuery($params = array())
  {
    $paramsJoined = array();
    foreach($params as $param => $value)
    {
      $paramsJoined[] = "$param=$value";
    }
    $query = implode('&', $paramsJoined);
    return $query;
  }
  
  public function getRsvp($subject, $viewer)
  {
    $row = $subject->membership()->getRow($viewer);
    if( !$row ) {
      return false;
    }
    return $row->rsvp;
  }

  public function getEventActions($identity = null, $actionName = '')
  {
    if(!$identity)
    {
      return null;
    }
    $view = Zend_Registry::get('Zend_View');
    return $view->baseUrl() . '/?' .  $this->buildHttpQuery(array('action' => $actionName, 'id' => $identity));
  }

  public function getGroupTopicUrl($identity = null)
  {
    if(!$identity)
    {
      return null;
    }
    $view = Zend_Registry::get('Zend_View');
    return $view->baseUrl() . '/?' .  $this->buildHttpQuery(array('viewController' => 'GroupTopicDetail', 'id' => $identity));
  }

  public function getActivityActionHref($identity)
  {
    if(!$identity)
    {
      return null;
    }
    $view = Zend_Registry::get('Zend_View');
    return $view->baseUrl() . '/?' .  $this->buildHttpQuery(array('viewController' => 'FeedDetail', 'id' => $identity));
  }
  
  
  public function getCommentedTitleUrl($notification, $key = null)
  {
    $notificationSubject = $notification->getSubject();
    $notificationObject = $notification->getObject();
    
    $return = array(
        'title' => '',
        'url' => '',
    );
    switch ($notification->object_type)
    {
      case 'activity_action':
        $return['title'] = 'post';
        $return['url'] = 'javascript:void(0);';//$this->getBaseUrl() . $this->getActivityActionHref($notificationObject->getIdentity());
        break;

      case 'core_comment':
        $return['title'] = 'comment';
        $return['url'] = 'javascript:void(0);';//$this->getBaseUrl() . $this->getActivityActionHref($notificationObject->getIdentity());
        break;

      case 'blog':
        $return['title'] = 'blog';
        $return['url'] = 'javascript:void(0);';//$this->getBaseUrl() . $this->getBlogHref($notificationObject->getIdentity());
        break;

      default:
        // Log not implemented $notification->object_type
    }
    
    return $return;
  }
  public function sendPushNotification($deviceToken, $deviceType, $pushMessage, $customPushData = false, $receiver_id = null)
  {
//      echo '<pre>';
//      print_r($customPushData);
//      echo '</pre>';
//   
//      echo "token=$deviceToken, type=$deviceType, message=$pushMessage"; exit;
      if($deviceType AND $deviceType == Mgslapi_Model_DbTable_Devices::IOS_DEVICE_TYPE)
        {
            $message = new Zend_Mobile_Push_Message_Apns();            
            $message->setAlert($pushMessage);
            
            $receiver = Engine_Api::_()->user()->getUser($receiver_id);
            if($receiver->getIdentity()) {
              $message->setBadge((int) Engine_Api::_()->getDbtable('notifications', 'activity')->hasNotifications($receiver));
            } else {
              $message->setBadge(1);
            }
            $message->setSound('default');
            $message->setId(time());
            $message->setToken($deviceToken);
            
            if($customPushData)
            {
                foreach ($customPushData as $key => $value)
                {
                   $message->addCustomData($key,$value); 
                }
            }            
            $apns = new Zend_Mobile_Push_Apns();
            $apns->setCertificate('application/modules/Mgslapi/externals/Certificates.pem');
            // if you have a passphrase on your certificate:
            // $apns->setCertificatePassphrase('foobar');
            try {
                $apns->connect();
            } catch (Zend_Mobile_Push_Exception_ServerUnavailable $e) {
                // you can either attempt to reconnect here or try again later
//                die($e->getMessage());
            } catch (Zend_Mobile_Push_Exception $e) {
                //echo 'APNS Connection Error:' . $e->getMessage();
//                die($e->getMessage());
            }

            try { 
                $apns->send($message);                
            } catch (Zend_Mobile_Push_Exception_InvalidToken $e) {
                // you would likely want to remove the token from being sent to again
//                die($e->getMessage());
            } catch (Zend_Mobile_Push_Exception $e) {
                // all other exceptions only require action to be sent
//                die($e->getMessage());
            }
            $apns->close();
        }
        elseif($deviceType AND $deviceType == Mgslapi_Model_DbTable_Devices::ANDROID_DEVICE_TYPE)
        {
            $message = new Zend_Mobile_Push_Message_Gcm();
            $message->setId(time());
            $message->addToken($deviceToken);
            $message->setData(array(
                'message' => $pushMessage,
            ));
            if($customPushData)
            {
                foreach ($customPushData as $key => $value)
                {
                   $message->addData($key,$value); 
                }
            }            
            $gcm = new Zend_Mobile_Push_Gcm();
            $gcm->setApiKey('AIzaSyBBGwESApG6_BVURkpW0aUxmEPCHT-BuS8');
            $response = false;
            try {
 
                $response = $gcm->send($message);
            } catch (Zend_Mobile_Push_Exception $e) {
                // all other exceptions only require action to be sent or implementation of exponential backoff.
//                die($e->getMessage());
            }

            // handle all errors and registration_id's
            foreach ($response->getResults() as $k => $v) {
//                if ($v['registration_id']) {
//                    printf("%s has a new registration id of: %s\r\n", $k, $v['registration_id']);
//                }
//                if ($v['error']) {
//                    printf("%s had an error of: %s\r\n", $k, $v['error']);
//                }
//                if ($v['message_id']) {
//                    printf("%s was successfully sent the message, message id is: %s", $k, $v['message_id']);
//                }
            }
        }  
  }
}
