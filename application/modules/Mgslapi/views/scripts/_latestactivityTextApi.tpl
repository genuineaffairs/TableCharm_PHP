<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _activityText.tpl 9806 2012-10-30 23:54:12Z matthew $
 * @author     Jung
 */
?>
<?php if( empty($this->actions) ) {
  echo $this->translate("The action you are looking for does not exist.");
  return;
} else {
   $actions = $this->actions;
} ?>

<?php
  foreach( $actions as $action ): // (goes to the end of the file)
    try { // prevents a bad feed item from destroying the entire page
      // Moved to controller, but the items are kept in memory, so it shouldn't hurt to double-check
      if( !$action->getTypeInfo()->enabled ) continue;
      if( !$action->getSubject() || !$action->getSubject()->getIdentity() ) continue;
      if( !$action->getObject() || !$action->getObject()->getIdentity() ) continue;
      
      ob_start();
    ?>
  <?php if( !$this->noList ): ?>
    <ul class='feed'>  
    <li>
    <?php endif; ?>
    <?php $this->commentForm->setActionIdentity($action->action_id) ?>

    <?php //echo $this->itemPhoto($action->getSubject(), 'thumb.normal', $action->getSubject()->getTitle(), array('class' => 'thumb', 'width' => 100)) ?>
        <img src="<?php echo Engine_Api::_()->mgslapi()->getItemPhotoUrl($action->getSubject());  ?>" class="thumb" width="100" alt="">
        

    <div class='info'>     
        <?php echo $action->getContent()?>        
        <div class="clear"></div>
        <div class="date"> <?php echo $this->timestamp($action->date); ?></div>
    </div>
        <?php $allowed_actions = array(); ?>
            <?php if( $this->viewer->getIdentity() && (
                    $this->activity_moderate || (
                    ($this->viewer->getIdentity() == $this->activity_group) || (
                      $this->allow_delete && (
                        ('user' == $action->subject_type && $this->viewer->getIdentity() == $action->subject_id) ||
                        ('user' == $action->object_type && $this->viewer->getIdentity()  == $action->object_id)
                      )
                    )
                   )
                ) ): ?>
            <?php $allowed_actions[]= 'delete' ?>
        <?php endif; ?>
        
    <?php $shared_actions = ''; ?>
    <?php if( $action->getTypeInfo()->shareable && $this->viewer->getIdentity() ): ?>
        <?php if( $action->getTypeInfo()->shareable == 1 && $action->attachment_count == 1 && ($attachment = $action->getFirstAttachment()) ): ?>               
            
            <?php $allowed_actions[]= 'share' ?>
            <?php $shared_actions .= '@share_type='.$attachment->item->getType(). '@share_id='.$attachment->item->getIdentity(); ?>
        <?php elseif( $action->getTypeInfo()->shareable == 2 ): ?>
            <?php $allowed_actions[]= 'share' ?>
            <?php $shared_actions .= '@share_type='.$subject->getType(). '@share_id='.$subject->getIdentity() ; ?>
        <?php elseif( $action->getTypeInfo()->shareable == 3 ): ?>
            <?php $allowed_actions[]= 'share' ?>
            <?php $shared_actions .= '@share_type='.$object->getType(). '@share_id='.$object->getIdentity() ; ?>
        <?php elseif( $action->getTypeInfo()->shareable == 4 ): ?>
            <?php $allowed_actions[]= 'share' ?>
            <?php $shared_actions .= '@share_type='.$action->getType(). '@share_id='.$action->getIdentity() ; ?>
        <?php endif; ?>
      <?php endif; ?>
        
    <?php $report_actions = ''; ?>    
    <?php if(!$this->subject && $this->viewer->getIdentity() && $action->getTypeInfo()->type !='birthday_post' &&(!$this->viewer->isSelf($action->getSubject()))): ?>
        <?php $allowed_actions[]= 'report' ?>
        <?php $report_actions .= '@report_type='.$action->getType(). '@report_id='.$action->getIdentity() ; ?>
    <?php endif; ?>
        <a href="#popupAction@item_id=<?php echo $action->action_id?>@item_type=<?php echo $action->getType()?>@allowed_actions=<?php echo implode(',',$allowed_actions) . $shared_actions. $report_actions ?>" id="do_popup_action" class="details"><img src="<?php echo  $this->serverUrl((string)$this->baseUrl())?>/application/modules/Mgslapi/externals/images/action_icon.png" class="thumb" alt="" title="" /></a>
    <div class="clear"></div>
      <?php if( $action->getTypeInfo()->attachable && $action->attachment_count > 0 ): // Attachments ?>
          <?php if( $action->attachment_count > 0 && count($action->getAttachments()) > 0 ): ?>
            <?php if( count($action->getAttachments()) == 1 &&
                    null != ( $richContent = current($action->getAttachments())->item->getRichContent()) ): ?>                    
              <?php if(current($action->getAttachments())->item->getType() == 'video'): ?>
              <?php $richContent = current($action->getAttachments())->item ?>
                  <?php $richContent = Engine_Api::_()->mgslapi()->getRichContent($richContent->video_id) ?>                
                <div class="slider">
                    <div class="photo_container img_center">
                        <?php echo $richContent; ?>   
                    </div>
                </div>
              <?php else: ?>    
                <?php echo $richContent; ?>
              <?php endif; ?>
            <?php else: ?>
                <?php $alt = 0; $total = count($action->getAttachments())?>
              <?php foreach( $action->getAttachments() as $attachment ): ?>
                <?php if( $attachment->meta->mode == 0 ): // Silence ?>
                <?php elseif( $attachment->meta->mode == 1 ): // Thumb/text/title type actions ?>
                    <?php 
                      if ($attachment->item->getType() == "core_link")
                      {
                        $attribs = Array('target'=>'_blank');
                      }
                      else
                      {
                        $attribs = Array();
                      } 
                    ?>
                    <?php if( $attachment->item->getPhotoUrl() ): ?>
                        <?php if( $attachment->item->getType() == 'album_photo'): ?>
                        <div class="slider">
                            <ul>
                                <li>
                                    <div class="photo_container img_center">
                                        <a href="#"><img src="<?php echo Engine_Api::_()->mgslapi()->getItemPhotoUrl($attachment->item);  ?>" alt="<?php echo $attachment->item->getTitle() ?>" class="thumb"></a>                                        
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="clear"></div>                            
                        <?php else:  ?>
                        <div class="feed_attachment_aaf">
                            <img src="<?php echo Engine_Api::_()->mgslapi()->getItemPhotoUrl($attachment->item);  ?>" alt="<?php echo $attachment->item->getTitle() ?>" class="thumb">
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                        <div class="feed_item_content">
                            <div class="feed_link_title">
                        <?php
                          echo $this->htmlLink('javascript:void(0);', $attachment->item->getTitle() ? $attachment->item->getTitle() : '', $attribs);
                        ?>
                        </div>
                      <div class="feed_item_link_desc">
                        <?php echo $this->CustomViewMore($attachment->item->getDescription(), 90) ?>
                      </div>   
                    </div>
                <?php elseif( $attachment->meta->mode == 2 ): // Thumb only type actions ?>
                    <?php $alt = !$alt;?>
                    <div class="slider <?php echo ($total > 1) ? ($alt ? 'left_img' : 'right_img'):'';?>">
                        <ul>
                            <li>
                                <div class="photo_container <?php echo ($total > 1) ? (!$alt ? 'nomargin' : '') : 'img_center'; ?>"><a href="#"><img src="<?php echo Engine_Api::_()->mgslapi()->getItemPhotoUrl($attachment->item);  ?>" alt="<?php echo $attachment->item->getTitle() ?>" alt="" title="" /></a></div>
                                <div class="clear"></div>
                            </li>
                        </ul>
                    </div>
                <?php elseif( $attachment->meta->mode == 3 ): // Description only type actions ?>
                  <?php echo $this->CustomViewMore($attachment->item->getDescription(), 90); ?>
                <?php elseif( $attachment->meta->mode == 4 ): // Multi collectible thingy (@todo) ?>
                <?php endif; ?>
              <?php endforeach; ?>
              <?php endif; ?>
          <?php endif; ?>
      <?php endif; ?>

    <div class="clear"></div>
  <?php if( !$this->noList ): ?></li>
        <div ontouchcancel="touchCancel(event);" ontouchend="touchEnd(event);" ontouchmove="touchMove(event);" ontouchstart="touchStart(event);" class="bottom" id="<?php echo $action->action_id ?>">
            <?php $canComment = ( $action->getTypeInfo()->commentable && Engine_Api::_()->authorization()->isAllowed($action->getObject(), $this->viewer, 'comment')); ?>
            <?php if( $canComment ): ?>
                <?php if( $action->likes()->isLike($this->viewer) ): ?>
                  <a href="<?php echo $action->action_id ?>" id="do_unlike">Unlike </a>
                <?php else: ?>
                  <a href="<?php echo $action->action_id ?>" id="do_like">Like </a> 
                <?php endif; ?>
                <span class="fullstop"> .</span> <a href="<?php echo $action->action_id ?>" id="do_ccoment">Comments</a>
            <?php else: ?>
                &nbsp;
            <?php endif; ?>
            <div style="float:right">
                <img src="<?php echo  $this->serverUrl((string)$this->baseUrl())?>/application/modules/Mgslapi/externals/images/like.png"> <strong id="like_value_<?php echo $action->action_id ?>"><?php echo $this->locale()->toNumber($action->likes()->getLikeCount())?></strong>
            &nbsp;&nbsp;&nbsp;&nbsp; <img src="<?php echo  $this->serverUrl((string)$this->baseUrl())?>/application/modules/Mgslapi/externals/images/commenticon.png"> <strong id="comment_value_<?php echo $action->action_id ?>"><?php echo $this->locale()->toNumber($action->comments()->getCommentCount())?></strong>
            </div> 
        </div>
    <script>               
        window.mySwipe = new Swipe(
            document.getElementById('slider_<?php echo $unique_id;?>')
        );

    </script>
    </ul><?php endif; ?>

<?php
      ob_end_flush();
    } catch (Exception $e) {
      ob_end_clean();
      if( APPLICATION_ENV === 'development' ) {
        echo $e->__toString();
      }
    };
  endforeach;
?>