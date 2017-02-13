    var feed_id;
    var like_feed_id;
    var unlike_feed_id;
    var comment_feed_id;
    var photo_id;
    var last_comment_id;
    var auth_email_address;
    var auth_password;
    var baseurl;
    
    function touchStart(event) {
//        event.target.style.backgroundColor = "#d5d9e2";
    }
    
    function touchEnd(event) {
        //event.target.style.backgroundColor = "#eceff5";
        feed_id = event.target.id;
        
        if(event.target.id != 'do_unlike' && (event.target.id != 'do_like') && (event.target.id != 'do_ccoment') )
        {
            window.location.href = baseurl+'android/api/v3.0/previousfeed';
        }
    }
    
    function resetAll()
    {
        feed_id = -1;
        like_feed_id = -1;
        unlike_feed_id = -1;
        comment_feed_id = -1;
        photo_id = -1;
        return 'reset_all_completed';
    }
    
    function increaseCommentCounter()
    {
        var current_comment_value = $('#comment_value_'+ comment_feed_id).html();
        // increase comments
        $('#comment_value_'+ comment_feed_id).html(parseInt(current_comment_value) + 1);
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
    
    function setBaseURL(param)
    {
        baseurl = param;
    }
    
    
    function setPhotoId(param)
    {
        photo_id = param;
    }
    
    function removeAFeedById(param)
    {
        var element_name = 'div[id="' + param + '"]';
        $( element_name ).parents(".feed").remove();
    }   
    
    function hideViewPreviousCommentsDiv()
    {
        $( "#previous_comments" ).hide();
    }
    
    function insertLatestDataFromDevice(param)
    {
        //alert(JSON.stringify($.base64.decode(param)));
        $('#commentListData').append($.base64.decode(param));
        $("html, body").animate({scrollTop: $(document).height() - $(window).height()});
        last_comment_id = $(".commentsRow").last().attr("id");
    }
    
    function getNewestCommentId()
    {
        newest_comment_id = $(".commentsRow").last().attr("id");
        return 'newest_comment_id='+newest_comment_id;
    }
    
    function loadPreviousComments()
    {
        alert('email:'+auth_email_address+' password: '+auth_password+' feed_id: '+feed_id+' last_comment_id: '+last_comment_id);
//        $.post(baseurl+'android/api/v3.0/getPreviousCommentsOfAFeed',{email: auth_email_address, password: auth_password, feed_id: feed_id, last_comment_id: last_comment_id }, function(data) {
//        
//            $('.preLoader').remove();
//            //alert(JSON.stringify(data));
//            if (data) 
//            {
//
//                $('.previousComments').show();
//                $('#commentListData').prepend(data);
//
//                last_comment_id = $(".commentsRow").first().attr("id");
//                //alert(JSON.stringify($('.wrap').html()));
//
//            }
//            else
//            {
//                $('#previous_comments').hide();
//            }
//        });
        
        $.ajax({
            type: "POST",
            url: baseurl+'android/api/v3.0/getPreviousCommentsOfAFeed',
            dataType: "html",
            data: {email: auth_email_address, password: auth_password, feed_id: feed_id, last_comment_id: last_comment_id}
        }).done(function(html) {
            $('.preLoader').remove();
            if(html != '')
            {
                $('.previousComments').show();
                $('#commentListData').prepend(html);
                last_comment_id = $(".commentsRow").first().attr("id");
            }                
        }).fail(function() {
            $('.preLoader').remove();
             $('#previous_comments').hide();
        });
    }
    
    
    function loadPreviousCommentsOfAPhoto()
    {
        alert('email: '+auth_email_address +' pass:'+auth_password+' photo id: '+photo_id+ ' last_comment_id:'+ last_comment_id);
        //getPreviousCommentsOfAPhoto
//        $.post(baseurl+'android/api/v3.0/getPreviousCommentsOfAPhoto',{email: auth_email_address, password: auth_password, photo_id: photo_id, last_comment_id: last_comment_id }, function(data) {
//        
//            $('.preLoader').remove();
//            //alert(JSON.stringify(data));
//            if (data) 
//            {
//
//                $('.previousComments').show();
//                $('#commentListData').prepend(data);
//
//                last_comment_id = $(".commentsRow").first().attr("id");
//                //alert(JSON.stringify($('.wrap').html()));
//
//            }
//            else
//            {
//                $('#previous_comments').hide();
//            }
//            
//        });
        $.ajax({
            type: "POST",
            url: baseurl+'android/api/v3.0/getPreviousCommentsOfAPhoto',
            dataType: "html",
            data: {email: auth_email_address, password: auth_password, photo_id: photo_id, last_comment_id: last_comment_id}
        }).done(function(html) {
            $('.preLoader').remove();
            if(html != '')
            {
                $('.previousComments').show();
                $('#commentListData').prepend(html);
                last_comment_id = $(".commentsRow").first().attr("id");
            }
        }).fail(function() {
             $('#previous_comments').hide();
        });
    }
    
    
    
    function loadLatestComments()
    {
        alert('test');
        //alert('url: '+baseurl+'android/api/v3.0/getLatestCommentsOfAFeed');
        return false;
        //getLatestCommentsOfAFeed
//        $.post(baseurl+'android/api/v3.0/getLatestCommentsOfAFeed',{email: auth_email_address, password: auth_password, feed_id: feed_id }, function(data) {
//            //alert(JSON.stringify(data));
//            $('.preLoader').remove();
//            $('#previous_comments').show();
//            $('#commentListData').append(data);
//            last_comment_id = $( ".commentsRow" ).first().attr("id");
//            //alert(last_comment_id);
//        });       

        $.ajax({
            type: "POST",
            url: baseurl+'android/api/v3.0/getlatestcommentsofafeed',
            dataType: "html",
            data: {email: auth_email_address, password: auth_password, feed_id: feed_id }
        }).done(function(html) {
            $('.preLoader').remove();
            
            if(html != '')
            {
                $('#previous_comments').show();
                $('#commentListData').append(html);
                last_comment_id = $( ".commentsRow" ).first().attr("id");
            }         
                
        });
    }
    
    
    function loadLatestCommentsOfAPhoto()
    {
        //alert('email: '+auth_email_address+' password: '+ auth_password+ ' photo_id: '+photo_id);
        //getLatestCommentsOfAPhoto
//        $.post(baseurl+'android/api/v3.0/getLatestCommentsOfAPhoto',{email: auth_email_address, password: auth_password, photo_id: photo_id }, function(data) {
//            //alert(JSON.stringify(data));
//            $('.preLoader').remove();
//            $('#previous_comments').show();
//            $('#commentListData').append(data);
//            last_comment_id = $( ".commentsRow" ).first().attr("id");
//            //alert(last_comment_id);
//        });
        $.ajax({
            type: "POST",
            url: baseurl+'android/api/v3.0/getLatestCommentsOfAPhoto',
            dataType: "html",
            data: {email: auth_email_address, password: auth_password, photo_id: photo_id}
        }).done(function(html) {
            alert(JSON.stringify(html));
            $('.preLoader').remove();
            $('#previous_comments').show();
            $('#commentListData').append(html);
            last_comment_id = $( ".commentsRow" ).first().attr("id");
        });
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
            //alert("Data Loaded: " + html);
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
    
    
    $(document).ready(function(){
            alert('ready');
            return false;
            last_comment_id = -1;
            feed_id = $('.bottom').attr("id");
            //loadLatestComments();
                        
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
                return false;
                
                //window.location.href = baseurl+'android/api/v3.0/previousfeed';//'http://192.168.50.40/se/android/api/v3.0/previousfeed';
                
                
            });
            
    });