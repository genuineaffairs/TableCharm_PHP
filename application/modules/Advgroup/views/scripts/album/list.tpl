<!-- Header -->
<h2>
    <?php echo $this->group->__toString();
          echo $this->translate('&#187; Albums');
    ?>
</h2>
<!-- Menu Bar -->
<div class="group_discussions_options">
  <?php echo $this->htmlLink(array('route' => 'group_profile', 'id' => $this->group->getIdentity()), $this->translate('Back to Group'), array(
    'class' => 'buttonlink icon_back'
  )) ?>
  <?php if( $this->canUpload ): ?>
    <?php echo $this->htmlLink(array(
        'route' => 'group_extended',
        'controller' => 'album',
        'action' => 'create',
        'subject' => $this->subject()->getGuid(),
      ), $this->translate('Create Album'), array(
        'class' => 'buttonlink icon_group_photo_new'
    )) ?>
  <?php endif; ?>
</div>
<!-- Search Form -->
<div class="album_search_form">
    <?php echo $this->form->render($this);?>
</div>
<br/>
<!-- Content -->
  <?php if( $this->paginator->getTotalItemCount() > 0 ): $group = $this->group;?>
  <ul class="thumbs">
    <?php foreach( $this->paginator as $album ): ?>
     <li>
        <a class="thumbs_photo" href="<?php echo $album->getHref(); ?>" style="padding:1px;">
          <?php $photo = $album->getFirstCollectible();
                if($photo):?>
            <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal');?>)"></span>
          <?php else:?>
            <span style="background-image: url(./application/modules/Advgroup/externals/images/nophoto_group_thumb_normal.png)"></span>
          <?php endif;?>
        </a>
        <p class="thumbs_info" style="width:142px">
          <?php $title = Engine_Api::_()->advgroup()->subPhrase($album->getTitle(),23);
                if($title == '') $title = "Untitle Album";
                echo $this->htmlLink($album->getAlbumHref(),"<b>".$title."</b>");?>
          <br/>
          <?php echo $this->translate('By');?>
          <?php if($album->user_id != 0 ){
              $name = Engine_Api::_()->advgroup()->subPhrase($album->getMemberOwner()->getTitle(),20);
              echo $this->htmlLink($album->getMemberOwner()->getHref(), $name , array('class' => 'thumbs_author'));
            }
             else{
              $name = Engine_Api::_()->advgroup()->subPhrase($group->getOwner()->getTitle(), 20);
              echo $this->htmlLink($group->getOwner()->getHref(), $group->getOwner()->getTitle(), array('class' => 'thumbs_author'));
             }
          ?>
          <br />
          <?php echo $this->timestamp($album->creation_date) ?>
        </p>
      </li>
   <?php endforeach;?>
  </ul>
<?php if( $this->paginator->count() > 0 ): ?>
        <?php echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => true,
      'query' => $this->formValues,
)); ?>
  <?php endif; ?>
  <?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No albums have been uploaded.');?>
      <?php if($this->canUpload):?>
      <?php echo $this->translate('Create a %1$snew one%2$s',
              '<a href="'.$this->url(array('controller'=>'album','action' => 'create','subject' =>$this->group->getGuid()), 'group_extended').'">', '</a>');?>
        <?php endif;?>
    </span>
  </div>
  <?php endif; ?>