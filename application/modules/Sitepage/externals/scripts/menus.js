(function($) {
  $(window).load(function() {
    var sitepage_create = $('.sitepage_main_create:first');
    sitepage_create.click(function() {
      return false;
    });
    sitepage_create.after($('<ul id="circle_create_types"><li><a href="circleitems/create">Create Private Circle</a></li><li><a href="circleitems/create?public=1">Create Public Circle</a></li></ul>'));
    sitepage_create.hover(function() {
      $('#circle_create_types').stop().show("fast");
    }, function() {
      $('#circle_create_types').stop().hide("fast");
    });
    $('#circle_create_types').hover(function() {
      $(this).stop().show("fast");
    }, function() {
      $(this).stop().hide("fast");
    });
  });
})(jQuery);