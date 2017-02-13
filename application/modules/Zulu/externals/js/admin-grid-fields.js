(function($) {
  $(window).load(function() {
    $.registerGridViewEvents();
  });

  $.registerGridViewEvents = function() {
    $('.zulu_admin_field_dependent_field_wrapper').each(function() {
      var wrapper = $(this);

      // Attach event handler
      attachRemoveColumnHandler(wrapper);
      attachRemoveRowHandler(wrapper);

      attachAddColumnClickEvent(wrapper);
      attachAddRowClickEvent(wrapper);
      attachSaveGridViewEvent(wrapper);

      var toggle_gridview_edit = wrapper.parent().find('.toggle_gridview_edit');

      toggle_gridview_edit.unbind('click').click(function() {
        if (wrapper.hasClass('active')) {
          wrapper.removeClass('active').hide('fast');
          $(this).text('Show Grid View Edit');
        } else {
          wrapper.addClass('active').show('fast');
          $(this).text('Hide Grid View Edit');
        }
      });

    });
  }

  function attachSaveGridViewEvent(wrapper) {
    wrapper.find('.save_grid_view').unbind('click').click(function() {
      var data = wrapper.find('.grid-edit-form').serialize();
      $.ajax({
        url: '/admin/zulu/fields/grid-field',
        data: data,
        method: "POST",
        success: function(data) {
          if (data === '1') {
            alert('Your question has been saved');
          } else {
            alert('Your question has been failed to saved. Please contact administrator for more information')
          }
        }
      });
    });
  }

  function attachAddColumnClickEvent(wrapper) {
    wrapper.find('.add_column').unbind('click').click(function() {
      wrapper.find('.zulu-grid-table.grid-edit-table tr').each(function() {
        var pre_el = $(this).find('.normal-col').first().clone();
        pre_el.find('.remove_column').css('display', 'block');
        clearInputs(pre_el);
        $(this).append(pre_el);

        // Re-attach event handler
        attachRemoveColumnHandler(wrapper);
      });
    });
  }

  function attachAddRowClickEvent(wrapper) {
    wrapper.find('.add_row').unbind('click').click(function() {
      var edit_table = wrapper.find('.zulu-grid-table.grid-edit-table');
      var pre_el = edit_table.find('tr').eq(1).clone();
      pre_el.find('.remove_row').css('display', 'block');
      clearInputs(pre_el);
      edit_table.append(pre_el);
      reorderRow(wrapper);

      // Re-attach event handler
      attachRemoveRowHandler(wrapper);
    });
  }

  function attachRemoveColumnHandler(wrapper) {
    // Re-attach event handler
    wrapper.find('.remove_column').unbind('click').click(function() {
      var column_idx = $(this).parent('.normal-col').index();
      wrapper.find('.zulu-grid-table.grid-edit-table tr').each(function() {
        $(this).children().eq(column_idx).remove();
      });
    });
  }

  function attachRemoveRowHandler(wrapper) {
    // Re-attach event handler
    wrapper.find('.remove_row').unbind('click').click(function() {
      $(this).parents('tr').remove();
      reorderRow(wrapper);
    });
  }

  function reorderRow(wrapper) {
    var edit_table = wrapper.find('.zulu-grid-table.grid-edit-table');
    edit_table.find('tr').each(function() {
      $(this).find('td.grid-unused span.text').text('Row ' + $(this).index());
    });
  }

  function clearInputs(el) {
    el.find('select').val('');
    el.find('textarea').val('');
    el.find('input').val('');
  }
})(jQuery);