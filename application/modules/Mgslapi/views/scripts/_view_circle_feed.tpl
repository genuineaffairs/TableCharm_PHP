<?php $staticBaseUrl = $this->serverUrl((string) $this->layout()->staticBaseUrl);  ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta name="apple-mobile-web-app-capable" content="yes">
            <meta name="viewport" content="width=device-width, target-densitydpi=device-dpi, initial-scale=0, maximum-scale=0, user-scalable=no" />
            <title>Cicle feed</title>
            <link href="<?php echo $staticBaseUrl .'application/modules/Mgslapi/externals/styles/styles.css'?>" rel="stylesheet" type="text/css" />
            <script type="text/javascript">
                window.baseurl = '<?php echo $this->serverUrl((string) $this->layout()->staticBaseUrl); ?>';
            </script>
            <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
            <script src='<?php echo $staticBaseUrl .'application/modules/Mgslapi/externals/scripts/swipe.js'?>'></script>
            <script src='<?php echo $staticBaseUrl .'application/modules/Mgslapi/externals/scripts/jquery-base64/jquery.base64.min.js'?>'></script>
            <script src='<?php echo $staticBaseUrl .'application/modules/Mgslapi/externals/scripts/home.js'?>'></script>
    </head>
    <body>       
        <?php 
        //echo $this->subject->getCategory(); exit;
        ?>
      <div class="match_info">
            <div class="team_photo">
                <div class="img_container">
                    <img src="<?php echo Engine_Api::_()->mgslapi()->getItemPhotoUrl($this->subject);  ?>" alt="" title="" />
                </div>
            </div>
            <div class="team_details">
                <h3 class="title"><?php echo $this->subject->getTitle() ?></h3>
                <ul class="team_info">
                    <?php if($this->subject->category_id): ?>
                        <li><img src="<?php echo $staticBaseUrl .'application/modules/Mgslapi/externals/images/cetegory_icon.png'?>" alt="" title="" /><span><?php echo $this->subject->getCategory()->getTitle(); ?></span></li>
                    <?php endif; ?>
                    <?php if($this->subject->location): ?>
                        <li><img src="<?php echo $staticBaseUrl .'application/modules/Mgslapi/externals/images/location_icon.png'?>" alt="" title="" /><span><?php echo $this->subject->location; ?></span></li>
                    <?php endif; ?>
                    <?php if($this->subject->website): ?>
                        <li><img src="<?php echo $staticBaseUrl .'application/modules/Mgslapi/externals/images/web_icon.png'?>" alt="" title="" /><span><?php echo $this->subject->website; ?></span></li>
                    <?php endif; ?>
                    <?php if($this->subject->phone): ?>
                        <li class="nomargin"><img src="<?php echo $staticBaseUrl .'application/modules/Mgslapi/externals/images/cell_icon.png'?>" alt="" title="" /><span><?php echo $this->subject->phone; ?></span></li>                    
                    <?php endif; ?>
                </ul>
                <ul class="action_box">
                    <li id="circle_like_value"><?php echo $this->subject->like_count ?> Like</li>
                    <li><?php echo $this->subject->follow_count ?> followers</li>
                    <li><?php echo $this->subject->like_count ?> member</li>
                </ul>
            </div>
            <div class="clear"></div>
            <div class="action_menu">
                <ul>
                    <?php   $resource_id = $this->subject->getIdentity();
                        $resource_type = $this->subject->getType(); 
                    ?>
                    <?php $hasLike = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($resource_type, $resource_id);  ?>                    
                    <?php if($hasLike):?>
                        <li><a href="<?php echo $hasLike[0]['like_id']; ?>" id="do_unlike_circle"><?php echo $this->translate('Unlike');?></a></li>
                    <?php else:?>
                        <li><a href="0" id="do_like_circle"><?php echo $this->translate('Like');?></a></li>
                    <?php endif;?>
                    <?php $isFollow = $this->subject->follows()->isFollow($this->viewer); ?>
                    <?php if($isFollow):?>
                        <li><a href="#" id="do_follow_circle"><?php echo $this->translate('Unfollowing') ?></a></li>
                    <?php else:?>
                        <li><a href="#" id="do_follow_circle"><?php echo $this->translate('Following') ?></a></li>
                    <?php endif;?>                    
                    <li><a href="#" id="do_join_circle">Join</a></li>
                    <li><a href="#circle_member_list@circle_id=1" id="do_members_circle">Members</a></li>
                </ul>
            </div>
        </div>
        
        <!-- event content -->
        <div class="event_option">
            <ul>
                <li><a id="do_status_post" href="#statusAction">Status</a></li>
                <li><a id="do_photo_video_post" href="#photoVideoAction">Photo / Video</a></li>
                <li><a id="do_check_in_post" href="#checkInAction">Check in</a></li>
                <li><a id="do_event_circle" href="#circle_event_list@circle_id=1">Events</a></li>
            </ul>
        </div>
        <!-- end event content -->
        
        <div class="wrap"> 
            
        <?php if (!$this->activity): ?>
            <?php if ($this->action_id): ?>
                <h2><?php echo $this->translate("Activity Item Not Found") ?></h2>
                <p><?php echo $this->translate("The page you have attempted to access could not be found.") ?></p>
                <?php return;
            else:
                ?>
                <div class="tip">
                    <span><?php echo $this->translate("Nothing has been posted here yet - be the first!") ?></span>
                </div>
                <?php return;
            endif;
            ?>
            <?php endif; ?>
            <?php
            echo $this->activityLoopApi($this->activity, array(
                'action_id' => $this->action_id,
                'viewAllComments' => $this->viewAllComments,
                'viewAllLikes' => $this->viewAllLikes,
                'getUpdate' => $this->getUpdate,
                'deviceType' => $this->deviceType,
                'viewer' => $this->viewer,
                'subject' => $this->subject                
            ))
            ?>
            <!--<div class="clear"></div>  
            <div class="lazyloaderpane"></div>-->

            <!--</ul>-->
        </div>  
    <script type="text/javascript">
        $(function(){
            $('.info a').attr('href','javascript:void(0)');
            $('.view_more_link').removeAttr('onclick');        
            $('.view_less_link').removeAttr('onclick');        
        })
        $('.view_more_link').click(function(){
           $(this).parent().hide(); 
           $(this).parent().next().show(); 
        });
        $('.view_less_link').click(function(){
           $(this).parent().hide(); 
           $(this).parent().prev().show(); 
        });
    </script>
    </body>
</html>
