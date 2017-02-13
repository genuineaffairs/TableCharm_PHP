<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _communityad-pages.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$is_identity = $this->viewer()->getIdentity();
$adcancel_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('adcancel.enable', 1);
$adBlockWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.block.width', 150);
foreach ($this->communityads_array as $community_ad):
  $div_id = $this->identity . $community_ad['userad_id'];
  $encode_adId = Engine_Api::_()->communityad()->getDecodeToEncode('' . $community_ad['userad_id'] . '');
  if (!empty($community_ad['resource_type']) && !empty($community_ad['resource_id'])):
    $resource_url = Engine_Api::_()->communityad()->resourceUrl($community_ad['resource_type'], $community_ad['resource_id']);
  endif;
  ?>
  <?php Engine_Api::_()->communityad()->adViewCount($community_ad['userad_id'], $community_ad['campaign_id']); ?>
  <?php //endif;     ?>
  <div class="caab_list" >
    <!-- DIV: Which show when click on cross of advertisment. -->
    <div id= "cmad_ad_cancel_<?php echo $div_id; ?>" style="display:none; width:<?php echo $adBlockWidth; ?>px;" class="cmadrem">
      <div class="cmadrem_rl">
        <?php echo '<a class="" title="' . $this->translate('Cancel reporting this ad') . '" href="javascript:void(0);" onclick="adUndo(' . $div_id . ', \'cmad\');">' . $this->translate('Undo') . '</a>'; ?>
      </div>
      <div class="cmadrem_con">
        <?php echo $this->translate("Do you want to report this? Why didn't you like it?"); ?>
        <form>
          <?php $ads_id = $encode_adId; ?>
          <div><input type="radio" name="adAction" value="0" onclick="adSave('Misleading', '<?php echo $ads_id; ?>', <?php echo $div_id; ?>, 'cmad')"/><?php echo $this->translate('Misleading'); ?></div>
          <div><input type="radio" name="adAction" value="1" onclick="adSave('Offensive', '<?php echo $ads_id; ?>', <?php echo $div_id; ?>, 'cmad')"/><?php echo $this->translate('Offensive'); ?></div>
          <div><input type="radio" name="adAction" value="2" onclick="adSave('Inappropriate', '<?php echo $ads_id; ?>', <?php echo $div_id; ?>, 'cmad')"/><?php echo $this->translate('Inappropriate'); ?></div>
          <div><input type="radio" name="adAction" value="3" onclick="adSave('Licensed Material', '<?php echo $ads_id; ?>', <?php echo $div_id; ?>, 'cmad')"/><?php echo $this->translate('Licensed Material'); ?></div>
          <div><input type="radio" name="adAction" value="4" onclick="otherAdCannel(4, '<?php echo $div_id; ?>', 'cmad')" id="cmad_other_<?php echo $div_id; ?>"/><?php echo $this->translate('Other'); ?></div>

          <div>
            <textarea name="cmad_other_text_<?php echo $div_id; ?>" onclick="this.value = ''" onblur="if (this.value == '')
                this.value = '<?php echo $this->string()->escapeJavascript($this->translate('Specify your reason here..')) ?>';"  id="cmad_other_text_<?php echo $div_id; ?>" style="display:none;" /><?php echo $this->translate('Specify your reason here..') ?></textarea>
          </div>

          <div>
            <?php echo '<a href="javascript:void(0);" onclick="adSave(\'Other\', \'' . $ads_id . '\', ' . $div_id . ', \'cmad\')" id="cmad_other_button_' . $div_id . '"  style="display:none" class="cmadrem_button">' . $this->translate('Report') . '</a>'; ?>
          </div>
        </form>
      </div>	
    </div>

    <!-- DIV:  Which default show, This div contain the all information about advertisment. -->
    <div class="cmaddis" id="cmad_ad_<?php echo $div_id; ?>" style="width:<?php echo $adBlockWidth ?>px;">
      <div class="cmaddis_close">
        <?php
        if (!empty($is_identity) && !empty($adcancel_enable)) {
          echo '<a class="" title="' . $this->translate('Report this ad') . '" href="javascript:void(0);" onclick="adCancel(' . $div_id . ', \'cmad\');"></a>';
        }
        ?>
      </div>
      <div class="cmadaddis" style="width:<?php echo $adBlockWidth ?>px;">
        <!--tital code start here for both-->
        <div class="cmaddis_title">
          <?php
          // Title if has existence on site then "_blank" not work else work.
          if (!empty($community_ad['resource_type']) && !empty($community_ad['resource_id'])) {
            $set_target = '';
          } else {
            $set_target = 'target="_blank"';
          }
          echo '<a href="' . $this->url(array('adId' => $encode_adId), 'communityad_adredirect', true) . '" ' . $set_target . ' >' . ucfirst($community_ad['cads_title']) . "</a>";
          ?>
        </div>
        <?php if (!empty($community_ad['resource_type']) && !empty($community_ad['resource_id'])) { ?>
          <div class="cmaddis_adinfo">
            <?php
            if (!empty($resource_url['status'])) {
              echo '<a href="' . $this->url(array('adId' => $encode_adId), 'communityad_adredirect', true) . '">' . $resource_url['title'] . "</a>";
            } else {
              echo $resource_url['title'];
            }
            ?>
          </div>
        <?php } else if (!empty($this->hideCustomUrl)) {
          ?>
          <div class="cmaddis_adinfo"><a title="<?php echo $community_ad['cads_url'] ?>" href="<?php echo $this->url(array('adId' => $encode_adId), 'communityad_adredirect', true) ?>" target="_blank" ><?php echo $this->translate(Engine_Api::_()->communityad()->truncation(Engine_Api::_()->communityad()->adSubTitle($community_ad['cads_url']), 25)) ?></a></div>
        <?php } ?>
        <!--image code start here for both-->
        <div class="cmaddis_image">
          <a href="<?php echo $this->url(array('adId' => $encode_adId), 'communityad_adredirect', true) ?>"  <?php echo $set_target ?>><?php echo $this->itemPhoto($community_ad, '', '') ?></a>
        </div>

        <!--description code start here for both-->
        <div class="cmaddis_body">	
          <a href="<?php echo $this->url(array('adId' => $encode_adId), 'communityad_adredirect', true) ?>"  <?php echo $set_target ?>><?php echo $community_ad['cads_body'] ?></a>

        </div>
        <!--description code end here for both-->
      </div>	
      <?php
      // Condition: If Like:1 & Resource Type & Resource Id is available in database then this means that advertisment has existence in community site then we show the 'Like' option of the user.
      $like_id = '';
      $is_module_enabled = Engine_Api::_()->communityad()->isModuleEnabled($community_ad['resource_type']);

      if (!empty($this->user_id) && !empty($community_ad['like']) && !empty($community_ad['resource_type']) && !empty($community_ad['resource_id']) && !empty($is_module_enabled) && !empty($resource_url)) {
        $checkResourceType = $resourceType = $community_ad['resource_type'];
        $resource_id = $community_ad['resource_id'];
        $owner_id = $community_ad['owner_id'];
        $ad_id = $community_ad['userad_id'];

        // Queary: Return array of 'My Friend Id' which liked this advertisment.
        //$friendLikeId = Engine_Api::_()->getDbTable('likes', 'communityad')->isFriendLiked( $ad_id );
        $resource_info = Engine_Api::_()->getDbtable('modules', 'communityad')->getModuleInfo($resourceType);
        if (!empty($resource_info)):
          $checkResourceType = $resource_info['table_name'];
        endif;
        $like_ids = Engine_Api::_()->communityad()->check_availability($ad_id);
        $likeAdInfo = Engine_Api::_()->communityad()->likeAdInfo($resource_id, $resourceType, $ad_id, $like_ids);
        $peoplesLike = '';
        if (empty($likeAdInfo)):
          ?>
          <div class="cmaddis_cont"> <?php echo $this->translate('You like this.') ?></div>
          <?php
        else:
          if (empty($likeAdInfo['is_like']) && empty($likeAdInfo['friend_like'])) {
            if (!empty($likeAdInfo['total_like'])) {
              ?>
              <div class="cmaddis_cont"><a href="<?php echo $this->url(array('module' => 'communityad', 'controller' => 'display', 'action' => 'adfriendlike', 'resource_type' => $resourceType, 'resource_id' => $resource_id, 'call_status' => 'public', 'ad_id' => $ads_id), 'default', true) ?>" class="smoothbox"><?php echo $this->translate(array('%s person likes this.', '%s people like this.', $likeAdInfo['total_like']), $this->locale()->toNumber($likeAdInfo['total_like'])) ?> </a></div>
              <?php
            }
          } else if (!empty($likeAdInfo['is_like']) && empty($likeAdInfo['friend_like'])) {
            if (!empty($likeAdInfo['total_like'])):
              $peoplesLike = $this->translate(array('%s other likes this.', '%s others like this.', $likeAdInfo['total_like']), $this->locale()->toNumber($likeAdInfo['total_like']));
            endif;
            ?>
            <div class="cmaddis_cont"><?php echo sprintf(Zend_Registry::get('Zend_Translate')->_('You and %s'), '<a href="' . $this->url(array('module' => 'communityad', 'controller' => 'display', 'action' => 'adfriendlike', 'resource_type' => $resourceType, 'resource_id' => $resource_id, 'call_status' => 'public', 'ad_id' => $ads_id), 'default', true) . '" class="smoothbox">' . $peoplesLike . '</a>') ?> 
            </div> 
            <?php
          } else if (empty($likeAdInfo['is_like']) && !empty($likeAdInfo['friend_like'])) {
            if (!empty($likeAdInfo['total_like'])) :
              $peoplesLike = $this->translate(array('%s other likes this.', '%s others like this.', $likeAdInfo['total_like']), $this->locale()->toNumber($likeAdInfo['total_like']));
            endif;
            ?>
            <div class="cmaddis_cont"><?php echo sprintf(Zend_Registry::get('Zend_Translate')->_('%s %s'), $likeAdInfo['friend_like'], '<a href="' . $this->url(array('module' => 'communityad', 'controller' => 'display', 'action' => 'adfriendlike', 'resource_type' => $resourceType, 'resource_id' => $resource_id, 'call_status' => 'public', 'ad_id' => $ads_id), 'default', true) . '" class="smoothbox">' . $peoplesLike . '</a>') ?></div>
            <?php
          } else if (!empty($likeAdInfo['is_like']) && !empty($likeAdInfo['friend_like'])) {
            if (!empty($likeAdInfo['total_like'])) :
              $peoplesLike = $this->translate(array('%s other likes this.', '%s others like this.', $likeAdInfo['total_like']), $this->locale()->toNumber($likeAdInfo['total_like']));
            endif;
            ?>
            <div class="cmaddis_cont"><?php echo sprintf(Zend_Registry::get('Zend_Translate')->_('%s %s'), $likeAdInfo['friend_like'], '<a href="' . $this->url(array('module' => 'communityad', 'controller' => 'display', 'action' => 'adfriendlike', 'resource_type' => $resourceType, 'resource_id' => $resource_id, 'call_status' => 'public', 'ad_id' => $ads_id), 'default', true) . '" class="smoothbox">' . $peoplesLike . '</a>') ?> </div>
            <?php
          }
        endif;
        // Function Calling: Return int or empty, If loggden user does not liked this advertisment then return empty and if loggden user done liked this advertisment then return id of liked from 'core_likes' table.							
        if (!empty($like_ids[0]['like_id'])) {
          $unlike_show = "display:block";
          $like_show = "display:none";
          $like_id = $like_ids[0]['like_id'];
        } else {
          $unlike_show = "display:none;";
          $like_show = "display:block;";
          $like_id = 0;
        }
        ?>
        <?php if (empty($like_id)): // Condition: Show 'Like Link' only when loggden user not like this advertisment. else show message that you have liked this advertismant.         ?>
          <div class="cmad_like_button" id= "<?php echo $resourceType ?>_ad_board_unlikes_<?php echo $encode_adId; ?>" style ='display:none;' >
            <div class="cmaddis_cont"><?php echo $this->translate('You like this.') ?></div>
            <a href = "javascript:void(0);" onclick = "communityad_likeinfo('<?php echo $encode_adId; ?>', '<?php echo $resourceType ?>', '<?php echo $resource_id ?>', '<?php echo $owner_id ?>', 'ad_board', '<?php echo $resource_url['like']; ?>')">
              <i class="like_thumbdown_icon"></i>
              <span><?php echo $this->translate('Unlike') ?></span>
            </a>									
          </div>
          <div class="cmad_like_button" id= "<?php echo $resourceType ?>_ad_board_most_likes_<?php echo $encode_adId; ?>" style ='<?php echo $like_show; ?>'>
            <a href = "javascript:void(0);" onclick = "communityad_likeinfo('<?php echo $encode_adId; ?>', '<?php echo $resourceType; ?>', '<?php echo $resource_id ?>', '<?php echo $owner_id ?>', 'ad_board', '<?php echo $resource_url['like']; ?>')">
              <i class="like_thumbup_icon"></i>
              <span><?php echo $this->translate('Like') ?></span>
            </a>
          </div>
          <input type ="hidden" id = "ad_board_likeid_info_<?php echo $encode_adId; ?>" value = "<?php echo $like_id; ?>"  />
          <?php
        endif;
      }
      ?>
      <input type ="hidden" id = "mixinfolike_<?php echo $encode_adId; ?>" value = "<?php echo $like_id; ?>"  />

    </div>
  </div>	

<?php endforeach; ?>