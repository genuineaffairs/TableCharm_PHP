window.addEvent('domready', function() {
  var friend_menu = $$('.core_main_friend')[0];
  if (friend_menu && typeof friend_request_count !== "undefined") {
    var count_bubble = new Element('span', {
      'class': 'count-bubble',
      html: friend_request_count
    });
    friend_menu.appendChild(count_bubble);
  }
});