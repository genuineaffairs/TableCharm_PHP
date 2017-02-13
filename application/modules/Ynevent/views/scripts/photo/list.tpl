
<h2>  
  <?php echo $this->event->__toString()." ".$this->translate("&#187; Photos") ?>
</h2>

<?php if( $this->canUpload ): ?>
  <div class="ynevent_photos_list_options">
    <?php echo $this->htmlLink(array(
        'route' => 'event_extended',
        'controller' => 'photo',
        'action' => 'upload',
        'subject' => $this->subject()->getGuid(),
      ), $this->translate('Upload Photos'), array(
        'class' => 'buttonlink icon_event_photo_new'
    )) ?>
  </div>
<?php endif; ?>

<div class='layout_middle'>
  <?php if( $this->paginator->count() > 0 ): ?>
    <?php echo $this->paginationControl($this->paginator); ?>
    <br />
  <?php endif; ?>
  <ul class="thumbs thumbs_nocaptions">
    <?php foreach( $this->paginator as $photo ): ?>
      <li>
        <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
          <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
        </a>
      </li>
    <?php endforeach;?>
  </ul>
  <?php if( $this->paginator->count() > 0 ): ?>
    <br />
    <?php echo $this->paginationControl($this->paginator); ?>
  <?php endif; ?>
</div>