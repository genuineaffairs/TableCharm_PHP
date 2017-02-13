<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: level.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  var fetchLevelSettings =function(level_id){
    window.location.href= en4.core.baseUrl+'admin/document/settings/level/id/'+level_id;
  }
</script>

<h2><?php echo $this->translate('Documents Plugin'); ?></h2>

<?php if( count($this->navigation) ): ?>
	<div class='seaocore_admin_tabs'>
		<?php	echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
	</div>
<?php endif; ?>

<div class='clear seaocore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>

<script type="text/javascript">

	if($('secure_allow-wrapper')) {
		$('secure_allow-1').addEvent('click', function(){
				$('secure_show-wrapper').setStyle('display', ($(this).get('value') == '1'?'block':'none'));
		});

		$('secure_allow-0').addEvent('click', function(){
				$('secure_show-wrapper').setStyle('display', ($(this).get('value') == '0'?'none':'block'));
		});
	}
	
	if($('email_allow-wrapper')) {
		$('email_allow-1').addEvent('click', function(){
				$('email_show-wrapper').setStyle('display', ($(this).get('value') == '1'?'block':'none'));
		});

		$('email_allow-0').addEvent('click', function(){
				$('email_show-wrapper').setStyle('display', ($(this).get('value') == '0'?'none':'block'));
		});
	}

	if($('download_allow-wrapper')) {
		$('download_allow-1').addEvent('click', function(){
				$('download_show-wrapper').setStyle('display', ($(this).get('value') == '1'?'block':'none'));
		});

		$('download_allow-0').addEvent('click', function(){
				$('download_show-wrapper').setStyle('display', ($(this).get('value') == '0'?'none':'block'));
		});
	}

	$('profile_doc-1').addEvent('click', function(){
			$('profile_doc_show-wrapper').setStyle('display', ($(this).get('value') == '1'?'block':'none'));
	});

	$('profile_doc-0').addEvent('click', function(){
			$('profile_doc_show-wrapper').setStyle('display', ($(this).get('value') == '0'?'none':'block'));
	});

	window.addEvent('domready', function() {
		if($('secure_show-wrapper') && $('secure_allow-wrapper'))
			$('secure_show-wrapper').setStyle('display', ($('secure_allow-1').checked ?'block':'none'));

		if($('email_show-wrapper') && $('email_allow-wrapper'))
			$('email_show-wrapper').setStyle('display', ($('email_allow-1').checked ?'block':'none'));

		if($('download_show-wrapper') && $('download_allow-wrapper'))
			$('download_show-wrapper').setStyle('display', ($('download_allow-1').checked ?'block':'none'));

		$('profile_doc_show-wrapper').setStyle('display', ($('profile_doc-1').checked ?'block':'none'));
	});
</script>