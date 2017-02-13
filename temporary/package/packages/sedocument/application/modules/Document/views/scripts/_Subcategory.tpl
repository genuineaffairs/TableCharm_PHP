<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _Subcategory.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
echo "
	<li id='subcategory_id-label' style='display:none;' > 


		 <span >" . $this->translate('Subcategory') . "</span>


			<select name='subcategory_id' id='subcategory_id' onchange='subcate(this.value,0);' >
	
			</select>

	</li>";
?>
<?php
echo "
	<li id='subsubcategory_id-label' style='display:none;'>


		 <span >" . $this->translate('3%s Level Category', "<sup>rd</sup>") . "</span>


			<select name='subsubcategory_id' id='subsubcategory_id' onchange='changesubsubcategory(this.value);'>

			</select>

	</li>";
?>
<script type="text/javascript">
  function subcate(subcate, subsubcate) {
  	$('subcategory').value = subcate;
    changesubcategory(subcate, subsubcate);
  }
</script>
<script type="text/javascript">
  function changesubsubcategory(subsubcate) {
    if($('subsubcategory'))
  	$('subsubcategory').value = subsubcate;
   }
</script>
<script type="text/javascript">

  if($('subcategory_id'))
    $('subcategory_id').style.display = 'none';
  if($('subcategory_id-label'))
    $('subcategory_id-label').style.display = 'none';
  if($('subsubcategory_id'))
    $('subsubcategory_id').style.display = 'none';
  if($('subsubcategory_id-label'))
    $('subsubcategory_id-label').style.display = 'none';
</script>