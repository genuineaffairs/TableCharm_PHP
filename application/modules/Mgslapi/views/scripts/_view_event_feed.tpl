<?php $staticBaseUrl = $this->serverUrl((string) $this->layout()->staticBaseUrl);  ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta name="apple-mobile-web-app-capable" content="yes">
            <meta name="viewport" content="width=device-width, target-densitydpi=device-dpi, initial-scale=0, maximum-scale=0, user-scalable=no" />
            <title>Untitled Document</title>
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
            <div class="event_holder">
            <div class="match_info_container">
                <div class="team_photo">
                    <div class="img_container">
                        <img src="<?php echo Engine_Api::_()->mgslapi()->getItemPhotoUrl($this->subject);  ?>" alt="" title="" />
                    </div>
                </div>
                <?php
                    // Convert the dates for the viewer
                    $startDateObject = new Zend_Date(strtotime($this->subject->starttime));
                    $endDateObject = new Zend_Date(strtotime($this->subject->endtime));
                    if( $this->viewer && $this->viewer->getIdentity() ) {
                      $tz = $this->viewer->timezone;
                      $startDateObject->setTimezone($tz);
                      $endDateObject->setTimezone($tz);
                    }
                ?>
                <div class="team_details">
                    <h3 class="event_title"><?php echo $this->subject->getTitle() ?></h3>
                    <span class="time_stamp"><?php echo $this->translate('%1$s at %2$s',$this->locale()->toDate($startDateObject), $this->locale()->toTime($startDateObject)) ?></span>
                    <span class="time_stamp"><?php echo $this->translate('%1$s at %2$s',$this->locale()->toDate($endDateObject), $this->locale()->toTime($endDateObject)) ?></span>
                    <?php if( !empty($this->subject->location) ): ?>
                    <div class="event_location">
                        <span>Where:</span> <?php echo $this->subject->location ?>
                    </div>
                    <?php endif ?>
                </div>
                <div class="clear"></div>
            </div>
            <div class="event_action_menu">
                <ul>
                    <li><a href="#" id="do_attending">Attending</a></li>
                    <li><a href="#" id="do_maybe_attending">Maybe Attending</a></li>
                    <li><a href="#" id="do_not_attending">Not Attending </a></li>
                </ul>
            </div>
        </div>

        <div class="event_details_holder">
        <?php if( !empty($this->subject->description) ): ?>
            <p class="event_desc"><?php echo nl2br($this->subject->description) ?></p>
        <?php endif ?>
            

            <table>
<!--                <tr>
                    <td>Fee: </td>
                    <td>10$</td>
                </tr>-->
                <?php if( !empty($this->subject->location) ): ?>
                    <tr>
                        <td>Where:</td>
                        <td><?php echo $this->subject->location ?></td>
                    </tr>
                <?php endif ?>
                
                <?php if( !empty($this->subject->host) ): ?>
                    <?php if( $this->subject->host != $this->subject->getParent()->getTitle()): ?>
                        <tr>
                            <td>Host:</td>
                            <td><?php echo $this->subject->host ?></td>
                        </tr>
                      <?php endif ?>
                    <tr>
                        <td>Led by:</td>
                        <td><?php echo strip_tags($this->subject->getParent()->__toString())?></td>
                    </tr>
                <?php endif; ?>
                
                <?php if( !empty($this->subject->category_id) ): ?>
                <tr>
                    <td>Category:</td>
                    <td><?php echo $this->subject->categoryName() ?></td>
                </tr>
                <?php endif ?>
                
<!--                <tr>
                    <td>Email: </td>
                    <td> asdfasdf@dadf.com</td>
                </tr>
                <tr>
                    <td>Url:</td>
                    <td>http://blogorblogor.com</td>
                </tr>
                <tr>
                    <td>Phone:</td>
                    <td>+88 01265498546</td>
                </tr>
                <tr>
                    <td>Contact:</td>
                    <td>Lorem Ipsum is simply dummy text</td>
                </tr>-->
                <tr>
                    <td>RSVPs:</td>
                    <td><?php echo $this->locale()->toNumber($this->subject->getAttendingCount()) ?> attending<br />
                        <?php echo $this->locale()->toNumber($this->subject->getMaybeCount()) ?> maybe attending<br />
                        <?php echo $this->locale()->toNumber($this->subject->getNotAttendingCount()) ?> not attending<br />
                        <?php echo $this->locale()->toNumber($this->subject->getAwaitingReplyCount()) ?> awaiting reply<br />
                    </td>
                </tr>
            </table>
        </div>
        <!-- event content -->
        <div class="event_option event_3Menu event_menu_option">
            <ul>
                <li><a id="do_status_post" href="#statusAction">Status</a></li>
                <li><a id="do_photo_video_post" href="#photoVideoAction">Photo / Video</a></li>
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
