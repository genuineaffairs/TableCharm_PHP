<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<script type="text/javascript">

  var tagAction = function(tag){
    if($('filter_form')) {
       form=document.getElementById('filter_form');
      }else if($('filter_form_page')){
				form=$('filter_form_page');
    }   
    form.elements['tag'].value = tag;
    if( $('filter_form'))
    $('filter_form').submit();
		else
		$('filter_form_page').submit();
  }
</script>

<form id='filter_form_page' class='global_form_box' method='get' action='<?php echo $this->url(array(), 'sitepagenote_browse', true) ?>' style='display: none;'>
	<input type="hidden" id="tag" name="tag"  value=""/>
</form>

<h3><?php echo $this->translate('Popular Note Tags'); ?> (<?php echo $this->count_only ?>)</h3>
<ul class="sitepage_sidebar_list">
	<li>
		<?php foreach ($this->tag_array as $key => $frequency): ?>
			<?php $step = $this->tag_data['min_font_size'] + ($frequency - $this->tag_data['min_frequency']) * $this->tag_data['step'] ?>
			<?php if($this->tag == $this->tag_id_array[$key]) :?>
				<?php $key =  '<b>'.$key .'</b>';?>
			<?php endif;?>
			<a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $this->tag_id_array[$key]; ?>);' style="float:none;font-size:<?php echo $step ?>px;" title=''><?php echo $key ?><sup><?php echo $frequency ?></sup></a>
		<?php endforeach; ?>
		<br/>
		<b class="explore_tag_link"><?php echo $this->htmlLink(array('route' => 'sitepagenote_tags', 'action' => 'tagscloud'), $this->translate('Explore Tags &raquo;')) ?></b>
	</li>
</ul>