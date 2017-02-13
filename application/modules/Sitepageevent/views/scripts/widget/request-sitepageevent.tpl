<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: request-sitepageevent.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  var sitepageeventWidgetRequestSend = function(action, event_id, notification_id, rsvp)
  {
    var url;
    if( action == 'accept' )
    {
      url = '<?php echo $this->url(array('controller' => 'index', 'action' => 'accept'), 'sitepageevent_extended', true) ?>';
    }
    else if( action == 'reject' )
    {
      url = '<?php echo $this->url(array('controller' => 'index', 'action' => 'reject'), 'sitepageevent_extended', true) ?>';
    }
    else
    {
      return false;
    }

    (new Request.JSON({
      'url' : url,
      'data' : {
        'event_id' : event_id,
        'format' : 'json',
        'rsvp' : rsvp
        //'token' : '<?php //echo $this->token()  ?>'
      },
      'onSuccess' : function(responseJSON)
      {
        if( !responseJSON.status )
        {
          $('sitepageevent-widget-request-' + notification_id).innerHTML = responseJSON.error;
        }
        else
        {
          $('sitepageevent-widget-request-' + notification_id).innerHTML = responseJSON.message;
        }
      }
    })).send();
  }
</script>

<li id="sitepageevent-widget-request-<?php echo $this->notification->notification_id ?>">
  <?php echo $this->itemPhoto($this->notification->getObject(), 'thumb.icon') ?>
  <div>
    <div>
      <?php echo $this->translate('%1$s has invited you to the sitepage event %2$s', $this->htmlLink($this->notification->getSubject()->getHref(), $this->notification->getSubject()->getTitle()), $this->htmlLink($this->notification->getObject()->getHref(), $this->notification->getObject()->getTitle())); ?>
    </div>
    <div>
      <button type="submit" onclick='sitepageeventWidgetRequestSend("accept", <?php echo $this->notification->getObject()->getIdentity() ?>, <?php echo $this->notification->notification_id ?>, 2)'>
        <?php echo $this->translate('Attending'); ?>
      </button>
      <button type="submit" onclick='sitepageeventWidgetRequestSend("accept", <?php echo $this->notification->getObject()->getIdentity() ?>, <?php echo $this->notification->notification_id ?>, 1)'>
        <?php echo $this->translate('Maybe Attending'); ?>
      </button>
      <?php echo $this->translate('or'); ?>
      <a href="javascript:void(0);" onclick='sitepageeventWidgetRequestSend("reject", <?php echo $this->notification->getObject()->getIdentity() ?>, <?php echo $this->notification->notification_id ?>)'>
        <?php echo $this->translate('ignore request'); ?>
      </a>
    </div>
  </div>
</li>