window.onload = function () {
  var checkKeyCode = function (key) {
    return (key >= 48 && key <= 90) || (key >= 186 && key <= 192) || (key >= 219 && key <= 222) || (key >= 96 && key <= 111);
  };
  var txtDescription = document.getElementById('description');

  if (/^[0-9]+$/.test(txtDescription.getAttribute("max_length"))) {
    var func = function (e) {
      var len = parseInt(this.getAttribute("max_length"), 10);
      if (this.value.length >= len && checkKeyCode(e.keyCode)) {
        alert('Maximum length exceeded: ' + len);
        this.value = this.value.substr(0, len);
        return false;
      }
    };
    txtDescription.onkeydown = func;
  }
};

(function ($) {
  function hideFieldsByLabels(labels) {
    $('#resumes_create .form-elements>div').each(function (i, el) {
      el = $(el);
      var el_label_text = el.find('div.form-label>label').eq(0).text().replace(/\*/g, '');
      for (var i = 0; i < labels.length; i++) {
        if (el_label_text === labels[i]) {
          el.css('display', 'none');
          break;
        }
      }
    });
  }

  function hideNonPlayerFields() {
    if (typeof non_player_category_ids !== "undefined") {
      for (var i = 0; i < non_player_category_ids.length; i++) {
        if ($('#category_id').val() === non_player_category_ids[i]) {
          if (typeof player_labels !== "undefined") {
            hideFieldsByLabels(player_labels);
            break;
          }
        }
      }
    }
  }

  function removeMandatory(labels) {
    $('#resumes_create .form-elements>div').each(function (i, el) {
      el = $(el);
      var el_label = el.find('div.form-label>label').eq(0);
      var el_label_text = el_label.text().replace(/\*/g, '');
      for (var i = 0; i < labels.length; i++) {
        if (el_label_text === labels[i]) {
          el_label.find('span.required-indicator').eq(0).remove();
        }
      }
    });
  }

  var addRequiredLabels = function () {
    $.each($('#global_content label.required'), function (label) {
      $(label).html('<span class="required-indicator">*</span>' + $(label).text().replace(/\*/g, ''));
    });
  };

  function toggleMandatoryFields() {
    if (typeof non_mandatory_agent_fields !== "undefined") {
      if ($('#category_id').val() === agent_id) {
        removeMandatory(non_mandatory_agent_fields);
      } else {
        addRequiredLabels();
      }
    }
  }

  if (typeof is_mobile !== 'undefined' && is_mobile) {
    $(document).ready(function() {
      toggleMandatoryFields();
    });
    $(window).on('onChangeFields', function () {
      hideNonPlayerFields();
      toggleMandatoryFields();
    });
  } else {
    window.addEvent('domready', function () {
      Array.each($('a.menu_core_main'), function (menuLink) {
        $(menuLink).attr('title', $(menuLink).text());
      });
      toggleMandatoryFields();
    });

    window.addEvent('onChangeFields', function () {
      hideNonPlayerFields();
      toggleMandatoryFields();
    });
  }
})(jQuery);