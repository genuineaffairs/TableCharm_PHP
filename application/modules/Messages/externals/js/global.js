window.addEvent('domready', function() {
  var message_menu = $('menu-new_message');
  if (message_menu && typeof message_count !== "undefined") {
    var count_bubble = new Element('span', {
      'class': 'count-bubble',
      html: message_count
    });
    message_menu.appendChild(count_bubble);
  }
});