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
        <div class="wrap">                        
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
