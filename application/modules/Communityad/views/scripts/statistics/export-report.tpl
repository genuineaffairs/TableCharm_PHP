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
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js');
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'externals/calendar/styles.css');
?>
<script type="text/javascript">
var showMarkerInDate="<?php echo $this->showMarkerInDate ?>";
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

  var cal_start_cal_onHideStart = function(){
     if(showMarkerInDate == 0) return;
    // check end date and make it the same date if it's too
    cal_end_cal.calendars[0].start = new Date( $('start_cal-date').value );
    // redraw calendar
    cal_end_cal.navigate(cal_end_cal.calendars[0], 'm', 1);
    cal_end_cal.navigate(cal_end_cal.calendars[0], 'm', -1);
  }
  var cal_end_cal_onHideStart = function(){
     if(showMarkerInDate == 0) return;
    // check start date and make it the same date if it's too
    cal_start_cal.calendars[0].end = new Date( $('end_cal-date').value );
    // redraw calendar
    cal_start_cal.navigate(cal_start_cal.calendars[0], 'm', 1);
    cal_start_cal.navigate(cal_start_cal.calendars[0], 'm', -1);
  }

  en4.core.runonce.add(function(){
   cal_start_cal_onHideStart();
   cal_end_cal_onHideStart();
  });

  window.addEvent('domready', function() {
    if($('start_cal-minute') && $('end_cal-minute')) {
			$('start_cal-minute').style.display= 'none';
			$('end_cal-minute').style.display= 'none';
		}
		if($('start_cal-ampm') && $('end_cal-ampm')) {
			$('start_cal-ampm').style.display= 'none';
			$('end_cal-ampm').style.display= 'none';
		}
		if($('start_cal-hour') && $('end_cal-hour')) {
			$('start_cal-hour').style.display= 'none';
			$('end_cal-hour').style.display= 'none';
		}
    
    var empty = '<?php echo $this->empty ?>';
    var no_ads = '<?php echo $this->no_ads ?>';
    var prefield = '<?php echo $this->prefield ?>';

    form = $('report_form');
    form.setAttribute("method","get");
  
    if(prefield == 1) {
      onsubjectChange($('ad_subject'));
      $('filter').value = '<?php  echo $this->filter_value ?>';
      addList($('filter')); 
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
    }
	});


  function addList(formElement) {
    var e1 = formElement.value;
    var e2 = $('campaign_list-wrapper');
    var e3 = $('ad_list-wrapper');

    switch(e1) {
      case 'no':
			e2.setStyle('display', 'none');
			e3.setStyle('display', 'none');
      break;

      case 'campaign':
			e2.setStyle('display', 'block');
			e3.setStyle('display', 'none');
      break;

      case 'ad':
			e2.setStyle('display', 'none');
			e3.setStyle('display', 'block');
      break;
    }
  }

  function onsubjectChange(formElement) {
    var e1 = formElement.value;
    if(e1 == 'ad') {
      addOption('<?php echo $this->translate("Ads") ?>', 'ad' );
    }
    else if(e1 == 'campaign') {
      removeOption('ad' );
			if($('ad_list-wrapper').style.display == 'block') {
				$('ad_list-wrapper').style.display = 'none'; 
			}
		}
  }

  function addOption(text,value )
  {
    var addoption = false;
    for (var i = ($('filter').options.length-1); i >= 0; i--) 
      { 
				var val = $('filter').options[ i ].value; 
				if (val == value) {
					addoption = true;
					break; 
				}
      } 
    if(!addoption) {
      var optn = document.createElement("OPTION");
      optn.text = text;
      optn.value = value;
      $('filter').options.add(optn);
    }
  }
  
  function removeOption(value) 
  {
    for (var i = ($('filter').options.length-1); i >= 0; i--) 
    { 
      var val = $('filter').options[ i ].value; 
      if (val == value) {
				$('filter').options[i] = null;
				break; 
      }
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
    form = $('report_form');
		if(formElement.value == 1) {
      $('tip').style.display= 'none';
    }
  }
    
</script>
<style type="text/css">
.global_form div.form-element
{
	max-width:400px;
}
.global_form input + label
{
	max-width:380px;
}
#start_cal-wrapper select,
#end_cal-wrapper select {
	margin-left:5px;
} 
</style>
<div class="headline">
  <h2>
    <?php echo $this->translate('Advertising'); ?>
  </h2>
  <?php if (count($this->navigation)) { ?>
      <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
    </div>
  <?php } ?>
</div>
<div class="layout_middle">
	<div class="tip" id ='tip' style='display:none;'>
		<span>
			<?php echo $this->translate("There are no activities found in the selected date range.") ?>
		</span>
	</div> 
	<div class="tip" id ='tip2' style='display:none;'>
		<?php $site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title')?>
		<span>
			<?php if (Engine_Api::_()->communityad()->enableCreateLink()): ?>
						<?php echo $this->translate('You have not created any Ad yet. %1$sCreate an ad%2$s to advertise on %3$s.', '<a href="'.$this->url(array(), 'communityad_listpackage', true). '">', '</a>', $site_title); ?>
					<?php endif; ?>
			
		</span>
	</div>
	<?php echo $this->reportform->render($this) ?>
</div>

<script type="text/javascript">
		var e2 = $('campaign_list-wrapper');
    var e3 = $('ad_list-wrapper');
    e2.setStyle('display', 'none');
    e3.setStyle('display', 'none');
</script>