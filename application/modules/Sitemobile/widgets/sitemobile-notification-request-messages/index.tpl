<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (!empty($this->loadingViaAjax)) : ?>
  <div class="sm-mini-menu">

    <a data-rel="#recent_requests"  href="javascript://" onclick='showRecentRequestContent();'  class="sm-mini-menu-icon popup_attach_notification" style="z-index: 9999">
      <span class="ui-icon ui-icon-user"></span>
      <?php if (!empty($this->requestsCount)): ?>
        <span class="count-bubble"><?php echo "$this->requestsCount" ?></span>
      <?php endif; ?>
    </a>
    <a href="javascript://" data-rel="#messages_popup" onclick='showMessagesContent();'  class="sm-mini-menu-icon popup_attach_notification" style="z-index: 9999">
      <span class="ui-icon ui-icon-envelope"></span>
      <?php if (!empty($this->messageCount)): ?>
        <span class="count-bubble"><?php echo "$this->messageCount" ?></span>
      <?php endif; ?>
    </a>
    <a href="javascript://" data-rel="#recent_activity" data-content="recent_activity" onclick='showUpdatesContent();' class="sm-mini-menu-icon popup_attach_notification" style="z-index: 9999">
      <span class="ui-icon ui-icon-globe"></span>
      <?php if (!empty($this->notificationCount)): ?>
        <span class="count-bubble"><?php echo "$this->notificationCount" ?></span>
      <?php endif; ?>
    </a>
    <?php if($this->showCartIcon):?>
     <a href="<?php echo $this->url(array('action' => 'cart'), 'sitestoreproduct_product_general', true); ?>"  class="sm-mini-menu-icon" style="z-index: 9999">
      <span class="ui-icon ui-icon-shopping-cart"></span>
      <?php if (!empty($this->cartProductCounts)): ?>
        <span class="count-bubble"><?php echo "$this->cartProductCounts" ?></span>
      <?php endif; ?>
    </a>
    <?php endif; ?>
  </div>
<?php else: ?>
  <div class="sm-mini-menu">
    <a href="<?php echo $this->url(array(), 'recent_request', true); ?>" class="sm-mini-menu-icon">
      <span class="ui-icon ui-icon-user"></span>
      <?php if (!empty($this->requestsCount)): ?>
        <span class="count-bubble"><?php echo "$this->requestsCount" ?></span>
      <?php endif; ?>
    </a>
    <a href="<?php echo $this->url(array('action' => 'inbox'), 'messages_general', true); ?>" class="sm-mini-menu-icon">
      <span class="ui-icon ui-icon-envelope"></span>
      <?php if (!empty($this->messageCount)): ?>
        <span class="count-bubble"><?php echo "$this->messageCount" ?></span>
      <?php endif; ?>
    </a>
    <a href="<?php echo $this->url(array(''), 'recent_activity', true); ?>" data-content="recent_activity"  class="sm-mini-menu-icon">
      <span class="ui-icon ui-icon-globe"></span>
      <?php if (!empty($this->notificationCount)): ?>
        <span class="count-bubble"><?php echo "$this->notificationCount" ?></span>
      <?php endif; ?>
    </a>
    <?php if($this->showCartIcon):?>
     <a href="<?php echo $this->url(array('action' => 'cart'), 'sitestoreproduct_product_general', true); ?>"  class="sm-mini-menu-icon" style="z-index: 9999">
      <span class="ui-icon ui-icon-shopping-cart"></span>
      <?php if (!empty($this->cartProductCounts)): ?>
        <span class="count-bubble"><?php echo "$this->cartProductCounts" ?></span>
      <?php endif; ?>
    </a>
    <?php endif; ?>
  </div>
<?php endif; ?>

<?php if (!empty($this->loadingViaAjax)) : ?>
    <div data-role="popup" id="recent_activity" class="sm-pulldown-contents" data-arrow="true" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-theme="none">
      <div class="popup_notification_arrow ui-icon ui-icon-caret-up sm-pulldown-arrow"></div>
			<div class="sm-ui-popup-top sm-pulldown-header">
				<?php echo $this->htmlLink(array('route' => 'user_extended', 'module' => 'user', 'controller' => 'settings', "action" => "notifications"), $this->translate(''), array('id' => '', 'class' => 'ui-icon ui-icon-cog')) ?>
				<span class="sm-pulldown-heading"><?php echo $this->translate("Notifications"); ?> </span>
			</div>
      <div class="sm-ui-popup-container-wrapper ui-body-c">
        <div class="sm-ui-popup-container sm-content-list" style="overflow:auto">	
          <ul class="notifications_menu sm-ui-lists" id="notifications_menu">
            <div class="sm-ui-popup-loading" id="notifications_loading"></div>
          </ul>
        </div>
        <div class="sm-ui-popup-notification-footer">
         <center> <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'notifications'),
					$this->translate('View All Updates'),
                 array('id' => '')) ?></center>
        </div>
      </div>
    </div>
  <div data-role="popup" id="recent_requests" class="sm-pulldown-contents" data-arrow="true" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15" data-theme="none">
    <div class="popup_notification_arrow ui-icon ui-icon-caret-up sm-pulldown-arrow"></div>
		<div class="sm-ui-popup-top sm-pulldown-header">
			<a href="<?php echo $this->url(array('action' => 'browse'), 'user_general', true); ?>" class="ui-icon ui-icon-plus"></a>
			<span class="sm-pulldown-heading"><?php echo $this->translate('Requests'); ?></span>
		</div>
    <div class="sm-ui-popup-container-wrapper ui-body-c">
      <div class="sm-ui-popup-container sm-content-list">
        <ul class="notifications_menu sm-ui-lists" id="recent_request_menu">
          <div class="sm-ui-popup-loading" id="recent_request_loading"></div>
        </ul>
      </div>
      <div class="sm-ui-popup-notification-footer">
         <center>
           <a href="<?php echo $this->url(array(), 'recent_request', true);?>"><?php echo $this->translate('View All Requests');?></a></center>
      </div>
    </div>
  </div>

  <div data-role="popup" id="messages_popup" class="sm-pulldown-contents" data-arrow="true" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15" data-theme="none">
    <div class="popup_notification_arrow ui-icon ui-icon-caret-up sm-pulldown-arrow"></div>
		<div class="sm-ui-popup-top sm-pulldown-header">
			<a href="<?php echo $this->url(array('action' => 'compose'), 'messages_general', true); ?>" class="ui-icon ui-icon-edit"></a>
			<span class="sm-pulldown-heading"><?php echo $this->translate("Messages"); ?></span>
		</div>
    <div class="sm-ui-popup-container-wrapper ui-body-c">
      <div class="sm-ui-popup-container">
        <ul class="notifications_menu" id="messages_popup_menu">
          <div class="sm-ui-popup-loading" id="messages_popup_loading"></div>
        </ul>
      </div>
      <div class="sm-ui-popup-notification-footer">
        <center>
          <a href="<?php echo $this->url(array('action' => 'inbox'), 'messages_general', true);?>"><?php echo $this->translate('View All Messages');?></a>
        </center>
      </div>
    </div>
  </div>

  <script type="text/javascript">         
    function showUpdatesContent() {
      //  var popup=$('#recent_activity-popup');
      // resizePopup(popup,{maxwidth:400,maxheight:410});
     // sm4.activity.notificationCountUpdate($.mobile.activePage);
      $.ajax({
        type: "GET",
        'url' : sm4.core.baseUrl + 'activity/notifications/pulldown',
        dataType: "html",
        'data' : {
          'format' : 'html',
          'page' : 1,
          'isajax': 1
        },
        'success' : function(responseHTML, textStatus, xhr) {
            sm4.activity.notificationCountUpdate($.mobile.activePage);
          $(document).data('loaded', true);
          $.mobile.activePage.find('#notification_loading').css('display', 'none');
          $.mobile.activePage.find('#notifications_menu').html(responseHTML).listview().listview('refresh');
          sm4.core.runonce.trigger();
          $.mobile.activePage.find('#recent_activity').trigger("create");
          //sm4.core.refreshPage();
          $.mobile.activePage.find('#notifications_menu').bind('click', function(event){
            $.mobile.showPageLoadingMsg();
            event.preventDefault(); //Prevents the browser from following the link.

            var current_link = $(event.target);

            var notification_li = $(current_link).parents('li');
                      
            var forward_link;
            if( current_link.attr('href') ) {
              forward_link = current_link.attr('href');
            } else{
              forward_link = notification_li.find('a:last-child').attr('href');
            }

            if(forward_link){
              $.ajax({
                type: "POST",
                dataType: "json",
                url : sm4.core.baseUrl + 'activity/notifications/markread',
                data : {
                  format     : 'json',
                  'actionid' : notification_li.attr('value')
                },
                success:function( response ) {
                  notification_li.removeClass('sm-ui-lists-highlighted');
                  $.mobile.changePage(forward_link);
                }});
            }
          });

        }
      })
    }

    function showRecentRequestContent() {
      //      $('#recent_request_menu').css('display', 'block');
      //      if ($(window).width() > 200)
      //        var width = 200;
      //      else 
      //        var width = $(window).width();
      //      $('#recent_request').parent().css({'width': (width - 20), 'height' : ($(window).height() - 10)})
      $.ajax({
        type: "GET",
        'url' : sm4.core.baseUrl + 'activity/notifications/pulldown-request',
        dataType: "html",
        'data' : {
          'format' : 'html',
          'page' : 1,
          'isajax': 1
        },
        'success' : function(responseHTML, textStatus, xhr) {
            sm4.activity.requestCountUpdate($.mobile.activePage);
          $(document).data('loaded', true);
          $.mobile.activePage.find('#recent_request_loading').css('display', 'none');
          $.mobile.activePage.find('#recent_request_menu').html(responseHTML);
          if($.mobile.activePage.find('#recent_request_menu').find('script').length > 1)
          $.mobile.activePage.find('#recent_request_menu').find('script').remove()
          $.mobile.activePage.find('#recent_request_menu').listview().listview('refresh');
          sm4.core.runonce.trigger();
          $.mobile.activePage.find('#recent_requests').trigger("create");
          //sm4.core.refreshPage();
        }});
    }

    function showMessagesContent() {
      //      $('#messages_popup_menu').css('display', 'block');
      //      if ($(window).width() > 200)
      //        var width = 200;
      //      else 
      //        var width = $(window).width();
      //      $('#messages_popup').parent().css({'width': (width - 20), 'height' : ($(window).height() - 10)})
      $.ajax({
        type: "GET",
        'url' : sm4.core.baseUrl + 'messages/inbox',
        dataType: "html",
        'data' : {
          'format' : 'html',
          'page' : 1,
          'isajax': 1
        },
        'success' : function(responseHTML, textStatus, xhr) {
          $(document).data('loaded', true);
          $.mobile.activePage.find('#messages_popup_loading').css('display', 'none');
          $.mobile.activePage.find('#messages_popup_menu').html(responseHTML).listview().listview('refresh');
          sm4.core.runonce.trigger();
          $.mobile.activePage.find('#messages_popup').trigger("create");
        //  sm4.core.refreshPage();
        }});
    }
  </script>
<?php endif; ?>