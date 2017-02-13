<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-likes.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="sm-ui-popup-container-wrapper">
  <?php $this->addHelperPath(APPLICATION_PATH . '/application/modules/Sitemobile/modules/User/View/Helper', 'User_View_Helper'); ?>
  <?php if ($this->likes->getTotalItemCount() > 0): // COMMENTS -------  ?>
    <?php $action = $this->action; ?>
    <?php $viewer = Engine_Api::_()->user()->getViewer(); ?>

    <?php if ($this->page == 1): ?>
      <div class="sm-ui-popup-top ui-header ui-bar-a">
        <?php if (!empty($action)): ?> 
          <a data-iconpos="notext" data-icon="chevron-left" data-corners="true" data-shadow="true" class="ui-icon-left " onclick="$('#comment-activity-item-' + <?php echo $action->action_id ?>).css('display', 'block');$('#like-comment-item-' + <?php echo $action->action_id ?>).css('display', 'none');"><?php //echo $this->translate('back');?></a>
        <?php else : ?>
          <?php $this->headScriptSM()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitemobile/externals/scripts/smActivity.js'); ?>
        <?php endif; ?>
        <a href="javascript:void(0);" data-iconpos="notext" data-icon="remove" data-corners="true" data-shadow="true" data-iconshadow="true" class="ui-icon-right" onclick="$('.ui-page-active').removeClass('pop_back_max_height');$('#feedsharepopup').remove();$(window).scrollTop(parentScrollTop)"></a>
        <h2 class="ui-title"><?php echo $this->translate('People who like this'); ?></h2>
      </div>

      <div class="sm-ui-popup-container sm-ui-popup-likes sm-content-list">
        <ul id="likecommentmembers_ul" class="ui-member-list" data-role="listview" data-icon="none">
        <?php endif; ?>
        <?php foreach ($this->likes as $like): ?>
          <?php $user = $this->item($like->poster_type, $like->poster_id); ?>
          <?php
          $table = Engine_Api::_()->getDbtable('block', 'user');
          $select = $table->select()
                  ->where('user_id = ?', $user->getIdentity())
                  ->where('blocked_user_id = ?', $viewer->getIdentity())
                  ->limit(1);
          $row = $table->fetchRow($select);
          ?>
          <li>
            <?php if ($row == NULL && $this->viewer()->getIdentity() && $this->userFriendshipSM($user)): ?>
              <div class="ui-item-member-action">
                <?php echo $this->userFriendshipSM($user) ?>
              </div>
            <?php endif; ?>
            <a href="<?php echo $user->getHref() ?>">
              <?php echo $this->itemPhoto($user, 'thumb.icon') ?>
              <div class="ui-list-content">
                <h3><?php echo $user->getTitle() ?></h3>
              </div>
            </a>
          </li>
        <?php endforeach; ?>
        <?php if ($this->page == 1): ?>
        </ul>
        <div class="like_viewmore" id="like_commentviewmore" style="display: none;">
          <?php
          echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
              'id' => 'like_comment_viewmore_link',
              'class' => 'buttonlink icon_viewmore',
              'onclick' => 'sm4.activity.comment_likes(' . $this->action_id . ',' . $this->comment_id . ',' . ($this->page + 1) . ');'
          ))
          ?>
        </div>
      </div>	
    <?php endif; ?>
<?php endif; ?>
</div>
<div style="display:none;">
  <script type="text/javascript">
<?php if ($this->page && $this->likes->getCurrentPageNumber() >= $this->likes->count()): ?>
         var nextlikecommentpage = 0;
<?php else: ?>
         var nextlikecommentpage = 1;
<?php endif; ?>
<?php if (!empty($action)): ?>
            window.onscroll = sm4.activity.doOnScrollLoadCommentLikes('<?php echo $this->action_id; ?>', '<?php echo $this->comment_id; ?>', '<?php echo ($this->page + 1); ?>');    
           
<?php endif; ?>      
  
  </script>  
</div>
