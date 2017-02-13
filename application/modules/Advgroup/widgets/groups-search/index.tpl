<?php
    echo $this->form->render($this)
?>
<br/>
<script type="text/javascript">
 var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
</script>