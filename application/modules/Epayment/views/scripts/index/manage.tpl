<?php


?>

<div class="headline">
  <h2>
    <?php echo $this->translate('RSS Feeds');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>

<script type="text/javascript">
  var searchEpayments = function() {
    $('filter_form').submit();
  }
</script>

<div class='layout_right epayment_browse_layout_right'>
  <?php echo $this->form->render($this) ?>

  <?php if( count($this->quickNavigation) > 0 ): ?>
    <div class="quicklinks">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->quickNavigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>

<div class='layout_middle epayment_browse_layout_middle'>

  <?php if( 0 == count($this->paginator) ): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('There are no feeds yet.') ?>
        <?php if( $this->canCreate): ?>
          <?php echo $this->translate('Why don\'t you %1$screate one%2$s?',
            '<a href="'.$this->url(array('action' => 'create'), 'epayment_general').'">', '</a>') ?>
        <?php endif; ?>
      </span>
    </div>

  <?php else: // $this->epayments is NOT empty ?>

    <?php if( $this->tag || $this->search):?>
      <div class="epayments_browse_filter_details">
        <?php echo $this->translate('Showing feeds posted'); ?>
        <?php if ($this->tag): ?>
          <?php echo $this->translate('using tag #%s', $this->htmlLink(
            $this->url(array('tag'=>$this->tag,'action'=>'manage'), 'epayment_general', true),
            $this->tagObject ? $this->tagObject->text : $this->tag
          ));?>
        <?php endif; ?>
        <?php if ($this->search): ?>
          <?php echo $this->translate('with keyword %s', $this->htmlLink(
            $this->url(array('search'=>$this->search,'action'=>'manage'), 'epayment_general', true),
            $this->search
          ));?>
        <?php endif; ?>  
        <a href="<?php echo $this->url(array('action'=>'manage'), 'epayment_general', true) ?>">(x)</a>
      </div>
    <?php endif; ?>

 
      <h3 class="sep">
        <span>
          <?php if ($this->categoryObject): ?>
            <?php echo $this->translate($this->categoryObject->category_name); ?>
            <?php $this->headTitle($this->categoryObject->category_name); ?>
          <?php else: ?>  
            <?php echo $this->translate('All Categories'); ?>
          <?php endif; ?>
        </span>
      </h3> 

    <ul class="epayments_browse">
      <?php foreach ($this->paginator as $epayment): ?>
      <li id="epayment-item-<?php echo $epayment->epayment_id ?>">
        <?php echo $this->htmlLink(
          $epayment->getHref(),
          $this->itemPhoto($epayment, 'thumb.normal', $epayment->getTitle()),
          array('class' => 'epayments_browse_photo')
        ) ?>
        <div class="epayments_browse_options">
          <?php echo $this->htmlLink($epayment->getEditHref(), $this->translate('Edit Feed'), array(
            'class' => 'buttonlink icon_epayment_edit'
          )) ?>
          <?php echo $this->htmlLink($epayment->getDeleteHref(), $this->translate('Delete Feed'), array(
            'class' => 'buttonlink icon_epayment_delete'
          )) ?>
        </div>
        <div class="epayments_browse_info">
          <h3>
            <?php echo $this->htmlLink($epayment->getHref(), $epayment->getTitle()) ?>
          </h3>
          <div class="epayments_browse_info_date">
            <?php echo $this->timestamp($epayment->creation_date) ?>
            -
            <?php echo $this->translate(array('%s comment', '%s comments', $epayment->comment_count), $this->locale()->toNumber($epayment->comment_count)) ?>
            -
            <?php echo $this->translate(array('%s view', '%s views', $epayment->view_count), $this->locale()->toNumber($epayment->view_count)) ?>
            -
            <?php echo $this->translate(array('%1$s like', '%1$s likes', $epayment->like_count), $this->locale()->toNumber($epayment->like_count)); ?>
          </div>
          <?php if (!empty($epayment->description)): ?>
            <div class="epayments_browse_info_desc">
              <?php  echo $epayment->description ?>
            </div>
          <?php endif; ?>
        </div>
      </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; // $this->epayments is NOT empty ?>

  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
     // 'params' => array('route'=>'epayment_browse'),
    //'params' => $this->formValues,
  )); ?>
</div>
