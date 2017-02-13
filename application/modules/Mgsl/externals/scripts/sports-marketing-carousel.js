window.addEvent('domready', function() {
	$('sports-marketing-carousel').set('tween', {duration: 'long'});
	var timer = setInterval(moveCarousel, 5000);
});

function moveCarousel() {
	var images = [
		'MGSLFP_Image_01.jpg',
		'MGSLFP_Image_02.jpg',
		'MGSLFP_Image_03.jpg',
		'MGSLFP_Image_04.jpg',
		'MGSLFP_Image_05.jpg',
		'MGSLFP_Image_06.jpg',
		'MGSLFP_Image_07.jpg',
		'MGSLFP_Image_08.jpg',
		'MGSLFP_Image_08.jpg',
		'MGSLFP_Image_10.jpg',
		'MGSLFP_Image_11.jpg',
		'MGSLFP_Image_12.jpg',
		'MGSLFP_Image_13.jpg'
	];

	var imageToDisplay = images[Math.floor(Math.random()*images.length)];
	var imagePath = en4.core.staticBaseUrl + 'application/modules/Mgsl/externals/images/carousel';

	$('sports-marketing-carousel').tween('background-image', 'url('+ imagePath + '/' + imageToDisplay +')');
}