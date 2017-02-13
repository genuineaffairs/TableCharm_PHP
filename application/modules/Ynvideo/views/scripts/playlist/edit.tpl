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
    function updateUploader()    {
        if($('photo_delete').checked) {
            $('photo_group-wrapper').style.display = 'block';
        } else {
            $('photo_group-wrapper').style.display = 'none';
        }
    }
</script>

<div class="headline">
    <h2>
        <?php echo $this->translate('Edit playlist'); ?>
    </h2>
</div>
<?php echo $this->form->render($this); ?>