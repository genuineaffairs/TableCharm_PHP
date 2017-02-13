(function($) {

  if (!Array.indexOf)
  {
    Array.indexOf = [].indexOf ?
            function(arr, obj, from) {
              return arr.indexOf(obj, from);
            } :
            function(arr, obj, from) { // (for IE6)
              var l = arr.length,
                      i = from ? parseInt((1 * from) + (from < 0 ? l : 0), 10) : 0;
              i = i < 0 ? 0 : i;
              for (; i < l; i++) {
                if (i in arr && arr[i] === obj) {
                  return i;
                }
              }
              return -1;
            };
  }

  $.onAddPeople = function(user_ids, type) {
    if (user_ids.length > 0) {
      $.ajax({
        url: '/zulu/index/get-members-by-id',
        data: 'user_ids=' + user_ids,
        type: 'POST',
        beforeSend: function() {
          $('ul.zulu_access_list .loader').fadeIn();
        },
        success: function(data) {
          $('ul.zulu_access_list .loader').fadeOut();
          
          if($('#' + type).val().trim().length > 0) {
            user_ids = $('#' + type).val().split(',');
          } else {
            user_ids = new Array();
          }

          for (var i = 0; i < data.length; i++) {
            if (user_ids.indexOf(data[i].id.toString()) === -1) {
              var img = data[i].photo;
              var label = '<div class="autocompleter-choice">' + data[i].label + '</div>';
              var el = $('<li class="autocompleter-choices">' + img + label + '</li>').data('token', data[i]);

              // Bind click event to list items
              el.bind('click', function() {
                $(this).toggleClass('selected');
              });

              $('.' + type + '_access_list').append(el);
              user_ids.push(data[i].id);
            }
          }
          $('#' + type).val(user_ids.join(','));
        }
      });
    }
  };

  $.removeListItems = function(type) {
    var activeList = $('.' + type + '_access_list');
    activeList.find('li.selected').remove();

    renewHiddenVals(type);
  };

  var renewHiddenVals = function(type) {
    var activeList = $('.' + type + '_access_list');
    var user_ids = new Array();

    activeList.find('li').each(function() {
      var id = $(this).data('token').id;

      user_ids.push(id);
    });
    $('#' + type).val(user_ids.join(','));
  };
  
  $(window).load(function() {
    if(typeof access_list !== 'undefined' && access_list !== '') {
      for(var key in access_list) {
        if($.isArray(access_list[key])) {
          onAddPeople(access_list[key].join(','), key);
        }
      }
    }
  });
})(jQuery);

var onAddPeople = jQuery.onAddPeople;