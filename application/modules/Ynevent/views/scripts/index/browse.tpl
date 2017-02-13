<?php $repeateType = array(
			'0' => '', 
			'1' => $this->translate('daily'), 
			'7' => $this->translate('weekly'), 
			'30' => $this->translate('monthly'));
?>
<?php if (!empty($this->message)) : ?>
	<div class="tip">
		<span><?php echo $this->translate($this->message)?></span>
	</div>	
<?php endif;?>
<?php if( count($this->paginator) > 0 ): ?>
  <ul class='ynevents_browse'>
    <?php foreach( $this->paginator as $event ): ?>
      <li>
        <div class="ynevents_photo">
          <?php echo $this->htmlLink($event->getHref(), $this->itemPhoto($event, 'thumb.normal')) ?>
        </div>
        <div class="ynevents_options">
        </div>
        <div class="ynevents_info">
          <div class="ynevents_title">
            <h3>
            	<?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?>
            </h3>
          </div>
      <div class="ynevents_members">
        <?php echo $this->locale()->toDateTime($event->starttime) ?>
      </div>
          <div class="ynevents_members">
            <?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()),$this->locale()->toNumber($event->membership()->getMemberCount())) ?>
            <?php echo $this->translate('led by') ?>
            <?php echo $this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle()) ?>
          </div>
          <?php if ($event->repeat_type > 0) : ?>
          <div class="ynevents_members ynevents_title_repeat_type">
          		<?php
          		$nextEvent = $event->getNextEvent();
          		if (is_object($nextEvent)) : ?>
          			<img class="ynevent_arrow" src="application/modules/Ynevent/externals/images/types/event.png" style="margin-top: 3px;"/>
          			<a href="<?php echo $nextEvent->getHref();?>" title="<?php echo $this->translate('go to next event'); ?>" target="blank" style="margin-right: 10px;"><?php echo $this->translate('Next event'); ?></a>
				<?php endif;?>   
				<span title="<?php echo $this->translate('repeat type');?>"><?php echo $repeateType[(string)$event->repeat_type]; ?></span>       	
          </div>
          <?php endif;?>
          <div class="ynevents_desc">
            <?php
            	if(trim($event->brief_description) != "")
					echo $event->brief_description;
				else 
            		echo $event->getDescription() ?>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>

  <?php if( $this->paginator->count() > 1 ): ?>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->formValues,
    )); ?>
  <?php endif; ?>

<?php else: ?>
	<?php if (empty($this->message)) : ?>
	  <div class="tip">
	    <span>
	    <?php if( $this->filter != "past" ): ?>
	      <?php echo $this->translate('Nobody has created an event yet.') ?>
	      <?php if( $this->canCreate ): ?>
	        <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array('action'=>'create'), 'event_general').'">', '</a>'); ?>
	      <?php endif; ?>
	    <?php else: ?>
	      <?php echo $this->translate('There are no past events yet.') ?>
	    <?php endif; ?>
	    </span>
	  </div>
  	<?php endif;?>

<?php endif; ?>
