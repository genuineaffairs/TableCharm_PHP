<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>

<script type="text/javascript">
    en4.core.runonce.add(function() {
        updateSubCategories();
    });
</script>

<div class="headline">
    <h2>
        <?php echo $this->translate('Videos'); ?>
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

<?php echo $this->partial('_categories_script.tpl', array('categories' => $this->categories)) ?>

<?php
    echo $this->form->render();
?>
