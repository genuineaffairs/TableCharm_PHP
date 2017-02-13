 <?php if( count($this->paginator) > 0 ): ?>
    <ul class='groups_browse'>
      <?php foreach( $this->paginator as $group ): ?>
        <li>
          <div class="groups_photo">
            <?php echo $this->htmlLink($group->getHref(), $this->itemPhoto($group, 'thumb.normal')) ?>
          </div>
          <div class="groups_options">
          </div>
          <div class="groups_info">
            <div class="groups_title">
              <h3><?php $group_name = Engine_Api::_()->advgroup()->subPhrase($group->getTitle(),60);
                        echo $this->htmlLink($group->getHref(), $group_name);
                  ?></h3>
            </div>
            <div class="groups_members">
              <?php echo $this->translate(array('%s member', '%s members', $group->membership()->getMemberCount()),$this->locale()->toNumber($group->membership()->getMemberCount())) ?>
              <?php echo $this->translate('led by');?> <?php echo $this->htmlLink($group->getOwner()->getHref(), $group->getOwner()->getTitle()) ?>
            </div>
            <div class="groups_desc">
                <?php echo Engine_Api::_()->advgroup()->subPhrase(strip_tags($group->description),450); ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
<?php else: ?>
     <div class="tip">
       <span>
        <?php echo $this->translate('There are no groups yet.') ?>
          <?php if( $this->canCreate): ?>
            <?php echo $this->translate('Why don\'t you %1$screate one%2$s?',
            '<a href="'.$this->url(array('action' => 'create'), 'group_general').'">', '</a>') ?>
          <?php endif; ?>
       </span>
     </div>
<?php endif; ?>
<?php if( $this->paginator->count() > 1 ): ?>
     <?php echo $this->paginationControl($this->paginator, null, null, array(
            'pageAsQuery' => true,
            'query' => $this->formValues,
          )); ?>
<?php endif; ?>