<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formSubcategory.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
echo "
	<div id='subcategory_backgroundimage' class='form-wrapper'></div>
	<div id='subcategory_id-wrapper' class='form-wrapper' style='display:none;'>
		<div id='subcategory_id-label' class='form-label'>
		 	<label for='subcategory_id' class='optional'>" . $this->translate('Subcategory') . "</label>
		</div>
		<div id='subcategory_id-element' class='form-element'>
			<select name='subcategory_id' id='subcategory_id' onchange='changesubcategory(this.value);'>
			</select>
		</div>
	</div>";
?>
<?php
echo "
	<div id='subsubcategory_backgroundimage' class='form-wrapper'> </div>
	<div id='subsubcategory_id-wrapper' class='form-wrapper' style='display:none;'>
		<div id='subsubcategory_id-label' class='form-label'>
			<label for='subsubcategory_id' class='optional'>" . $this->translate('3%s Level Category', "<sup>rd</sup>") . "</label>
		</div>
		<div id='subsubcategory_id-element' class='form-element'>
			<select name='subsubcategory_id' id='subsubcategory_id'>
			</select>
		</div>
	</div>";
?>
<script type="text/javascript">

  var sub = '';
  var subcatname = '';
  var show_subcat = 1;
	<?php if (!empty($this->document->category_id)) : ?>
    show_subcat = 0;
  <?php endif; ?>

    var subcategories = function(category_id, sub, subcatname)
    {
  		if($('buttons-wrapper')) {
		  	$('buttons-wrapper').style.display = 'none';
			}
    	var url = '<?php echo $this->url(array('action' => 'sub-category'), 'document_general', true);?>';
     
      $('subcategory_backgroundimage').style.display = 'block';
      $('subcategory_id').style.display = 'none';
      $('subsubcategory_id').style.display = 'none';
      if($('subcategory_id-label'))
        $('subcategory_id-label').style.display = 'none';
        $('subcategory_backgroundimage').innerHTML = '<div class="form-label"></div><div class="form-element"><img src="application/modules/Core/externals/images/loading.gif" alt="" /></div>';
	    if($('subsubcategory_id-label'))
        $('subsubcategory_id-label').style.display = 'none';        

      en4.core.request.send(new Request.JSON({      	
        url : url,
        data : {
          format : 'json', 
          category_id_temp : category_id
        },
        onSuccess : function(responseJSON) {          
  	  		if($('buttons-wrapper')) {
				  	$('buttons-wrapper').style.display = 'block';
					}
          $('subcategory_backgroundimage').style.display = 'none';
          clear('subcategory_id');
          var  subcatss = responseJSON.subcats;		

          addOption($('subcategory_id')," ", '0');
          for (i=0; i< subcatss.length; i++) {
            addOption($('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']);
            if(show_subcat == 0) {
              $('subcategory_id').disabled = 'disabled';
              if($('subsubcategory_id'))
              $('subsubcategory_id').disabled = 'disabled';
            }
            $('subcategory_id').value = sub;
          }
				
          if(category_id == 0) {
            clear('subcategory_id');
            $('subcategory_id').style.display = 'none';
            if($('subcategory_id-label'))
              $('subcategory_id-label').style.display = 'none';
            if($('subsubcategory_id-label'))
              $('subsubcategory_id-label').style.display = 'none';
          }
        }
      }));
    };
	
    function clear(ddName)
    { 
      for (var i = (document.getElementById(ddName).options.length-1); i >= 0; i--) 
      { 
        document.getElementById(ddName).options[ i ]=null; 
      } 
    }	
	
    function addOption(selectbox,text,value)
    {
      var optn = document.createElement("OPTION");
      optn.text = text;
      optn.value = value;
	
      if(optn.text != '' && optn.value != '') {
        $('subcategory_id').style.display = 'block';
        if($('subcategory_id-wrapper'))
          $('subcategory_id-wrapper').style.display = 'block';
        if($('subcategory_id-label'))
          $('subcategory_id-label').style.display = 'block';
        selectbox.options.add(optn);
      } else {
        $('subcategory_id').style.display = 'none';
        if($('subcategory_id-wrapper'))
          $('subcategory_id-wrapper').style.display = 'none';
        if($('subcategory_id-label'))
          $('subcategory_id-label').style.display = 'none';
        selectbox.options.add(optn);
      }
    }

    function addSubOption(selectbox,text,value)
    {
      var optn = document.createElement("OPTION");
      optn.text = text;
      optn.value = value;
      if(optn.text != '' && optn.value != '') {
        $('subsubcategory_id').style.display = 'block';
         if($('subsubcategory_id-wrapper'))
          $('subsubcategory_id-wrapper').style.display = 'block';
         if($('subsubcategory_id-label'))
          $('subsubcategory_id-label').style.display = 'block';
        selectbox.options.add(optn);
      } else {
        $('subsubcategory_id').style.display = 'none';
         if($('subsubcategory_id-wrapper'))
          $('subsubcategory_id-wrapper').style.display = 'none';
         if($('subsubcategory_id-label'))
          $('subsubcategory_id-label').style.display = 'none';
        selectbox.options.add(optn);
      }
    }
	
    var cat = '<?php echo $this->category_id ?>';
    if(cat != '') {
      sub = '<?php echo $this->subcategory_id; ?>';
      subcatname = '<?php echo $this->subcategory_name; ?>';
      subcategories(cat, sub, subcatname);
    }

    function changesubcategory(subcatid) {
      if($('buttons-wrapper')) {
		  	$('buttons-wrapper').style.display = 'none';
			}
      var url = '<?php echo $this->url(array('action' => 'subsub-category'), 'document_general', true);?>';
      $('subsubcategory_backgroundimage').style.display = 'block';
      $('subsubcategory_id').style.display = 'none';
      if($('subsubcategory_id-label'))
        $('subsubcategory_id-label').style.display = 'none';
        $('subsubcategory_backgroundimage').innerHTML = '<div class="form-label"></div><div  class="form-element"><img src="application/modules/Core/externals/images/loading.gif" /></center></div>';
        en4.core.request.send(new Request.JSON({
        url : url,
        data : {
          format : 'json',
          subcategory_id_temp : subcatid
        },
        onSuccess : function(responseJSON) {
  	  		if($('buttons-wrapper')) {
				  	$('buttons-wrapper').style.display = 'block';
					}
          $('subsubcategory_backgroundimage').style.display = 'none';
          clear('subsubcategory_id');
          var  subsubcatss = responseJSON.subsubcats;

          addSubOption($('subsubcategory_id')," ", '0');
          for (i=0; i< subsubcatss.length; i++) {
            addSubOption($('subsubcategory_id'), subsubcatss[i]['category_name'], subsubcatss[i]['category_id']);
          }
        }
      }));
    }
</script>