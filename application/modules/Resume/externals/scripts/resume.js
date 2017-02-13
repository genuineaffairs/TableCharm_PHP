window.onload = function() {
  var checkKeyCode = function(key) {
    return (key >= 48 && key <= 90) || (key >= 186 && key <= 192) || (key >= 219 && key <= 222) || (key >= 96 && key <= 111);
  };
  var txtDescription = document.getElementById('description');

  if (/^[0-9]+$/.test(txtDescription.getAttribute("max_length"))) {
    var func = function(e) {
      var len = parseInt(this.getAttribute("max_length"), 10);
      console.log(this.value.length);
      if (this.value.length >= len && checkKeyCode(e.keyCode)) {
        alert('Maximum length exceeded: ' + len);
        this.value = this.value.substr(0, len);
        return false;
      }
    };
    txtDescription.onkeydown = func;
  }
};

function hideFieldsByLabels(labels) {
  $$('#resumes_create .form-elements>div').each(function(el) {
    var el_label_text = el.getChildren('div.form-label>label').get('text').toString().replace('*', '');
    for (var i = 0; i < labels.length; i++) {
      if (el_label_text === labels[i]) {
        el.setStyle('display', 'none');
        break;
      }
    }
  });
}

function hideNonPlayerFields() {
  if (typeof non_player_category_ids !== "undefined") {
    for (var i = 0; i < non_player_category_ids.length; i++) {
      if ($('category_id').get('value') === non_player_category_ids[i]) {
        if (typeof player_labels !== "undefined") {
          hideFieldsByLabels(player_labels);
          break;
        }
      }
    }
  }
}

function removeMandatory(labels) {
  $$('#resumes_create .form-elements>div').each(function(el) {
    var el_label = el.getChildren('div.form-label>label');
    var el_label_text = el_label.get('text').toString().replace('*', '');
    for (var i = 0; i < labels.length; i++) {
      if (el_label_text === labels[i]) {
        el_label.getChildren('span.required-indicator')[0].destroy();
      }
    }
  });
}

window.addEvent('domready', function() {
  Array.each($$('a.menu_core_main'), function(menuLink) { menuLink.set('title', menuLink.get('text')); });
  toggleMandatoryFields();
});

var addRequiredLabels = function() {
  Array.each($$('#global_content label.required'), function(label) {
    label.set('html', '<span class="required-indicator">*</span>' + label.get('text').toString().replace('*', ''));
  });
};

function toggleMandatoryFields() {
  if (typeof non_mandatory_agent_fields !== "undefined") {
    if ($('category_id').get('value') === agent_id) {
      removeMandatory(non_mandatory_agent_fields);
    } else {
      addRequiredLabels();
    }
  }
}

window.addEvent('onChangeFields', function() {
  hideNonPlayerFields();
  toggleMandatoryFields();
});