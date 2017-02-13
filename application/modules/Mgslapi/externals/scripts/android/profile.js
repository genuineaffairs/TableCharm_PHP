    var feed_id;
    var like_feed_id;
    var unlike_feed_id;
    var comment_feed_id;
    var last_feed_id;
    var newest_feed_id;
    var logged_in_user_id;
    var auth_email_address;
    var auth_password;
    var ajax_request_in_progress;
    var actionType;
    var event_id;
    var profile_user_id;
    
    function addLatestFeeds(param)
    {
        //alert(JSON.stringify($.base64.decode(param)));
        $('#loading').remove();
        $('.wrap').prepend($.base64.decode(param));
        $("body").animate({ scrollTop: 0 }, "fast");
        
        return 'OK =';
        
    }
    
    function touchStart(event) {
        //event.target.style.backgroundColor = "#d5d9e2";
    }
    
    function touchEnd(event) {
        //event.target.style.backgroundColor = "#eceff5";
        feed_id = event.target.id;
        if(event.target.id != 'do_unlike' && (event.target.id != 'do_like') && (event.target.id != 'do_ccoment') )
        {
            window.location.href = baseurl+'android/api/v3.0/previousfeed'; //'http://192.168.50.40/se/android/api/v3.0/previousfeed';
        }
        
        
    }
    
    function resetAll()
    {
        feed_id = -1;
        like_feed_id = -1;
        unlike_feed_id = -1;
        comment_feed_id = -1;
        last_feed_id = -1;
        newest_feed_id = -1;
        actionType = ''; 
        return 'reset_all_completed';
    }
    
    function increaseCommentCounter()
    {
        var current_comment_value = $('#comment_value_'+ comment_feed_id).html();
        // increase comments
        $('#comment_value_'+ comment_feed_id).html(parseInt(current_comment_value) + 1);
    }
    
    function postFeedLike(feedId, userId)
    {
//        $.post(baseurl+'android/api/v3.0/postFeedLike', {email: auth_email_address, password: auth_password, feed_id: feedId, user_id: userId}).done(function(data) {
////            alert("Data Loaded: " + data);
//            //alert(JSON.stringify(data));
//        });
        $.ajax({
            type: "POST",
            url: baseurl+'android/api/v3.0/postFeedLike',
            dataType: "html",
            data: {email: auth_email_address, password: auth_password, feed_id: feedId, user_id: userId}
        }).done(function(html) {
            //alert(JSON.stringify(html));
        });
    }

    function postFeedUnlike(feedId, userId)
    {
//        $.post(baseurl+'android/api/v3.0/postFeedUnlike', {email: auth_email_address, password: auth_password, feed_id: feedId, user_id: userId}).done(function(data) {
//            //alert("Data Loaded: " + data);
//        });
        $.ajax({
            type: "POST",
            url: baseurl+'android/api/v3.0/postFeedUnlike',
            dataType: "html",
            data: {email: auth_email_address, password: auth_password, feed_id: feedId, user_id: userId}
        }).done(function(html) {
            //alert("Data Loaded: " + html);
        });
    }

    
    function getCommentId()
    {
        return 'comment_id='+comment_feed_id;
    }
    
    
    function getFeedId()
    {
        return 'feed_id='+feed_id;
    }
    
    function getFeedContent()
    {
        var div_id = '#' + feed_id;
        var content = $(div_id).parent().html();
        return 'feed_content='+ content;
    }
    
    function getFeedContentById(feedId)
    {
        var div_id = '#' + feedId;
        var content = $(div_id).parent().html();
        return 'feed_content_by_id='+ content;
    }
    
    function getLikedFeedId()
    {
        return 'like_feed_id='+ like_feed_id;
    }
    
    function getUnLikedFeedId()
    {
        return 'unlike_feed_id='+unlike_feed_id;
    }
    
        function setAuthEmail(param)
    {
        auth_email_address = param;
    }
    
    function setAuthPassword(param)
    {
        auth_password = param;
    }
    
    function setLoggedInUserId(userid)
    {
        logged_in_user_id = userid;
    }
   
    function setProfileUserId(userid)
    {
        //alert(userid);
        profile_user_id = userid;
    }

    function setEventId(prarm)
    {
        event_id = prarm;
    }    
    
    function getActionType()
    {
        return actionType;
    }
    
    function getNewestFeedId()
    {
        newest_feed_id = $(".bottom").first().attr("id");
        //alert(newest_feed_id);
        return 'newest_feed_id='+newest_feed_id;
    }
    
    function removeAFeedById(param)
    {
        var element_name = 'div[id="' + param + '"]';
        $( element_name ).parents(".feed").remove();
    }    
    /*
    function likeAPost(action_id)
    {
        $.post("http://192.168.50.40/iseandroid/api/android/api/v3.0/dump.php", { post_id: action_id },
        function(data) {
          alert("Like A Post. Data Loaded: " + data);
        });
    }
    
    function unLikeAPost(action_id)
    {
        $.post("http://192.168.50.40/iseandroid/api/android/api/v3.0/dump.php", { post_id: action_id },
        function(data) {
          alert("Unike A Post. Data Loaded: " + data);
        });
    }
    */
    
    $(document).ready(function(){
        
        ajax_request_in_progress = 0;
        newest_feed_id = $( ".bottom" ).first().attr("id");
        
            $(window).scroll(function(){
                
                var d = $(window).scrollTop() + $(window).height() + 250;
                var t = $(document).height();
                
                feed_id = -1;
                like_feed_id = -1;
                unlike_feed_id = -1;
                last_feed_id = -1;
                newest_feed_id = -1;
                
                last_feed_id = $( ".bottom" ).last().attr("id");
                newest_feed_id = $( ".bottom" ).first().attr("id");
                
                if($("#loading").length == 0) {
                    $('.wrap').append('<div id="loading">Loading</div>');
                }
                
                //'<div id="loading">Loading ...'+d+' => '+t+'</div>'
                //<img alt="Loading" src="http://localhost/iseandroid/api/android/api/v3.0/images/loading.gif">
                
                
                if(d >= t)
                {               
                    if(ajax_request_in_progress == 0) {
                        
//                        $.post(baseurl+'android/api/v3.0/previousfeed',{ maxid: last_feed_id, viewer_id: logged_in_user_id, user_id: profile_user_id, event_id: event_id }, function(data) {
//
//                            $('#loading').remove();
//                            $('.wrap').append(data);
//                        });
                        
                        $.ajax({
                            type: "POST",
                            url: baseurl+'android/api/v3.0/previousfeed',
                            dataType: "html",
                            data: {maxid: last_feed_id, viewer_id: logged_in_user_id, user_id: profile_user_id, event_id: event_id }
                        }).done(function(html) {
                            $('#loading').remove();
                            $('.wrap').append(html);
                        });
                        
                    }
                    
                    
                }
                
            });
            
            
            
            $('body').on('click', '#do_like', function() {
            
                event.preventDefault();
                
                var like = $(this).attr("href");
                
                
                var current_like_value = $('#like_value_'+ like).html();
                
                // increase likes
                $('#like_value_'+like).html(parseInt(current_like_value) + 1);
                
                $(this).replaceWith('<a id="do_unlike" href="' + like + '">Unlike</a>');
                
                like_feed_id = like;
                unlike_feed_id = -1;
                postFeedLike(like_feed_id,logged_in_user_id);
                return false;
                //window.location.href = baseurl+'android/api/v3.0/previousfeed';//'http://192.168.50.40/se/android/api/v3.0/previousfeed';
                
            });
            
            
            $('body').on('click', '#do_unlike', function() {
                
                    event.preventDefault();
                    
                    var unlike = $(this).attr("href");
                    var current_like_value = $('#like_value_'+ unlike).html();
                    // decrise likes
                    $('#like_value_'+unlike).html(parseInt(current_like_value) - 1);
                    
                    $(this).replaceWith('<a id="do_like" href="' + unlike + '">Like</a>');
                    
                    unlike_feed_id = unlike;
                    like_feed_id = -1;
                    postFeedUnlike(unlike_feed_id,logged_in_user_id);
                    return false;
                    //window.location.href = baseurl+'android/api/v3.0/previousfeed';//'http://192.168.50.40/se/android/api/v3.0/previousfeed';
                    
             });
            
            
            
            $('body').on('click', '#do_ccoment', function() { 
                
                event.preventDefault();
                //resetAll();
                
                var comment = $(this).attr("href");
                comment_feed_id = comment;
                //return false;
                
                window.location.href = baseurl+'android/api/v3.0/previousfeed';//'http://192.168.50.40/se/api/v3.0/previousfeed';
                
                
            });

            $('body').on('click', '#do_popup_action', function() {

                event.preventDefault();
                actionType = $( this ).attr("href");
                //alert(actionType);
                window.location.href = baseurl+'android/api/v3.0/previousfeed';
                //return false;

            });
            
            $(document).bind("ajaxSend", function(){
               // alert('ajax request started');
                ajax_request_in_progress = 1;
            }).bind("ajaxComplete", function(){
                //alert('ajax request stopped');
                ajax_request_in_progress = 0;
            });
            
            //profile section
            $('body').on('click', '#do_status_post', function() {

                event.preventDefault();
                actionType = $( this ).attr("href");
                //alert(actionType);
                window.location.href = baseurl+'android/api/v3.0/previousfeed';
                //return false;

            });
            
            $('body').on('click', '#do_photo_video_post', function() {

                event.preventDefault();
                actionType = $( this ).attr("href");
                //alert(actionType);
                window.location.href = baseurl+'android/api/v3.0/previousfeed';
                //return false;

            });
            
            $('body').on('click', '#do_check_in_post', function() {

                event.preventDefault();
                actionType = $( this ).attr("href");
                //alert(actionType);
                window.location.href = baseurl+'android/api/v3.0/previousfeed';
                //return false;

            });
            
            //end profile section
            
            
            
    });
