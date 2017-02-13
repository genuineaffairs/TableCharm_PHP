<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/css.php?request=/application/modules/Sitepagepoll/externals/styles/style_sitepagepoll.css')
?>

<?php 

$breadcrumb = array(
    array("href" => $this->sitepage->getHref(),"title" => $this->sitepage->getTitle(),"icon" => "arrow-r"),
    array("href" => $this->sitepage->getHref(array('tab' => $this->tab_selected_id)),"title"=>"Polls","icon" => "arrow-d")
    );

echo $this->breadcrumb($breadcrumb);
?>

<div class="layout_middle">
  <div class='global_form'>
    <?php echo $this->form->render($this) ?>
    <a href="javascript: void(0);" onclick="addAnotherOption(false,'');" id="addOptionLink"><?php echo $this->translate("Add another option") ?></a>

    <script type="text/javascript">
      //<!--

      sm4.core.runonce.add(function(){

        // check end date and make it the same date if it's too
        cal_end_time.calendars[0].start = new Date( $('end_time-date').value );
        // redraw calendar
        cal_end_time.navigate(cal_end_time.calendars[0], 'm', 1);
        cal_end_time.navigate(cal_end_time.calendars[0], 'm', -1);

      });
      
      var addAnotherOption ='';
      sm4.core.runonce.add(function() {
        var maxOptions = <?php echo $this->maxOptions ?>;
        var options = <?php echo Zend_Json::encode($this->options) ?>;
        var optionParent = $('#options').parent();
        addAnotherOption = $(document).ready = function (dontFocus, label) {  
          if (maxOptions && $('#pollOptionInput').length >= maxOptions) {
            return !alert(new String('<?php echo $this->string()->escapeJavascript($this->translate("A maximum of %s options are permitted.")) ?>').replace(/%s/, maxOptions));
          
            return false;
          }
         // 
         
          var optionElement = new $('<input />', {
            'type': 'text',
            'name': 'optionsArray[]',
            'id': 'pollOptionInput',
            'value': label,
            'data-mini': true,
           //'data-role':none,
            'events': {
              'keydown': function(event) {
                if (event.key == 'enter') {
                  if (this.get('value').trim().length > 0) {
                    addAnotherOption();
                    return false;
                  } else
                    return true;
                } else
                  return true;
              } // end keypress event
            } // end events
          });

          if( dontFocus ) {
            optionParent.append(optionElement);
          } else {
            optionParent.append(optionElement).focus();
          }

          optionParent.append($('#addOptionLink'));
          optionParent.trigger("create");

          if( maxOptions && $('#pollOptionInput').length >= maxOptions ) {
            $('#addOptionLink').remove();
          }
        }//end of anotherOption function
      
        // Do stuff
        if( $.type(options) == 'array' && options.length > 0 ) {
          options.each(function(label) {
            addAnotherOption(true, label);
          });
          if( options.length == 1 ) {
            addAnotherOption(true);
          }
        } else {
          // display two boxes to start with
          addAnotherOption(true,'');
          addAnotherOption(true,'');
        }
      });//end of runonce function
      // -->
    </script>
  </div>
</div> 

<script type="text/javascript">
  var endsettingss = 0;
  function updateTextFields(value) {
		if (value == 0)
    {
      if($("#end_time-wrapper"))
      $("#end_time-wrapper").css("display","none");
    } else if (value == 1)
    { if($("#end_time-wrapper"))
      $("#end_time-wrapper").css("display","block");
    }
  }

  sm4.core.runonce.add(updateTextFields(endsettingss));

</script>