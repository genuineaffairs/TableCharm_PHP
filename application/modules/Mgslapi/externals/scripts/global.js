$(window).load(function() {
  hideElementsOnPhoneApp();
});
sm4.core.runonce.add(function() { hideElementsOnPhoneApp(); });

/**
 * Hide unnecessary elements in phone app web view
 * @returns {undefined}
 */
function hideElementsOnPhoneApp() {
  // Always hide head lines in web view
  $(".headline").css("display", "none");
  // Hide custom elements which are defined in controller action
  if(typeof hideElements !== 'undefined') {
    for(var i = 0; i < hideElements.length; i++) {
      $(hideElements[i]).css("display", "none");
    }
  }
}