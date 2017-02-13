<ul class="generic_layout_container" id="active_group">
  <?php foreach( $this->groups as $item ): ?>
    <li>
      <div class="group_photo">
        <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'), array('class' => 'thumb')) ?>
      </div>
      <?php
        $title = Engine_Api::_()->advgroup()->subPhrase($item->getTitle(),18);
        $owner_name = Engine_Api::_()->advgroup()->subPhrase($item->getOwner()->getTitle(),13);
     ?>
      <div class="group_info" style="padding: 2px 0px 2px 0px">
          <?php echo "<b>".$this->htmlLink($item->getHref(),$title)."</b>"; ?>
        <div class="group_owner" style="font-size: 11px; color:#7E7E7E;">
          <?php echo $this->translate('led by %1$s',
              $this->htmlLink($item->getOwner()->getHref(),$owner_name))?>
          <br/>
          <?php if(!$item->topic_id):
                $item->topic_count = 0;
                endif;
                  echo $this->translate(array('%s topic', '%s topics', $item->topic_count), $this->locale()->toNumber($item->topic_count)) ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
<?php if(count($this->groups)>= $this->limit):?>
<br/>
<div style="padding-right :10px;float:right; font-weight: bold; padding-bottom: 4px; padding-right: 22px;">
     <?php echo $this->htmlLink($this->url(array('action'=>'listing'), 'group_general'),
     $this->translate("View more"), array('class'=>'group_viewmore')); ?>
</div>
<?php endif;?>