(function ($) {
  $(window).load(function () {
    $('.multifile-form-element').each(function () {
      var wrapper = $(this);

      // Attach event handlers
      attachRemoveRowHandler(wrapper);
      attachDeleteFileHandler(wrapper);
      attachAddRowClickEvent(wrapper);
    });

    function attachAddRowClickEvent(wrapper) {
      wrapper.find('.add_row').click(function () {
        var file_rows = wrapper.find('.zulu-file-rows');
        var clone_el = file_rows.find('.file-row').eq(0).clone();
        file_rows.append(clone_el);

        // Re-attach event handler
        attachRemoveRowHandler(wrapper);
      });
    }

    function attachRemoveRowHandler(wrapper) {
      // Re-attach event handler
      wrapper.find('.remove_row').unbind('click').click(function () {
        $(this).parent('.file-row').remove();
      });
    }

    function attachDeleteFileHandler(wrapper) {
      var files_delete_input = wrapper.find('.files_delete');
      // Re-attach event handler
      wrapper.find('.delete_file').click(function () {
        $(this).parent('.old-file-row').remove();
        var files_delete = files_delete_input.val();
        var delete_path = $(this).attr('file-data');
        // Append delete file list
        files_delete_input.val(files_delete + (files_delete ? ',' : '') + delete_path);
      });
    }
  });
})(jQuery);