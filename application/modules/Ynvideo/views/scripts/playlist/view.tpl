<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>
<div class="ynvideo_playlist_headline">
    <h2>
        <?php echo $this->translate('Playlist') . ' ' . $this->playlist->title?>        
        <span class="ynvideo_link_share"> 
            <?php
                if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
                    echo $this->htmlLink(array(
                        'module' => 'activity',
                        'controller' => 'index',
                        'action' => 'share',
                        'route' => 'default',
                        'type' => $this->playlist->getType(),
                        'id' => $this->playlist->getIdentity(),
                        'format' => 'smoothbox'
                            ), $this->translate("Share"), array(
                        'class' => 'smoothbox'
                    ));
                }
            ?>
        </span>
    </h2>
    
    <div class="ynvideo_playlist_headline_control">
        <?php 
            if ($this->canEdit) {
                echo $this->htmlLink(
                        array(
                            'route' => 'video_playlist',
                            'action' => 'edit',
                            'playlist_id' => $this->playlist->getIdentity()
                        ), 
                        $this->translate('Edit'),
                        array('class' => 'buttonlink icon_video_edit')
                );
            }
        ?>
        &nbsp;
        <?php 
            if ($this->canDelete) {
                echo $this->htmlLink(
                        array(
                            'route' => 'video_playlist',
                            'action' => 'delete',
                            'playlist_id' => $this->playlist->getIdentity()
                        ), 
                        $this->translate('Delete'),
                        array('class' => 'buttonlink icon_video_delete smoothbox')
                );
            }
        ?>
    </div>
    
    <div class="ynvideo_clear"></div>
</div>
<?php
    $totalVideo = $this->videoPaginator->getTotalItemCount();
?>


<div class="ynvideo_playlist_detail_description">
    <?php echo $this->playlist->description ?>
</div>

<div class="ynvideo_date ynvideo_block">
    <?php
        echo $this->timestamp($this->playlist->creation_date)
    ?>
</div>

<?php if ($totalVideo > 0) : ?>
    <ul class="ynvideo_videos_manage ynvideo_frame ynvideo_block">
        <h3>
            <?php echo $this->translate(array('%s video', '%s videos', $totalVideo), $this->locale()->toNumber($totalVideo)) ?>
        </h3>
        <?php foreach ($this->videoPaginator as $video) : ?>
            <li>
                <?php echo $this->partial('_video.tpl', array('video' => $video, 'canRemove' => $this->canRemove)) ?>
            </li>   
        <?php endforeach; ?>
        <?php if ($this->videoPaginator->getCurrentItemCount() < $totalVideo) : ?>
            <li class="ynvideo_pages">
                <?php echo $this->paginationControl($this->videoPaginator, null, null, array('query' => $this->params));?>
            </li>
        <?php endif; ?>
    </ul>
<?php else : ?>
    <div class="tip ynvideo_block">
        <span>
            <?php
                if (array_key_exists('search', $this->params)) {
                    echo $this->translate('There are no videos.'); 
                } else {
                    if (isset($this->viewer) && $this->playlist->user_id == $this->viewer->getIdentity()) {
                        echo $this->translate('You do not have any videos in this playlist. Please add one.'); 
                    } else {
                        echo $this->translate('There are no videos.');
                    }
                }
            ?>
        </span>
    </div>
<?php endif; ?>