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
  
  var dateAction =function(start_date, end_date){
		var form = document.getElementById('filter_form_archives');
		form.elements['start_date'].value = start_date;
		form.elements['end_date'].value = end_date
    $('filter_form_archives').submit();
  }

</script>

<form id='filter_form_archives' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'browse'), 'document_browse', true) ?>' style='display: none;'>
	<input type="hidden" id="start_date" name="start_date"  value=""/>
	<input type="hidden" id="end_date" name="end_date"  value=""/>
</form>

<div class='layout_right'>
	<div class="seaocore_gutter_blocks generic_layout_container">
		<ul class="seaocore_sidebar_list">
			<?php foreach ($this->archive_list as $archive): ?>
				<li>
					<a href='javascript:void(0);' onclick='javascript:dateAction(<?php echo $archive['date_start']?>, <?php echo $archive['date_end']?>);' <?php if ($this->start_date==$archive['date_start']) echo " style='font-weight: bold;'";?>><?php echo $archive['label']?></a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>  
</div>