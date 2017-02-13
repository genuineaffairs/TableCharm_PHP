<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">

  var tagCloudAction = function(tag){
    if($('filter_form')) {
       var form = document.getElementById('filter_form');
      }else if($('filter_form_tag')){
				var form = document.getElementById('filter_form_tag');
    }

    form.elements['tag'].value = tag;
		form.submit();
  }
</script>

<form id='filter_form_tag' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'browse'), 'document_browse', true) ?>' style='display: none;'>
	<input type="hidden" id="tag" name="tag"  value=""/>
</form>

<?php if($this->owner_id): ?>
	<h3><?php echo $this->translate($this->owner->getTitle()), $this->translate("'s Tags") ?></h3>
<?php else: ?>
	<h3><?php echo $this->translate('Popular Tags ');?>(<?php echo $this->count_only ?>)</h3>
<?php endif; ?>
<ul class="seaocore_sidebar_list">
	<li>
		<div>
			<?php foreach($this->tag_array as $key => $frequency):?>
				<?php $step = $this->tag_data['min_font_size'] + ($frequency - $this->tag_data['min_frequency'])*$this->tag_data['step'] ?>
				<a href='javascript:void(0);' onclick='javascript:tagCloudAction(<?php echo $this->tag_id_array[$key]; ?>);' style="font-size:<?php echo $step ?>px;" title=''><?php echo $key ?><sup><?php echo $frequency ?></sup></a> 
			<?php endforeach;?>
		</div>		
	</li>
	<li><?php echo $this->htmlLink(array('route' => 'document_tagscloud'), $this->translate('Explore Tags &raquo;'), array('class'=>'more_link')) ?></li>
</ul>