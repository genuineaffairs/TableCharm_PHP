<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _jsSwitch.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<?php if(!Engine_Api::_()->zulu()->isMobileMode()) : ?>
<script type="text/javascript">

en4.core.runonce.add(function() {
  
  var topLevelId = '<?php echo sprintf('%d', (int) @$this->topLevelId) ?>';
  var topLevelValue = '<?php echo sprintf('%d', (int) @$this->topLevelValue) ?>';
  var elementCache = {};

  function getFieldsElements(selector) {
    if( selector in elementCache || $type(elementCache[selector]) ) {
      return elementCache[selector];
    } else {
      return elementCache[selector] = $$(selector);
    }
  }
  
  function updateFieldValue(element, value) {
    if( element.get('tag') == 'option' ) {
      element = element.getParent('select');
    } else if( element.get('type') == 'checkbox' || element.get('type') == 'radio' ) {
      element.set('checked', Boolean(value));
      return;
    }
    if (element.get('tag') == 'select') {
      if (element.get('multiple')) {
        element.getElements('option').each(function(subEl){
          subEl.set('selected', false);
        });
      }
    }
    if( element ) {
      element.set('value', value);
    }
  }

  var changeFields = window.changeFields = function(element, force, isLoad) {
    var fieldShow = false;
    element = $(element);

    // We can call this without an argument to start with the top level fields
    if( !$type(element) ) {
      getFieldsElements('.parent_' + topLevelId).each(function(element) {
        changeFields(element, force, isLoad);
      });
      return;
    }

    // If this cannot have dependents, skip
    if( !$type(element) || !$type(element.onchange) ) {
      return;
    }

    // Get the input and params
    var field_id = element.get('class').match(/field_([\d]+)/i)[1];
    var parent_field_id = element.get('class').match(/parent_([\d]+)/i)[1];
    var parent_option_id = element.get('class').match(/option_([\d]+)/i)[1];

    //console.log(field_id, parent_field_id, parent_option_id);

    if( !field_id || !parent_option_id || !parent_field_id ) {
      return;
    }

    force = ( $type(force) ? force : false );

    // Now look and see
    // Check for multi values
    var option_id = [];
    if( element.name.indexOf('[]') > 0 ) {
      if( element.type == 'checkbox' ) { // MultiCheckbox
        getFieldsElements('.field_' + field_id).each(function(multiEl) {
          if( multiEl.checked ) {
            option_id.push(multiEl.value);
          }
        });
      } else if( element.get('tag') == 'select' && element.multiple ) { // Multiselect
        element.getChildren().each(function(multiEl) {
          if( multiEl.selected ) {
            option_id.push(multiEl.value);
          }
        });
      }
    } else if( element.type == 'radio' ) {
      if( element.checked ) {
        option_id = [element.value];
      }
    } else {
      option_id = [element.value];
    }

    //console.log(option_id, $$('.parent_'+field_id));

    // Iterate over children
    getFieldsElements('.parent_' + field_id).each(function(childElement) {
      //console.log(childElement);
      var childContainer;
      if( childElement.getParent('form').get('class') == 'field_search_criteria' ) {
        childContainer = $try(function(){ return childElement.getParent('li').getParent('li'); });
      }
      if( !childContainer ) {
         childContainer = childElement.getParent('div.form-wrapper');
      }
      if( !childContainer ) {
        childContainer = childElement.getParent('div.form-wrapper-heading');
      }
      if( !childContainer ) {
        childContainer = childElement.getParent('li');
      }
      //console.log(option_id);
      //var childLabel = childContainer.getElement('label');
      var childOptions = childElement.get('class').match(/option_([\d]+)/gi);
      for(var i = 0; i < childOptions.length; i++) {
        for(var j = 0; j < option_id.length; j++) {
          if(childOptions[i] == "option_" + option_id[j]) {
            var childOptionId = option_id[j];
            break;
          }
        }
      }

      //var childOptionId = childElement.get('class').match(/option_([\d]+)/i)[1];
      var childIsVisible = ( 'none' != childContainer.getStyle('display') );
      var skipPropagation = false;
      //var childFieldId = childElement.get('class').match(/field_([\d]+)/i)[1];

      // Forcing hide
      var nextForce;
      if( force == 'hide' && !option_id.contains(childOptionId)) {
        if( !childElement.hasClass('field_toggle_nohide') ) {
          childContainer.setStyle('display', 'none');
          if( !isLoad ) {
            updateFieldValue(childElement, null);
          }
        }
        nextForce = force;
      } else if( force == 'show' ) {
        childContainer.setStyle('display', '');
        nextForce = force;
      } else if( !$type(option_id) == 'array' || !option_id.contains(childOptionId) ) {
        // Hide fields not tied to the current option (but propogate hiding)
        if( !childElement.hasClass('field_toggle_nohide') ) {
          childContainer.setStyle('display', 'none');
          if(childContainer.getParent('.zulu_child_fields_wrapper') && !fieldShow) {
            childContainer.getParent('.zulu_child_fields_wrapper').setStyle('display', 'none');
          }
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
        childContainer.setStyle('display', '');
        fieldShow = true;
        if(childContainer.getParent('.zulu_child_fields_wrapper')) {
          childContainer.getParent('.zulu_child_fields_wrapper').setStyle('display', '');
        }
        nextForce = undefined;
        //if( childIsVisible ) {
        //  skipPropagation = true;
        //}
      }

      if( !skipPropagation ) {
        changeFields(childElement, nextForce, isLoad);
      }
    });

    window.fireEvent('onChangeFields');
  }
  
  changeFields(null, null, true);
});

</script>

<?php else: ?>
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
    if( !$.type(element) || $.type(element) == 'null' || $.type(element.get(0)) == 'undefined') { 
      getFieldsElements('.parent_' + topLevelId).each(function(key, element) { 
        //if ($(element).get(0).nodeName == 'SPAN') return;
        changeFields((element), force, isLoad);
      });
      return;
    }

    // If this cannot have dependents, skip
    if( !$.type(element) || $.type(element.get(0)) == 'undefined' || element.get(0).onchange == null ) { 
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
<?php endif; ?>