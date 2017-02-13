<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: admodule-create.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">

	window.addEvent('domready', function() {
		if( $('is_adedit').value == 0 ){
			if( $('is_error').value == 0 ) {
				$('adtable_name-wrapper').style.display = 'none';
				$('addbtable_title-wrapper').style.display = 'none';
				$('adtable_title-wrapper').style.display = 'none';
				$('adtable_body-wrapper').style.display = 'none';
				//$('adtable_photo-wrapper').style.display = 'none';
				$('adtable_owner-wrapper').style.display = 'none';
				$('dummy_title-wrapper').style.display = 'none';
				$('is_error').value = 1;
			}
		}
	});

	var adModuleInfo = function( module_name )
	{
		if( $('is_adedit').value == 0 ){
			$('adtable_name-wrapper').style.display = 'block';
			var label_name = $('adtable_name-label').innerHTML;
			$('adtable_name-label').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/loader.gif" /></center>';
			$('adtable_name-element').style.display = 'none';
			$('addbtable_title-wrapper').style.display = 'none';
			$('adtable_title-wrapper').style.display = 'none';
			$('adtable_owner-wrapper').style.display = 'none';
			$('adtable_body-wrapper').style.display = 'none';
			//$('adtable_photo-wrapper').style.display = 'none';
			$('dummy_title-wrapper').style.display = 'none';

			en4.core.request.send(new Request.JSON({
				url : en4.core.baseUrl + 'admin/communityad/widgets/admodulesinfo?module_name=' + module_name,
				data : {
					format : 'json'
				},
				onSuccess : function(responseJSON) {
					// Condition: Return the 'table name' acording to the module name, If selected module info is not available in 'communityad_module' table then it would be return 0.
					if( responseJSON.table_name != 0 ) {
						$('adtable_name-element').style.display = 'block';
						$('addbtable_title-wrapper').style.display = 'block';
						$('adtable_title-wrapper').style.display = 'block';
						$('adtable_owner-wrapper').style.display = 'block';
						$('dummy_title-wrapper').style.display = 'block';
						$('adtable_body-wrapper').style.display = 'block';
						//$('adtable_photo-wrapper').style.display = 'block';
						$('adtable_name-label').innerHTML = 'Ad Table Name';//label_name;
						$('adtable_name').value = responseJSON.table_name;
						$('addbtable_title').value = responseJSON.title_field;
						$('adtable_title').value = responseJSON.title_field;
						$('adtable_body').value = responseJSON.body_field;
						//$('adtable_photo').value = responseJSON.image_field;
						$('adtable_owner').value = responseJSON.owner_field;
						$('adtable_title-element').getElement('.description').innerHTML = '';
						$('adtable_body-element').getElement('.description').innerHTML = '';
						//$('adtable_photo-element').getElement('.description').innerHTML = '';
						$('adtable_owner-element').getElement('.description').innerHTML = '';
					}else {
						$('adtable_name-element').style.display = 'block';
						$('addbtable_title-wrapper').style.display = 'block';
						$('adtable_title-wrapper').style.display = 'block';
						$('adtable_body-wrapper').style.display = 'block';
						//$('adtable_photo-wrapper').style.display = 'block';
						$('adtable_owner-wrapper').style.display = 'block';
						$('dummy_title-wrapper').style.display = 'block';
						$('addbtable_title').value = '';
						$('adtable_title').value = '';
						$('adtable_body').value = '';
						//$('adtable_photo').value = '';
						$('adtable_owner').value = '';
						$('adtable_title-element').getElement('.description').innerHTML = '<?php echo $this->translate("Ex: title") ?>';
						$('adtable_body-element').getElement('.description').innerHTML = '<?php echo $this->translate("Ex: body or description") ?>';
						//$('adtable_photo-element').getElement('.description').innerHTML = 'Ex: photo_id or image_id';
						$('adtable_owner-element').getElement('.description').innerHTML = '<?php echo $this->translate("Ex: owner_id or user_id") ?>';
						$('adtable_name-label').innerHTML = label_name;
						$('adtable_name').value = '';
					}
				}
			}));
		}
	};
</script>

	<h2><?php echo $this->translate("Community Ads Plugin") ?></h2>

	<?php if( count($this->navigation) ): ?>
	<div class='communityad_admin_tabs'>
		<?php
			// Render the menu
			echo $this->navigation()->menu()->setContainer($this->navigation)->render()
		?>
	</div>
	<?php endif; ?>

	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'widgets', 'action' => 'admodule-manage'), $this->translate("Back to Manage Modules for Ads"), array('class'=>'cmad_icon_back buttonlink')) ?>
	<br style="clear:both;" /><br />
	
	
	<div class='settings'>
	  <?php
			if( ($this->module_form_count > 1) || (!empty($this->modules_id)) ) {
				echo $this->form->render($this);
			}else {
				echo '<div class="tip"><span>'. $this->translate('Advertising is currently enabled for all content modules on your site.'). '</span></div>';
			}
		?>
	</div>

	<style type="text/css">
	.settings form
	{
		float:none;
	}
	.settings .form-description 
	{
		max-width: 850px;
	}
	.settings .form-element .description 
	{
		max-width: 600px;
	}
	.settings .form-element 
	{
		width:650px;
	}
	input[type="checkbox"]
	{
		float:left;
	}
	label.optional
	{
		float:left;
		max-width:600px;
	}
	#dummy_title-wrapper .form-label
	{
		width:600px;
	}
	</style>