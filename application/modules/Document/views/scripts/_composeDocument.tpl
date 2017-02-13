<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Document/externals/scripts/composer_document.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/Swiff.Uploader.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/Fx.ProgressBar.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/FancyUpload2.js');
  $this->headLink()
    ->appendStylesheet($this->layout()->staticBaseUrl . 'externals/fancyupload/fancyupload.css');
  $this->headTranslate(array(
    'Overall Progress ({total})', 'File Progress', 'Uploading "{name}"',
    'Upload: {bytesLoaded} with {rate}, {timeRemaining} remaining.', '{name}',
    'Remove', 'Click to remove this entry.', 'Upload failed',
    '{name} already added.',
    '{name} ({size}) is too small, the minimal file size is {fileSizeMin}.',
    '{name} ({size}) is too big, the maximal file size is {fileSizeMax}.',
    '{name} could not be added, amount of {fileListMax} files exceeded.',
    '{name} ({size}) is too big, overall filesize of {fileListSizeMax} exceeded.',
    'Server returned HTTP-Status <code>#{code}</code>',
    'Security error occurred ({text})',
    'Error caused a send or load operation to fail ({text})',
  ));
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    var type = 'wall';
    if (composeInstance.options.type) type = composeInstance.options.type;
    composeInstance.addPlugin(new Composer.Plugin.Document({
      title : '<?php echo $this->string()->escapeJavascript($this->translate('Add Document')) ?>',
      lang : {
        'Add Document' : '<?php echo $this->string()->escapeJavascript($this->translate('Add Document')) ?>',
        'Title' : '<?php echo $this->string()->escapeJavascript($this->translate('Title')) ?>',
        'Description' : '<?php echo $this->string()->escapeJavascript($this->translate('Description')) ?>',
        'File' : '<?php echo $this->string()->escapeJavascript($this->translate('File')) ?>',
        'Select File' : '<?php echo $this->string()->escapeJavascript($this->translate('Select File')) ?>',
        'cancel' : '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
        'Loading...' : '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
        'Unable to upload document. Please click cancel and try again': '<?php echo $this->string()->escapeJavascript($this->translate('Unable to upload document. Please click cancel and try again')) ?>'
      },
      requestOptions : {
        'url'  : en4.core.baseUrl + 'document/document/compose-upload/c_type/'+type
      },
      fancyUploadOptions : {
        'url'  : en4.core.baseUrl + 'document/document/compose-upload/format/json/c_type/'+type,
        'path' : en4.core.basePath + 'externals/fancyupload/Swiff.Uploader.swf'
      }
    }));
  });
</script>