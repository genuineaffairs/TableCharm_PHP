window.addEvent('domready', function() {
	$("wheres-my-sport-link").addEvent('click', function(event){
		event.stop();
		Smoothbox.open(new Element('div', {
			'class' : 'wheres-my-sport-widget',
			'html' : '<h1 class="wheres-my-sport-header">Where is my sport?</h1>'
			+ '<div class="wheres-my-sport-content" style="padding: 20px;">'
			+ '<div class="intro-text">'
			+ '<p>'
			+ 'Just because your sport is not listed, it does not mean you '
			+ 'cannot sign up to MGSL today! It just means that user profiles '
			+ 'tailored to your sport are not yet available.'
			+ '</p>'
			+ '</div>'
			+ '<div class="sports-coming-soon">'
			+ '<h2>Coming Soon to MGSL!</h2>'
			+ '<p>'
			+ 'We will be adding support for the following sports in the very '
			+ 'near future:'
			+ '</p>'
			+ '<ul>'
			+ '<li class="coming-soon-soccer">Soccer</li>'
			+ '<li class="coming-soon-basketball">Basketball</li>'
			+ '</ul>'
			+ '</div>'
			// + '<div class="have-your-say">'
			// + '<h2>Have Your Say</h2>'
			// + '<p>'
			// + 'Just because your sport is not listed, it does not mean you '
			// + 'cannot sign up to MGSL today! It just means that user profiles '
			// + 'tailored to your sport are not yet available.'
			// + '</p>'
			// + '</div>'
			+ '</div>'
		}));

		$$('div.wheres-my-sport-widget').getParent().getParent().addClass('wheres-my-sport-modal');
	});
});
