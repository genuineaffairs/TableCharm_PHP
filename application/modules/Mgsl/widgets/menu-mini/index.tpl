<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<div id='core_menu_mini_menu'>
  <?php
    // Reverse the navigation order (they're floating right)
    $count = count($this->navigation);
    foreach( $this->navigation->getPages() as $item ) $item->setOrder(--$count);
  ?>
  <ul>
    <?php if($this->search_check):?>
      <li id="global_search_form_container">
        <form id="global_search_form" action="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>" method="get">
          <input type='text' class='text suggested' name='query' id='global_search_field' size='20' maxlength='100' alt='<?php echo $this->translate('Search') ?>' />
        </form>
      </li>
    <?php endif;?>

    <?php if( $this->viewer->getIdentity()) :?>
    <li id='core_menu_mini_menu_update'>
      <span onclick="togglePulldown(this, showNotifications);" style="display: inline-block;" id="updates_pulldown"class="mini_menu_pulldown inactive">
        <div class="pulldown_contents_wrapper">
          <div class="pulldown_contents">
            <ul class="notifications_menu" id="notifications_menu">
              <div class="notifications_loading" id="notifications_loading">
                <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='float:left; margin-right: 5px;' />
                <?php echo $this->translate("Loading ...") ?>
              </div>
            </ul>
          </div>
          <div class="pulldown_options">
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'notifications'),
               $this->translate('View All Updates'),
               array('id' => 'notifications_viewall_link')) ?>
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Mark All Read'), array(
              'id' => 'notifications_markread_link',
            )) ?>
          </div>
        </div>
        <a href="javascript:void(0);" id="updates_toggle" <?php if( $this->notificationCount ):?> class="new_updates"<?php endif;?>><?php echo $this->translate(array('%s Update', '%s Updates', $this->notificationCount), $this->locale()->toNumber($this->notificationCount)) ?></a>
      </span>
    </li>
    <?php endif; ?>

    <li>
      <span onclick="togglePulldown(this, followCogLink);" style="display: inline-block;" id="cog_pulldown" class="mini_menu_pulldown inactive">
        <div class="pulldown_contents_wrapper">
          <div class="pulldown_contents">
            <ul class="cog_menu" id="cog_menu">
              <?php foreach( $this->navigation as $item ): ?>
                <li><?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), array_filter(array(
                  'class' => ( !empty($item->class) ? $item->class : null ),
                  'alt' => ( !empty($item->alt) ? $item->alt : null ),
                  'target' => ( !empty($item->target) ? $item->target : null ),
                ))) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
        <a href="javascript:void(0);"></a>
      </span>
    </li>
  </ul>
</div>


<script type='text/javascript'>
  var notificationUpdater;

  en4.core.runonce.add(function(){
    if($('global_search_field')){
      new OverText($('global_search_field'), {
        poll: true,
        pollInterval: 500,
        positionOptions: {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });
    }

    if($('notifications_markread_link')){
      $('notifications_markread_link').addEvent('click', function() {
        //$('notifications_markread').setStyle('display', 'none');
        en4.activity.hideNotifications('<?php echo $this->string()->escapeJavascript($this->translate("0 Updates"));?>');
      });
    }

    <?php if ($this->updateSettings && $this->viewer->getIdentity()): ?>
    notificationUpdater = new NotificationUpdateHandler({
              'delay' : <?php echo $this->updateSettings;?>
            });
    notificationUpdater.start();
    window._notificationUpdater = notificationUpdater;
    <?php endif;?>
  });

  var togglePulldown = function(pulldownToToggle, activateFn) {
    $$('#core_menu_mini_menu .mini_menu_pulldown').each(function(pulldown) {
      if (pulldownToToggle === pulldown && pulldownToToggle.hasClass('inactive')) {
        pulldownToToggle.removeClass('inactive').addClass('active');
        if (activateFn)
        {
          activateFn();
        }
      }
      else {
        pulldown.removeClass('active').addClass('inactive'); // i.e. de-activate any other active pulldowns
      }
    });
  }

  var showNotifications = function() {
    en4.activity.updateNotifications();
    new Request.HTML({
      'url' : en4.core.baseUrl + 'activity/notifications/pulldown',
      'data' : {
        'format' : 'html',
        'page' : 1
      },
      'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if( responseHTML ) {
          // hide loading icon
          if($('notifications_loading')) $('notifications_loading').setStyle('display', 'none');

          $('notifications_menu').innerHTML = responseHTML;
          $('notifications_menu').addEvent('click', function(event){
            event.stop(); //Prevents the browser from following the link.

            var current_link = event.target;
            var notification_li = $(current_link).getParent('li');

            // if this is true, then the user clicked on the li element itself
            if( notification_li.id == 'core_menu_mini_menu_update' ) {
              notification_li = current_link;
            }

            var forward_link;
            if( current_link.get('href') ) {
              forward_link = current_link.get('href');
            } else{
              forward_link = $(current_link).getElements('a:last-child').get('href');
            }

            if( notification_li.get('class') == 'notifications_unread' ){
              notification_li.removeClass('notifications_unread');
              en4.core.request.send(new Request.JSON({
                url : en4.core.baseUrl + 'activity/notifications/markread',
                data : {
                  format     : 'json',
                  'actionid' : notification_li.get('value')
                },
                onSuccess : function() {
                  window.location = forward_link;
                }
              }));
            } else {
              window.location = forward_link;
            }
          });
        } else {
          $('notifications_loading').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("You have no new updates."));?>';
        }
      }
    }).send();
  };

  var followCogLink = function() {
    // ensure that clicking on a li element will go to the URL of its child link element
    $('cog_menu').addEvent('click', function(event) {
      event.stop(); // prevents browser from following the link
      var target = event.target;
      window.location = target.get('href') ?
        target.get('href') :
        $(target).getElements('a:last-child').get('href');
    });
  };

  /*
  function focusSearch() {
    if(document.getElementById('global_search_field').value == 'Search') {
      document.getElementById('global_search_field').value = '';
      document.getElementById('global_search_field').className = 'text';
    }
  }
  function blurSearch() {
    if(document.getElementById('global_search_field').value == '') {
      document.getElementById('global_search_field').value = 'Search';
      document.getElementById('global_search_field').className = 'text suggested';
    }
  }
  */
</script>