window.addEvent('load', function() {
  Smoothbox.Modal.prototype.addEvent('close', function(el) {
    if(el.options.url === "/members/friend-request") {
      location.reload();
    }
  });
});