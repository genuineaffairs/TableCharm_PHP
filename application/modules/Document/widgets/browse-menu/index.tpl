<div class="headline">
  <h2>
    <?php echo $this->translate('Documents') ?>
  </h2>
  <div class="tabs">
    <?php
      // render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
  </div>
</div>
