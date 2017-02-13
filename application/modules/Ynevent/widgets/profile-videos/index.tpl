
  <div class="ynevent_album_options">
    <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
      	<?php echo $this->htmlLink(
  				array(
	  				'route' => 'event_extended', 
	  				'controller'=>'video',
	  				'action'=>'manage',
	  				'subject' => $this->subject()->getGuid()
  				), 
  				$this->translate('Manage Videos'), 
  				array(
    				'class' => 'buttonlink icon_event_video'
  				)
  		)?>
    <?php endif; ?>
    <?php if( $this->canCreate ): ?>
	    <?php echo $this->htmlLink(array(
	        'route' => 'video_general',
	        'action' => 'create',
	        'parent_type' =>'event',
	        'subject_id' =>  $this->event->event_id,
	    	'tab' => $this->identity,
	      ), $this->translate('Create New Video'), array(
	        'class' => 'buttonlink icon_event_video_new'
	    )) ?>
  <?php endif; ?>
  </div>
  <br />



<!-- Search Bar 
<div class="poll_search_form">
  <?php echo $this->form->render($this);?>
</div>
<br/>
-->
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
