window.addEvent('domready', function () {
  if ($('is_children_account')) {
    $('is_children_account').addEvent('change', function () {
      document.location = 'signup?is_children_account=' + $('is_children_account').get('value');
    });

    // This hack is used to prevent form element being populated
    var hidden = new Element('input', {type: 'hidden', id: 'email', name: 'email'});
    hidden.inject($('parental_email'), 'after');
  }
});