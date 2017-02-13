<h2 class="importer_heading">
  <?php echo $this->translate('Event Calendar'); ?>
</h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<?php echo $this->content()->renderWidget('Ecalendar.plugin-updates'); ?>
