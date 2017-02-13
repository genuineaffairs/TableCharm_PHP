<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _activityText.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php
if (empty($this->actions)) {
  echo $this->translate("The action you are looking for does not exist.");
  return;
} else {
  $actions = $this->actions;
}
?>

  <?php if (!$this->feedOnly && !$this->getUpdate): ?>
  <ul class='feed' id="activity-feed">
  <?php endif ?>

  <?php
  foreach ($actions as $action): // (goes to the end of the file)
    try { // prevents a bad feed item from destroying the entire page
      // Moved to controller, but the items are kept in memory, so it shouldn't hurt to double-check
      if (!$action->getTypeInfo()->enabled)
        continue;
      if (!$action->getSubject() || !$action->getSubject()->getIdentity())
        continue;
      if (!$action->getObject() || !$action->getObject()->getIdentity())
        continue;

      ob_start();
      ?>
    <?php if (!$this->noList): ?><li id="activity-item-<?php echo $action->action_id ?>" data-activity-feed-item="<?php echo $action->action_id ?>"><?php endif; ?>
    <?php $this->commentForm->setActionIdentity($action->action_id) ?>
        <script type="text/javascript">
          (function(){
            var action_id = '<?php echo $action->action_id ?>';
            sm4.core.runonce.add(function(){
              sm4.activity.attachComment($('#activity-comment-form-' + action_id)).bind(this);
            });
          })();
        </script>

          <?php // User's profile photo  ?>

        <div style='float:right;'>
          <!--<a href='#<?php //echo $action->getGuid()?>_options' data-icon="gear" data-inset="true" data-role='button' data-iconpos='notext' data-rel="popup" ></a>-->
          <!--<div data-role="popup" id='<?php //echo $action->getGuid()?>_options' <?php //echo $this->dataHtmlAttribs("popup_content", array('data-theme'=>"c")); ?>  style="margin-right: 20px;">-->
            <!--<a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right"><?php //echo $this->translate('Close');?></a>-->
          <?php
          if ($this->viewer()->getIdentity() && (
                  $this->activity_moderate || (
                  ($this->viewer()->getIdentity() == $this->activity_group) || (
                  $this->allow_delete && (
                  ('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
                  ('user' == $action->object_type && $this->viewer()->getIdentity() == $action->object_id)
                  ))))):
          ?>
            <a data-role="button" data-inline="true" data-mini="true" data-icon="delete" data-iconpos="notext" href="javascript:void(0);" data-url="<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'delete', 'action_id' => $action->action_id), 'default', 'true'); ?>" onclick="javascript:sm4.activity.activityremove(this, '0', '<?php echo $action->action_id ?>');" data-message="Are you sure that you want to delete this activity item? This action cannot be undone."><?php echo $this->translate('Delete'); ?></a> 
            <!--</li>-->
          <?php endif; ?>
          <!--</div>-->
        </div>

        <div  class='feed_item_photo'><?php echo $this->htmlLink($action->getSubject()->getHref(), $this->itemPhoto($action->getSubject(), 'thumb.icon', $action->getSubject()->getTitle())) ?></div>

        <div class='feed_item_body'>

            <?php // Main Content ?>
          <span class="<?php echo ( empty($action->getTypeInfo()->is_generated) ? 'feed_item_posted' : 'feed_item_generated' ) ?>">
            <?php echo $action->getContent() ?>
          </span>      
            <?php // Attachments ?>
            <?php if ($action->getTypeInfo()->attachable && $action->attachment_count > 0): // Attachments ?>
            <div class='feed_item_attachments'>
                <?php if ($action->attachment_count > 0 && count($action->getAttachments()) > 0): ?>
                  <?php if (count($action->getAttachments()) == 1 &&
                          null != ( $richContent = current($action->getAttachments())->item->getRichContent())): ?>                    
                      <?php echo $richContent; ?>
                    <?php else: ?>
                      <?php foreach ($action->getAttachments() as $attachment): ?>
                    <span class='feed_attachment_<?php echo $attachment->meta->type ?>'>
                        <?php if ($attachment->meta->mode == 0): // Silence ?>
                        <?php elseif ($attachment->meta->mode == 1): // Thumb/text/title type actions ?>
                        <div>
                          <?php
                          if ($attachment->item->getType() == "core_link") {
                            $attribs = Array('target' => '_blank');
                          } else {
                            $attribs = Array();
                          }
                          ?>
                              <?php if ($attachment->item->getPhotoUrl()): ?>
                                <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), $attribs) ?>
                              <?php endif; ?>
                          <div>
                            <div class='feed_item_link_title'>
                              <?php
                              echo $this->htmlLink($attachment->item->getHref(), $attachment->item->getTitle() ? $attachment->item->getTitle() : '', $attribs);
                              ?>
                            </div>
                            <div class='feed_item_link_desc'>
                        <?php echo $this->viewMore($attachment->item->getDescription()) ?>
                            </div>
                          </div>
                        </div>
                      <?php elseif ($attachment->meta->mode == 2): // Thumb only type actions ?>
                        <div class="feed_attachment_photo">
                        <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), array('class' => 'feed_item_thumb')) ?>
                        </div>
                    <?php elseif ($attachment->meta->mode == 3): // Description only type actions  ?>
                      <?php echo $this->viewMore($attachment->item->getDescription()); ?>
                    <?php elseif ($attachment->meta->mode == 4): // Multi collectible thingy (@todo) ?>
                    <?php endif; ?>
                    </span>
                <?php endforeach; ?>
        <?php endif; ?>
      <?php endif; ?>
            </div>
            <?php endif; ?>

						<div id='comment-likes-activity-item-<?php echo $action->action_id ?>'>
							<?php // Icon, time since, action links ?>
							<?php
							$icon_type = 'activity_icon_' . $action->type;
							list($attachment) = $action->getAttachments();
							if (is_object($attachment) && $action->attachment_count > 0 && $attachment->item):
								$icon_type .= ' item_icon_' . $attachment->item->getType() . ' ';
							endif;
							$canComment = ( $action->getTypeInfo()->commentable &&
											$this->viewer()->getIdentity() &&
											Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment') &&
											!empty($this->commentForm) );
							?>
							<div data-role="controlgroup" data-type="horizontal" style='width:100%;text-align:right;'>
								<div class='feed_item_date feed_item_icon <?php echo $icon_type ?>'>
									<ul>
										<?php echo $this->timestamp($action->getTimeValue()) ?>
										<?php if ($canComment): ?>
											<?php if ($action->likes()->isLike($this->viewer())): ?>
												<a href="javascript:void(0);" data-role="button" data-inline="true" data-mini="true" onclick="javascript:sm4.activity.unlike('<?php echo $action->action_id ?>');"><?php echo $this->translate('Unlike') ?></a>
											<?php else: ?>
												<a href="javascript:void(0);" data-role="button" data-inline="true" data-mini="true" onclick="javascript:sm4.activity.like('<?php echo $action->action_id ?>');"><?php echo $this->translate('Like') ?></a>
											<?php endif; ?>
											<?php if (Engine_Api::_()->getApi('settings', 'core')->core_spam_comment): // Comments - likes  ?>
												<li data-role="button" data-inline="true" data-mini="true">
													<a href="<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'viewcomment', 'action_id' => $action->getIdentity(), 'format' => 'smoothbox'), 'default', 'true'); ?>" data-role="button" data-inline="true" data-mini="true"><?php echo $this->translate('Comment'); ?></a>
												</li>
											<?php else: ?>
												<li data-role="button" data-inline="true" data-mini="true">
													<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Comment'), array('onclick' => 'document.getElementById("' . $this->commentForm->getAttrib('id') . '").style.display = ""; document.getElementById("' . $this->commentForm->submit->getAttrib('id') . '").style.display = "block"; document.getElementById("' . $this->commentForm->body->getAttrib('id') . '").focus();')) ?>
												</li>
											<?php endif; ?>
											<?php if ($this->viewAllComments): ?>
												<script type="text/javascript">
												//                 en4.core.runonce.add(function() {
												//                   document.getElementById('<?php //echo $this->commentForm->getAttrib('id')  ?>').style.display = "";
												//                   document.getElementById('<?php //echo $this->commentForm->submit->getAttrib('id') ?>').style.display = "block";
												//                   document.getElementById('<?php //echo $this->commentForm->body->getAttrib('id') ?>').focus();
												//                 });
												</script>
											<?php endif ?>
										<?php endif; ?>
										
										<?php // Share  ?>
										<?php if ($action->getTypeInfo()->shareable && $this->viewer()->getIdentity()): ?>
											<?php if ($action->getTypeInfo()->shareable == 1 && $action->attachment_count == 1 && ($attachment = $action->getFirstAttachment())): ?>
												<a href="<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $attachment->item->getType(), 'id' => $attachment->item->getIdentity(), 'format' => 'smoothbox'), 'default', 'true'); ?>" class="" data-role="button" data-inline="true" data-mini="true"><?php echo $this->translate('Share'); ?></a>
											<?php elseif ($action->getTypeInfo()->shareable == 2): ?>
												<a href="<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $subject->getType(), 'id' => $subject->getIdentity(), 'format' => 'smoothbox'), 'default', 'true'); ?>" class="" data-role="button" data-inline="true" data-mini="true"><?php echo $this->translate('Share'); ?></a>
											<?php elseif ($action->getTypeInfo()->shareable == 3): ?>
												<a href="<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $object->getType(), 'id' => $object->getIdentity(), 'format' => 'smoothbox'), 'default', 'true'); ?>" class="" data-role="button" data-inline="true" data-mini="true"><?php echo $this->translate('Share'); ?></a>
											<?php elseif ($action->getTypeInfo()->shareable == 4): ?>
												<a href="<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $action->getType(), 'id' => $action->getIdentity(), 'format' => 'smoothbox'), 'default', 'true'); ?>" class="" data-role="button" data-inline="true" data-mini="true"><?php echo $this->translate('Share'); ?></a>
											<?php endif; ?>
										<?php endif; ?>
									</ul>
								</div>
							</div>


			        <?php if ($action->getTypeInfo()->commentable): // Comments - likes  ?>
								<div class='comments'>
									<ul>
									<?php if ($action->likes()->getLikeCount() > 0 && (count($action->likes()->getAllLikesUsers()) > 0)): ?>
											<!--<div></div>-->
											<div class="comments_likes">
												<?php if ($action->likes()->getLikeCount() <= 3 || $this->viewAllLikes): ?>
													<?php echo $this->translate(array('%s likes this.', '%s like this.', $action->likes()->getLikeCount()), $this->fluentList($action->likes()->getAllLikesUsers())) ?>
												<?php else: ?>
													<?php echo $this->htmlLink($action->getSubject()->getHref(array('action_id' => $action->action_id, 'show_likes' => true)), $this->translate(array('%s person likes this', '%s people like this', $action->likes()->getLikeCount()), $this->locale()->toNumber($action->likes()->getLikeCount())))
													?>
												<?php endif; ?>
											</div>
                      <?php endif; ?>
											<?php if ($action->comments()->getCommentCount() > 0): ?>
												<?php if ($action->comments()->getCommentCount() > 5 && !$this->viewAllComments): ?>
													<!--<li>-->
														<div></div>
														<div class="comments_viewall">
															<?php if ($action->comments()->getCommentCount() > 2): ?>
																<?php echo $this->htmlLink($action->getSubject()->getHref(array('action_id' => $action->action_id, 'show_comments' => true)), $this->translate(array('View all %s comment', 'View all %s comments', $action->comments()->getCommentCount()), $this->locale()->toNumber($action->comments()->getCommentCount()))) ?>
															<?php else: ?>
																<?php echo $this->htmlLink('javascript:void(0);', $this->translate(array('View all %s comment', 'View all %s comments', $action->comments()->getCommentCount()), $this->locale()->toNumber($action->comments()->getCommentCount())), array('onclick' => 'sm4.activity.viewComments(' . $action->action_id . ');')) ?>
															<?php endif; ?>
														</div>
													<!--</li>-->
												<?php endif; ?>
												<?php
												//echo '<pre>';  
												//var_dump($action->getComments($this->viewAllComments));
												//echo '</pre>';
												//die('The End');
												?>
												
												<?php foreach ($action->getComments($this->viewAllComments) as $comment): ?>
													<li id="comment-<?php echo $comment->comment_id ?>">
														<div class="comments_author_photo">
															<?php echo $this->htmlLink($this->item($comment->poster_type, $comment->poster_id)->getHref(), $this->itemPhoto($this->item($comment->poster_type, $comment->poster_id), 'thumb.icon', $action->getSubject()->getTitle())) ?>
														</div>
														<div class="comments_info">
															<span class='comments_author'>
																<?php echo $this->htmlLink($this->item($comment->poster_type, $comment->poster_id)->getHref(), $this->item($comment->poster_type, $comment->poster_id)->getTitle()); ?>
															</span>
															<span class="comments_body">
																<?php echo $this->viewMore($comment->body) ?>
															</span>
															<ul class="comments_date">
															<div data-role="controlgroup" data-type="horizontal" >
																<?php echo $this->timestamp($comment->creation_date); ?>
																	<?php if ($this->viewer()->getIdentity() && (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) || ($this->viewer()->getIdentity() == $comment->poster_id) || $this->activity_moderate )): ?>
																		<a data-role="button" data-inline="true" data-mini="true"  href="javascript:void(0);" data-url="<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'delete', 'action_id' => $action->action_id, 'comment_id' => $comment->comment_id), 'default', 'true'); ?>" onclick="javascript:sm4.activity.activityremove(this, '<?php echo $comment->comment_id ?>', '<?php echo $action->action_id ?>');" data-message="Are you sure that you want to delete this comment? This action cannot be undone."><?php echo $this->translate('delete'); ?></a>
																	<?php endif; ?>
																	<?php if ($canComment):
																		$isLiked = $comment->likes()->isLike($this->viewer());
																	?>
																		<?php if (!$isLiked): ?>
																			<a data-role="button" data-inline="true" data-mini="true" href="javascript:void(0)" onclick="sm4.activity.like(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)"> <?php echo $this->translate('like') ?></a>
																		<?php else: ?>
																			<a data-role="button" data-inline="true" data-mini="true" href="javascript:void(0)" onclick="sm4.activity.unlike(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)">
																			<?php echo $this->translate('unlike') ?>
																			</a>
																		<?php endif ?>
																	<?php endif ?>
																	<?php if ($comment->likes()->getLikeCount() > 0): ?>
																		<a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>" onclick="sm4.activity.comment_likes(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)" class="comments_comment_likes" >
																			<?php echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
																		</a>
																	<?php endif ?>
																</div>
															</ul>
														</div>
													</li>
												<?php endforeach; ?>
												
                      <?php endif; ?>
										</ul>
										<?php if ($canComment)
										echo $this->commentForm->render() /*
										<form>
										<textarea rows='1'>Add a comment...</textarea>
										<button type='submit'>Post</button>
										</form>
										*/ ?>
									</div>
									<?php endif; ?>
							  </div> <!-- End of Comment Likes -->
              </div>
      <?php if (!$this->noList): ?></li><?php endif; ?>

      <?php
      ob_end_flush();
    } catch (Exception $e) {
      ob_end_clean();
      if (APPLICATION_ENV === 'development') {
        echo $e->__toString();
      }
    };
  endforeach;
  ?>

<?php if (!$this->feedOnly && !$this->getUpdate): ?>
  </ul>
<?php endif ?>
