<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<?php if( !$this->noForm ): ?>

  

  <h2 class="mbl">
    <?php echo $this->translate('Sign Up Today'); ?>
  </h2>
  <div class='mbm'>
      Click <a href="signup?is_children_account=1">here</a> to register child accounts.
  </div>
  <form enctype="application/x-www-form-urlencoded" action="<?php echo $this->baseUrl('signup') ?>" method="post" id="signup">
    <div class="name-wrapper">
      <div class="mrm first-name-wrapper">
        <input class="text-field short-field" type="text" name="first_name" id="first_name" placeholder="First Name" tabindex="5" />
      </div>
      <div class="mbm last-name-wrapper">
        <input class="text-field short-field" type="text" placeholder="Last Name"  name="last_name" id="last_name" tabindex="6" />
      </div>
    </div>
    <div class="mbm">
      <input class="text-field long-field" name="email" id="email" type="text" placeholder="Your Email" tabindex="7" />
    </div>
    <div class="mbm">
      <input class="text-field long-field" name="password" type="password" placeholder="New Password" tabindex="8" /> 
    </div>
    <div class="mbm">
      <input class="text-field long-field" name="passconf" type="password" placeholder="Confirm Password" tabindex="9" />
    </div>
   <div id="terms-wrapper" class="form-wrapper">
   <div id="terms-element" class="form-element">
   <input type="hidden" value="" name="terms"></input>
   <input id="terms" type="checkbox" tabindex="10" value="1" name="terms"></input>
   <label class="null" for="terms"> I have read and agree to the <a href="/help/terms" target="_blank"> terms of service </a>. </label>
   </div>
   </div> 
   
   
    
   <div class="mbm">
      <div class="mrm birthday-wrapper">
        <div class="birthday-label mbm">Birthday:</div>
        <div class="birthday-options-wrapper">
          <select name="date-of-birth-month" tabindex="10">
            <option value="-1">Month:</option>
            <option value="1">Jan</option>
            <option value="2">Feb</option>
            <option value="3">Mar</option>
          </select>

          <select name="date-of-birth-day" tabindex="11">
            <option value="-1">Day:</option>
            <option value="1">1</option>
            <option value="2">2</option>
          </select>

          <select name="date-of-birth-year" tabindex="12">
            <option value="-1">Year:</option>
            <option value="2013">2013</option>
            <option value="2012">2012</option>
          </select>
        </div>
      </div>
</div>
    
    <div class="gender-wrapper mbm mtl">
      <div class="gender-option-wrapper">
        <input type="radio" name="gender" id="gender" value="1" tabindex="14" class="gender-option" />
        <label class="gender-label">Male</label>
      </div>
      <div class="gender-option-wrapper">
        <input type="radio" name="gender" id="gender" value="1" class="gender-option" />
        <label class="gender-label">Female</label>
      </div>
    </div>
    
    <div class="signup-button-wrapper">
        <button type="submit" class="mvm signup-button" tabindex="15">Sign Up</button>
    </div>

    <input type="hidden" name="language" value="English" id="language">
    <input type="hidden" name="timezone" value="Australia/Sydney" id="timezone">
  </form>
<?php endif; ?>