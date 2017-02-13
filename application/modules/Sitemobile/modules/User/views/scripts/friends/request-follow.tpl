<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: request-follow.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<script type="text/javascript">
  var userWidgetRequestSend = function(action, user_id, notification_id)
  {
    var url;
    if( action == 'confirm' )
    {
      url = '<?php echo $this->url(array('controller' => 'friends', 'action' => 'confirm'), 'user_extended', true) ?>';
    }
    else if( action == 'reject' )
    {
      url = '<?php echo $this->url(array('controller' => 'friends', 'action' => 'ignore'), 'user_extended', true) ?>';
    }
    else
    {
      return false;
    }

    sm4.core.request.send({
      type: "POST", 
      dataType: "json", 
      url : url,
      data: {
        'token' : '<?php echo $this->token() ?>',
        'user_id' : user_id,
        'format':'json'
      },
      success: function( responseJSON, textStatus, jqXHR) {
        if( !responseJSON.status )
        {
          $.mobile.activePage.find('#user-widget-request-' + notification_id).html(responseJSON.error);
        }
        else
        {
          $.mobile.activePage.find('#user-widget-request-' + notification_id).html(responseJSON.message);
        }
        $.mobile.activePage.find('#user-widget-request-' + notification_id).removeClass('ui-li-has-thumb');
        $.mobile.activePage.find('#user-widget-request-' + notification_id).closest('ul').listview().listview('refresh');
        $.mobile.activePage.find('#user-widget-request-' + notification_id).find('a').addClass('ui-link');
      }
    });

  }
</script>

<li id="user-widget-request-<?php echo $this->notification->notification_id ?>">
  <div class="ui-link-inherit"> 
    <?php echo $this->itemPhoto($this->notification->getSubject(), 'thumb.icon') ?>
    <h3><?php echo $this->translate('%1$s has requested to follow you.', $this->htmlLink($this->notification->getSubject()->getHref(), $this->notification->getSubject()->getTitle())); ?></h3>
    <p class="sm-ui-lists-action">
      <a href="javascript:void(0);" onclick='userWidgetRequestSend("confirm", <?php echo $this->string()->escapeJavascript($this->notification->getSubject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>)'>
        <strong><?php echo $this->translate('Allow'); ?></strong>
      </a>
      <?php echo $this->translate('or'); ?>
      <a href="javascript:void(0);" onclick='userWidgetRequestSend("reject", <?php echo $this->string()->escapeJavascript($this->notification->getSubject()->getIdentity()) ?>, <?php echo $this->notification->notification_id ?>)'>
        <?php echo $this->translate('ignore request'); ?>
      </a>
    </p>
  </div>
</li>