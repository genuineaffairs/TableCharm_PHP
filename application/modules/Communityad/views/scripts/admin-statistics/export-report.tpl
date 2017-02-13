<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: export-report.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<style type="text/css">
.settings .form-element
{
	max-width:400px;
}
</style>
<?php
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl.'application/modules/Core/externals/scripts/composer.js');
?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js');
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'externals/calendar/styles.css');

 $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl.'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">
  var maxRecipients = 10;

  function removeFromToValue(id)
  {
    // code to change the values in the hidden field to have updated values
    // when recipients are removed.
    var toValues = $('toValues').value;
    var toValueArray = toValues.split(",");
    var toValueIndex = "";

    var checkMulti = id.search(/,/);

    // check if we are removing multiple recipients
    if (checkMulti!=-1){
      var recipientsArray = id.split(",");
      for (var i = 0; i < recipientsArray.length; i++){
        removeToValue(recipientsArray[i], toValueArray);
      }
    }
    else{
      removeToValue(id, toValueArray);
    }

    // hide the wrapper for usernames if it is empty
    if ($('toValues').value==""){
      $('toValues-wrapper').setStyle('display', 'none');
    }
    else {
      $('toValues-wrapper').setStyle('display', 'block');
    }

    $('to').disabled = false;
  }

  function removeToValue(id, toValueArray){
    for (var i = 0; i < toValueArray.length; i++){
      if (toValueArray[i]==id) toValueIndex =i;
    }

    toValueArray.splice(toValueIndex, 1);
    $('toValues').value = toValueArray.join();
  }

  en4.core.runonce.add(function() {
      if ($('toValues').value==""){
				$('toValues-wrapper').setStyle('display', 'none');
      }
    
      new Autocompleter.Request.JSON('to', '<?php echo $this->url(array('module' => 'communityad', 'controller' => 'statistics', 'action' => 'suggestusers'), 'admin_default', true) ?>', {

        'minLength': 1,
        'delay' : 250,
        'selectMode': 'pick',
        'autocompleteType': 'message',
        'multiple': false,
        'className': 'cmad_admin-autosuggest',
        'filterSubset' : true,
        'tokenFormat' : 'object',
        'tokenValueKey' : 'label',
        'injectChoice': function(token){

				var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'id':token.label});
	      new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
	      choice.inputValue = token.label;
	      this.addChoiceEvents(choice).inject(this.choices);
	      choice.store('autocompleteChoice', token);
            
        },
        onPush : function(){
          if( $('toValues').value.split(',').length >= maxRecipients ){
            $('to').disabled = true;
          }
					if ($('toValues').value==""){
						$('toValues-wrapper').setStyle('display', 'none');
					}
					else {
						$('toValues-wrapper').setStyle('display', 'block');
					}
        }
      });

    });

</script>

<script type="text/javascript">

en4.core.runonce.add(function()
  {
          
          en4.core.runonce.add(function init()
          {
            monthList = [];
            myCal = new Calendar({ 'start_cal[date]': 'M d Y', 'end_cal[date]' : 'M d Y' }, {
              classes: ['event_calendar'],
              pad: 0,
              direction: 0
            });
          }); 
  });

  window.addEvent('domready', function() { 
    $('start_cal-minute').style.display= 'none';
    $('start_cal-ampm').style.display= 'none';
    $('start_cal-hour').style.display= 'none';
    $('end_cal-minute').style.display= 'none';
    $('end_cal-ampm').style.display= 'none';
    $('end_cal-hour').style.display= 'none';

    var empty = '<?php echo $this->empty ?>';
    var no_ads = '<?php echo $this->no_ads ?>';
   
    form = $('adminreport_form');
    form.setAttribute("method","get");
    var e3 = $('ads-wrapper');
    e3.setStyle('display', 'none');

    onsubjectChange($('ad_subject'));
    onChangeTime($('time_summary'));
    onchangeFormat($('format_report'));

    // display message tip
    if(empty == 1) {
      if(no_ads == 1) {
	$('tip2').style.display= 'block';
      } else {
	$('tip').style.display= 'block';
      }
    }
    
  });

  function onsubjectChange(formElement) {
    var e1 = formElement.value;
    if(e1 == 'ad') {
      $('ads-wrapper').setStyle('display', 'block');
      $('campaigns-wrapper').setStyle('display', 'none');
    }
    else if(e1 == 'campaign') {
      $('campaigns-wrapper').setStyle('display', 'block');
      $('ads-wrapper').setStyle('display', 'none');
    }
  }

  function onChangeTime(formElement) {

    if(formElement.value == 'Monthly') {
      $('start_group').setStyle('display', 'block');
      $('end_group').setStyle('display', 'block');
      $('time_group2').setStyle('display', 'none');
    }
    else if(formElement.value == 'Daily') {
      $('start_group').setStyle('display', 'none');
      $('end_group').setStyle('display', 'none');
      $('time_group2').setStyle('display', 'block');
    }
    
  }
  
  function onchangeFormat(formElement) {

    form = $('adminreport_form');
		if(formElement.value == 1) {
      $('tip').style.display= 'none';
    }
  }
    
</script>

<h2><?php echo $this->translate("Community Ads Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
<div class='communityad_admin_tabs'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>
<div class="tip" id = 'tip' style='display:none;'>
	<span>
		<?php echo $this->translate("There are no activities found in the selected date range.") ?>
	</span>
</div>
<div class="tip" id ='tip2' style='display:none;'>
	<span>
		<?php echo $this->translate("No ads have been created on your site yet.") ?>
	</span>
</div>
<br />
<div class="seaocore_settings_form">
	<div class="settings">
		<?php echo $this->reportform->render($this) ?>
	</div>
</div>	