<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formSubcategory.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $cateDependencyArray = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCatDependancyArray();
	$request = Zend_Controller_Front::getInstance()->getRequest();
	$module = $request->getModuleName();
	$action = $request->getActionName();
?>

<?php if($module == 'sitepage' && ($action == 'home' || $action == 'manage' || $action == 'index')):?>
	<?php
	echo "
		<li id='subcategory_id-label'> 


			<span >" . $this->translate('Subcategory') . "</span>


				<select name='subcategory_id' id='subcategory_id' onchange='subcate(this.value,0);'>
		
				</select>

		</li>";
	?>
	<?php
	echo "
		<li id='subsubcategory_id-label'>


			<span >" . $this->translate('3%s Level Category', "<sup>rd</sup>") . "</span>


				<select name='subsubcategory_id' id='subsubcategory_id' onchange='changesubsubcategory(this.value);'>

				</select>

		</li>";
	?>
<?php else:?>
<?php echo "
		<div id='subcategory_id-label' class='form-wrapper'>
			<div class='form-label'><label>" . $this->translate('Subcategory', "<sup>rd</sup>") . "</label></div>
			<div class='form-element'>
				<select name='subcategory_id' id='subcategory_id' onchange='subcate(this.value,0);'>
      </select>
      </div>
		</div>";
?>
<?php echo "
		<div id='subsubcategory_id-label' class='form-wrapper'>
			<div class='form-label'><label>" . $this->translate('3%s Level Category', "<sup>rd</sup>") . "</label></div>
			<div class='form-element'>
				<select name='subsubcategory_id' id='subsubcategory_id' onchange='changesubsubcategory(this.value,0);'>
				</select>
      </div>
		</div>";
?>
<?php endif;?>

<script type="text/javascript">
  en4.core.runonce.add(function(){
  if($('subcategory_id'))
    $('subcategory_id').style.display = 'none';
  if($('subcategory_id-label'))
    $('subcategory_id-label').style.display = 'none';
    if($('subsubcategory_id'))
    $('subsubcategory_id').style.display = 'none';
  if($('subsubcategory_id-label'))
    $('subsubcategory_id-label').style.display = 'none';
  });
  var cateDependencyArray = '<?php echo json_encode($cateDependencyArray); ?>';
  function subcate(subcate, subsubcate) {

		if(cateDependencyArray.indexOf(subcate) == -1 || subcate == 0) {
			return;
		}
		var sel = document.getElementById("subcategory_id");

		//get the selected option
		var selectedText = sel.options[sel.selectedIndex].text;
  	$('subcategory').value = subcate;
    changesubcategory(subcate, subsubcate,selectedText);
  }

  function changesubsubcategory(subsubcate) {

		if(cateDependencyArray.indexOf(subsubcate) == -1 || subsubcate == 0) {
			return;
		}
		var sel = document.getElementById("subsubcategory_id");
    var form_sub;
		//get the selected option
		var selectedText = sel.options[sel.selectedIndex].text;
	  if($('filter_form')) {
	    form_sub=document.getElementById('filter_form');
	  } else if($('filter_form_category')){
			form_sub=$('filter_form_category');
		}
		form_sub.elements['subsubcategoryname'].value = selectedText;
		form_sub.elements['subsubcategory'].value = subsubcate;
   }


</script>