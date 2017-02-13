<?php $currentModuleCoreApi = Engine_Api::_()->getApi('core', 'mgslapi'); ?>
<?php echo $this->headLink()->setStylesheet($this->baseUrl() . '/application/modules/' . ucfirst($currentModuleCoreApi->getModuleName()) . '/externals/styles/style.css'); ?>
<div>
    <div class="formheader">Mgslapi API V3.0 Test Tool</div>
    <div style="width:300px; margin:auto;">
        <div id="form_box">
            <select name="formfield" id="formfield">
                <option value="" label="-Select Your Form-" selected="selected">-Select Your Form-</option>
                <optgroup label="General">
                    <option value="login" label="login">login</option>   
                    <option value="getFriends" label="getFriends">getFriends</option>  
                    <option value="getMembers" label="getMembers">getMembers</option> 
                    <option value="statusUpdate" label="statusUpdate">statusUpdate</option>  
                </optgroup>
                <optgroup label="Feed">
                    <option value="feed" label="feed">feed</option>                    
                    <option value="getCalenderFeed" label="getCalenderFeed">getCalenderFeed</option>  
                    <option value="getCircleFeed" label="getCircleFeed">getCircleFeed</option>                    
                    <option value="getLatestFeed" label="getLatestFeed">getLatestFeed</option> 
                    <option value="ShareAFeed" label="ShareAFeed">ShareAFeed</option>  
                    <option value="ReportAFeed" label="ReportAFeed">ReportAFeed</option>  
                    <option value="DeleteAFeed" label="DeleteAFeed">DeleteAFeed</option>                     
                    <option value="postFeedLike" label="postFeedLike">postFeedLike</option>  
                    <option value="postFeedUnlike" label="postFeedUnlike">postFeedUnlike</option>  
                </optgroup>
                <optgroup label="Message">
                    <option value="getMessageList" label="getMessageList">getMessageList</option>                    
                    <option value="getPreviousConversactions" label="getPreviousConversactions">getPreviousConversactions</option>                    
                    <option value="getLatestConversactions" label="getLatestConversactions">getLatestConversactions</option>                    
                    <option value="replyMessage" label="replyMessage">replyMessage</option>                    
                    <option value="postNewMessage" label="postNewMessage">postNewMessage</option>    
                    <option value="getAllUnreadMessagesList" label="getAllUnreadMessagesList">getAllUnreadMessagesList</option> 
                </optgroup>
                <optgroup label="Album">
                    <option value="getAlbums" label="getAlbums">getAlbums</option>                    
                    <option value="getMyAlbums" label="getMyAlbums">getMyAlbums</option>                    
                    <option value="getAlbumPhoto" label="getAlbumPhoto">getAlbumPhoto</option>  
                    <option value="photoUpload" label="photoUpload">photoUpload</option> 
                    <option value="getLatestCommentsOfAPhoto" label="getLatestCommentsOfAPhoto">getLatestCommentsOfAPhoto</option>  
                    <option value="getPreviousCommentsOfAPhoto" label="getPreviousCommentsOfAPhoto">getPreviousCommentsOfAPhoto</option>  
                    <option value="getNewestCommentsOfAPhoto" label="getNewestCommentsOfAPhoto">getNewestCommentsOfAPhoto</option>  
                </optgroup>
                <optgroup label="Comment"> 
                    <option value="postComment" label="postComment">postComment</option>  
                    <!--<option value="getFeedLastComment" label="getFeedLastComment">getFeedLastComment</option>-->  
                    <option value="getNewestCommentOfAFeed" label="getNewestCommentOfAFeed">getNewestCommentOfAFeed</option>  
                    <option value="getLatestCommentsOfAFeed" label="getLatestCommentsOfAFeed">getLatestCommentsOfAFeed</option>  
                    <option value="getPreviousCommentsOfAFeed" label="getPreviousCommentsOfAFeed">getPreviousCommentsOfAFeed</option>  
                </optgroup>
                <optgroup label="Video">
                    <option value="getVideoList" label="getVideoList">getVideoList</option>  
                    <option value="getMyVideoList" label="getMyVideoList">getMyVideoList</option>  
                    <option value="videoUpload" label="videoUpload">videoUpload</option>
                </optgroup>
                <optgroup label="Calender">
                    <option value="getCalenderList" label="getCalenderList">getCalenderList</option>  
                    <option value="getMyCalenderList" label="getMyCalenderList">getMyCalenderList</option> 
                    <option value="calenderRSVP" label="calenderRSVP">calenderRSVP</option>   
                    <option value="attendingAnEvent" label="attendingAnEvent">attendingAnEvent</option> 
                </optgroup> 
                <optgroup label="Circle">
                    <option value="getCircleList" label="getCircleList">getCircleList</option>  
                    <option value="getMyCircleList" label="getMyCircleList">getMyCircleList</option>  
                    <option value="likeACircle" label="likeACircle">likeACircle</option>  
                    <option value="unlikeACircle" label="unlikeACircle">unlikeACircle</option>  
                    <option value="followACricle" label="followACricle">followACricle</option>  
                    <option value="unfollowACricle" label="unfollowACricle">unfollowACricle</option>  
                    <option value="joinACircle" label="joinACircle">joinACircle</option>  
                    <option value="leaveACircle" label="leaveACircle">leaveACircle</option>  
                    <option value="getMembersOfACircle" label="getMembersOfACircle">getMembersOfACircle</option>  
                    <option value="getEventofACircle" label="getEventofACircle">getEventofACircle</option>  
                </optgroup>              
               
                <optgroup label="Friend request">
                    <option value="getAllUnseenFriendRequests" label="getAllUnseenFriendRequests">getAllUnseenFriendRequests</option>  
                    <option value="acceptFriendRequest" label="acceptFriendRequest">acceptFriendRequest</option>  
                    <option value="denyFriendRequest" label="denyFriendRequest">denyFriendRequest</option>  
                </optgroup>
                <optgroup label="Check in">
                    <option value="getNearestLocationsOfAUser" label="getNearestLocationsOfAUser">getNearestLocationsOfAUser</option>  
                    <option value="postUserLocation" label="postUserLocation">postUserLocation</option>  
                </optgroup>
                <optgroup label="Others">
                    <option value="sendPushNotification" label="sendPushNotification">sendPushNotification</option>  
                    <option value="getAllBadgeCount" label="getAllBadgeCount">getAllBadgeCount</option>  
                    <option value="clearBadgeCount" label="clearBadgeCount">clearBadgeCount</option>                   
                    <option value="getAllUnseenNotification" label="getAllUnseenNotification">getAllUnseenNotification</option> 
                    <option value="readNotification" label="readNotification">readNotification</option> 
                    <option value="updateDeviceInfo" label="updateDeviceInfo">updateDeviceInfo</option>
                    <option value="getjsbaseurl" label="getjsbaseurl">getjsbaseurl</option>  
                    <option value="image-resize" label="image-resize">image-resize</option>
                </optgroup>
            </select>      
            <br /><br />

        </div>
      
      <form style="display:block" method="post">
        <label>PHPSESSID:</label><br />
        <input type="text" name="PHPSESSID" value="" />
        <input type="submit" value="POST" />
      </form>

        <div style="margin-left:-5px;">
            <!-- login -->
            <form id="api_login" action="<?php echo $this->url(array('action' => 'login'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: login</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'login'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />

                    <input type="submit" value="login" />
                </div>
            </form>
            
            <!-- feed -->
            <form id="api_feed" action="<?php echo $this->url(array('action' => 'feed'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: feed</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'feed'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="saydul@technobd.com" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="123456" /><br />
                    
                    <label>user_id:</label><br />
                    <input name="user_id" type="text" value="" /><br />

                    <input type="submit" value="feed" />
                </div>
            </form>
            
            <!-- getCalenderFeed -->
            <form id="api_getCalenderFeed" action="<?php echo $this->url(array('action' => 'getCalenderFeed'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getCalenderFeed</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getCalenderFeed'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <label>event_id:</label><br />
                    <input name="event_id" type="text" value="" /><br />

                    <input type="submit" value="feed" />
                </div>
            </form>
            
            <!-- getLatestFeed -->
            <form id="api_getLatestFeed" action="<?php echo $this->url(array('action' => 'getLatestFeed'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getLatestFeed</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getLatestFeed'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <label>user_id:</label><br />
                    <input name="user_id" type="text" value="" /><br />
                    
                    <label>newest_feed_id:</label><br />
                    <input name="newest_feed_id" type="text" value="" /><br />

                    <input type="submit" value="feed" />
                </div>
            </form>
            
            <!-- getMessageList -->
            <form id="api_getMessageList" action="<?php echo $this->url(array('action' => 'getMessageList'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getMessageList</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getMessageList'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />  

                    <label>page:</label><br />
                    <input name="page" type="text" value="" /><br />                  

                    <input type="submit" value="feed" />
                </div>
            </form>
            
            <!-- getPreviousMessage -->
            <form id="api_getPreviousConversactions" action="<?php echo $this->url(array('action' => 'getPreviousConversactions'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getPreviousConversactions</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getPreviousConversactions'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <label>message_id:</label><br />
                    <input name="message_id" type="text" value="" /><br />
                    
                    <label>last_conversation_id:</label><br />
                    <input name="last_conversation_id" type="text" value="" /><br />

                    <input type="submit" value="feed" />
                </div>
            </form>
            
            <!-- getLatestConversactions -->
            <form id="api_getLatestConversactions" action="<?php echo $this->url(array('action' => 'getLatestConversactions'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getLatestConversactions</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getLatestConversactions'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <label>message_id:</label><br />
                    <input name="message_id" type="text" value="" /><br />
                    
                    <label>last_conversation_id:</label><br />
                    <input name="last_conversation_id" type="text" value="" /><br />

                    <input type="submit" value="feed" />
                </div>
            </form>
            
            <!-- replyMessage -->
            <form id="api_replyMessage" action="<?php echo $this->url(array('action' => 'replyMessage'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: replyMessage</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'replyMessage'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <label>message_id:</label><br />
                    <input name="message_id" type="text" value="" /><br />
                    
                    <label>last_conversation_id:</label><br />
                    <input name="last_conversation_id" type="text" value="" /><br />
                    
                    <label>body:</label><br />
                    <textarea name="body" style="width: 280px"></textarea><br />

                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- postNewMessage -->
            <form id="api_postNewMessage" action="<?php echo $this->url(array('action' => 'postNewMessage'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: postNewMessage</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'postNewMessage'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <label>user_id:</label><br />
                    <input name="user_id" type="text" value="" /><br />
                    
                    <label>subject:</label><br />
                    <input name="subject" type="text" value="" /><br />
                    
                    <label>body:</label><br />
                    <textarea name="body" style="width: 280px"></textarea><br />

                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getAlbums -->
            <form id="api_getAlbums" action="<?php echo $this->url(array('action' => 'getAlbums'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getAlbums</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getAlbums'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />

                    <label>page:</label><br />
                    <input name="page" type="text" value="" /><br />

                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getMyAlbums -->
            <form id="api_getMyAlbums" action="<?php echo $this->url(array('action' => 'getMyAlbums'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getMyAlbums</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getMyAlbums'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />

                    <label>page:</label><br />
                    <input name="page" type="text" value="" /><br />

                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getAlbumPhoto -->
            <form id="api_getAlbumPhoto" action="<?php echo $this->url(array('action' => 'getAlbumPhoto'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getAlbumPhoto</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getAlbumPhoto'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />

                    <label>album_id:</label><br />
                    <input name="album_id" type="text" value="" /><br />

                    <label>page:</label><br />
                    <input name="page" type="text" value="" /><br />

                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getLatestCommentsOfAPhoto -->
            <form id="api_getLatestCommentsOfAPhoto" action="<?php echo $this->url(array('action' => 'getLatestCommentsOfAPhoto'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getLatestCommentsOfAPhoto</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getLatestCommentsOfAPhoto'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="saydul@technobd.com" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="123456" /><br />

                    <label>photo_id:</label><br />
                    <input name="photo_id" type="text" value="" /><br />

                    <label>page:</label><br />
                    <input name="page" type="text" value="" /><br />

                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getPreviousCommentsOfAPhoto -->
            <form id="api_getPreviousCommentsOfAPhoto" action="<?php echo $this->url(array('action' => 'getPreviousCommentsOfAPhoto'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getPreviousCommentsOfAPhoto</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getPreviousCommentsOfAPhoto'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="saydul@technobd.com" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="123456" /><br />

                    <label>photo_id:</label><br />
                    <input name="photo_id" type="text" value="" /><br />

                    <label>last_comment_id:</label><br />
                    <input name="last_comment_id" type="text" value="" /><br />

                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getNewestCommentsOfAPhoto -->
            <form id="api_getNewestCommentsOfAPhoto" action="<?php echo $this->url(array('action' => 'getNewestCommentsOfAPhoto'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getNewestCommentsOfAPhoto</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getNewestCommentsOfAPhoto'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="saydul@technobd.com" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="123456" /><br />

                    <label>photo_id:</label><br />
                    <input name="photo_id" type="text" value="" /><br />

                    <label>newest_comment_id:</label><br />
                    <input name="newest_comment_id" type="text" value="" /><br />

                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getFriends -->
            <form id="api_getFriends" action="<?php echo $this->url(array('action' => 'getFriends'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getFriends</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getFriends'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />

                    <label>page:</label><br />
                    <input name="page" type="text" value="" /><br />

                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getMembers -->
            <form id="api_getMembers" action="<?php echo $this->url(array('action' => 'getMembers'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getMembers</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getMembers'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />

                    <label>page:</label><br />
                    <input name="page" type="text" value="" /><br />

                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- statusUpdate -->
            <form id="api_statusUpdate" action="<?php echo $this->url(array('action' => 'statusUpdate'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: statusUpdate</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'statusUpdate'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="saydul@technobd.com" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="123456" /><br />
                    
                    <label>body:</label><br />
                    <textarea id="body" rows="6" cols="20" name="body"></textarea>
                    
                    <label>subject_type:</label><br />
                    <select name="subject_type" id="comment_type">
                        <option value=""></option>
                        <option value="event">event</option>
                        <option value="sitepage_page">sitepage_page</option>
                    </select><br/>
                    
                    <label>subject_id:</label><br />
                    <input name="subject_id" type="text" value="" /><br />
                    
                    <label>newest_feed_id:</label><br />
                    <input name="newest_feed_id" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- photoUpload -->
            <form id="api_photoUpload" action="<?php echo $this->url(array('action' => 'photoUpload'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post" enctype="multipart/form-data">
                <div id="form_box">                            
                    <h2>End-point: photoUpload</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'photoUpload'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="saydul@technobd.com" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="123456" /><br />                    
                    
                    <label>body:</label><br />
                    <textarea id="body" rows="6" cols="20" name="body"></textarea>
                    
                    <label>image:</label><br />
                    <input name="image" type="file" /><br />                    
                    
                    <label>subject_type:</label><br />
                    <select name="subject_type" id="comment_type">
                        <option value=""></option>
                        <option value="event">event</option>
                        <option value="sitepage_page">sitepage_page</option>
                    </select><br/>
                    
                    <label>subject_id:</label><br />
                    <input name="subject_id" type="text" value="" /><br />
                    
                    <label>newest_feed_id:</label><br />
                    <input name="newest_feed_id" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- videoUpload -->
            <form id="api_videoUpload" action="<?php echo $this->url(array('action' => 'videoUpload'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: videoUpload</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'videoUpload'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="saydul@technobd.com" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="123456" /><br />
                    
                    <label>body:</label><br />
                    <textarea id="body" rows="6" cols="20" name="body"></textarea>
                    
                    <label>youtube:</label><br />
                    <input name="youtube" type="text" value="" /><br />
                    
                    <label>vimeo:</label><br />
                    <input name="vimeo" type="text" value="" /><br />
                    
                    <label>subject_type:</label><br />
                    <select name="subject_type" id="comment_type">
                        <option value=""></option>
                        <option value="event">event</option>
                        <option value="sitepage_page">sitepage_page</option>
                    </select><br/>
                    
                    <label>subject_id:</label><br />
                    <input name="subject_id" type="text" value="" /><br />
                    
                    <label>newest_feed_id:</label><br />
                    <input name="newest_feed_id" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- postComment -->
            <form id="api_postComment" action="<?php echo $this->url(array('action' => 'postComment'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: postComment</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'postComment'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />

                    <label>comment_type:</label><br />
                    <select name="comment_type" id="comment_type">
                        <option value="feed">feed</option>
                        <option value="album">album</option>
                        <option value="album_photo">album_photo</option>
                        <option value="video">video</option>
                    </select><br/>
                    
                    <label>id:</label><br />
                    <input name="id" type="text" value="" /><br />
                    
                    <label>last_comment_id:</label><br />
                    <input name="last_comment_id" type="text" value="" /><br />
                    
                    <label>body:</label><br />
                    <textarea id="body" rows="6" cols="20" name="body"></textarea>
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- postFeedLike -->
            <form id="api_postFeedLike" action="<?php echo $this->url(array('action' => 'postFeedLike'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: postFeedLike</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'postFeedLike'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <label>feed_id:</label><br />
                    <input name="feed_id" type="text" value="" /><br />
                    
                    <label>comment_id:</label><br />
                    <input name="comment_id" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- postFeedUnlike -->
            <form id="api_postFeedUnlike" action="<?php echo $this->url(array('action' => 'postFeedUnlike'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: postFeedUnlike</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'postFeedUnlike'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <label>feed_id:</label><br />
                    <input name="feed_id" type="text" value="" /><br />
                    
                    <label>comment_id:</label><br />
                    <input name="comment_id" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
<!--            
             getFeedLastComment 
            <form id="api_getFeedLastComment" action="<?php echo $this->url(array('action' => 'getFeedLastComment'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getFeedLastComment</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getFeedLastComment'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <label>feed_id:</label><br />
                    <input name="feed_id" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>-->
            
            <!-- getNewestCommentOfAFeed -->
            <form id="api_getNewestCommentOfAFeed" action="<?php echo $this->url(array('action' => 'getNewestCommentOfAFeed'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getNewestCommentOfAFeed</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getNewestCommentOfAFeed'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="saydul@technobd.com" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="123456" /><br />
                    
                    <label>feed_id:</label><br />
                    <input name="feed_id" type="text" value="" /><br />
                    
                    <label>newest_comment_id:</label><br />
                    <input name="newest_comment_id" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getLatestCommentsOfAFeed -->
            <form id="api_getLatestCommentsOfAFeed" action="<?php echo $this->url(array('action' => 'getLatestCommentsOfAFeed'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getLatestCommentsOfAFeed</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getLatestCommentsOfAFeed'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <label>feed_id:</label><br />
                    <input name="feed_id" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getPreviousCommentsOfAFeed  -->
            <form id="api_getPreviousCommentsOfAFeed" action="<?php echo $this->url(array('action' => 'getPreviousCommentsOfAFeed'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getPreviousCommentsOfAFeed</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getPreviousCommentsOfAFeed'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <label>feed_id:</label><br />
                    <input name="feed_id" type="text" value="" /><br />
                    
                    <label>last_comment_id:</label><br />
                    <input name="last_comment_id" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getVideoList  -->
            <form id="api_getVideoList" action="<?php echo $this->url(array('action' => 'getVideoList'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getVideoList</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getVideoList'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <label>page:</label><br />
                    <input name="page" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getMyVideoList  -->
            <form id="api_getMyVideoList" action="<?php echo $this->url(array('action' => 'getMyVideoList'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getMyVideoList</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getMyVideoList'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <label>page:</label><br />
                    <input name="page" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getCalenderList  -->
            <form id="api_getCalenderList" action="<?php echo $this->url(array('action' => 'getCalenderList'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getCalenderList</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getCalenderList'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <label>page:</label><br />
                    <input name="page" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getMyCalenderList  -->
            <form id="api_getMyCalenderList" action="<?php echo $this->url(array('action' => 'getMyCalenderList'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getMyCalenderList</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getMyCalenderList'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <label>page:</label><br />
                    <input name="page" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- calenderRSVP  -->
            <form id="api_calenderRSVP" action="<?php echo $this->url(array('action' => 'calenderRSVP'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: calenderRSVP</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'calenderRSVP'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="saydul@technobd.com" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="123456" /><br />
                    
                    <label>event_id:</label><br />
                    <input name="event_id" type="text" value="" /><br />
                    
                    <label>type:</label><br />
                    <select name="type" id="type">
                        <option value="attending">attending</option>
                        <option value="maybe">maybe</option>
                        <option value="not">not</option>
                    </select><br/>
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- attendingAnEvent  -->
            <form id="api_attendingAnEvent" action="<?php echo $this->url(array('action' => 'attendingAnEvent'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: attendingAnEvent</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'attendingAnEvent'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="saydul@technobd.com" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="123456" /><br />
                    
                    <label>event_id:</label><br />
                    <input name="event_id" type="text" value="" /><br />
                    
                    <label>type:</label><br />
                    <select name="type" id="type">
                        <option value="attending">attending</option>
                        <option value="maybe">maybe</option>
                        <option value="not">not</option>
                    </select><br/>
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getCircleList  -->
            <form id="api_getCircleList" action="<?php echo $this->url(array('action' => 'getCircleList'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getCircleList</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getCircleList'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="saydul@technobd.com" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="123456" /><br />
                    
                    <label>page:</label><br />
                    <input name="page" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getCircleFeed  -->
            <form id="api_getCircleFeed" action="<?php echo $this->url(array('action' => 'getCircleFeed'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getCircleFeed</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getCircleFeed'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="saydul@technobd.com" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="123456" /><br />
                    
                    <label>circle_id:</label><br />
                    <input name="circle_id" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getMyCircleList  -->
            <form id="api_getMyCircleList" action="<?php echo $this->url(array('action' => 'getMyCircleList'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getMyCircleList</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getMyCircleList'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="saydul@technobd.com" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="123456" /><br />
                    
                    <label>page:</label><br />
                    <input name="page" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- likeACircle  -->
            <form id="api_likeACircle" action="<?php echo $this->url(array('action' => 'likeACircle'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: likeACircle</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'likeACircle'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="saydul@technobd.com" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="123456" /><br />
                    
                    <label>circle_id:</label><br />
                    <input name="circle_id" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- unlikeACircle  -->
            <form id="api_unlikeACircle" action="<?php echo $this->url(array('action' => 'unlikeACircle'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: unlikeACircle</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'unlikeACircle'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="saydul@technobd.com" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="123456" /><br />
                    
                    <label>circle_id:</label><br />
                    <input name="circle_id" type="text" value="" /><br />
                    
                    <label>like_id:</label><br />
                    <input name="like_id" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- followACricle  -->
            <form id="api_followACricle" action="<?php echo $this->url(array('action' => 'followACricle'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: followACricle</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'followACricle'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="saydul@technobd.com" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="123456" /><br />
                    
                    <label>circle_id:</label><br />
                    <input name="circle_id" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- unfollowACricle  -->
            <form id="api_unfollowACricle" action="<?php echo $this->url(array('action' => 'unfollowACricle'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: unfollowACricle</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'unfollowACricle'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="saydul@technobd.com" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="123456" /><br />
                    
                    <label>circle_id:</label><br />
                    <input name="circle_id" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- joinACircle  -->
            <form id="api_joinACircle" action="<?php echo $this->url(array('action' => 'joinACircle'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: joinACircle</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'joinACircle'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="saydul@technobd.com" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="123456" /><br />
                    
                    <label>circle_id:</label><br />
                    <input name="circle_id" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getMembersOfACircle  -->
            <form id="api_getMembersOfACircle" action="<?php echo $this->url(array('action' => 'getMembersOfACircle'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getMembersOfACircle</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getMembersOfACircle'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="saydul@technobd.com" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="123456" /><br />
                    
                    <label>circle_id:</label><br />
                    <input name="circle_id" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getMembersOfACircle  -->
            <form id="api_getEventofACircle" action="<?php echo $this->url(array('action' => 'getEventofACircle'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getEventofACircle</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getEventofACircle'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="saydul@technobd.com" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="123456" /><br />
                    
                    <label>circle_id:</label><br />
                    <input name="circle_id" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- leaveACircle  -->
            <form id="api_leaveACircle" action="<?php echo $this->url(array('action' => 'leaveACircle'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: leaveACircle</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'leaveACircle'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="saydul@technobd.com" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="123456" /><br />
                    
                    <label>circle_id:</label><br />
                    <input name="circle_id" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getAllBadgeCount  -->
            <form id="api_getAllBadgeCount" action="<?php echo $this->url(array('action' => 'getAllBadgeCount'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getAllBadgeCount</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getAllBadgeCount'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- clearBadgeCount  -->
            <form id="api_clearBadgeCount" action="<?php echo $this->url(array('action' => 'clearBadgeCount'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: clearBadgeCount</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'clearBadgeCount'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />       
                    
                    <label>type:</label><br />
                    <select name="type" id="type">
                        <option value="message">message</option>
                        <option value="notification">notification</option>
                        <option value="friends">friends</option>
                    </select><br/>
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getAllUnreadMessagesList  -->
            <form id="api_getAllUnreadMessagesList" action="<?php echo $this->url(array('action' => 'getAllUnreadMessagesList'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getAllUnreadMessagesList</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getAllUnreadMessagesList'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <label>page:</label><br />
                    <input name="page" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getAllUnseenNotification  -->
            <form id="api_getAllUnseenNotification" action="<?php echo $this->url(array('action' => 'getAllUnseenNotification'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getAllUnseenNotification</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getAllUnseenNotification'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- readNotification  -->
            <form id="api_readNotification" action="<?php echo $this->url(array('action' => 'readNotification'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: readNotification</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'readNotification'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <label>notification_id:</label><br />
                    <input name="notification_id" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- sendPushNotification  -->
            <form id="api_sendPushNotification" action="<?php echo $this->url(array('action' => 'sendPushNotification'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: sendPushNotification</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'sendPushNotification'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <label>device_token:</label><br />
                    <input name="device_token" type="text" value="" /><br />
                    
                    <label>message</label><br />
                    <textarea name="message"></textarea><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- updateDeviceInfo  -->
            <form id="api_updateDeviceInfo" action="<?php echo $this->url(array('action' => 'updateDeviceInfo'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: updateDeviceInfo</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'updateDeviceInfo'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <label>device_token:</label><br />
                    <input name="device_token" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getAllUnseenFriendRequests  -->
            <form id="api_getAllUnseenFriendRequests" action="<?php echo $this->url(array('action' => 'getAllUnseenFriendRequests'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getAllUnseenFriendRequests</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getAllUnseenFriendRequests'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- acceptFriendRequest   -->
            <form id="api_acceptFriendRequest" action="<?php echo $this->url(array('action' => 'acceptFriendRequest'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: acceptFriendRequest</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'acceptFriendRequest'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <label>user_id:</label><br />
                    <input name="user_id" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- denyFriendRequest   -->
            <form id="api_denyFriendRequest" action="<?php echo $this->url(array('action' => 'denyFriendRequest'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: denyFriendRequest</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'denyFriendRequest'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <label>user_id:</label><br />
                    <input name="user_id" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- ShareAFeed   -->
            <form id="api_ShareAFeed" action="<?php echo $this->url(array('action' => 'ShareAFeed'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: ShareAFeed</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'ShareAFeed'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <label>type:</label><br />
                    <select name="type" id="type">
                        <option value="activity_action">activity_action</option>
                        <option value="album_photo">album_photo</option>
                        <option value="event">event</option>
                        <option value="video">video</option>
                        <option value="group">group</option>
                    </select><br/>
                    
                    <label>id:</label><br />
                    <input name="id" type="text" value="" /><br />                    
                    
                    <label>body:</label><br />
                    <textarea name="body" style="width: 280px"></textarea><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- ReportAFeed   -->
            <form id="api_ReportAFeed" action="<?php echo $this->url(array('action' => 'ReportAFeed'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: ReportAFeed</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'ReportAFeed'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />                    
                    
                    <label>type:</label><br />
                    <select name="type" id="type">
                        <option value="activity_action">activity_action</option>
                        <option value="album_photo">album_photo</option>
                        <option value="event">event</option>
                        <option value="video">video</option>
                        <option value="group">group</option>
                    </select><br/>
                    
                    <label>id:</label><br />
                    <input name="id" type="text" value="" /><br />                       
                    
                    <label>category:</label><br />
                    <select id="category" name="category">
                        <option label="Inappropriate Content" value="inappropriate">Inappropriate Content</option>
                        <option label="Spam" value="spam">Spam</option>
                        <option label="Abuse" value="abuse">Abuse</option>
                        <option label="Licensed Material" value="licensed">Licensed Material</option>
                        <option label="Other" value="other">Other</option>
                    </select><br />
                    
                    <label>description:</label><br />
                    <textarea name="description" style="width: 280px"></textarea><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- DeleteAFeed   -->
            <form id="api_DeleteAFeed" action="<?php echo $this->url(array('action' => 'DeleteAFeed'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: DeleteAFeed</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'DeleteAFeed'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="" /><br />
                    
                    <label>feed_id:</label><br />
                    <input name="feed_id" type="text" value="" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getNearestLocationsOfAUser   -->
            <form id="api_getNearestLocationsOfAUser" action="<?php echo $this->url(array('action' => 'getNearestLocationsOfAUser'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getNearestLocationsOfAUser</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getNearestLocationsOfAUser'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="info@myglobalsportlink.com" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="dhtlm4532" /><br />
                    
                    <label>latitude:</label><br />
                    <input name="latitude" type="text" value="23.709920999999998" /><br />
                    
                    <label>longitude:</label><br />
                    <input name="longitude" type="text" value="90.40714299999999" /><br />
                    
                    <label>query_string:</label><br />
                    <input name="query_string" type="text" value="Sankharia Bazaar" /><br />
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- postUserLocation   -->
            <form id="api_postUserLocation" action="<?php echo $this->url(array('action' => 'postUserLocation'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: postUserLocation</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'postUserLocation'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                

                    <label>email:</label><br />
                    <input name="email" type="text" value="info@myglobalsportlink.com" /><br />

                    <label>password:</label><br />
                    <input name="password" type="password" value="dhtlm4532" /><br />
                    
                    <label>location_string:</label><br />
                    <input name="location_string" type="text" value="" /><br />
                    
                    <label>body:</label><br />
                    <textarea name="body" style="width: 280px"></textarea><br />                    
                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- getjsbaseurl -->
            <form id="api_getjsbaseurl" action="<?php echo $this->url(array('action' => 'getjsbaseurl'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: getjsbaseurl</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'getjsbaseurl'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                    
                    <input type="submit" value="Submit" />
                </div>
            </form>
            
            <!-- image-resize -->
            <form id="api_image-resize" action="<?php echo $this->url(array('action' => 'image-resize'), 'mgslapi_android', true).'/?sitemood=full' ?>" method="post">
                <div id="form_box">                            
                    <h2>End-point: image-resize</h2>
                    <h4>url: <?php echo $this->serverUrl($this->url(array('action' => 'image-resize', 'url'=>'e','w'=>'widht', 'h'=>'height', 'type'=>'ratio'), 'mgslapi_android', true)).'/?sitemood=full' ?></h4>                    
                    <p>e: Encoded url</p>
                    <p>width: width of the image</p>
                    <p>height: height of the image</p>
                    <p>type: type can be ratio or exact</p>
                    <input type="submit" value="image-resize" />
                </div>
            </form>
        </div>
    </div>
    <span class="clr"></span>
</div>


<script type="text/javascript">
    window.addEvent('domready', function() {
        $$('form[id^=api_]').setStyle('display', 'none');
        if ( $('formfield').get('value') ) {
            $('api_'+$('formfield').get('value')).setStyle('display', 'block');
        }

        $('formfield').addEvent('change', function () {
            $(document.body).getElements('form[id^=api_]').setStyle('display', 'none');
            $('api_'+this.value).setStyle('display', 'block');
        });
    });
</script>