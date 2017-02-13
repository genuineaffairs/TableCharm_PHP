<div class='global_form zulu-progress-wrapper'>
    <div>
        <div>
            <div class='progress'>
                <?php for($i = 0; $i < $this->steps['total_steps']; $i++) :
                        if($this->steps['cur_step'] != ($i+1)) {
                            $class = ' inactive';
                        } else {
                            $class = '';
                        }
                    ?>
                    <div class='progress-bar<?php echo $class; ?>' style='width: <?php echo (100 / $this->steps['total_steps']) ?>%'>
                        Step <?php echo ($i+1); ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</div>

<link href="<?php echo $this->css ?>" media="screen" rel="stylesheet"s type="text/css" />