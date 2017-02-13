<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: view.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php
if (!$this->video || $this->video->status != 1):
  echo $this->translate('The video you are looking for does not exist or has not been processed yet.');
  return; // Do no render the rest of the script in this mode
endif;
?>

<script type="text/javascript">
  function rating_over(rating) {
    if ($.mobile.activePage.data('rated') == 1) {
      $.mobile.activePage.find('#rating_text').html("<?php echo $this->translate('you have already rated'); ?>");
    }
    else if ( <?php echo $this->viewer_id; ?> === 0) {
      $.mobile.activePage.find('#rating_text').html("<?php echo $this->translate('Only logged-in user can rate'); ?>");
    }
    else {
      $.mobile.activePage.find('#rating_text').html("<?php echo $this->translate('Please click to rate'); ?>");
      for (var x = 1; x <= 5; x++) {
        if (x <= rating) {
          $.mobile.activePage.find('#rate_' + x).attr('class', 'rating_star_big_generic rating_star_big');
        } else {
          $.mobile.activePage.find('#rate_' + x).attr('class', 'rating_star_big_generic rating_star_big_disabled');
        }
      }
    }
  }

  function rating_out() {
    $.mobile.activePage.find('#rating_text').html(" <?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count), $this->locale()->toNumber($this->rating_count)) ?>");
       if ($.mobile.activePage.data('pre_rate') !== 0) {
         set_rating();
       }
       else {
         for (var x = 1; x <= 5; x++) {
           $.mobile.activePage.find('#rate_' + x).attr('class', 'rating_star_big_generic rating_star_big_disabled');
         }
       }
  }

  function set_rating() {
    var rating = $.mobile.activePage.data('pre_rate');
        var current_total_rate = $.mobile.activePage.data('current_total_rate');
        if (current_total_rate) {
          var current_total_rate = $.mobile.activePage.data('current_total_rate');
          if (current_total_rate === 1) {
            $.mobile.activePage.find('#rating_text').html(current_total_rate + '<?php echo $this->string()->escapeJavascript($this->translate(" rating")) ?>');
          }
          else {
            $.mobile.activePage.find('#rating_text').html(current_total_rate + '<?php echo $this->string()->escapeJavascript($this->translate(" rating")) ?>');
          }
        }
        else {
          $.mobile.activePage.find('#rating_text').html("<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count), $this->locale()->toNumber($this->rating_count)) ?>");
        }

        for (var x = 1; x <= parseInt(rating); x++) {
          $.mobile.activePage.find('#rate_' + x).attr('class', 'rating_star_big_generic rating_star_big');
        }

        for (var x = parseInt(rating) + 1; x <= 5; x++) {
          $.mobile.activePage.find('#rate_' + x).attr('class', 'rating_star_big_generic rating_star_big_disabled');
        }

        var remainder = Math.round(rating) - rating;
        if (remainder <= 0.5 && remainder != 0) {
          var last = parseInt(rating) + 1;
          $.mobile.activePage.find('#rate_' + last).attr('class', 'rating_star_big_generic rating_star_big_half');
        }
  }

  function videoRate(rating,video_id) {
 $.mobile.activePage.find('#rating_text').html("<?php echo $this->translate('Thank you for rating!'); ?>");
    for (var x = 1; x <= 5; x++) {
      $.mobile.activePage.find('#rate_' + x).attr('onclick', '');
    }
    sm4.core.request.send({
      type: "POST",
      dataType: "json",
      'url': '<?php echo $this->url(array('module' => 'video', 'controller' => 'index', 'action' => 'rate'), 'default', true) ?>',
      'data': {
        'format': 'json',
        'rating': rating,
        'video_id' : video_id
      },
      beforeSend: function() {
        $.mobile.activePage.data('rated', 1);
        var total_votes = $.mobile.activePage.data('total_votes');
        total_votes = total_votes+1;
        var pre_rate = ($.mobile.activePage.data('pre_rate') + rating) / total_votes;
        $.mobile.activePage.data('total_votes', total_votes);
        $.mobile.activePage.data('pre_rate', pre_rate);
        set_rating();
      },
      success: function(response)
      {//add(set_rating);
        $.mobile.activePage.find('#rating_text').html(response[0].total + '<?php echo $this->string()->escapeJavascript($this->translate("rating")) ?>');
        $.mobile.activePage.data('current_total_rate', response[0].total);
      }
    });

  }

  sm4.core.runonce.add(function() {
    $.mobile.activePage.data('pre_rate',<?php echo $this->video->rating; ?>);
    $.mobile.activePage.data('rated', '<?php echo $this->rated; ?>');
    $.mobile.activePage.data('total_votes',<?php echo $this->rating_count; ?>);
    set_rating();
  });

  function tagAction(tag){
    $.mobile.activePage.find('#tag').val(tag);
    $.mobile.activePage.find('#filter_form').submit();
  }

</script>
<div class="ui-page-content">
  <form id='filter_form' class='global_form_box' method='post' action='<?php echo $this->url(array('module' => 'video', 'controller' => 'index', 'action' => 'browse'), 'default', true) ?>' style='display:none;'>
    <input type="hidden" id="tag" name="tag" value=""/>
  </form>

  <div class="sm-ui-cont-head">
    <div class="sm-ui-cont-author-photo">
<?php echo $this->htmlLink($this->subject()->getOwner(), $this->itemPhoto($this->subject()->getOwner(), 'thumb.icon')) ?>
    </div>
    <div class="sm-ui-cont-cont-info">
      <div class="sm-ui-cont-author-name">
<?php echo $this->htmlLink($this->subject()->getOwner(), $this->subject()->getOwner()->getTitle()) ?>
      </div>
      <div class="sm-ui-cont-cont-date">
        <?php echo $this->timestamp($this->video->creation_date) ?> 
        <?php if ($this->category): ?>
          - 
          <?php
          echo $this->htmlLink(array(
              'route' => 'video_general',
              'QUERY' => array('category' => $this->category->category_id)
                  ), $this->translate($this->category->category_name)
          )
          ?>
        <?php endif; ?>
        <?php if (count($this->videoTags)): ?>
          -
          <?php foreach ($this->videoTags as $tag): ?>
            <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>#<?php echo $tag->getTag()->text ?></a>&nbsp;
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <div class="sm-ui-cont-cont-date">
<?php echo $this->translate(array('%s view', '%s views', $this->video->view_count), $this->locale()->toNumber($this->video->view_count)) ?>
      </div>
    </div>
  </div>
  <div class="sm-ui-video-view">
    <?php if (!empty($this->video->description)): ?>
      <div class="sm-ui-cont-cont-des">
      <?php echo nl2br($this->video->description) ?>
      </div>
        <?php endif ?>
        <?php if ($this->video->duration): ?>
      <div class="sm-ui-video-duration">
        <strong>
          <?php
          if ($this->video->duration >= 3600) {
            $duration = gmdate("H:i:s", $this->video->duration);
          } else {
            $duration = gmdate("i:s", $this->video->duration);
          }
          echo $duration;
          ?>
        </strong>	
      </div>
    <?php endif ?>
      <!--VIDEO PLAYER CONDITION FOR MOBILE APP AND MOBILE.-->
      <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
      <?php if ($this->video->type == 3): ?>      
      <a onclick="window.videoPlayer.player('<?php echo $this->video_location ?>')" >
          <div class="sm-content-list ui-listgrid-view">
                      <?php
                      if ($this->video->photo_id) {
                          echo $this->itemPhoto($this->video, 'thumb.profile');
                      } else {
                          echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Video/externals/images/video.png">';
                      }
                      ?>
                      <div class="ui-listview-play-btn"><i class="ui-icon ui-icon-play"></i></div>
                      <?php if ($this->video->duration): ?>
                          <p class="ui-li-aside">
                              <?php
                              if ($this->video->duration >= 3600) {
                                  $duration = gmdate("H:i:s", $this->video->duration);
                              } else {
                                  $duration = gmdate("i:s", $this->video->duration);
                              }
                              echo $duration;
                              ?>
                          </p>
                      <?php endif ?>
          </div></a>
      <?php elseif ($this->video->type == 1): ?>
      <a onclick="window.videoPlayer.youtube('<?php echo $this->video->code ?>')" >
      <div class="sm-content-list ui-listgrid-view">
            <?php
            if ($this->video->photo_id) :
              echo $this->itemPhoto($this->video, 'thumb.profile');
            else:
              echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Video/externals/images/video.png">';
            endif;
            ?>
            <div class="ui-listview-play-btn"><i class="ui-icon ui-icon-play"></i></div>
            <?php if ($this->video->duration): ?>
                <p class="ui-li-aside">
                    <?php
                    if ($this->video->duration >= 3600) {
                        $duration = gmdate("H:i:s", $this->video->duration);
                    } else {
                        $duration = gmdate("i:s", $this->video->duration);
                    }
                    echo $duration;
                    ?>
                </p>
            <?php endif ?>
          </div></a>
       <?php else: ?>
        <div class="sm-ui-video-embed"><?php echo $this->videoEmbedded ?></div>
       <?php endif; ?>
      <?php else:?>
          <?php if ($this->video->type == 3): ?>
        <div class="tip clr">
          <span><?php echo $this->translate("For Playing this video, the Flash Plugin is required. But Mobile / Tablet device browser does not support Flash Plugin. So you can not play this video now.");?></span>
        </div>
            <div class="video_embed" class="sm-ui-video-embed" style="display:none;"></div>
          <?php else: ?>
          <div class="sm-ui-video-embed"><?php echo $this->videoEmbedded ?></div>
          <?php endif; ?>
      <?php endif; ?>
    <div class="sm-ui-video-rating">
      <div id="video_rating" class="rating" onmouseout="rating_out();" valign="top">
        <span id="rate_1" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?> onclick="videoRate(1,<?php echo $this->video->video_id; ?>);"<?php endif; ?> onmouseover="rating_over(1);"></span>
        <span id="rate_2" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?> onclick="videoRate(2,<?php echo $this->video->video_id; ?>);"<?php endif; ?> onmouseover="rating_over(2);"></span>
        <span id="rate_3" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?> onclick="videoRate(3,<?php echo $this->video->video_id; ?>);"<?php endif; ?> onmouseover="rating_over(3);"></span>
        <span id="rate_4" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?> onclick="videoRate(4,<?php echo $this->video->video_id; ?>);"<?php endif; ?> onmouseover="rating_over(4);"></span>
        <span id="rate_5" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?> onclick="videoRate(5,<?php echo $this->video->video_id; ?>);"<?php endif; ?> onmouseover="rating_over(5);"></span>
        <div id="rating_text" class="rating_text"><?php echo $this->translate('click to rate'); ?></div>
      </div>
    </div>	

    <div data-type="horizontal" data-role="controlgroup" valign="top" class="sm-ui-video-op">
      <?php if ($this->can_embed): ?>
        <?php
        echo $this->htmlLink(array(
            'module' => 'video',
            'controller' => 'video',
            'action' => 'embed',
            'route' => 'default',
            'id' => $this->video->getIdentity(),
            'format' => 'smoothbox'
                ), $this->translate("Embed"), array(
            'class' => 'smoothbox',
            'data-role' => "button", 'data-icon' => "wrench", "data-iconpos" => "left", "data-inset" => 'false', 'data-mini' => "true", 'data-corners' => "true", 'data-shadow' => "true"
        ));
        ?>
      <?php endif ?>
      <?php if (Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
        <?php
        echo $this->htmlLink(array(
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'share',
            'route' => 'default',
            'type' => 'video',
            'id' => $this->video->getIdentity(),
            'format' => 'smoothbox',
                ), $this->translate("Share"), array(
            'class' => 'smoothbox',
            'data-role' => "button", 'data-icon' => "comments-alt", "data-iconpos" => "left", "data-inset" => 'false', 'data-mini' => "true", 'data-corners' => "true", 'data-shadow' => "true"
        ));
        ?>
        <?php
        echo $this->htmlLink(array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'route' => 'default',
            'subject' => $this->video->getGuid(),
            'format' => 'smoothbox'
                ), $this->translate("Report"), array(
            'class' => 'smoothbox',
            'data-role' => "button", 'data-icon' => "flag", "data-iconpos" => "left", "data-inset" => 'false', 'data-mini' => "true", 'data-corners' => "true", 'data-shadow' => "true"
        ));
        ?>
<?php endif ?>
    </div>
  </div>	
</div>