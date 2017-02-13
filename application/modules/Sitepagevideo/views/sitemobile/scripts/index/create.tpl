<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: create.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */
?>
<?php 
$breadcrumb = array(
    array("href"=>$this->sitepage->getHref(),"title"=>$this->sitepage->getTitle(),"icon"=>"arrow-r"),
    array("href"=>$this->sitepage->getHref(array('tab' => $this->tab_selected_id)),"title"=>"Videos","icon"=>"arrow-d"));

echo $this->breadcrumb($breadcrumb);
?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitemobile/externals/scripts/core.js');
?>
<script type="text/javascript">
	var tagsUrl = '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>';
//	var validationUrl = '<?php //echo $this->url(array('module' => 'video', 'controller' => 'index', 'action' => 'validation'), 'default', true) ?>';
	var validationErrorMessage = "<?php echo $this->translate("We could not find a video there - please check the URL and try again. If you are sure that the URL is valid, please click %s to continue.", "<a class='ui-link' href='javascript://' onclick='sm4.core.Module.video.index.ignoreValidation();'>".$this->translate("here")."</a>"); ?>";
	var checkingUrlMessage = '<?php echo $this->string()->escapeJavascript($this->translate('Checking URL...')) ?>';
</script>

<?php echo $this->form->render($this);?>

<script type="text/javascript">

  function checkVideoType() {
    if($('#type').val() == '3') {
      $("#Filedata-wrapper").css('display', '');
      $("#url-wrapper").css('display', 'none');
    } else {
      $("#url-wrapper").css('display', '');
      $("#Filedata-wrapper").css('display', 'none');
    }
  }

  function displayVideoType() {
    checkVideoType();
    $('#type').change(function() {
      checkVideoType();
    });
  }

	$(document).ready(function() {
		sm4.core.Module.autoCompleter.attach("tags", '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>', {'singletextbox': true, 'limit':10, 'minLength': 1, 'showPhoto' : true, 'search' : 'text'}, 'toValues');  
    
    //console.log($._data( $("#upload")[0], "events" ));
	});
  
  
  sm4.core.runonce.add(function() {
    displayVideoType();
  });
    
</script>