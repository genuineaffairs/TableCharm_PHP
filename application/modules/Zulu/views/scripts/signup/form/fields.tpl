<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: fields.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'zulu', array(
    'topLevelId' => $this->form->getTopLevelId(),
    'topLevelValue' => $this->form->getTopLevelValue(),
  ));
?>

<?php if(Engine_Api::_()->zulu()->isMobileMode()) : ?>

<?php if( $settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.guardianverification', 0) == 1 ): ?>
<script type="text/javascript">
jQuery(document).ready(function(){
	displayOrHideGuardianEmail();

	signUpEmail = "<?php echo $_SESSION['User_Plugin_Signup_Account']['data']['email']; ?>";
	var birthdayDivID = jQuery(jQuery('label:contains("Birthday")')[0]).parent().next().attr('id');

	jQuery('#' + birthdayDivID + ' select').change(function(e){
		displayOrHideGuardianEmail();
	});
});

function displayOrHideGuardianEmail() {
	if (birthdateBelowAgeLimit()) {
		enableGuardianEmailDisplay();
		
	} else {
		disableGuardianEmailDisplay();
	}
}

function birthdateBelowAgeLimit() {
	var birthdayDiv = jQuery(jQuery('label:contains("Birthday")')[0]).parent().next();

	var monthOfBirth 	= birthdayDiv.children()[0].value;
	var dayOfBirth 		= birthdayDiv.children()[1].value;
	var yearOfBirth 	= birthdayDiv.children()[2].value;

	var ageBelowLimit = false;

	if (monthOfBirth !== "0" && dayOfBirth !== "0" && yearOfBirth !== "0") {
		var age = determineUsersAge(parseInt(dayOfBirth, 10), parseInt(monthOfBirth, 10), parseInt(yearOfBirth, 10));

		if (age < 15) {
			ageBelowLimit = true;
		}
	}

	return ageBelowLimit;
}

function enableGuardianEmailDisplay() {
	var guardianEmailLabel = jQuery(jQuery('label:contains("Guardian Email")')[0]);

	if (guardianEmailLabel.attr('class') !== 'required') {
		guardianEmailLabel.attr('class', 'required');
		guardianEmailLabel.html('<span class="required-indicator">*</span>' + guardianEmailLabel.text());

		jQuery(guardianEmailLabel.parent().parent().find('input')).after(
				'<span class="guardian-email-description">'
			+	'Guardian email verification is required for members under the age of 15'
			+ 	'</span>'
		);
	}

	jQuery(guardianEmailLabel.parents('form')[0]).bind('submit', validateForm);

	guardianEmailLabel.parent().parent().show();
}

function disableGuardianEmailDisplay() {
	var guardianEmailLabel = jQuery(jQuery('label:contains("Guardian Email")')[0]);
	guardianEmailLabel.parent().parent().hide();

	jQuery(guardianEmailLabel.parents('form')[0]).unbind('submit');
}

function determineUsersAge(day, month, year) {
	var today = new Date();
    var birthDate = new Date(year, month, day, 0, 0, 0, 0);
    var age = today.getFullYear() - birthDate.getFullYear();
    var m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}

function validateForm(e) {
	var guardianEmail = jQuery(jQuery('label:contains("Guardian Email")')[0]).parent().next().children('input')[0].value;

	if (validEmail(guardianEmail) === false) {
		e.preventDefault();
		showGuardianEmailErrorMessage(guardianEmailInvalidError());
		return false;
	} else if (guardianEmail === signUpEmail) {
		e.preventDefault();
		showGuardianEmailErrorMessage(guardianEmailCannotBeSameError());
		return false;
	} else {
		jQuery('.form-errors li.guardian-email-error').remove();
		return true;
	}
}

function showGuardianEmailErrorMessage(messageContents) {
	var formErrors = jQuery('.layout_core_content form .form-errors');

	if (formErrors.length === 0) {
		jQuery('.layout_core_content form .form-elements').before('<ul class="form-errors">'+messageContents+'</ul>');
	}

	var guardianEmailError = jQuery('.form-errors li.guardian-email-error');

	if (guardianEmailError.length === 0) {
		jQuery('.form-errors').append(messageContents);
	}

	window.scrollTo(0, 0);
}

function guardianEmailInvalidError() {
	return 	'<li class="guardian-email-error">Guardian Email'
		+	'<ul>'
		+		'<li>'
		+ 		'As the age you have entered is under the minimum age of '
		+ 		'15, you must enter the email of guardian who can approve '
		+ 		'the verification of your account.'
		+ 		'</li>'
		+ 	'</ul>'	
		+ 	'</li>';
}

function guardianEmailCannotBeSameError() {
	return 	'<li class="guardian-email-error">Guardian Email'
		+	'<ul>'
		+		'<li>'
		+ 		'The email you enter for guardian verification cannot be the same as your own email.'
		+ 		'</li>'
		+ 	'</ul>'	
		+ 	'</li>';
}

function validEmail(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
</script>
<?php endif; ?>

<?php endif; ?>

<?php if(Engine_Api::_()->zulu()->isMobileMode()) : ?>
  <?php if ($this->sa_participation_list) : ?>
  <script type="text/javascript">
    //<![CDATA[
    <?php echo $this->sa_participation_list; ?>    //]]>
  </script>
  <?php endif; ?>
  <script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Zulu/externals/js/profile-fields.js"></script>
  <script type="text/javascript">
    sm4.core.runonce.add(function() {
      <?php if(Zend_Controller_Front::getInstance()->getRequest()->getParam('from_app') != 1) : ?>
      sm4.user.buildFieldPrivacySelector($.mobile.activePage.find('.global_form').find('[data-field-id]'));
      <?php endif; ?>
      jQuery.profileInit();
    });
  </script>
<?php endif; ?>

<?php echo $this->form->render($this) ?>
