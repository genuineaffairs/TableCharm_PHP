<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>

<?php
    function full_url()
    {
        $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
        $protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0, strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")) . $s;
        $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
        return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
    }
?>

<?php
if (!$this->video || $this->video->status != 1):
    echo $this->translate('The video you are looking for does not exist or has not been processed yet.');
    return; // Do no render the rest of the script in this mode
endif;
?>

<?php
if ($this->video->type == Ynvideo_Plugin_Factory::getUploadedType() ||
    $this->video->type == Ynvideo_Plugin_Factory::getVideoURLType()) :
    $this->headScript()
            ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js');
?>
    <script type='text/javascript'>
        en4.core.runonce.add(function() {
            flashembed("video_embed", {
                src: "<?php echo $this->layout()->staticBaseUrl ?>externals/flowplayer/flowplayer-3.1.5.swf",
                width: 480,
                height: 386,
                wmode: 'transparent'
            }, {
                config: {
                    clip: {
                        url: "<?php echo $this->video_location; ?>",
                        autoPlay: false,
                        duration: "<?php echo $this->video->duration ?>",
                        autoBuffering: true
                    },
                    plugins: {
                        controls: {
                            background: '#000000',
                            bufferColor: '#333333',
                            progressColor: '#444444',
                            buttonColor: '#444444',
                            buttonOverColor: '#666666'
                        }
                    },
                    canvas: {
                        backgroundColor:'#000000'
                    }
                }
            });
        });

    </script>
<?php endif ?>

<script type="text/javascript">
    en4.core.runonce.add(function() {
        var pre_rate = <?php echo $this->video->rating; ?>;
        var rated = '<?php echo $this->rated; ?>';
        var video_id = <?php echo $this->video->video_id; ?>;
        var total_votes = <?php echo $this->rating_count; ?>;
        var viewer = <?php echo $this->viewer_id; ?>;

        var rating_over = window.rating_over = function(rating) {
            if( rated == 1 ) {
                $('rating_text').innerHTML = "<?php echo $this->translate('you already rated'); ?>";
                //set_rating();
            } else if( viewer == 0 ) {
                $('rating_text').innerHTML = "<?php echo $this->translate('please login to rate'); ?>";
            } else {
                $('rating_text').innerHTML = "<?php echo $this->translate('click to rate'); ?>";
                for(var x=1; x<=5; x++) {
                    if(x <= rating) {
                        $('rate_'+x).set('class', 'ynvideo_rating_star_big_generic ynvideo_rating_star_big');
                    } else {
                        $('rate_'+x).set('class', 'ynvideo_rating_star_big_generic ynvideo_rating_star_big_disabled');
                    }
                }
            }
        }

        var rating_out = window.rating_out = function() {
            //$('rating_text').innerHTML = " <?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count), $this->locale()->toNumber($this->rating_count)) ?>";
            $('rating_text').innerHTML = en4.core.language.translate(['%s rating', '%s ratings', total_votes], total_votes);
            
            if (pre_rate != 0){
                set_rating();
            }
            else {
                for(var x=1; x<=5; x++) {
                    $('rate_'+x).set('class', 'ynvideo_rating_star_big_generic ynvideo_rating_star_big_disabled');
                }
            }
        }

        var set_rating = window.set_rating = function() {
            var rating = pre_rate;
            $('rating_text').innerHTML = en4.core.language.translate(['%s rating', '%s ratings', total_votes], total_votes);
            //$('rating_text').innerHTML = "<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count), $this->locale()->toNumber($this->rating_count)) ?>";
            for(var x=1; x<=parseInt(rating); x++) {
                $('rate_'+x).set('class', 'ynvideo_rating_star_big_generic ynvideo_rating_star_big');
            }

            for(var x=parseInt(rating)+1; x<=5; x++) {
                $('rate_'+x).set('class', 'ynvideo_rating_star_big_generic ynvideo_rating_star_big_disabled');
            }

            var remainder = Math.round(rating)-rating;
            if (remainder <= 0.5 && remainder !=0){
                var last = parseInt(rating)+1;
                $('rate_'+last).set('class', 'ynvideo_rating_star_big_generic ynvideo_rating_star_big_half');
            }
        }

        var rate = window.rate = function(rating) {
            $('rating_text').innerHTML = "<?php echo $this->translate('Thanks for rating!'); ?>";
            for(var x=1; x<=5; x++) {
                $('rate_'+x).set('onclick', '');
            }
            (new Request.JSON({
                'format': 'json',
                'url' : '<?php echo $this->url(array('action' => 'rate'), 'video_general', true) ?>',
                'data' : {
                    'format' : 'json',
                    'rating' : rating,
                    'video_id': video_id
                },
                'onRequest' : function(){
                    rated = 1;
                    total_votes = total_votes+1;
                    pre_rate = (pre_rate+rating)/total_votes;
                    set_rating();
                },
                'onSuccess' : function(responseJSON, responseText)
                {
                	var total = responseJSON[0].total;
                	total_votes = responseJSON[0].total;
                	$('rating_text').innerHTML = en4.core.language.translate(['%s rating', '%s ratings', total_votes], total_votes);
                    //$('rating_text').innerHTML = responseJSON[0].total + " <?php $this->translate('ratings')?>";
                }
            })).send();

        }

        var tagAction = window.tagAction = function(tag){
            $('tag').value = tag;
            $('filter_form').submit();
        }

        set_rating();
    });
</script>

<div class="ynvideo_video_view_headline">
    <div class="ynvideo_video_info">
        <div class="ynvideo_video_view_title">
            <?php echo htmlspecialchars($this->video->getTitle()) ?>
        </div>
        <div class="video_desc">
            <?php echo $this->translate('Posted by') ?>
            <?php
            $poster = $this->video->getOwner();
            if ($poster) {
                echo $this->htmlLink($poster, $poster->getTitle());
            }
            ?>
        </div>
    </div>

    <!-- AddThis Button BEGIN -->
    <div class="addthis_toolbox addthis_default_style">
        <a class="addthis_button_facebook"></a>
        <a class="addthis_button_twitter"></a>
        <a class="addthis_button_email"></a>
        <a class="addthis_button_print"></a>
        <a class="addthis_button_google"></a>
        <a class="addthis_button_compact"></a>
        <a class="addthis_counter addthis_bubble_style"></a>
    </div>
    <script type="text/javascript"
    src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4f33860960a3e172"></script>
    <!-- AddThis Button END -->
    <div class="ynvideo_clear"></div>
</div>

<form id="filter_form" class="global_form_box" method="post"
      action="<?php echo $this->url(array('action' => 'list'), 'video_general', true) ?>" style='display:none;'>
    <input type="hidden" id="tag" name="tag" value=""/>
</form>

<div class="video_view video_view_container">
    <?php if ($this->video->type == Ynvideo_Plugin_Factory::getUploadedType() || $this->video->type == Ynvideo_Plugin_Factory::getVideoURLType()): ?>
        <div id="video_embed" class="video_embed"></div>
    <?php else: ?>
        <div class="video_embed">
            <?php
            echo $this->videoEmbedded
            ?>
        </div>
    <?php endif; ?>
    <div class="ynvideo_video_view_description ynvideo_video_show_less" id="ynvideo_video">
        <div class="left">
            <div class="video_date">
                <?php echo $this->translate('Posted') ?>
                <?php echo $this->timestamp($this->video->creation_date) ?>
                <span class="video_views">
                    |&nbsp;
                    <?php echo $this->translate(array('%s favorite', '%s favorites', $this->video->favorite_count), $this->locale()->toNumber($this->video->favorite_count)) ?>
                </span>
            </div>
            <div id="video_rating" class="rating" onmouseout="rating_out();">
                <span id="rate_1" class="rating_star_big_generic ynvideo_rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?>onclick="rate(1);"<?php endif; ?> onmouseover="rating_over(1);"></span>
                <span id="rate_2" class="rating_star_big_generic ynvideo_rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?>onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></span>
                <span id="rate_3" class="rating_star_big_generic ynvideo_rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?>onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></span>
                <span id="rate_4" class="rating_star_big_generic ynvideo_rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?>onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></span>
                <span id="rate_5" class="rating_star_big_generic ynvideo_rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?>onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></span>
                <span id="rating_text" class="rating_text ynvideo_rating_text"><?php echo $this->translate('click to rate'); ?></span>
            </div>
        </div>

        <div class="right">
            <?php echo $this->translate(array('%1$s view', '%1$s %2$s views', $this->video->view_count), $this->locale()->toNumber($this->video->view_count)) ?>
            <div class="video_button_add_to_area">
                <button class="ynvideo_uix_button ynvideo_add_button" id="ynvideo_btn_video_<?php echo $this->video->getIdentity() ?>" video-id="<?php echo $this->video->getIdentity() ?>">
                    <div>
                        <?php echo $this->translate('Add to') ?>
                    </div>
                </button>
            </div>
        </div>

        <div class="video_desc">
            <div class="ynvideo_text_header">
                <?php echo $this->translate('Description') ?>
            </div>
            <?php echo $this->video->description; ?>
        </div>
        <div class="video_category">

            <?php
                if ($this->video->category_id) {

                    $category = $this->categories[$this->video->category_id];
                    if (is_object($category)) {
						echo '<div class="ynvideo_text_header">' . $this->translate('Category') . '</div>';
	                    echo $this->htmlLink($category->getHref(), $category->category_name);
					}

                    if ($this->video->category_id != $this->video->subcategory_id && $this->video->subcategory_id) {

                        $subCategory = $this->categories[$this->video->subcategory_id];
                        if (is_object($subCategory)) {
							echo ' &#187; ';
                        	echo $this->htmlLink($subCategory->getHref(), $subCategory->category_name);
                        }
                    }
                }
            ?>
        </div>

        <div class="video_tags">
            <div class="ynvideo_text_header">
                <?php echo $this->translate('Tags') ?>
            </div>
            <?php if (count($this->videoTags)): ?>
                <?php foreach ($this->videoTags as $index => $tag): ?>
                    <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>
                        <?php echo $tag->getTag()->text ?>
                    </a>
                    <?php if ($index < count($this->videoTags) - 1) : ?>
                        ,&nbsp;
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="ynvideo_show_less_more">
        <a href="javascript:void(0)" class="ynvideo_link_more">
            <?php echo $this->translate('Show more') ?>
        </a>
    </div>
</div>

<div class="video_options">
    <a href="javascript:void(0)" onclick="viewURL()"><?php echo $this->translate('URL') ?></a>
    &nbsp;|&nbsp;
    <?php if ($this->can_embed): ?>
        <?php
            echo $this->htmlLink(array(
                    'module' => 'ynvideo',
                    'controller' => 'video',
                    'action' => 'embed',
                    'route' => 'default',
                    'id' => $this->video->getIdentity(),
                    'format' => 'smoothbox'
            ), $this->translate("HTML code"), array(
                'class' => 'smoothbox'
            ));
        ?>
        &nbsp;|&nbsp;
    <?php endif ?>

    <?php if (Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
    <a href="javascript:void(0)" onclick="sendToFriends()"><?php echo $this->translate('Send to friends') ?></a>
    &nbsp;|&nbsp;
    <?php endif; ?>

    <a href="ymsgr:sendIM?m=<?php echo full_url()?>"><?php echo $this->translate('Send to Yahoo') ?></a>
    &nbsp;|&nbsp;

    <?php if (Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
        <?php
        echo $this->htmlLink(array(
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'share',
            'route' => 'default',
            'type' => 'video',
            'id' => $this->video->getIdentity(),
            'format' => 'smoothbox'
                ), $this->translate("Share"), array(
            'class' => 'smoothbox'
        ));
        ?>
        &nbsp;|&nbsp;

        <?php
            echo $this->htmlLink(array(
                'module' => 'core',
                'controller' => 'report',
                'action' => 'create',
                'route' => 'default',
                'subject' => $this->video->getGuid(),
                'format' => 'smoothbox'
                    ), $this->translate("Report"), array(
                'class' => 'smoothbox'
                    //'class' => 'buttonlink smoothbox icon_report'
            ));
        ?>
        &nbsp;|&nbsp;

        <?php if ($this->can_edit): ?>
            <?php
            echo $this->htmlLink(array(
                'route' => 'video_general',
                'action' => 'edit',
                'video_id' => $this->video->video_id
                    ), $this->translate('Edit Video'));
            ?>
            &nbsp;|&nbsp;
        <?php endif; ?>
        <?php if ($this->can_delete && $this->video->status != 2): ?>
            <?php
            echo $this->htmlLink(array(
                    'route' => 'video_general',
                    'action' => 'delete',
                    'video_id' => $this->video->video_id,
                    'format' => 'smoothbox'
                ),
                $this->translate('Delete Video'),
                array(
                    'class' => 'smoothbox'
                ));
            ?>
            &nbsp;|&nbsp;
        <?php endif; ?>
    <?php endif ?>

    <div class="ynvideo_block" style="display:none">
        <form id="ynvideo_form_return_url">
            <label><?php echo $this->translate('URL')?></label>
            <input type="text" id="ynvideo_return_url" class="ynvideo_return_url"/>
            <div class="ynvideo_center ynvideo_block">
                <a href="javascript:void(0)" onclick="closeSmoothbox()" class="ynvideo_bold_link">
                    <?php echo $this->translate('Close')?>
                </a>
            </div>
        </form>
    </div>

    <div id="ynvideo_div_send_friend" class="ynvideo_block">
        <form action="<?php echo $this->url(array('module' => 'ynvideo', 'controller' => 'video', 'action' => 'send-to-friend'), 'default', true)?>"
              method="post" id="ynvideo_from_send_to_friends">
            <div id="ynvideo_send_result"></div>
            <input type="hidden" name="video_id" value="<?php echo $this->video->getIdentity()?>" />
            <?php echo $this->translate("Recipient's email"); ?>(<font color="#FF0000">*</font>):<br />
            <input name="send_emails" type="text" size = "60" class="form_element" />
            <br /><?php echo $this->translate("Separate multiple email addresses (up to %d) with commas.", $this->numberOfEmail) ?><br /><br />
            <?php echo $this->translate("Message:") ?><br />
            <textarea class="form_element" name="send_message" rows="2" cols="62"></textarea>
            <br /><br />
            <div style="display:none" id="result_send"></div>
            <button name="_send" type="submit">
                <?php echo $this->translate("Send"); ?>
            </button>
            <input type="hidden" name="url_send" id="url_send" value="<?php echo full_url(); ?>" />
        </form>
    </div>

</div>
<script language="javascript" type="text/javascript">
    jQuery('.ynvideo_show_less_more a').bind('click', function() {
        if (jQuery(this).hasClass('ynvideo_link_more')) {
            jQuery(this).html('<?php echo $this->translate('Show less') ?>');
            jQuery(this).removeClass('ynvideo_link_more');
            jQuery(this).addClass('ynvideo_link_less');
            jQuery('#ynvideo_video').removeClass('ynvideo_video_show_less');
        } else {
            jQuery(this).html('<?php echo $this->translate('Show more') ?>');
            jQuery(this).addClass('ynvideo_link_more');
            jQuery(this).removeClass('ynvideo_link_less');
            jQuery('#ynvideo_video').addClass('ynvideo_video_show_less');
        }
    });

    function closeSmoothbox() {
        var block = Smoothbox.instance;
        block.close();
    }

    function viewURL() {
        Smoothbox.open($('ynvideo_form_return_url'));
        jQuery('#TB_ajaxContent > form > .ynvideo_return_url').val(document.URL);
    }

    function sendToFriends() {
        if (jQuery('#ynvideo_div_send_friend').is(':hidden')) {
            jQuery('#ynvideo_div_send_friend').show();
            jQuery('#ynvideo_div_send_friend .form_element').each(function() {
                jQuery(this).val('');
            });
        } else {
            jQuery('#ynvideo_div_send_friend').hide();
            jQuery('#ynvideo_send_result').hide();
        }
    }

    jQuery('#ynvideo_from_send_to_friends').submit(function() {
        var params = jQuery(this).serializeArray();
        var action = jQuery(this).attr('action');
        jQuery.post(action, params, function(data){
            jQuery('#ynvideo_send_result').show();

            if (typeof(data.result) != 'undefined') {
                if (data.result == 1) {
                    // handle sending email sucessfully
                    jQuery('#ynvideo_send_result').html(data.message).css('color', '#546D50');
                } else {
                    // handle sending email unsucessfully
                    jQuery('#ynvideo_send_result').html(data.message).css('color', 'red');
                }
            } else {
                jQuery('#ynvideo_send_result').html('<?php echo $this->translate('There is an error occured, please try again !!!') ?>').css('color', 'red');
            }
        });

        return false;
    });
</script>

<style>
    .ynvideo_return_url {
        width: 100%;
    }
</style>