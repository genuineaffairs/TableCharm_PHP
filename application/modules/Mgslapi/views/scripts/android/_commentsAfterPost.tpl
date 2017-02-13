<?php $comments = $this->comments;
foreach($comments as $comment):?>
    <div class="commentsRow" id="<?php echo $comment->comment_id;?>">
    <div class="comments_author_photo">
        <a href="#"><img src="<?php echo Engine_Api::_()->mgslapi()->getItemPhotoUrl($this->item($comment->poster_type, $comment->poster_id), 'thumb.profile');  ?>"  alt="" title="" /></a>   
    </div>
    <div class="comments_info">
        <a class="comments_author_name" href="#"><?php echo $this->item($comment->poster_type, $comment->poster_id)->getTitle();?></a>
        <div class="desc"><?php echo $comment->body ?></div>
        <div class="timestamp">
            <p><?php echo strip_tags($this->timestamp($comment->creation_date))?></p>
            <span>.</span>
            <div class="feed_like"><img src="<?php echo  $this->serverUrl((string)$this->baseUrl())?>/application/modules/Mgslapi/externals/images/commenticon.png" alt="" title="" /><span class="comment_count"><?php echo $comment->likes()->getLikeCount()?></span></div>
        </div>
    </div>
    <div class="clear"></div>
</div>
<?php endforeach; ?>