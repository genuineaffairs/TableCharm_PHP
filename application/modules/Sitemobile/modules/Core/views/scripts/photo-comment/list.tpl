<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: list.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php
$this->headTranslate(array(
    'Are you sure you want to delete this?',
));
?>

<?php if (!$this->page): ?>
  <div class="sm-ui-popup-container-wrapper">
    <div id="photo-comments">
    <?php endif; ?>
    <div class="sm-ui-popup-top ui-header ui-bar-a">
      <a onclick="$('.ps-popup-wapper').remove()" class="ui-icon-right ui-btn ui-shadow ui-btn-corner-all ui-btn-icon-notext ui-btn-up-a" data-iconshadow="true" data-shadow="true" data-corners="true" data-icon="remove" data-iconpos="notext" href="javascript:void(0);" data-wrapperels="span" data-theme="a" title=""><span class="ui-btn-inner"><span class="ui-btn-text"></span><span class="ui-icon ui-icon-remove ui-icon-shadow">&nbsp;</span></span></a>
      <h2 class="ui-title photo_comments_options">        
        <?php echo $this->translate(array('%s comment', '%s comments', $this->comments->getTotalItemCount()), $this->locale()->toNumber($this->comments->getTotalItemCount())) ?>
      </h2>
    </div>
    <div class="sm-ui-popup-container">
      <div class="comments">
        <ul>
          <?php if ($this->likes->getTotalItemCount() > 0): // LIKES -------------  ?>   
            <li>
              <?php if ($this->viewAllLikes || $this->likes->getTotalItemCount() <= 1): ?>
                <?php $this->likes->setItemCountPerPage($this->likes->getTotalItemCount()) ?>
                <div> </div>
                <div class="comments_likes">
                  <?php echo $this->translate(array('%s likes this', '%s like this', $this->likes->getTotalItemCount()), $this->fluentList($this->subject()->likes()->getAllLikesUsers())) ?>
                </div>
              <?php else: ?>
                <div> </div>
                <div class="comments_likes">
                  <?php
                  echo $this->htmlLink('javascript:void(0);', $this->translate(array('%s person likes this', '%s people like this', $this->likes->getTotalItemCount()), $this->locale()->toNumber($this->likes->getTotalItemCount())), array('onclick' => 'sm4.core.photocomments.showLikes("' . $this->subject()->getType() . '", "' . $this->subject()->getIdentity() . '");')
                  );
                  ?>
                </div>
              <?php endif; ?>
            </li>
          <?php endif; ?>
          <?php if ($this->comments->getTotalItemCount() > 0): // COMMENTS ------- ?>

            <?php if ($this->page && $this->comments->getCurrentPageNumber() > 1): ?>
              <li>
                <div> </div>
                <div class="comments_viewall">
                  <?php
                  echo $this->htmlLink('javascript:void(0);', $this->translate('View previous comments'), array('class' => 'ui-link',
                      'onclick' => 'sm4.core.photocomments.loadComments("' . $this->subject()->getType() . '", "' . $this->subject()->getIdentity() . '", "' . ($this->page - 1) . '")'
                  ))
                  ?>
                </div>
              </li>
            <?php endif; ?>

            <?php if (!$this->page && $this->comments->getCurrentPageNumber() < $this->comments->count()): ?>
              <li>
                <div> </div>
                <div class="comments_viewall">
                  <?php
                  echo $this->htmlLink('javascript:void(0);', $this->translate('View more comments'), array('class' => 'ui-link',
                      'onclick' => 'sm4.core.photocomments.loadComments("' . $this->subject()->getType() . '", "' . $this->subject()->getIdentity() . '", "' . ($this->comments->getCurrentPageNumber()) . '")'
                  ))
                  ?>
                </div>
              </li>
            <?php endif; ?>

            <?php
            // Iterate over the comments backwards (or forwards!)
            $comments = $this->comments->getIterator();
            if ($this->page):
              $i = 0;
              $l = count($comments) - 1;
              $d = 1;
              $e = $l + 1;
            else:
              $i = count($comments) - 1;
              $l = count($comments);
              $d = -1;
              $e = -1;
            endif;
            for (; $i != $e; $i += $d):
              $comment = $comments[$i];
              $poster = $this->item($comment->poster_type, $comment->poster_id);
              $canDelete = ( $this->canDelete || $poster->isSelf($this->viewer()) );
              ?>
              <li id="comment-<?php echo $comment->comment_id ?>">
                <div class="comments_author_photo">
                  <?php
                  echo $this->htmlLink($poster->getHref(), $this->itemPhoto($poster, 'thumb.icon', $poster->getTitle(), array('class' => 'ui-link'))
                  )
                  ?>
                </div>
                <div class="comments_info">
                  <div class='comments_author'>
                    <?php echo $this->htmlLink($poster->getHref(), $poster->getTitle(), array('class' => 'ui-link')); ?>
                  </div>
                  <div class="comments_body">
                    <?php echo $this->viewMore($comment->body) ?>
                  </div>
                  <div class="comments_date">
                    <?php if ($canDelete): ?>
                      <a href="javascript:void(0);" class="ui-link" onclick="sm4.core.photocomments.deleteComment('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>', '<?php echo $comment->comment_id ?>')">
                        <?php echo $this->translate('delete') ?>
                      </a>
                      <span class="sep"> -</span>
                    <?php endif; ?>
                    <?php
                    if ($this->canComment):
                      $isLiked = $comment->likes()->isLike($this->viewer());
                      ?>
                      <?php if (!$isLiked): ?>
                        <a href="javascript:void(0)"  onclick="sm4.core.photocomments.like(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)" class="comment_likes ui-link">
                          <?php echo $this->translate('like') ?>
                        </a>
                      <?php else: ?>
                        <a href="javascript:void(0)" onclick="sm4.core.photocomments.unlike(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)" class="comment_likes ui-link">
                          <?php echo $this->translate('unlike') ?>
                        </a>
                      <?php endif ?>
                      <span class="sep"> -</span>
                    <?php endif ?>
                    <?php if ($comment->likes()->getLikeCount() > 0): ?>
                      <a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>" onclick="sm4.core.photocomments.comment_likes(<?php echo sprintf("'%d'", $comment->comment_id) ?>)" class="comments_comment_likes ui-link">
                        <?php echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
                      </a>
                      <span class="sep"> -</span>
                    <?php endif ?>
                    <?php echo $this->timestamp($comment->creation_date); ?>
                  </div>
                  <?php /*
                    <div class="comments_date">
                    <?php echo $this->timestamp($comment->creation_date); ?>
                    <?php if( $comment->likes()->getLikeCount() > 0 ): ?>
                    -
                    <a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>" class="comments_comment_likes ui-link" title="<?php echo $this->translate('Loading...') ?>">
                    <?php echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
                    </a>
                    <?php endif ?>
                    </div>
                    <div class="comments_comment_options">
                    <?php if( $canDelete && $this->canComment ): ?>
                    -
                    <?php endif ?>
                    </div>
                   *
                   */ ?>
                </div>
              </li>
            <?php endfor; ?>

            <?php if ($this->page && $this->comments->getCurrentPageNumber() < $this->comments->count()): ?>
              <li>
                <div> </div>
                <div class="comments_viewall">
                  <?php
                  echo $this->htmlLink('javascript:void(0);', $this->translate('View later comments'), array('class' => 'ui-link',
                      'onclick' => 'sm4.core.photocomments.loadComments("' . $this->subject()->getType() . '", "' . $this->subject()->getIdentity() . '", "' . ($this->page + 1) . '")'
                  ))
                  ?>
                </div>
              </li>
            <?php endif; ?>
          <?php else: ?>
            <li>
              <div class="no-comments">
                <i class="ui-icon ui-icon-comment-alt"></i>
                <span><?php echo $this->translate('No Comments') ?></span>
              </div>	
            </li>
          <?php endif; ?>

        </ul>
      </div>
    </div>
    <?php if (isset($this->form)): ?>
      <div class="ps-carousel-comments-post-input sm-comments-post-comment"  id="photo-comment-form-input">
        <div>
          <input type="text" placeholder="<?php echo $this->translate('Write a comment...'); ?>" data-role="none" class="ui-input-field" />
        </div> 
      </div>
      <div class="ps-carousel-comments-post-body sm-comments-post-comment-form" id="photo-comment-form-body" style="display: none;">
        <form id="photo-comment-form"  data-ajax="false" method="post" enctype="application/x-www-form-urlencoded">
          <table>
            <tr>    
              <td class="sm-cmf-left">
                <div>
                  <?php
                  foreach ($this->form->getElements() as $key => $value):
                    if ($key != "submit") : echo $this->form->$key;
                    endif;
                  endforeach;
                  ?>
                </div>
              </td>
              <td>
                <?php echo $this->form->submit?>
              </td>
            </tr>
          </table>
        </form>
      </div>
    <?php endif; ?>
    <?php if (!$this->page): ?>
    </div>
  </li>	
<?php endif; ?>