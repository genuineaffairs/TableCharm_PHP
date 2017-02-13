  <h2>
    <?php echo $this->group->__toString();
          echo '&#187;';
          if($this->album->getTitle()!='') echo $this->album->getTitle();
          else echo 'Untitle Album';
    ?>
</h2>

<div class="group_discussions_options">
  <?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'album', 'action' => 'list','subject' => $this->group->getGuid(),'album_id'=>$this->album->getIdentity()), $this->translate('Back to Album List'), array(
    'class' => 'buttonlink icon_back'
  )) ?>
  <?php if($this->canEdit) echo $this->htmlLink(array('route' => 'group_extended','controller' => 'album', 'action' => 'edit', 'group_id' => $this->group->getIdentity(),'album_id'=>$this->album->getIdentity()), $this->translate('Edit Album'), array(
    'class' => 'buttonlink icon_group_edit smoothbox'
  )) ?>
  <?php if($this->canEdit  && $this->album->getTitle() !== 'Group Profile') echo $this->htmlLink(array('route' => 'group_extended','controller' => 'album', 'action' => 'delete', 'group_id' => $this->group->getIdentity(),'album_id'=>$this->album->getIdentity()), $this->translate('Delete Album'), array(
    'class' => 'buttonlink icon_group_delete smoothbox'
  )) ?>
  <?php if($this->canEdit)
      echo $this->htmlLink(array('route' => 'group_extended','controller' => 'photo', 'action' => 'upload', 'subject' => $this->group->getGuid(),'album_id'=>$this->album->getIdentity()), $this->translate('Add More Photos'), array(
    'class' => 'buttonlink icon_group_photo_new'
  )) ?>
</div>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <p><?php echo $this->album->description?></p>
  <br/>
  <ul class="thumbs">
    <?php foreach( $this->paginator as $photo ): ?>
      <li style="height: 130px;">
        <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
          <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
        </a>
      </li>
    <?php endforeach;?>
  </ul>
  <?php if( $this->paginator->count() > 0 ): ?>
        <?php echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => true,
      'query' => $this->formValues,
)); ?>
  <?php endif; ?>
  <br/>
  <?php echo $this->action("list", "comment", "core", array("type"=>"advgroup_album", "id"=>$this->album->getIdentity())); ?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No photos have been uploaded in this album yet.');?>
    </span>
  </div>

<?php endif; ?>