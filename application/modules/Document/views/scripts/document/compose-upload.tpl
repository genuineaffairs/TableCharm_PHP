<script type="text/javascript">
  $try(function(){
    parent.en4.document.getComposer().processResponse(<?php echo $this->jsonInline($this->getVars()) ?>);
  });
  $try(function() {
    parent._composeDocumentResponse = <?php echo $this->jsonInline($this->getVars()) ?>;
  });
</script>