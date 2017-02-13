<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _sponsoredStoryPreview.tpl  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  var story_main_title = '<?php echo $this->translate($this->viewer()->getTitle()) ?>';
  var storyType_id = 0;
</script>

<div class="cmad_sp_wrapper">
  <b><?php echo $this->translate("Preview Your Sponsored Story"); ?></b>
  <div class="cadcp_preview" id="createPrivew">
	<?php
	$ad_body = $this->translate("Example ad body text.");
	$titleTruncationLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.title', 25);
	$viewerTruncatedTitle = Engine_Api::_()->communityad()->truncation($this->viewer()->getTitle(), $titleTruncationLimit);
	$ownerTitle = '<span id="story_main_title" class="cmad_show_tooltip_wrapper"><b><a href="javascript:void(0);">' . $viewerTruncatedTitle . '</a></b>
      	<div class="cmad_show_tooltip">
					<img src="' . $this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/images/tooltip_arrow.png" style="width:13px;height:9px;" />'
			. $this->translate("_sponsored_viewer_title_tooltip") .
			'</div>
			</span>';
	$storyTitle = '<a href="javascript:void(0);">' . $this->translate('Item Title.') . '</a>';
	$adTitle = '<div id="story_main_title_str">' . $this->translate('%s likes %s', $ownerTitle, $storyTitle) . '</div>';
	?>
    <div class="cmad_sdab">
	  <div id="story_main_photo" class="cmad_sdab_sp cmad_show_tooltip_wrapper">
		<?php echo $this->htmlLink('javascript:void(0)', $this->itemPhoto($this->viewer(), 'thumb.icon')); ?>
		<div class="cmad_show_tooltip">
		  <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/tooltip_arrow.png" style="width:13px;height:9px;" />
		  <?php echo $this->translate("_sponsored_viewer_photo_tooltip"); ?>
		</div>
	  </div>
	  <div class="cmad_sdab_body">
		<?php
		  $url = $this->url(array('page_id' => 100), 'communityad_help', true);
		?>
  		<div id="story_help_and_lernmore" style="display:none;"><?php echo $this->translate('Sorry, we not get any content for sponsored story for more detail please %s', '<a href="' . $url . '">Click Here</a>') ?></div>
  		<div class="cmad_sdab_title" style="overflow:hidden;">
		  <?php echo $adTitle; ?>
        </div>
        <div class="cmad_sdab_cont"> 
		  <div class="cmad_sdab_cont_img cmad_show_tooltip_wrapper" id="story_content_photo">
			<a id="story_content_photo" href="javascript:void(0);"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/blankImage.png" alt="" /></a>
			<div class="cmad_show_tooltip">
			  <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/tooltip_arrow.png" />
			  <?php echo $this->translate("_sponsored_content_photo_tooltip"); ?>
			</div>
		  </div>
		  <div id="story_content_title_div" class="cmad_sdab_cont_body cmad_show_tooltip_wrapper" style="clear:none;">
			<a href="javascript:void(0);" id="story_content_title"> <?php echo $this->translate('Item Title'); ?> </a>
			<div class="cmad_show_tooltip">
			  <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/tooltip_arrow.png" />
			  <?php echo $this->translate("_sponsored_content_title_tooltip"); ?>
			</div>
		  </div>
		</div>

		<div id="cmad_show_post_wrapper" class="cmad_sdab_sp_post cmad_sdab_cont_stat" style="display:none">
					1 minutes ago
		</div>

		<div class="cmad_show_tooltip_wrapper" id="cmad_show_tooltip_wrapper">
		  <div class="cmaddis_cont" style="display:block;">
			<a href="javascript:void(0);" class="cmad_like_button" style="display:block;"><i class="like_thumbup_icon"></i><span id="like_button"><?php echo $this->translate('Like This Item'); ?></span></a>
			  </div>

			  <div class="cmad_show_tooltip">
				<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/tooltip_arrow.png" />
			<?php echo $this->translate("_sponsored_like_tooltip"); ?>
		  </div>
		</div>
	  </div>
	</div>
  </div>
</div>
