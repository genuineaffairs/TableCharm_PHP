<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<?php if (!empty($this->ajax_enabled)) : ?>
  <script type="text/javascript">
    window.addEvent('load', function() {
  	var url = en4.core.baseUrl + 'widget/index/mod/communityad/name/sponsored-stories';
  	var request = new Request.HTML({
  	  url : url,
  	  method: 'get',
  	  data : {
  		format : 'html',
  		'load_content' : 1,
  		'isAjaxEnabled' : '<?php echo $this->ajax_enabled; ?>',
  		'itemCount' : '<?php echo $this->limit; ?>'
  	  },
  	  onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
  		$('story_ajax_id').innerHTML = responseHTML;
  	  }
  	});
  	request.send();
    });
  </script>
<?php endif; ?>
  <script type="text/javascript">
    // FUNCTION: Call for show 'Like' and 'Unlike' in the widgets.
    var communityad_likeinfo = function(ad_id, resource_type, resource_id, owner_id, widgetType, core_like) {
      // SENDING REQUEST TO AJAX
      var request = createLike(ad_id, resource_type, resource_id, owner_id, widgetType, core_like);
      // RESPONCE FROM AJAX
      request.addEvent('complete', function(responseJSON) {
        if(responseJSON.like_id )
        {
          $(widgetType + '_likeid_info_'+ ad_id).value = responseJSON.like_id;
          $(resource_type + '_' + widgetType + '_most_likes_' + ad_id).style.display = 'none';
          $(resource_type + '_' + widgetType + '_unlikes_'+ ad_id).style.display = 'block';
        }
        else
        {
          $(widgetType + '_likeid_info_'+ ad_id).value = 0;
          $(resource_type + '_' + widgetType + '_most_likes_' + ad_id).style.display = 'block';
          $(resource_type +'_' + widgetType + '_unlikes_'+ ad_id).style.display = 'none';
        }
      });
    }
  </script>

<?php if (empty($this->load_content)) {
 ?>
	<div id="story_ajax_id">
  <?php }; ?>
<?php if (!empty($this->load_content)) { ?>
    <div class="cmad_ad_clm">
  	<div>
  	  <div class="cmad_bloack_top">
		<?php if (Engine_Api::_()->communityad()->enableCreateLink()) : ?>
		<?php echo '<b>' . $this->translate('Sponsored Stories') . '</b>'; ?>
		<?php endif; ?>
		<?php
		  $is_show_adboard = Engine_Api::_()->getApi('settings', 'core')->getSetting('show.adboard', 1);
		  if( !empty($is_show_adboard) ):
		    $sponcerdURL = $this->url(array(), 'sponcerd_display', true);
		    echo '<a href="' . $sponcerdURL . '">' . $this->translate('See All') . '</a>'; 
		  endif;
		?>
  	  </div>
	  <?php
		  $div_id = 0;

		  // FOR HSOW THE TEMPLATES.
		  echo $this->partial('application/modules/Communityad/views/scripts/sponsored-story/sponcerdStoriesPartial.tpl', array('modArray' => $this->communityads_array,  'rootTitleTruncationLimit' => $this->rootTitleTruncationLimit, 'titleTruncationLimit' => $this->titleTruncationLimit));
	  ?>


		</div>
	  </div>
  <?php }; ?>
<?php if (empty($this->load_content)) { ?>
		</div>
<?php }; ?>