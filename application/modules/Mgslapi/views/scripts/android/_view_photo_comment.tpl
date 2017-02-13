<?php
$comments = $this->comments->getIterator();
if ($this->page AND $this->page > 1):
    //echo 'if'; exit;
    $i = 0;
    $l = count($comments) - 1;
    $d = 1;
    $e = $l + 1;
else:
    //echo 'else'; exit;
    $i = count($comments) - 1;
    $l = count($comments);
    $d = -1;
    $e = -1;
endif;

for (; $i != $e; $i += $d):
    $comment = $comments[$i];
    $poster = $this->item($comment->poster_type, $comment->poster_id);
    ?>
    <div class="commentsRow" id="<?php echo $comment->comment_id ?>">
        <div class="comments_author_photo">
            <a href="#"><img src="<?php echo Engine_Api::_()->mgslapi()->getItemPhotoUrl($poster); ?>" alt="" title="" /></a>
        </div>
        <div class="comments_info">
            <?php echo $this->htmlLink('#', $poster->getTitle()); ?><br />
            <div class="desc"><?php echo $comment->body ?></div>
            <div class="timestamp">
                <p><?php echo $this->timestamp($comment->creation_date); ?></p>
                <!--<span>.</span>-->
                <?php
                if ($this->canComment):
                    $isLiked = $comment->likes()->isLike($this->viewer);

//                    if (!$isLiked):
                        ?>
                
                        <!--<div class="feed_like"><a href="#">Like</a></div>-->
                    <?php // else: ?>
                        <!--<div class="feed_like"><a href="#">Unlike</a></div>-->
        <?php // endif ?>
    <?php endif ?>
            </div>
        </div>
        <div class="clear"></div>
    </div>
<?php endfor; ?>   