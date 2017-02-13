(function($) {
  $(window).load(function() {
    $('.grid-form-element').each(function() {
      var wrapper = $(this);

      // Attach event handler
      attachRemoveRowHandler(wrapper);

      attachAddRowClickEvent(wrapper);

    });
  });

  function attachAddRowClickEvent(wrapper) {
    wrapper.find('.add_row').click(function() {
      var edit_table = wrapper.find('.zulu-grid-table.grid-edit-table');
      var pre_el = edit_table.find('tr').eq(1).clone();
      pre_el.find('.remove_row').css('display', 'block');
//      pre_el.find('select').replaceWith('<input class="text" type="text" name="' + pre_el.find('select').attr('name') + '" value="" />');
      clearInputs(pre_el);
      edit_table.append(pre_el);
      reorderRow(wrapper);

      // Re-attach event handler
      attachRemoveRowHandler(wrapper);
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
      $(this).find('td.grid-unused span.text').text($(this).index());
    });
  }

  function clearInputs(el) {
    el.find('select').val('');
    el.find('textarea').val('');
    el.find('input:not(:hidden)').val('');
  }
})(jQuery);