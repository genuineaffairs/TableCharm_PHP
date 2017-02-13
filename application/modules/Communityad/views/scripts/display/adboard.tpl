<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: adboard.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<!--Div for plugin message for demo site-->
<div id="demo_msg_communityad"></div>
<div class="layout_middle" id="advertisment_content">
  <div class="cadcomp_vad_header">
    <h3><?php echo $this->translate("Ad Board") ?></h3>

    <?php
    $viewer_id = $this->viewer()->getIdentity();
    if (!empty($this->getStoryStatus) && !empty($viewer_id)) {
      $communityadURL = $this->url(array(), 'communityad_display', true);
      $sponcerdURL = $this->url(array(), 'sponcerd_display', true);

      echo '<div class="caab_ad_links"><a href="' . $communityadURL . '" class="caab_ad_links_selected">' . $this->translate($this->getCommunityadTitle) . '</a> <a href="' . $sponcerdURL . '">' . $this->translate('Sponsored Stories') . '</a></div>';
    }
    ?>

    <?php if (Engine_Api::_()->communityad()->enableCreateLink()) : ?>
      <div class="cmad_hr_link">
        <?php $create_ad_url = $this->url(array(), 'communityad_listpackage', true); ?>
        <a href="<?php echo $create_ad_url; ?>"><?php echo $this->translate("Create an Ad"); ?> &raquo;</a>
      </div>
    <?php endif; ?>
  </div>
  <div class="caab_ad">
    <?php if (empty($this->noResult)) { ?>
      <?php
      $this->identity = '999999999999';
      include APPLICATION_PATH . '/application/modules/Communityad/views/scripts/_adsDisplay.tpl';
    } else {
      ?>

      <div class='tip' style="text-align:center;padding-top:15px;">
        <span style="float:none;">
          <?php echo $this->translate('No advertisements have been created yet.'); ?>
          <?php if (Engine_Api::_()->communityad()->enableCreateLink()): ?>
            <?php echo $this->translate(' Be the first to %1$screate an ad%2$s.', '<a href="' . $this->url(array(), 'communityad_listpackage', true) . '">', '</a>'); ?>
          <?php endif; ?>
        </span>
      </div>
    <?php } ?>
    <div style="clear:both"></div>
  </div>	
</div>

