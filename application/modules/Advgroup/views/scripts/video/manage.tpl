<!-- Header -->
<h2>
    <?php echo $this->group->__toString();
          echo $this->translate('&#187;');
          echo $this->translate('Videos');
    ?>
</h2>

<!-- Menu Bar -->
<div class="group_discussions_options">
  <?php echo $this->htmlLink(array('route' => 'group_profile', 'id' => $this->group->getIdentity()), $this->translate('Back to Group'), array(
    'class' => 'buttonlink icon_back'
  )) ?>
  <?php echo $this->htmlLink(array('route' => 'group_extended', 'controller'=>'video','action'=>'list','subject' => $this->subject()->getGuid()), $this->translate('Browse Videos'), array(
    'class' => 'buttonlink icon_group_video'
  )) ?>
 <?php if( $this->canCreate ): ?>
    <?php echo $this->htmlLink(array(
        'route' => 'video_general',
        'action' => 'create',
        'parent_type' =>'group',
        'subject_id' =>  $this->group->group_id,
      ), $this->translate('Create New Video'), array(
        'class' => 'buttonlink icon_group_video_new'
    )) ?>
  <?php endif; ?>
</div>

<!-- Search Bar -->
<div class="poll_search_form">
  <?php echo $this->form->render($this);?>
</div>
<br/>

<!-- Content -->
<?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <ul class="ynvideo_videos_manage">
        <h3>
            <?php
            $totalVideo = $this->paginator->getTotalItemCount();
            echo $this->translate(array('%1$s video', '%1$s video', $totalVideo), $this->locale()->toNumber($totalVideo));
            ?>
        </h3>
        <?php foreach ($this->paginator as $item): ?>
            <li>
                <div class="ynvideo_thumb_wrapper video_thumb_wrapper">
                    <?php if ($item->duration): ?>
                        <?php echo $this->partial('_video_duration.tpl','ynvideo', array('video' => $item)) ?>
                    <?php endif; ?>
                    <?php
                    if ($item->photo_id) {
                        echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'));
                    } else {
                        echo '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Ynvideo/externals/images/video.png">';
                    }
                    ?>
                    <span class="video_button_add_to_area">
                        <button class="ynvideo_uix_button ynvideo_add_button" video-id="<?php echo $item->getIdentity() ?>">
                            <div class="ynvideo_plus" />
                        </button>
                    </span>
                </div>
                <div class='video_options'>
                    <?php
                    echo $this->htmlLink(array(
                        'route' => 'video_general',
                        'action' => 'edit',
                        'video_id' => $item->video_id
                        ), $this->translate('Edit Video'), array('class' => 'buttonlink icon_video_edit'))
                    ?>
                    <?php
                    if ($item->status != 2) {
                        echo $this->htmlLink(array(
                            'route' => 'video_general',
                            'action' => 'delete',
                            'video_id' => $item->video_id,
                            'format' => 'smoothbox'
                            ), $this->translate('Delete Video'), array('class' => 'buttonlink smoothbox icon_video_delete'));
                    }
                    ?>
                </div>
                <div class="video_info video_info_in_list">
                    <div class="ynvideo_title">
                        <?php echo $this->htmlLink($item->getHref(), htmlspecialchars($item->getTitle())) ?>
                    </div>
                    <div class="video_stats">
                        <?php echo $this->partial('_video_views_stat.tpl','ynvideo', array('video' => $item)) ?>
                        <div class="ynvideo_block">
                            <?php echo $this->partial('_video_rating_big.tpl','ynvideo', array('video' => $item)) ?>
                        </div>
                    </div>
                    <div class="video_desc ynvideo_block">
                            <?php echo $this->string()->truncate($this->string()->stripTags($item->description), 300) ?>
                    </div>
                    <?php if ($item->status == 0): ?>
                        <div class="tip">
                            <span>
                        <?php echo $this->translate('Your video is in queue to be processed - you will be notified when it is ready to be viewed.') ?>
                            </span>
                        </div>
                            <?php elseif ($item->status == 2): ?>
                        <div class="tip">
                            <span>
                        <?php echo $this->translate('Your video is currently being processed - you will be notified when it is ready to be viewed.') ?>
                            </span>
                        </div>
                            <?php elseif ($item->status == 3): ?>
                        <div class="tip">
                            <span>
                        <?php echo $this->translate('Video conversion failed. Please try %1$suploading again%2$s.', '<a href="' . $this->url(array('action' => 'create', 'type' => 3)) . '">', '</a>'); ?>
                            </span>
                        </div>
                            <?php elseif ($item->status == 4): ?>
                        <div class="tip">
                            <span>
                        <?php echo $this->translate('Video conversion failed. Video format is not supported by FFMPEG. Please try %1$sagain%2$s.', '<a href="' . $this->url(array('action' => 'create', 'type' => 3)) . '">', '</a>'); ?>
                            </span>
                        </div>
                            <?php elseif ($item->status == 5): ?>
                        <div class="tip">
                            <span>
                        <?php echo $this->translate('Video conversion failed. Audio files are not supported. Please try %1$sagain%2$s.', '<a href="' . $this->url(array('action' => 'create', 'type' => 3)) . '">', '</a>'); ?>
                            </span>
                        </div>
                            <?php elseif ($item->status == 7): ?>
                        <div class="tip">
                            <span>
                        <?php echo $this->translate('Video conversion failed. You may be over the site upload limit.  Try %1$suploading%2$s a smaller file, or delete some files to free up space.', '<a href="' . $this->url(array('action' => 'create', 'type' => 3)) . '">', '</a>'); ?>
                            </span>
                        </div>
                            <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
  </ul>
  <br/>
  <div class="ynvideo_pages">
      <?php
      echo $this->paginationControl($this->paginator, null, null, array(
          'pageAsQuery' => true,
          'query' => $this->formValues,
      ));
      ?>
  </div>

<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('You do not have any videos.'); ?>
        </span>
    </div>
<?php endif; ?>   