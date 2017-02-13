/**
 * Originally used in: profile form
 */
(function($) {

  var profileInit = function() {
    // Purpose: try to make 2 blocks's height become the same if they are nearly equal
    var blocks = $('.row.profile_form').find('.col-md-6').find('.inner_wrapper');
    // Define the maximum difference tolerance
    var tolerance = 100;
    blocks.each(function(index) {
      if (index % 2 === 0 && typeof blocks.eq(index + 1) !== 'undefined') {
        var even = blocks.eq(index), odd = blocks.eq(index + 1);
        var diffHeight = Math.abs(even.height() - odd.height());

        if (diffHeight < tolerance && diffHeight !== 0) {
          var divSpan = $('<div class="spanHeight" style="clear:both;height:' + diffHeight + 'px"></div>');
          if (even.height() < odd.height()) {
            even.append(divSpan);
          } else {
            odd.append(divSpan);
          }
        }
      }
    });

    // African fields handler
    var africanFields = new FieldsHandler();
    africanFields.active_flg = {country_of_residence: 0, primary_sport: 0, participation_level: 0};

    var special_field_group = $('.special_hidden_fields');
    var participationAllVals = FieldsHandler.prototype.getAllCheckedBoxText('.participation_level');
    var countryField = $('.country_of_residence');
    var sportField = $('.primary_sport');

    africanFields.showOrHideFieldsByOtherFieldsValue(special_field_group, countryField.find("option:selected").val(), 'ZA', 'country_of_residence');
    africanFields.showOrHideFieldsByOtherFieldsValue(special_field_group, sportField.find("option:selected").val(), 'Rugby_Union', 'primary_sport');
    africanFields.showOrHideFieldsByOtherFieldsValue(special_field_group, participationAllVals, sa_participation_list, 'participation_level');

    countryField.change(function() {
      africanFields.showOrHideFieldsByOtherFieldsValue(special_field_group, $(this).val(), 'ZA', 'country_of_residence');
    });

    sportField.change(function() {
      africanFields.showOrHideFieldsByOtherFieldsValue(special_field_group, $(this).val(), 'Rugby_Union', 'primary_sport');
    });

    $('.participation_level').change(function() {
      var allVals = FieldsHandler.prototype.getAllCheckedBoxText('.participation_level');
      africanFields.showOrHideFieldsByOtherFieldsValue(special_field_group, allVals, sa_participation_list, 'participation_level');
    });

    // Valke fields handler
    var valkeFields = new FieldsHandler();
    valkeFields.active_flg = {club_or_school: 0};

    var listOfSchools = $('.list_of_schools');
    var listOfClubs = $('.list_of_clubs');
    var valkeShowFields = [listOfSchools.closest('.form-wrapper'), listOfClubs.closest('.form-wrapper')];

    valkeFields.showFieldsByOtherFieldsValue(valkeShowFields, FieldsHandler.prototype.getSelectedBoxText('.club_or_school'), 'Both', 'club_or_school');
    $('.club_or_school').change(function() {
      valkeFields.showFieldsByOtherFieldsValue(valkeShowFields, FieldsHandler.prototype.getSelectedBoxText('.club_or_school'), 'Both', 'club_or_school');
    });

    var provinceFields = new FieldsHandler();
    provinceFields.active_flg = {sa_provinces: 0};

    provinceFields.hideFieldsByOtherFieldsValue(valkeShowFields, FieldsHandler.prototype.getSelectedBoxText('.sa_provinces'), 'Valke', 'sa_provinces');
    $('.sa_provinces').change(function() {
      valkeFields.showFieldsByOtherFieldsValue(valkeShowFields, FieldsHandler.prototype.getSelectedBoxText('.club_or_school'), 'Both', 'club_or_school');
      provinceFields.hideFieldsByOtherFieldsValue(valkeShowFields, FieldsHandler.prototype.getSelectedBoxText('.sa_provinces'), 'Valke', 'sa_provinces');
    });
  };
  // Bind this function to global jQuery
  $.profileInit = profileInit;
  // Run init script
  $(window).load(function() {
    profileInit();
  });

  function FieldsHandler() {
  }
  FieldsHandler.prototype = {
    active_flg: {},
    getAllCheckedBoxText: function(class_name) {
      var allVals = [];
      $(class_name).parent().find(':checked').each(function() {
        allVals.push($(this).parent().find('label').text());
      });
      return allVals;
    },
    getSelectedBoxText: function(class_name) {
      var val = '';
      $(class_name).parent().find(':selected').each(function() {
        val = $(this).text();
      });
      return val;
    },
    arrayIntersect: function(arr1, arr2) {
      for (var i = 0; i < arr1.length; i++) {
        for (var j = 0; j < arr2.length; j++) {
          if (arr1[i].trim() === arr2[j].trim()) {
            return true;
          }
        }
      }
      return false;
    },
    // Check if all necessary fields for displaying special fields group are active
    isAllFieldActive: function() {
      for (var key in this.active_flg) {
        if (this.active_flg.hasOwnProperty(key) && this.active_flg[key] === 0) {
          return false;
        }
      }
      return true;
    },
    showOrHideFieldsByOtherFieldsValue: function(showField, val1, val2, otherFieldClass) {
      this.checkOtherFieldActive(val1, val2, otherFieldClass);

      if (this.isAllFieldActive()) {
        this.showFieldsGroup(showField);
      } else {
        this.hideFieldsGroup(showField);
      }
    },
    showFieldsByOtherFieldsValue: function(showField, val1, val2, otherFieldClass) {
      this.checkOtherFieldActive(val1, val2, otherFieldClass);
      if (this.isAllFieldActive()) {
        this.showFieldsGroup(showField);
      }
    },
    hideFieldsByOtherFieldsValue: function(showField, val1, val2, otherFieldClass) {
      this.checkOtherFieldActive(val1, val2, otherFieldClass);

      if (!this.isAllFieldActive()) {
        this.hideFieldsGroup(showField);
      }
    },
    checkOtherFieldActive: function(val1, val2, otherFieldClass) {
      var otherFieldActive = false;

      if ($.isArray(val1) && $.isArray(val2)) {
        otherFieldActive = this.arrayIntersect(val1, val2);
      } else {
        otherFieldActive = val1 === val2;
      }

      if (otherFieldActive) {
        this.active_flg[otherFieldClass] = 1;
      } else {
        this.active_flg[otherFieldClass] = 0;
      }
    },
    showFieldsGroup: function(showField) {
      if ($.isArray(showField)) {
        for (var i = 0; i < showField.length; i++) {
          showField[i].css('display', 'block');
        }
      } else {
        showField.css('display', 'block');
      }
    },
    hideFieldsGroup: function(showField) {
      if ($.isArray(showField)) {
        for (var i = 0; i < showField.length; i++) {
          showField[i].css('display', 'none');
        }
      } else {
        showField.css('display', 'none');
      }
    }
  };
})(jQuery);