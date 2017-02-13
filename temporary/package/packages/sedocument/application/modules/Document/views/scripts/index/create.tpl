<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
  $this->headScript()
    ->appendFile($this->seaddonsBaseUrl().'/externals/autocompleter/Observer.js')
    ->appendFile($this->seaddonsBaseUrl().'/externals/autocompleter/Autocompleter.js')
    ->appendFile($this->seaddonsBaseUrl().'/externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->seaddonsBaseUrl().'/externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">
  en4.core.runonce.add(function()
  {
    new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>', {
      'postVar' : 'text',

      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest',
      'customChoices' : true,
      'filterSubset' : true,
      'multiple' : true,
      'injectChoice': function(token){
        var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token.label, 'id':token.id});
        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
        choice.inputValue = token;
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      }
    });
  });

	function hidebutton() {
		if(document.getElementById('submit_button'))
			document.getElementById('submit_button').style.display='none';
		if(document.getElementById('loading_img'))
			document.getElementById('loading_img').style.display='block';
	}  
  
	function showlightbox() {
		document.getElementById('light').style.display='block';
		document.getElementById('fade').style.display='block';
	}
</script>

<?php if($this->excep_error == 1): ?>
	<ul class="form-errors">
		<li style="font-size:12px;">
			<?php echo $this->excep_message ?>
		</li>
	</ul>
<?php endif; ?>

<?php if($this->is_error == 1): ?>
	<ul class="form-errors">
		<li style="font-size:12px;">
			<?php echo $this->api_error ?>
		</li>
	</ul>
<?php endif; ?>

<?php if( count($this->navigation) ): ?>
	<div class="headline">
		<h2>
	  	<?php echo $this->translate('Documents'); ?>
		</h2>
  	<div class='tabs'>
    	<?php echo $this->navigation()->setContainer($this->navigation)->menu()->render() ?>
  	</div>
	</div>  
<?php endif; ?>

<?php if($this->error_doc_limit == 1): ?>
	<ul class="form-errors">
		<li style="font-size:12px;">
			<?php echo $this->translate('You have already created ') . $this->entries . $this->translate(' documents, and reached the limit for number of documents. You cannot create more documents.'); ?>
		</li>
	</ul> 
<?php else: ?> 
	<?php if(empty($this->create_api)){ echo $this->translate($this->document_create); } else { echo $this->form->render($this); } ?>
<?php endif; ?>

<?php
	/* Include the common user-end field switching javascript */
	echo $this->partial('_jsSwitch.tpl', 'fields', array(
	// 		'topLevelId' => (int) @$this->topLevelId,
	// 		'topLevelValue' => (int) @$this->topLevelValue
	))
?>

<div id="light" class="document_uploading_white_content">
	<?php echo $this->translate('Uploading'); ?>
	<img src="application/modules/Document/externals/images/document-uploading.gif" alt="" />
</div>

<div id="fade" class="document_uploading_black_overlay"></div>

<script type="text/javascript">

	var getProfileType = function(category_id) {
		var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('profilemaps', 'document')->getMapping()); ?>;
		for(i = 0; i < mapping.length; i++) {
			if(mapping[i].category_id == category_id)
				return mapping[i].profile_type;
		}
		return 0;
	}

	var defaultProfileId = '<?php echo '0_0_'.$this->defaultProfileId ?>'+'-wrapper';
	if($type($(defaultProfileId)) && typeof $(defaultProfileId) != 'undefined') {
		$(defaultProfileId).setStyle('display', 'none');
	}

</script>

<script type="text/javascript">

	if($('default_visibility')) {
		$('default_visibility').addEvent('click', function(){
			if($('secure_allow-wrapper'))
				$('secure_allow-wrapper').setStyle('display', ($(this).get('value') == 'public'?'none':'block'));

			if($('download_allow-wrapper'))
				$('download_allow-wrapper').setStyle('display', ($(this).get('value') == 'public'?'none':'block'));

			if($('email_allow-wrapper'))
				$('email_allow-wrapper').setStyle('display', ($(this).get('value') == 'public'?'none':'block'));
		});

		window.addEvent('domready', function() {

			if($('secure_allow-wrapper'))
				$('secure_allow-wrapper').setStyle('display', ($('secure_allow-wrapper').get('value') == 'public'?'none':'block'));

			if($('download_allow-wrapper'))
				$('download_allow-wrapper').setStyle('display', ($('download_allow-wrapper').get('value') == 'public'?'none':'block'));

			if($('email_allow-wrapper'))
				$('email_allow-wrapper').setStyle('display', ($('email_allow-wrapper').get('value') == 'public'?'none':'block'));

		});
	}

</script>