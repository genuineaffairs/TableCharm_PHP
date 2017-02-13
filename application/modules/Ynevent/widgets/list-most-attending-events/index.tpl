
<ul class="generic_list_widget">
  <?php foreach( $this->paginator as $item ): ?>
    <li>
      <div class="photo">
        <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('class' => 'thumb')) ?>
      </div>
      <div class="info">
        <div class="title">
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
        </div>
        <div class="stats">
          <?php echo $this->timestamp(strtotime($item->starttime)) ?>
          - <?php echo $this->translate('led by %1$s',
              $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle())) ?>
          <?php if( $this->popularType == 'view' ): ?>
            - <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>
          <?php else /*if( $this->popularType == 'member' )*/: ?>
            - <?php echo $this->translate(array('%s attending', '%s attendings', $item->member_attending), $this->locale()->toNumber($item->member_attending)) ?>
          <?php endif; ?>
        </div>
      </div>
      
    </li>
  <?php endforeach; ?>
</ul>
