<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

  <ul class="thumbs">
    <?php foreach( $this->paginator as $photo ): ?>
      <li>
        <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
          <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
        </a>
        <p class="thumbs_info">
          <?php echo $this->translate('By');?>
          <?php echo $this->htmlLink($photo->getOwner()->getHref(), $photo->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
          <br />
          <?php echo $this->timestamp($photo->creation_date) ?>
        </p>
      </li>
    <?php endforeach;?>
  </ul>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No photos have been uploaded to this group yet.');?>
    </span>
  </div>

<?php endif; ?>
