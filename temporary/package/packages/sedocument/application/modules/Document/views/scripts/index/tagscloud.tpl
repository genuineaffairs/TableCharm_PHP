<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: tagcloud.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  
  var tagAllAction = function(tag){
    $('tag').value = tag;
    $('filter_form_tagscloud').submit();
  }

</script>

<h3><b><?php echo $this->translate('Popular Tags for Documents'); ?></b></h3>
<?php echo $this->translate('Browse the tags created for documents by the various members.'); ?>
<br />

<?php if(!empty($this->tag_array)):?>

	<form id='filter_form_tagscloud' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'browse'), 'document_browse', true) ?>' style='display: none;'>
		<input type="hidden" id="tag" name="tag"  value=""/>
	</form>

	<div style="margin-top:50px;">
		<?php foreach($this->tag_array as $key => $frequency):?>
			<?php $step = $this->tag_data['min_font_size'] + ($frequency - $this->tag_data['min_frequency'])*$this->tag_data['step'] ?>
			<a href='javascript:void(0);' onclick='javascript:tagAllAction(<?php echo $this->tag_id_array[$key]; ?>);' style="font-size:<?php echo $step ?>px;" title=''><?php echo $key ?><sup><?php echo $frequency ?></sup></a>&nbsp; 
		<?php endforeach;?>
	</div>
	<br /><br /><br /><br /><br />

<?php endif; ?>