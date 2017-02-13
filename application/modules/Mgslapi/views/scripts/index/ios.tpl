<?php $currentModuleCoreApi = Engine_Api::_()->getApi('core', 'mgslapi'); ?>
<?php echo $this->headLink()->setStylesheet($this->baseUrl() . '/application/modules/' . ucfirst($currentModuleCoreApi->getModuleName()) . '/externals/styles/style.css'); ?>
<div>
  <div class="formheader">Mgslapi API Test Tool</div>
  <div style="width:300px; margin:auto;">
    <div id="form_box">
      <select name="formfield" id="formfield">
        <option value="" label="-Select Your Form-" selected="selected">-Select Your Form-</option>
        <optgroup label="General">
          <option value="login" label="login">login</option>   
          <option value="logout" label="logout">logout</option>
        </optgroup>
        <optgroup label="Feed">
          <option value="showFeeds" label="showFeeds">showFeeds</option>
          <option value="postFeed" label="postFeed">postFeed</option>
          <option value="removeFeed" label="removeFeed">removeFeed</option>
          <option value="likeFeed" label="likeFeed">likeFeed</option>
          <option value="unlikeFeed" label="unlikeFeed">unlikeFeed</option>
          <option value="shareFeed" label="shareFeed">shareFeed</option>
          <option value="fetchFeed" label="fetchFeed">fetchFeed</option>
          <option value="comment" label="comment">comment</option>
        </optgroup>

        <optgroup label="Profile">
          <option value="fetchAboutInfo" label="fetchAboutInfo">fetchAboutInfo</option>
          <option value="fetchMedicalRecord" label="fetchMedicalRecord">fetchMedicalRecord</option>
          <option value="fetchMemberList" label="fetchMemberList">fetchMemberList</option>
          <option value="fetchGeneralInfo" label="fetchGeneralInfo">fetchGeneralInfo</option>
          <option value="fetchPhotoLibrary" label="fetchPhotoLibrary">fetchPhotoLibrary</option>
          <option value="fetchVideoLibrary" label="fetchVideoLibrary">fetchVideoLibrary</option>
          <option value="fetchProfileEvents" label="fetchProfileEvents">fetchProfileEvents</option>
          <option value="fetchJoinedCircles" label="fetchJoinedCircles">fetchJoinedCircles</option>
        </optgroup>

        <optgroup label="PhotoAlbum">
          <option value="fetchPhotoAlbumDetails" label="fetchPhotoAlbumDetails">fetchPhotoAlbumDetails</option>
          <option value="fetchPhotoDetails" label="fetchPhotoDetails">fetchPhotoDetails</option>
          <option value="deleteAlbum" label="deleteAlbum">deleteAlbum</option>
          <option value="deletePhoto" label="deletePhoto">deletePhoto</option>
          <option value="editAlbum" label="editAlbum">editAlbum</option>
          <option value="editPhoto" label="editPhoto">editPhoto</option>
        </optgroup>

        <optgroup label="Video">
          <option value="fetchVideoDetails" label="fetchVideoDetails">fetchVideoDetails</option>
          <option value="deleteVideo" label="deleteVideo">deleteVideo</option>
        </optgroup>

        <optgroup label="Message">
          <option value="fetchInboxConversations" label="fetchInboxConversations">fetchInboxConversations</option>
          <option value="fetchOutboxConversations" label="fetchOutboxConversations">fetchOutboxConversations</option>
          <option value="composeMessage" label="composeMessage">composeMessage</option>
          <option value="fetchMessagesOfConversation" label="fetchMessagesOfConversation">fetchMessagesOfConversation</option>
          <option value="sendReply" label="sendReply">sendReply</option>
          <option value="fetchNotifications" label="fetchNotifications">fetchNotifications</option>
          <option value="updateNotificationReadStatus" label="updateNotificationReadStatus">updateNotificationReadStatus</option>
        </optgroup>

        <optgroup label="User">
          <option value="fetchFriendRequests" label="fetchFriendRequests">fetchFriendRequests</option>
          <option value="acceptFriendRequest" label="acceptFriendRequest">acceptFriendRequest</option>
          <option value="rejectFriendRequest" label="rejectFriendRequest">rejectFriendRequest</option>
          <option value="addFriend" label="addFriend">addFriend</option>
          <option value="removeFriend" label="removeFriend">removeFriend</option>
          <option value="deleteUserAccount" label="deleteUserAccount">deleteUserAccount</option>
        </optgroup>

        <optgroup label="Medical Record">
          <option value="editMedicalRecordSharing" label="editMedicalRecordSharing">editMedicalRecordSharing</option>
          <option value="fetchMedicalSharingAccessLists" label="fetchMedicalSharingAccessLists">fetchMedicalSharingAccessLists</option>
        </optgroup>

        <optgroup label="Event">
          <option value="deleteEvent" label="deleteEvent">deleteEvent</option>
          <option value="fetchPersonalEvents" label="fetchPersonalEvents">fetchPersonalEvents</option>
          <option value="fetchEventGeneralInfo" label="fetchEventGeneralInfo">fetchEventGeneralInfo</option>
          <option value="updateEventRsvp" label="updateEventRsvp">updateEventRsvp</option>
          <option value="fetchEventGuests" label="fetchEventGuests">fetchEventGuests</option>
          <option value="inviteGuestsToEvent" label="inviteGuestsToEvent">inviteGuestsToEvent</option>
          <option value="acceptEventRequest" label="acceptEventRequest">acceptEventRequest</option>
          <option value="rejectEventRequest" label="rejectEventRequest">rejectEventRequest</option>
          <option value="cancelEventInvite" label="cancelEventInvite">cancelEventInvite</option>
          <option value="cancelEventRequest" label="cancelEventRequest">cancelEventRequest</option>
          <option value="joinEvent" label="joinEvent">joinEvent</option>
          <option value="leaveEvent" label="leaveEvent">leaveEvent</option>
          <option value="acceptEventInvite" label="acceptEventInvite">acceptEventInvite</option>
          <option value="rejectEventInvite" label="rejectEventInvite">rejectEventInvite</option>
          <option value="fetchEventPhotos" label="fetchEventPhotos">fetchEventPhotos</option>
          <option value="deleteEventPhoto" label="deleteEventPhoto">deleteEventPhoto</option>
          <option value="uploadEventPhoto" label="uploadEventPhoto">uploadEventPhoto</option>
          <option value="fetchEventDiscussions" label="fetchEventDiscussions">fetchEventDiscussions</option>
          <option value="fetchEventDiscussionDetails" label="fetchEventDiscussionDetails">fetchEventDiscussionDetails</option>
          <option value="postEventDiscussionTopic" label="postEventDiscussionTopic">postEventDiscussionTopic</option>
          <option value="postEventDiscussionReply" label="postEventDiscussionReply">postEventDiscussionReply</option>
          <option value="deleteEventTopicThread" label="deleteEventTopicThread">deleteEventTopicThread</option>
        </optgroup>

        <optgroup label="Circle">
          <option value="fetchCircleList" label="fetchCircleList">fetchCircleList</option>
          <option value="fetchMyCircles" label="fetchMyCircles">fetchMyCircles</option>
          <option value="fetchCirclesByLocation" label="fetchCirclesByLocation">fetchCirclesByLocation</option>
          <option value="fetchCircleInfo" label="fetchCircleInfo">fetchCircleInfo</option>
          <option value="fetchCircleGeneralInfo" label="fetchCircleGeneralInfo">fetchCircleGeneralInfo</option>
          <option value="fetchCircleMemberList" label="fetchCircleMemberList">fetchCircleMemberList</option>
          <option value="fetchCirclePhotoLibrary" label="fetchCirclePhotoLibrary">fetchCirclePhotoLibrary</option>
          <option value="fetchCirclePhotoAlbumDetails" label="fetchCirclePhotoAlbumDetails">fetchCirclePhotoAlbumDetails</option>
          <option value="fetchCircleVideoLibrary" label="fetchCircleVideoLibrary">fetchCircleVideoLibrary</option>
          <option value="likeCircle" label="likeCircle">likeCircle</option>
          <option value="followCircle" label="followCircle">followCircle</option>
          <option value="leaveCircle" label="leaveCircle">leaveCircle</option>
          <option value="joinCircle" label="joinCircle">joinCircle</option>
          <option value="cancelCircleMembershipRequest" label="cancelCircleMembershipRequest">cancelCircleMembershipRequest</option>
          <option value="deleteCircle" label="deleteCircle">deleteCircle</option>
          <option value="addMembersToCircle" label="addMembersToCircle">addMembersToCircle</option>
          <option value="fetchCircleSuggestedPeopleToInvite" label="fetchCircleSuggestedPeopleToInvite">fetchCircleSuggestedPeopleToInvite</option>
          <option value="removeMemberFromCircle" label="removeMemberFromCircle">removeMemberFromCircle</option>
          <option value="messageCircleOwner" label="messageCircleOwner">messageCircleOwner</option>
          <option value="fetchCircleOverview" label="fetchCircleOverview">fetchCircleOverview</option>
          <option value="fetchCircleEvents" label="fetchCircleEvents">fetchCircleEvents</option>
          <option value="fetchCircleNotes" label="fetchCircleNotes">fetchCircleNotes</option>
          <option value="fetchCircleDocuments" label="fetchCircleDocuments">fetchCircleDocuments</option>
          <option value="fetchCircleVideoDetails" label="fetchCircleVideoDetails">fetchCircleVideoDetails</option>
          <option value="fetchCircleNoteDetails" label="fetchCircleNoteDetails">fetchCircleNoteDetails</option>
          <option value="fetchCircleDocumentDetails" label="fetchCircleDocumentDetails">fetchCircleDocumentDetails</option>
          <option value="deleteCircleNote" label="deleteCircleNote">deleteCircleNote</option>
          <option value="uploadCircleNotePhoto" label="uploadCircleNotePhoto">uploadCircleNotePhoto</option>
          <option value="deleteCircleDocument" label="deleteCircleDocument">deleteCircleDocument</option>
          <option value="fetchCircleMembershipRequests" label="fetchCircleMembershipRequests">fetchCircleMembershipRequests</option>
          <option value="rejectCircleMembershipRequest" label="rejectCircleMembershipRequest">rejectCircleMembershipRequest</option>
          <option value="acceptCircleMembershipRequest" label="acceptCircleMembershipRequest">acceptCircleMembershipRequest</option>
          <option value="fetchCirclePhotoDetails" label="fetchCirclePhotoDetails">fetchCirclePhotoDetails</option>
          <!--<option value="createCircle" label="createCircle">createCircle</option>-->
        </optgroup>
        
        <optgroup label="Chat">
          <option value="fetchChatHistoryOfUser" label="fetchChatHistoryOfUser">fetchChatHistoryOfUser</option>
          <option value="fetchUnreadChatMessageCounts" label="fetchUnreadChatMessageCounts">fetchUnreadChatMessageCounts</option>
        </optgroup>
        
        <optgroup label="Resume">
          <option value="fetchResumeList" label="fetchResumeList">fetchResumeList</option>
          <option value="fetchSubfieldOptions" label="fetchSubfieldOptions">fetchSubfieldOptions</option>
          <option value="fetchMyResumes" label="fetchMyResumes">fetchMyResumes</option>
          <option value="fetchResumePackages" label="fetchResumePackages">fetchResumePackages</option>
          <option value="fetchResumeSummary" label="fetchResumeSummary">fetchResumeSummary</option>
          <option value="fetchResumeGeneralInfo" label="fetchResumeGeneralInfo">fetchResumeGeneralInfo</option>
          <option value="fetchResumeMap" label="fetchResumeMap">fetchResumeMap</option>
          <option value="fetchResumePhotos" label="fetchResumePhotos">fetchResumePhotos</option>
          <option value="fetchPaypalParams" label="fetchPaypalParams">fetchPaypalParams</option>
          <option value="deleteResume" label="deleteResume">deleteResume</option>
          <option value="uploadResumePhoto" label="uploadResumePhoto">uploadResumePhoto</option>
        </optgroup>

        <optgroup label="Common">
          <option value="postItemComment" label="postItemComment">postItemComment</option>
          <option value="deleteComment" label="deleteComment">deleteComment</option>
          <option value="likeItem" label="likeItem">likeItem</option>
          <option value="unlikeItem" label="unlikeItem">unlikeItem</option>
          <option value="shareItem" label="shareItem">shareItem</option>
          <option value="testPushNotification" label="testPushNotification">testPushNotification</option>
          <option value="fetchBadgeNumber" label="fetchBadgeNumber">fetchBadgeNumber</option>
        </optgroup>

        <optgroup label="Safa">
          <option value="registerUser" label="registerUser">registerUser</option>
          <option value="updateUser" label="updateUser">updateUser</option>
        </optgroup>

      </select>
      <br /><br />

    </div>

    <div style="margin-left:-5px;">
      <!-- login -->
      <?php $url = $this->url(array('action' => 'login'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_login" action="<?php echo $url ?>" method="post">
        <div id="form_box">                            
          <h2>End-point: login</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>                

          <label>email:</label><br />
          <input name="email" type="text" value="" /><br />

          <label>password:</label><br />
          <input name="password" type="password" value="" /><br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- showFeeds -->
      <?php $url = $this->url(array('action' => 'show-feeds'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_showFeeds" action="<?php echo $url ?>" method="post">
        <div id="form_box">                            
          <h2>End-point: showFeeds</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>                

          <label>Limit number:</label><br />
          <input type="text" value="" name="limitNumber" /><br />

          <label>Max id:</label><br />
          <input type="text" value="" name="maxid" /><br />

          <label>User id:</label><br />
          <input type="text" value="" name="user_id" /><br />
          
          <label>Feed id:</label><br />
          <input type="text" value="" name="action_id" /><br />

          <label>Subject: (This is the guid of the subject to get feeds about. Eg. sitepage_page_58)</label><br />
          <input type="text" value="" name="subject" /><br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- postFeed -->
      <?php $url = $this->url(array('action' => 'post-feed'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_postFeed" action="<?php echo $url ?>" method="post" enctype="multipart/form-data">
        <fieldset>
          <legend>End-point: postFeed</legend>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
        </fieldset>
        
        <fieldset>
          <legend>Common fields: </legend>
          <label>Attachment type:</label><br />
          <select name="attachmentType">
            <option value="">no attachment</option>
            <option value="video">video</option>
            <option value="photo">photo</option>
            <option value="link">link</option>
          </select>
          <br />
          <label>Status:</label><br />
          <input name="body" type="text" style="display: block;" />
          <label>Subject. The wall owner's guid. format: user_{user_id}</label><br />
          <input name="subject" type="text" style="display: block;" />
          <br />
        </fieldset>

        <fieldset>
          <legend>Attach video:</legend>
          <label>Video Type:</label><br />
          <select name="type" id="compose-video-form-type" class="compose-form-input" option="test">
            <option value="0">Choose Source</option>
            <option value="1">YouTube Video</option>
            <option value="2">Vimeo Video</option>
            <option value="4">Dailymotion Video</option>
            <option value="5">URL Video</option>
          </select>
          <br />
          <label>Video URL:</label><br />
          <input name="uri" id="compose-video-form-input" class="compose-form-input" type="text" style="display: block;" />
          <br />
          <label>Video file:</label><br />
          <input name="Filedata" type="file" style="display: block;" />
          <br />
        </fieldset>

        <fieldset>
          <legend>Attach photo:</legend>
          <label>Photo file:</label><br />
          <input name="Filedata" type="file" style="display: block;" />
          <br />
        </fieldset>

        <fieldset>
          <legend>Attach link:</legend>
          <label>Link:</label><br />
          <input name="uri" type="text" style="display: block;" />
          <br />
        </fieldset>

        <fieldset>
          <legend>Tag & Checkin:</legend>
          <label>
            Checkin string:
            (ex: resource_guid=0&type=place&label=Đồng Đen, Ho Chi Minh City, Vietnam&latitude=10.7897825&longitude=106.64309730000002)
          </label><br />
          <input name="composer[checkin]" type="text" style="display: block;" />
          <label>
            Tag friends: (input friend's id)
          </label><br />
          <input name="toValues" type="text" style="display: block;" />
        </fieldset>

        <fieldset>
          <input type="submit" value="Post" />
        </fieldset>
      </form>

      <!-- removeFeed -->
      <?php $url = $this->url(array('action' => 'remove-feed'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_removeFeed" action="<?php echo $url ?>" method="post">
        <div id="form_box">                            
          <h2>End-point: removeFeed</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>                

          <label>Feed id:</label><br />
          <input type="text" value="" name="feed_id" /><br />

          <label>Comment id:</label><br />
          <input type="text" value="" name="comment_id" /><br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- likeFeed -->
      <?php $url = $this->url(array('action' => 'like-feed'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_likeFeed" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: likeFeed</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Feed id:</label><br />
          <input type="text" value="" name="feed_id" /><br />

          <label>Comment id:</label><br />
          <input type="text" value="" name="comment_id" /><br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- unlikeFeed -->
      <?php $url = $this->url(array('action' => 'unlike-feed'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_unlikeFeed" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: unlikeFeed</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Feed id:</label><br />
          <input type="text" value="" name="feed_id" /><br />

          <label>Comment id:</label><br />
          <input type="text" value="" name="comment_id" /><br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- shareFeed -->
      <?php $url = $this->url(array('action' => 'share-feed'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_shareFeed" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: shareFeed</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Attachment object type:</label><br />
          <input type="text" value="" name="type" /><br />

          <label>Attachment object id:</label><br />
          <input type="text" value="" name="id" /><br />

          <label>Feed id:</label><br />
          <input type="text" value="" name="feed_id" /><br />

          <label>Reposted feed's body:</label><br />
          <input type="text" value="" name="body" /><br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- logout -->
      <?php $url = $this->url(array('action' => 'logout'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_logout" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: logout</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- fetchFeedInfo -->
      <?php $url = $this->url(array('action' => 'fetch-feed-info'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchFeed" action="<?php echo $url ?>" method="post">
        <div id="form_box">                            
          <h2>End-point: fetchFeedInfo</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>                

          <label>Feed id:</label><br />
          <input type="text" value="" name="feed_id" /><br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- comment -->
      <?php $url = $this->url(array('action' => 'comment'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_comment" action="<?php echo $url ?>" method="post">
        <div id="form_box">                            
          <h2>End-point: comment</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>                

          <label>Feed id:</label><br />
          <input type="text" value="" name="feed_id" /><br />

          <label>Comment body:</label><br />
          <input type="text" value="" name="body" /><br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- fetchAboutInfo -->
      <?php $url = $this->url(array('action' => 'fetch-about-info'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchAboutInfo" action="<?php echo $url ?>" method="post">
        <div id="form_box">                            
          <h2>End-point: fetchAboutInfo</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>                

          <label>User id:</label><br />
          <input type="text" value="" name="user_id" /><br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- fetchMedicalRecord -->
      <?php $url = $this->url(array('action' => 'fetch-medical-record'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchMedicalRecord" action="<?php echo $url ?>" method="post">
        <div id="form_box">                            
          <h2>End-point: fetchMedicalRecord</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>                

          <label>User id:</label><br />
          <input type="text" value="" name="user_id" /><br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- fetchMemberList -->
      <?php $url = $this->url(array('action' => 'fetch-member-list'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchMemberList" action="<?php echo $url ?>" method="post">
        <div id="form_box">                            
          <h2>End-point: fetchMemberList</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>                

          <label>User type:</label><br />
          <select name="user_type">
            <option value="">Everyone</option>
            <option value="friends">Friends</option>
          </select>
          <br />

          <label>Order by:</label><br />
          <select name="order">
            <option value="recent">Recent sign up</option>
            <option value="alphabet">Alphabet</option>
          </select>
          <br />

          <label>User name:</label><br />
          <input type="text" value="" name="displayname" />
          <br />
          
          <label>Show users who share their medical record with me:</label><br />
          <select name="medical_record_shared">
            <option value="0">0: No</option>
            <option value="1">1: Yes</option>
          </select>
          <br />
          
          <label>Pariticipation level:</label><br />
          <input type="text" value="" name="participation_level" />
          <br />
          
          <label>Primary sport:</label><br />
          <input type="text" value="" name="primary_sport" />
          <br />
          
          <label>Country of residence:</label><br />
          <input type="text" value="" name="country" />
          <br />

          <label>Page:</label><br />
          <input type="text" value="" name="page" />
          <br />

          <label>Items per page:</label><br />
          <input type="text" value="" name="items_per_page" />
          <br />

          <label>Friend list owner id:</label><br />
          <input type="text" value="" name="user_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- fetchGeneralInfo -->
      <?php $url = $this->url(array('action' => 'fetch-general-info'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchGeneralInfo" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchGeneralInfo</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>User id:</label><br />
          <input type="text" name="user_id" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- fetchPhotoLibrary -->
      <?php $url = $this->url(array('action' => 'fetch-photo-library'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchPhotoLibrary" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchPhotoLibrary</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>User id:</label><br />
          <input type="text" name="user_id" />
          <br />

          <label>Item count per page:</label><br />
          <input type="text" name="itemCountPerPage" />
          <br />

          <label>Page:</label><br />
          <input type="text" name="page" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- fetchPhotoAlbumDetails -->
      <?php $url = $this->url(array('action' => 'fetch-photo-album-details'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchPhotoAlbumDetails" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchPhotoAlbumDetails</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Album id:</label><br />
          <input type="text" name="album_id" />
          <br />

          <label>Item count per page:</label><br />
          <input type="text" name="itemCountPerPage" />
          <br />

          <label>Page:</label><br />
          <input type="text" name="page" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- fetchPhotoDetails -->
      <?php $url = $this->url(array('action' => 'fetch-photo-details'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchPhotoDetails" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchPhotoDetails</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Photo id:</label><br />
          <input type="text" name="photo_id" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- fetchVideoLibrary -->
      <?php $url = $this->url(array('action' => 'fetch-video-library'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchVideoLibrary" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchVideoLibrary</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>User id:</label><br />
          <input type="text" name="user_id" />
          <br />

          <label>Item count per page:</label><br />
          <input type="text" name="itemCountPerPage" />
          <br />

          <label>Page:</label><br />
          <input type="text" name="page" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- fetchVideoDetails -->
      <?php $url = $this->url(array('action' => 'fetch-video-details'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchVideoDetails" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchVideoDetails</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Video id:</label><br />
          <input type="text" name="video_id" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- postItemComment -->
      <?php $url = $this->url(array('action' => 'post-item-comment'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_postItemComment" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: postItemComment</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Item id:</label><br />
          <input type="text" name="id" />
          <br />

          <label>Item type: (album | video | album_photo)</label><br />
          <input type="text" name="type" />
          <br />

          <label>Comment body:</label><br />
          <input type="text" name="body" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- likeItem -->
      <?php $url = $this->url(array('action' => 'like-item'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_likeItem" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: likeItem</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Item id:</label><br />
          <input type="text" name="id" />
          <br />

          <label>Item type: (album | video | album_photo)</label><br />
          <input type="text" name="type" />
          <br />

          <label>Comment id:</label><br />
          <input type="text" name="comment_id" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- unlikeItem -->
      <?php $url = $this->url(array('action' => 'unlike-item'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_unlikeItem" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: unlikeItem</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Item id:</label><br />
          <input type="text" name="id" />
          <br />

          <label>Item type: (album | video | album_photo)</label><br />
          <input type="text" name="type" />
          <br />
          
          <label>Comment id:</label><br />
          <input type="text" name="comment_id" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- deleteAlbum -->
      <?php $url = $this->url(array('action' => 'delete-album'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_deleteAlbum" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: deleteAlbum</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Parent:</label><br />
          <input type="text" name="parent_guid" />
          <br />

          <label>Album id:</label><br />
          <input type="text" name="album_id" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- deletePhoto -->
      <?php $url = $this->url(array('action' => 'delete-photo'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_deletePhoto" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: deletePhoto</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Parent:</label><br />
          <input type="text" name="parent_guid" />
          <br />
          
          <label>Photo id:</label><br />
          <input type="text" name="photo_id" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- shareItem -->
      <?php $url = $this->url(array('action' => 'share-item'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_shareItem" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: shareItem</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Item type:</label><br />
          <input type="text" name="type" />
          <br />

          <label>Item id:</label><br />
          <input type="text" name="id" />
          <br />

          <label>Shared body:</label><br />
          <input type="text" name="body" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- deleteVideo -->
      <?php $url = $this->url(array('action' => 'delete-video'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_deleteVideo" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: deleteVideo</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Parent:</label><br />
          <input type="text" name="parent_guid" />
          <br />
          
          <label>Video id:</label><br />
          <input type="text" name="video_id" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- fetchInboxConversations -->
      <?php $url = $this->url(array('action' => 'fetch-inbox-conversations'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchInboxConversations" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchInboxConversations</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Page:</label><br />
          <input type="text" name="page" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- fetchOutboxConversations -->
      <?php $url = $this->url(array('action' => 'fetch-outbox-conversations'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchOutboxConversations" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchOutboxConversations</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Page:</label><br />
          <input type="text" name="page" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- composeMessage -->
      <?php $url = $this->url(array('action' => 'compose-message'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_composeMessage" action="<?php echo $url ?>" method="post" enctype="multipart/form-data">
        <fieldset>
          <legend>End-point: composeMessage</legend>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
        </fieldset>

        <fieldset>
          <legend>Common fields: </legend>
          <label>Attachment type:</label><br />
          <select name="attachmentType">
            <option value="">no attachment</option>
            <option value="video">video</option>
            <option value="photo">photo</option>
            <option value="link">link</option>
          </select>
          <br />
          <label>Subject</label><br />
          <input name="title" type="text" style="display: block;" />
          <label>Message:</label><br />
          <input name="body" type="text" style="display: block;" />
          <label>Receiver Ids: (separated by commas)</label><br />
          <input name="toValues" type="text" style="display: block;" />
          <label>Video/Photo file:</label><br />
          <input name="Filedata" type="file" style="display: block;" />
          <br />
        </fieldset>

        <fieldset>
          <legend>Attach video:</legend>
          <label>Video Type:</label><br />
          <select name="type" id="compose-video-form-type" class="compose-form-input" option="test">
            <option value="0">Choose Source</option>
            <option value="1">YouTube Video</option>
            <option value="2">Vimeo Video</option>
            <option value="4">Dailymotion Video</option>
            <option value="5">URL Video</option>
          </select>
          <br />
          <label>Video URL:</label><br />
          <input name="uri" id="compose-video-form-input" class="compose-form-input" type="text" style="display: block;" />
          <br />
        </fieldset>

        <fieldset>
          <legend>Attach link:</legend>
          <label>Link:</label><br />
          <input name="uri" type="text" style="display: block;" />
          <br />
        </fieldset>

        <fieldset>
          <input type="submit" value="Post" />
        </fieldset>
      </form>

      <!-- fetchMessagesOfConversation -->
      <?php $url = $this->url(array('action' => 'fetch-messages-of-conversation'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchMessagesOfConversation" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchMessagesOfConversation</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Conversation id:</label><br />
          <input type="text" name="id" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- sendReply -->
      <?php $url = $this->url(array('action' => 'send-reply'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_sendReply" action="<?php echo $url ?>" method="post" enctype="multipart/form-data">
        <fieldset>
          <legend>End-point: sendReply</legend>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
        </fieldset>

        <fieldset>
          <legend>Common fields: </legend>
          <label>Attachment type:</label><br />
          <select name="attachmentType">
            <option value="">no attachment</option>
            <option value="video">video</option>
            <option value="photo">photo</option>
            <option value="link">link</option>
          </select>
          <br />
          <label>Conversation id:</label><br />
          <input name="id" type="text" style="display: block;" />
          <label>Message:</label><br />
          <input name="body" type="text" style="display: block;" />
          <label>Video/Photo file:</label><br />
          <input name="Filedata" type="file" style="display: block;" />
          <br />
        </fieldset>

        <fieldset>
          <legend>Attach video:</legend>
          <label>Video Type:</label><br />
          <select name="type" id="compose-video-form-type" class="compose-form-input" option="test">
            <option value="0">Choose Source</option>
            <option value="1">YouTube Video</option>
            <option value="2">Vimeo Video</option>
            <option value="4">Dailymotion Video</option>
            <option value="5">URL Video</option>
          </select>
          <br />
          <label>Video URL:</label><br />
          <input name="uri" id="compose-video-form-input" class="compose-form-input" type="text" style="display: block;" />
          <br />
        </fieldset>

        <fieldset>
          <legend>Attach link:</legend>
          <label>Link:</label><br />
          <input name="uri" type="text" style="display: block;" />
          <br />
        </fieldset>

        <fieldset>
          <input type="submit" value="Post" />
        </fieldset>
      </form>

      <!-- fetchNotifications -->
      <?php $url = $this->url(array('action' => 'fetch-notifications'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchNotifications" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchNotifications</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Page:</label><br />
          <input name="page" type="text" style="display: block;" />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- updateNotificationReadStatus -->
      <?php $url = $this->url(array('action' => 'update-notification-read-status'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_updateNotificationReadStatus" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: updateNotificationReadStatus</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Notification id:</label><br />
          <input type="text" name="notification_id" />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- fetchFriendRequests -->
      <?php $url = $this->url(array('action' => 'fetch-friend-requests'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchFriendRequests" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchFriendRequests</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- acceptFriendRequest -->
      <?php $url = $this->url(array('action' => 'accept-friend-request'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_acceptFriendRequest" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: acceptFriendRequest</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Sender id:</label><br />
          <input type="text" name="user_id" />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- rejectFriendRequest -->
      <?php $url = $this->url(array('action' => 'reject-friend-request'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_rejectFriendRequest" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: rejectFriendRequest</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Sender id:</label><br />
          <input type="text" name="user_id" />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- addFriend -->
      <?php $url = $this->url(array('action' => 'add-friend'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_addFriend" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: addFriend</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>User id:</label><br />
          <input type="text" name="user_id" />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- removeFriend -->
      <?php $url = $this->url(array('action' => 'remove-friend'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_removeFriend" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: removeFriend</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>User id:</label><br />
          <input type="text" name="user_id" />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- fetchProfileEvents -->
      <?php $url = $this->url(array('action' => 'fetch-profile-events'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchProfileEvents" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchProfileEvents</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>User id:</label><br />
          <input type="text" name="user_id" /><br />

          <label>Item count per page:</label><br />
          <input type="text" name="itemCountPerPage" /><br />

          <label>Page number:</label><br />
          <input type="text" name="page" /><br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!-- deleteEvent -->
      <?php $url = $this->url(array('action' => 'delete-event'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_deleteEvent" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: deleteEvent</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Event id:</label><br />
          <input type="text" name="event_id" /><br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <?php if(!Engine_Api::_()->user()->getViewer()->getIdentity()) goto End; ?>
      <!-- editAlbum -->
      <?php $form = new Album_Form_Album_Edit(); ?>
      <?php $url = $this->url(array('action' => 'edit-album'), 'mgslapi_ios', true) ?>
      <?php $form->setAction($url)->setAttrib('target', '_blank'); ?>
      <?php $form->addElement('Text', 'fetch_mode', array('placeholder' => 'form_data', 'order' => -2, 'label' => 'Fetch mode'))
      ->addElement('Text', 'album_id', array('order' => -1, 'label' => 'Album id')); ?>
      <div target="_blank" id="api_editAlbum" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: editAlbum</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <?php echo $form->render($this); ?>
        </div>
      </div>

      <!-- editPhoto -->
      <?php $form = new Album_Form_Album_EditPhoto(); ?>
      <?php $url = $this->url(array('action' => 'edit-photo'), 'mgslapi_ios', true) ?>
      <?php $form->setAction($url)->setAttrib('target', '_blank'); ?>
      <?php $form->addElement('Text', 'fetch_mode', array('placeholder' => 'form_data', 'order' => -2, 'label' => 'Fetch mode'))
      ->addElement('Text', 'photo_id', array('order' => -1, 'label' => 'Photo id')); ?>
      <?php
      $form->addElement('Checkbox', 'cover', array(
      'label' => "Album Cover",
      ));
      // Get albums
      $albumTable = Engine_Api::_()->getItemTable('album');
      $myAlbums = $albumTable->select()
      ->from($albumTable, array('album_id', 'title'))
      ->where('owner_type = ?', 'user')
      ->where('owner_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity())
      ->query()
      ->fetchAll();

      $albumOptions = array('' => '');
      foreach ($myAlbums as $myAlbum) {
      $albumOptions[$myAlbum['album_id']] = $myAlbum['title'];
      }
      if (count($albumOptions) == 1) {
      $albumOptions = array();
      }
      if (empty($albumOptions)) {
      $form->removeElement('move');
      } else {
      $form->move->setMultiOptions($albumOptions);
      }
      // Submit or succumb!
      $form->addElement('Button', 'submit', array(
      'label' => 'Save Photo',
      'type' => 'submit',
      'decorators' => array(
      'ViewHelper'
      )
      ))->removeElement('delete');
      ?>
      <form target="_blank" id="api_editPhoto" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: editPhoto</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <?php echo $form->render($this); ?>
        </div>
      </form>

      <!--deleteComment-->
      <?php $url = $this->url(array('action' => 'delete-comment'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_deleteComment" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: deleteComment</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Item id:</label><br />
          <input type="text" name="id" />
          <br />

          <label>Item type: (album | video | album_photo)</label><br />
          <input type="text" name="type" />
          <br />

          <label>Comment id:</label><br />
          <input type="text" name="comment_id" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!--editMedicalRecordSharing-->
      <?php $url = $this->url(array('action' => 'edit-medical-record-sharing'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_editMedicalRecordSharing" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: editMedicalRecordSharing</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Full Access user list: (User ids, separated by commas)</label><br />
          <input type="text" name="full" />
          <br />

          <label>Read Only Access user list: (User ids, separated by commas)</label><br />
          <input type="text" name="read_only" />
          <br />

          <label>Emergency Summary user list: (User ids, separated by commas)</label><br />
          <input type="text" name="limited" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!--fetchMedicalSharingAccessLists-->
      <?php $url = $this->url(array('action' => 'fetch-medical-sharing-access-lists'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchMedicalSharingAccessLists" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchMedicalSharingAccessLists</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <input type="submit" value="Post" />
        </div>
      </form>

      <!--fetchCircleList-->
      <?php $url = $this->url(array('action' => 'fetch-circle-list'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchCircleList" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchCircleList</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Page number</label><br />
          <input type="text" name="page" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!--fetchJoinedCircles-->
      <?php $url = $this->url(array('action' => 'fetch-joined-circles'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchJoinedCircles" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchJoinedCircles</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>User id</label><br />
          <input type="text" name="user_id" />
          <br />

          <label>Page number</label><br />
          <input type="text" name="page" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!--fetchMyCircles-->
      <?php $url = $this->url(array('action' => 'fetch-my-circles'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchMyCircles" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchMyCircles</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Page number</label><br />
          <input type="text" name="page" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!--fetchCirclesByLocation-->
      <?php $url = $this->url(array('action' => 'fetch-circles-by-location'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchCirclesByLocation" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchCirclesByLocation</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Page number</label><br />
          <input type="text" name="page" />
          <br />

          <label>Location</label><br />
          <input type="text" name="search" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!--fetchCircleInfo-->
      <?php $url = $this->url(array('action' => 'fetch-circle-info'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchCircleInfo" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchCircleInfo</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Circle id</label><br />
          <input type="text" name="page_id" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchCircleGeneralInfo-->
      <?php $url = $this->url(array('action' => 'fetch-circle-general-info'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchCircleGeneralInfo" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchCircleGeneralInfo</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Circle id</label><br />
          <input type="text" name="page_id" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchCircleMemberList-->
      <?php $url = $this->url(array('action' => 'fetch-circle-member-list'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchCircleMemberList" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchCircleMemberList</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Circle id</label><br />
          <input type="text" name="page_id" />
          <br />
          
          <label>Page number</label><br />
          <input type="text" name="page" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchCirclePhotoLibrary-->
      <?php $url = $this->url(array('action' => 'fetch-circle-photo-library'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchCirclePhotoLibrary" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchCirclePhotoLibrary</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Circle id</label><br />
          <input type="text" name="page_id" />
          <br />

          <label>Page number</label><br />
          <input type="text" name="page" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchCirclePhotoAlbumDetails-->
      <?php $url = $this->url(array('action' => 'fetch-circle-photo-album-details'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchCirclePhotoAlbumDetails" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchCirclePhotoAlbumDetails</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Circle id</label><br />
          <input type="text" name="page_id" />
          <br />
          
          <label>Album id</label><br />
          <input type="text" name="album_id" />
          <br />

          <label>Photos page number</label><br />
          <input type="text" name="page" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchCircleVideoLibrary-->
      <?php $url = $this->url(array('action' => 'fetch-circle-video-library'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchCircleVideoLibrary" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchCircleVideoLibrary</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Circle id</label><br />
          <input type="text" name="page_id" />
          <br />

          <label>Page number</label><br />
          <input type="text" name="page" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!--likeCircle-->
      <?php $url = $this->url(array('action' => 'like-circle'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_likeCircle" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: likeCircle</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Resource id (e.g 54)</label><br />
          <input type="text" name="resource_id" />
          <br />
          
          <label>Resource type (e.g sitepage_page)</label><br />
          <input type="text" name="resource_type" />
          <br />

          <label>Like id (Get from 'fetch general info')</label><br />
          <input type="text" name="like_id" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--followCircle-->
      <?php $url = $this->url(array('action' => 'follow-circle'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_followCircle" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: followCircle</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Resource id (e.g 54)</label><br />
          <input type="text" name="resource_id" />
          <br />
          
          <label>Resource type (e.g sitepage_page)</label><br />
          <input type="text" name="resource_type" />
          <br />

          <label>Follow id (Get from 'fetch general info')</label><br />
          <input type="text" name="follow_id" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--leaveCircle-->
      <?php $url = $this->url(array('action' => 'leave-circle'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_leaveCircle" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: leaveCircle</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Circle id (e.g 54)</label><br />
          <input type="text" name="page_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--joinCircle-->
      <?php $url = $this->url(array('action' => 'join-circle'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_joinCircle" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: joinCircle</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Circle id (e.g 54)</label><br />
          <input type="text" name="page_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--cancelCircleMembershipRequest-->
      <?php $url = $this->url(array('action' => 'cancel-circle-membership-request'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_cancelCircleMembershipRequest" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: cancelCircleMembershipRequest</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Circle id (e.g 54)</label><br />
          <input type="text" name="page_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--deleteCircle-->
      <?php $url = $this->url(array('action' => 'delete-circle'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_deleteCircle" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: deleteCircle</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Circle id (e.g 54)</label><br />
          <input type="text" name="page_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--addMembersToCircle-->
      <?php $url = $this->url(array('action' => 'add-members-to-circle'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_addMembersToCircle" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: addMembersToCircle</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Circle id (e.g 54)</label><br />
          <input type="text" name="page_id" />
          <br />
          
          <label>User ids separated by commas (e.g `308,309,222`)</label><br />
          <input type="text" name="toValues" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchCircleSuggestedPeopleToInvite-->
      <?php $url = $this->url(array('action' => 'fetch-circle-suggested-people-to-invite'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchCircleSuggestedPeopleToInvite" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchCircleSuggestedPeopleToInvite</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Circle id (e.g 54)</label><br />
          <input type="text" name="page_id" />
          <br />
          
          <label>User name</label><br />
          <input type="text" name="user_name" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--removeMemberFromCircle-->
      <?php $url = $this->url(array('action' => 'remove-member-from-circle'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_removeMemberFromCircle" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: removeMemberFromCircle</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Circle id (e.g 54)</label><br />
          <input type="text" name="page_id" />
          <br />
          
          <label>Member id (Not user id)</label><br />
          <input type="text" name="member_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--messageCircleOwner-->
      <?php $url = $this->url(array('action' => 'message-circle-owner'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_messageCircleOwner" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: messageCircleOwner</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Circle id (e.g 54)</label><br />
          <input type="text" name="page_id" />
          <br />
          
          <label>Subject</label><br />
          <input type="text" name="title" />
          <br />
          
          <label>Message</label><br />
          <input type="text" name="body" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchCircleOverview-->
      <?php $url = $this->url(array('action' => 'fetch-circle-overview'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchCircleOverview" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchCircleOverview</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Circle id (e.g 54)</label><br />
          <input type="text" name="page_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchCircleEvents-->
      <?php $url = $this->url(array('action' => 'fetch-circle-events'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchCircleEvents" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchCircleEvents</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Circle id (e.g 54)</label><br />
          <input type="text" name="page_id" />
          <br />
          
          <label>Page number</label><br />
          <input type="text" name="page" />
          <br />
          
          <label>Event type</label><br />
          <select name="clicked_event">
            <option value="upcomingevent">Upcoming Events</option>
            <option value="pastevent">Past Events</option>
            <option value="myevent">My Events</option>
          </select>
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchCircleNotes-->
      <?php $url = $this->url(array('action' => 'fetch-circle-notes'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchCircleNotes" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchCircleNotes</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Circle id (e.g 54)</label><br />
          <input type="text" name="page_id" />
          <br />
          
          <label>Page number</label><br />
          <input type="text" name="page" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchCircleDocuments-->
      <?php $url = $this->url(array('action' => 'fetch-circle-documents'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchCircleDocuments" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchCircleDocuments</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Circle id (e.g 54)</label><br />
          <input type="text" name="page_id" />
          <br />
          
          <label>Page number</label><br />
          <input type="text" name="page" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchCircleVideoDetails-->
      <?php $url = $this->url(array('action' => 'fetch-circle-video-details'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchCircleVideoDetails" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchCircleVideoDetails</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Video id</label><br />
          <input type="text" name="video_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchCircleNoteDetails-->
      <?php $url = $this->url(array('action' => 'fetch-circle-note-details'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchCircleNoteDetails" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchCircleNoteDetails</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Note id</label><br />
          <input type="text" name="note_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchCircleDocumentDetails-->
      <?php $url = $this->url(array('action' => 'fetch-circle-document-details'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchCircleDocumentDetails" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchCircleDocumentDetails</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Document id</label><br />
          <input type="text" name="document_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--deleteCircleNote-->
      <?php $url = $this->url(array('action' => 'delete-circle-note'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_deleteCircleNote" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: deleteCircleNote</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Note id</label><br />
          <input type="text" name="note_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--uploadCircleNotePhoto-->
      <?php $url = $this->url(array('action' => 'upload-circle-note-photo'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_uploadCircleNotePhoto" action="<?php echo $url ?>" method="post" enctype="multipart/form-data" >
        <div id="form_box">
          <h2>End-point: uploadCircleNotePhoto</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Note id</label><br />
          <input type="text" name="note_id" />
          <br />
          
          <label>File</label><br />
          <input type="file" name="Filedata" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--deleteCircleDocument-->
      <?php $url = $this->url(array('action' => 'delete-circle-document'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_deleteCircleDocument" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: deleteCircleDocument</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Document id</label><br />
          <input type="text" name="document_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchCircleMembershipRequests-->
      <?php $url = $this->url(array('action' => 'fetch-circle-membership-requests'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchCircleMembershipRequests" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchCircleMembershipRequests</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Circle id</label><br />
          <input type="text" name="page_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--rejectCircleMembershipRequest-->
      <?php $url = $this->url(array('action' => 'reject-circle-membership-request'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_rejectCircleMembershipRequest" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: rejectCircleMembershipRequest</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Circle id</label><br />
          <input type="text" name="page_id" />
          <br />
          
          <label>Membership id</label><br />
          <input type="text" name="member_id" />
          <br />
          
          <label>User id</label><br />
          <input type="text" name="user_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--acceptCircleMembershipRequest-->
      <?php $url = $this->url(array('action' => 'accept-circle-membership-request'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_acceptCircleMembershipRequest" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: acceptCircleMembershipRequest</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Circle id</label><br />
          <input type="text" name="page_id" />
          <br />
          
          <label>Membership id</label><br />
          <input type="text" name="member_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchCirclePhotoDetails-->
      <?php $url = $this->url(array('action' => 'fetch-circle-photo-details'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchCirclePhotoDetails" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchCirclePhotoDetails</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Photo id</label><br />
          <input type="text" name="photo_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--testPushNotification-->
      <?php $url = $this->url(array('action' => 'test-push-notification'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_testPushNotification" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: testPushNotification</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Device type</label><br />
          <select name="device_type">
            <option value="1">iOS</option>
            <option value="2">Android</option>
          </select>
          <br />
          
          <label>Device token</label><br />
          <input type="text" name="device_token" />
          <br />
          
          <label>Pushed message</label><br />
          <input type="text" name="message" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchChatHistoryOfUser-->
      <?php $url = $this->url(array('action' => 'fetch-chat-history-of-user'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchChatHistoryOfUser" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchChatHistoryOfUser</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>User id</label><br />
          <input type="text" name="user_id" />
          <br />
          
          <label>Page</label><br />
          <input type="text" name="page" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchResumeList-->
      <?php $url = $this->url(array('action' => 'fetch-resume-list'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchResumeList" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchResumeList</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Page</label><br />
          <input type="text" name="page" />
          <br />
          
          <label>Name</label><br />
          <input type="text" name="user_name" />
          <br />
          
          <label>Gender</label><br />
          <input type="text" name="gender" />
          <br />
          
          <label>Sport</label><br />
          <input type="text" name="sport" />
          <br />
          
          <label>Participation level</label><br />
          <input type="text" name="category" />
          <br />
          
          <label>Position played</label><br />
          <input type="text" name="position_played" />
          <br />
          
          <label>Country</label><br />
          <input type="text" name="country" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <?php $url = $this->url(array('action' => 'fetch-subfield-options'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchSubfieldOptions" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchSubfieldOptions</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Parent field value</label><br />
          <input type="text" name="parent_field_value" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchMyResumes-->
      <?php $url = $this->url(array('action' => 'fetch-my-resumes'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchMyResumes" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchMyResumes</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Page</label><br />
          <input type="text" name="page" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchResumePackages-->
      <?php $url = $this->url(array('action' => 'fetch-resume-packages'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchResumePackages" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchResumePackages</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchResumeSummary-->
      <?php $url = $this->url(array('action' => 'fetch-resume-summary'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchResumeSummary" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchResumeSummary</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Resume id</label><br />
          <input type="text" name="resume_id" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchResumeGeneralInfo-->
      <?php $url = $this->url(array('action' => 'fetch-resume-general-info'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchResumeGeneralInfo" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchResumeGeneralInfo</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Resume id</label><br />
          <input type="text" name="resume_id" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchResumeMap-->
      <?php $url = $this->url(array('action' => 'fetch-resume-map'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchResumeMap" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchResumeMap</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Resume id</label><br />
          <input type="text" name="resume_id" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchResumePhotos-->
      <?php $url = $this->url(array('action' => 'fetch-resume-photos'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchResumePhotos" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchResumePhotos</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Resume id</label><br />
          <input type="text" name="resume_id" />
          <br />
          
          <label>Page</label><br />
          <input type="text" name="page" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--deleteResume-->
      <?php $url = $this->url(array('action' => 'delete-resume'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_deleteResume" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: deleteResume</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Resume id</label><br />
          <input type="text" name="resume_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--uploadResumePhoto-->
      <?php $url = $this->url(array('action' => 'upload-resume-photo'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_uploadResumePhoto" action="<?php echo $url ?>" method="post" enctype="multipart/form-data">
        <div id="form_box">
          <h2>End-point: uploadResumePhoto</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Resume id</label><br />
          <input type="text" name="resume_id" />
          <br />
          
          <label>Photo file</label><br />
          <input type="file" name="Filedata" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchPersonalEvents-->
      <?php $url = $this->url(array('action' => 'fetch-personal-events'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchPersonalEvents" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchPersonalEvents</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Event type</label><br />
          <select name="filter">
            <option value="future">Upcoming Events</option>
            <option value="past">Past Events</option>
            <option value="myevent">My Events</option>
          </select>
          <br />
          
          <label>Search text</label><br />
          <input type="text" name="search_text" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchEventGeneralInfo-->
      <?php $url = $this->url(array('action' => 'fetch-event-general-info'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchEventGeneralInfo" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchEventGeneralInfo</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Event id</label><br />
          <input type="text" name="event_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--updateEventRsvp-->
      <?php $url = $this->url(array('action' => 'update-event-rsvp'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_updateEventRsvp" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: updateEventRsvp</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Event id</label><br />
          <input type="text" name="event_id" />
          <br />
          
          <label>Option id</label><br />
          <select name="option_id">
            <option value="0">Not Attending</option>
            <option value="1">Maybe Attending</option>
            <option value="2">Attending</option>
          </select>
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchEventGuests-->
      <?php $url = $this->url(array('action' => 'fetch-event-guests'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchEventGuests" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchEventGuests</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Waiting list?</label><br />
          <select name="waiting">
            <option value=0>No</option>
            <option value=1>Yes</option>
          </select>
          <br />
          
          <label>Search text</label><br />
          <input type="text" name="search_text" />
          <br />
          
          <label>Page</label><br />
          <input type="text" name="page" />
          <br />
          
          <label>Event id</label><br />
          <input type="text" name="event_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--acceptEventRequest-->
      <?php $url = $this->url(array('action' => 'accept-event-request'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_acceptEventRequest" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: acceptEventRequest</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Event id</label><br />
          <input type="text" name="event_id" />
          <br />
          
          <label>User id</label><br />
          <input type="text" name="user_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--rejectEventRequest-->
      <?php $url = $this->url(array('action' => 'reject-event-request'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_rejectEventRequest" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: rejectEventRequest</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Event id</label><br />
          <input type="text" name="event_id" />
          <br />
          
          <label>User id</label><br />
          <input type="text" name="user_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--cancelEventInvite-->
      <?php $url = $this->url(array('action' => 'cancel-event-invite'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_cancelEventInvite" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: cancelEventInvite</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Event id</label><br />
          <input type="text" name="event_id" />
          <br />
          
          <label>User id</label><br />
          <input type="text" name="user_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--cancelEventRequest-->
      <?php $url = $this->url(array('action' => 'cancel-event-request'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_cancelEventRequest" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: cancelEventRequest</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Event id</label><br />
          <input type="text" name="event_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--inviteGuestsToEvent-->
      <?php $url = $this->url(array('action' => 'invite-guests-to-event'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_inviteGuestsToEvent" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: inviteGuestsToEvent</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Event id</label><br />
          <input type="text" name="event_id" />
          <br />
          
          <label>User ids (separated by commas)</label><br />
          <input type="text" name="users" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--joinEvent-->
      <?php $url = $this->url(array('action' => 'join-event'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_joinEvent" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: joinEvent</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Event id</label><br />
          <input type="text" name="event_id" />
          <br />
          
          <!--<label>Rsvp</label><br />
          <select name="rsvp">
            <option value="0">Not Attending</option>
            <option value="1">Maybe Attending</option>
            <option value="2">Attending</option>
          </select>
          <br />-->
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--leaveEvent-->
      <?php $url = $this->url(array('action' => 'leave-event'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_leaveEvent" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: leaveEvent</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Event id</label><br />
          <input type="text" name="event_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--acceptEventInvite-->
      <?php $url = $this->url(array('action' => 'accept-event-invite'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_acceptEventInvite" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: acceptEventInvite</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <!--<label>Rsvp id</label><br />
          <select name="rsvp">
            <option value="0">Not Attending</option>
            <option value="1">Maybe Attending</option>
            <option value="2">Attending</option>
          </select>
          <br />-->
          
          <label>Event id</label><br />
          <input type="text" name="event_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--rejectEventInvite-->
      <?php $url = $this->url(array('action' => 'reject-event-invite'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_rejectEventInvite" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: rejectEventInvite</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Event id</label><br />
          <input type="text" name="event_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchEventPhotos-->
      <?php $url = $this->url(array('action' => 'fetch-event-photos'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchEventPhotos" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchEventPhotos</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Event id</label><br />
          <input type="text" name="event_id" />
          <br />
          
          <label>Page</label><br />
          <input type="text" name="page" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--deleteEventPhoto-->
      <?php $url = $this->url(array('action' => 'delete-event-photo'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_deleteEventPhoto" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: deleteEventPhoto</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Photo id</label><br />
          <input type="text" name="photo_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--uploadEventPhoto-->
      <?php $url = $this->url(array('action' => 'upload-event-photo'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_uploadEventPhoto" action="<?php echo $url ?>" method="post" enctype="multipart/form-data">
        <div id="form_box">
          <h2>End-point: uploadEventPhoto</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Event id</label><br />
          <input type="text" name="event_id" />
          <br />
          
          <label>Photo file</label><br />
          <input type="file" name="Filedata" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchEventDiscussions-->
      <?php $url = $this->url(array('action' => 'fetch-event-discussions'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchEventDiscussions" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchEventDiscussions</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Event id</label><br />
          <input type="text" name="event_id" />
          <br />
          
          <label>Page</label><br />
          <input type="text" name="page" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchEventDiscussionDetails-->
      <?php $url = $this->url(array('action' => 'fetch-event-discussion-details'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchEventDiscussionDetails" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchEventDiscussionDetails</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Topic id</label><br />
          <input type="text" name="topic_id" />
          <br />
          
          <label>Page</label><br />
          <input type="text" name="page" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--postEventDiscussionTopic-->
      <?php $url = $this->url(array('action' => 'post-event-discussion-topic'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_postEventDiscussionTopic" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: postEventDiscussionTopic</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Event id</label><br />
          <input type="text" name="event_id" />
          <br />
          
          <label>Title</label><br />
          <input type="text" name="title" />
          <br />
          
          <label>Body</label><br />
          <input type="text" name="body" />
          <br />
          
          <label>Send me notifications when other members reply to this topic</label><br />
          <select name="watch">
            <option value="1">Yes</option>
            <option value="0">No</option>
          </select>
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--postEventDiscussionReply-->
      <?php $url = $this->url(array('action' => 'post-event-discussion-reply'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_postEventDiscussionReply" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: postEventDiscussionReply</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Topic id</label><br />
          <input type="text" name="topic_id" />
          <br />
          
          <label>Body</label><br />
          <input type="text" name="body" />
          <br />
          
          <label>Send me notifications when other members reply to this topic</label><br />
          <select name="watch">
            <option value="1">Yes</option>
            <option value="0">No</option>
          </select>
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchBadgeNumber-->
      <?php $url = $this->url(array('action' => 'fetch-badge-number'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchBadgeNumber" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchBadgeNumber</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchUnreadChatMessageCounts-->
      <?php $url = $this->url(array('action' => 'fetch-unread-chat-message-counts'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchUnreadChatMessageCounts" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchUnreadChatMessageCounts</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--deleteEventTopicThread-->
      <?php $url = $this->url(array('action' => 'delete-event-topic-thread'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_deleteEventTopicThread" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: deleteEventTopicThread</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Post id</label><br />
          <input type="text" name="post_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--deleteUserAccount-->
      <?php $url = $this->url(array('action' => 'delete-user-account'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_deleteUserAccount" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: deleteUserAccount</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <input type="submit" value="Post" />
        </div>
      </form>
      
      <!--fetchPaypalParams-->
      <?php $url = $this->url(array('action' => 'fetch-paypal-params'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_fetchPaypalParams" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: fetchPaypalParams</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>
          
          <label>Subject guid (e.g. resume_43)</label><br />
          <input type="text" name="subject" />
          <br />
          
          <label>Package id</label><br />
          <input type="text" name="package_id" />
          <br />
          
          <input type="submit" value="Post" />
        </div>
      </form>

      <!--registerUser-->
      <?php $url = $this->url(array('action' => 'register-user'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_registerUser" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: registerUser</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Safa code</label><br />
          <input type="text" name="safa_code" />
          <br />

          <label>First name</label><br />
          <input type="text" name="first_name" />
          <br />

          <label>Last name</label><br />
          <input type="text" name="last_name" />
          <br />

          <label>Email</label><br />
          <input type="text" name="email" />
          <br />

          <label>Password</label><br />
          <input type="text" name="password" />
          <br />

          <label>Phone number</label><br />
          <input type="text" name="phone_number" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!--updateUser-->
      <?php $url = $this->url(array('action' => 'update-user'), 'mgslapi_ios', true) ?>
      <form target="_blank" id="api_updateUser" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: updateUser</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <label>Safa code</label><br />
          <input type="text" name="safa_code" />
          <br />

          <label>Username</label><br />
          <input type="text" name="username" />
          <br />

          <label>Password</label><br />
          <input type="text" name="password" />
          <br />

          <input type="submit" value="Post" />
        </div>
      </form>

      <!--createCircle-->
      <?php /*
      <?php
        $package_id = 0;
        $viewer = Engine_Api::_()->user()->getViewer();
        $sitepage_is_approved = 'approved';
        $getPackageAuth = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepage');

        $package = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.lsettings', 0);

        if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
          //REDIRECT
          $package_id = $this->_getParam('id');
          if (empty($package_id)) {
            $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::ITEM_NOT_FOUND);
          }
          $this->view->package = $package = Engine_Api::_()->getItemTable('sitepage_package')->fetchRow(array('package_id = ?' => $package_id, 'enabled = ?' => '1'));
          if (empty($this->view->package)) {
            $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::ITEM_NOT_FOUND);
          }

          if (!empty($package->level_id) && !in_array($viewer->level_id, explode(",", $package->level_id))) {
            $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::ITEM_NOT_FOUND);
          }
        } else {
          $package_id = Engine_Api::_()->getItemtable('sitepage_package')->fetchRow(array('defaultpackage = ?' => 1))->package_id;
        }

        $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
        $row = $manageadminsTable->createRow();

        //FORM VALIDATION
        $form = new Sitepage_Form_Create(array("packageId" => $package_id, "owner" => $viewer));
      ?>
      <?php $url = $this->url(array('action' => 'create-circle'), 'mgslapi_ios', true) ?>
      <?php $form->setAction($url)->setAttrib('target', '_blank'); ?>
      <form target="_blank" id="api_createCircle" action="<?php echo $url ?>" method="post">
        <div id="form_box">
          <h2>End-point: createCircle</h2>
          <h4>url: <?php echo $this->serverUrl($url) ?></h4>

          <?php echo $form->render($this); ?>
        </div>
      </form>
      */ ?>

      <?php End: ?>
    </div>
  </div>
  <span class="clr"></span>
</div>


<script type="text/javascript">
  window.addEvent('domready', function () {
    $$('[id^=api_]').setStyle('display', 'none');
    if ($('formfield').get('value')) {
      $('api_' + $('formfield').get('value')).setStyle('display', 'block');
    }

    if (window.location.hash != '') {
      var value = window.location.hash.replace('#', '');
      $('api_' + value).setStyle('display', 'block');
      $('formfield').value = value;
    }

    $('formfield').addEvent('change', function () {
      $(document.body).getElements('[id^=api_]').setStyle('display', 'none');
      $('api_' + this.value).setStyle('display', 'block');
      window.location.hash = this.value;
    });
  });
</script>

<style type="text/css">
  fieldset {
    border-width: 1px !important;
    padding: 5px;
  }
  form {
    display: block;
  }
</style>
