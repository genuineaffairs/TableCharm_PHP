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
  <?php echo $this->htmlLink(array('route' => 'group_extended', 'controller'=>'video','action'=>'manage','subject' => $this->subject()->getGuid()), $this->translate('My Videos'), array(
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
 <?php if ($this->paginator->getTotalItemCount()> 0) : ?>
      <ul class="videos_browse" id="ynvideo_recent_videos">
              <?php foreach ($this->paginator as $item): ?>
                  <li style="margin-right: 18px;">
                      <?php
                      echo $this->partial('_video_listing.tpl', 'ynvideo', array(
                          'video' => $item,
                          'infoCol' => $this->infoCol,
                      ));
                      ?>
                  </li>
              <?php endforeach; ?>
      </ul>
      <br/>
      <div class ="ynvideo_pages">
          <?php echo $this->paginationControl($this->paginator, null, null, array(
            'pageAsQuery' => true,
            'query' => $this->formValues,
          )); ?>
      </div>
      
<?php else : ?>
      <div class="tip">
          <span>
              <?php echo $this->translate('There is no video found.'); ?>
          </span>
      </div>
<?php endif; ?>
