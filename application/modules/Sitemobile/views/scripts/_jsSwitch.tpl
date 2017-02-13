<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _jsSwitch.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
function getFieldsElements(selector) { 

    if( $.type(elementCache[selector]) != 'undefined' ) { 
      return elementCache[selector];
    } else { 
      return elementCache[selector] = $(selector);
    }
  }
  
  var topLevelId = '<?php echo sprintf('%d', (int) @$this->topLevelId) ?>';
  var topLevelValue = '<?php echo sprintf('%d', (int) @$this->topLevelValue) ?>';
  var elementCache = {};
sm4.core.runonce.add(function() { 
  
  function updateFieldValue(element, value) { 
    element = $(element);
    if( element.attr('tag') == 'option' ) {
      element = element.parent('select');
    } else if( element.attr('type') == 'checkbox' || element.attr('type') == 'radio' ) {
      element.attr('checked', Boolean(value));
      return;
    }
    if (element.attr('tag') == 'select') {
      if (element.attr('multiple')) {
        element.find('option').each(function(key,subEl){
          $(subEl).attr('selected', false);
        });
      }
    }
    if( element ) {
      element.val(value);
    }
  }

  var changeFields = window.changeFields = function(element, force, isLoad) {
    if ($.type(element) != 'null') {
      element = $(element);
    }

  //console.log(getFieldsElements('.parent_' + topLevelId));return;
    // We can call this without an argument to start with the top level fields
    if( !$.type(element) || $.type(element) == 'null' ) { 
      getFieldsElements('.parent_' + topLevelId).each(function(key, element) { 
        //if ($(element).get(0).nodeName == 'SPAN') return;
        changeFields((element), force, isLoad);
      });
      return;
    }

    // If this cannot have dependents, skip
    if( !$.type(element) || element.get(0).onchange == null ) { 
      return;
    }

    // Get the input and params
    var field_id = element.attr('class').match(/field_([\d]+)/i)[1];
    var parent_field_id = element.attr('class').match(/parent_([\d]+)/i)[1];
    var parent_option_id = element.attr('class').match(/option_([\d]+)/i)[1];


    if( !field_id || !parent_option_id || !parent_field_id ) {
      return;
    }

    force = ( $.type(force) ? force : false );

    // Now look and see
    // Check for multi values
			var option_id = [];
			if (element.attr('name') && element.attr('name').indexOf('[]') > 0) { 
      if( element.attr('type') == 'checkbox' ) { // MultiCheckbox
        getFieldsElements('.field_' + field_id).each(function(key, multiEl) {
          if( $(multiEl).attr('checked')== 'checked' ) {
            option_id.push($(multiEl).val());
          }
        });
      } else if( element.attr('tag') == 'select' && element.multiple ) { // Multiselect
        element.children().each(function(key, multiEl) {
          if( $(multiEl).attr('selected')== 'selected' ) {
            option_id.push($(multiEl).val());
          }
        });
      }
    } else if( element.attr('name') == 'radio' ) { 
      if( element.attr('checked') == 'checked' ) {
        option_id = [element.val()];
      }
    } else { 
      option_id = [element.val()];
    }

    //console.log(option_id, $$('.parent_'+field_id));

    // Iterate over children
    
    
    getFieldsElements('.parent_' + field_id).each(function(key, childElement) {
      childElement = $(childElement);
      //console.log(childElement);
      var childContainer;
      if( childElement.parent('form').attr('class') == 'field_search_criteria' ) {
        childContainer = childElement.closest('li').closest('li');
      }
      if( !childContainer || $.type(childContainer.get(0)) == 'undefined') { 
         childContainer = childElement.parents('div.form-wrapper');
      }
      if( !childContainer || $.type(childContainer.get(0)) == 'undefined' ) {
        childContainer = childElement.parents('div.form-wrapper-heading');
      }
      if( !childContainer || $.type(childContainer.get(0)) == 'undefined') {
        childContainer = childElement.parents('li');
      }

      //console.log(option_id);
      //var childLabel = childContainer.getElement('label');
      var childOptions = childElement.attr('class').match(/option_([\d]+)/gi);
      for(var i = 0; i < childOptions.length; i++) {
        for(var j = 0; j < option_id.length; j++) {
          if(childOptions[i] == "option_" + option_id[j]) {
            var childOptionId = option_id[j];
            break;
          }
        }
      }

      //var childOptionId = childElement.get('class').match(/option_([\d]+)/i)[1];
      var childIsVisible = ( 'none' != childContainer.css('display') );
      var skipPropagation = false;
      //var childFieldId = childElement.get('class').match(/field_([\d]+)/i)[1];

      // Forcing hide
      var nextForce;
      if (force == 'hide' && option_id.indexOf(childOptionId) < 0) {
        if( !childElement.hasClass('field_toggle_nohide') ) {
          childContainer.css('display', 'none');
          if( !isLoad ) {
            updateFieldValue(childElement, null);
          }
        }
        nextForce = force;
      } else if( force == 'show' ) {
        childContainer.css('display', '');
        nextForce = force;
      } else if (!$.type(option_id) == 'array' || option_id.indexOf(childOptionId) < 0) { 
        // Hide fields not tied to the current option (but propogate hiding)
        if( !childElement.hasClass('field_toggle_nohide') ) {
          childContainer.css('display', 'none');
          if( !isLoad ) {
            updateFieldValue(childElement, null);
          }
        }
        nextForce = 'hide';
        if( !childIsVisible ) {
          skipPropagation = true;
        }
      } else {
        // Otherwise show field and propogate (nothing, show?)
        childContainer.css('display', 'block');
        nextForce = undefined;
        //if( childIsVisible ) {
        //  skipPropagation = true;
        //}
      }

      if( !skipPropagation ) {
        changeFields(childElement, nextForce, isLoad);
      }
    });

    $(window).trigger('onChangeFields');
  }
  
  changeFields(null, null, true);
});

</script>