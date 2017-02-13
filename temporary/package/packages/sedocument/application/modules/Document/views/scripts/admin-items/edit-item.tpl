<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit-item.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headScript()
        ->appendFile($this->seaddonsBaseUrl() . '/externals/calendar/calendar.compat.js');
$this->headLink()
        ->appendStylesheet($this->seaddonsBaseUrl() . '/externals/calendar/styles.css');
?>

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

  en4.core.runonce.add(function(){

    // check end date and make it the same date if it's too
    cal_starttime.calendars[0].start = new Date( $('starttime-date').value );
    // redraw calendar
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', 1);
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', -1);

    cal_starttime_onHideStart();
    // cal_endtime_onHideStart();
  });

  var cal_starttime_onHideStart = function(){
    // check end date and make it the same date if it's too
    cal_endtime.calendars[0].start = new Date( $('starttime-date').value );
    // redraw calendar
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1);
  }
  var cal_endtime_onHideStart = function(){
    // check start date and make it the same date if it's too
    cal_starttime.calendars[0].end = new Date( $('endtime-date').value );
    // redraw calendar
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', 1);
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', -1);
  }

  window.addEvent('domready', function() {
    if($('starttime-minute')) {
      $('starttime-minute').style.display= 'none';
    }
    if($('starttime-ampm')) {
      $('starttime-ampm').style.display= 'none';
    }
    if($('starttime-hour')) {
      $('starttime-hour').style.display= 'none';
    }

    //End date work
    if($('endtime-minute')) {
      $('endtime-minute').style.display= 'none';
    }
    if($('endtime-ampm')) {
      $('endtime-ampm').style.display= 'none';
    }
    if($('endtime-hour')) {
      $('endtime-hour').style.display= 'none';
    }
    ///// End End date work

  });
</script>

<?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>

<?php if( @$this->closeSmoothbox ): ?>
	<script type="text/javascript">
  		TB_close();
	</script>
<?php endif; ?>