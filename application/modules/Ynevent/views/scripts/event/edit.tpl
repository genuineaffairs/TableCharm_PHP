
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js');
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'externals/calendar/styles.css');
?>
<script type="text/javascript">
    var myCalStart = false;
    var myCalEnd = false;

    en4.core.runonce.add(function init() 
    {
        monthList = [];
        myCal = new Calendar({ 'starttime[date]': 'M d Y', 'endtime[date]' : 'M d Y' }, {
            classes: ['event_calendar'],
            pad: 0,
            direction: 0
        });
    });
</script>
<?php echo $this->form/* ->setAttrib('class', 'global_form_popup') */->render($this) ?>


<script type="text/javascript">
    var maxDate = "<?php echo $this->gEndDate; ?>";    
    var cal_starttime_onHideStart = function(){
        // check end date and make it the same date if it's too
        //cal_starttime.calendars[0].start = new Date( maxDate );
        cal_endtime.calendars[0].start = new Date( $('starttime-date').value );        
        cal_repeatend.calendars[0].start = new Date( $('starttime-date').value ); 
        
        // redraw calendar
        cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
        cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1);
        
        cal_repeatend.navigate(cal_repeatend.calendars[0], 'm', 1);
        cal_repeatend.navigate(cal_repeatend.calendars[0], 'm', -1);
    }
    var cal_endtime_onHideStart = function(){
        // check start date and make it the same date if it's too
        cal_starttime.calendars[0].end = new Date( $('endtime-date').value );
        // redraw calendar
        cal_starttime.navigate(cal_starttime.calendars[0], 'm', 1);
        cal_starttime.navigate(cal_starttime.calendars[0], 'm', -1);
    }    
    
    
    var cal_repeatend_onHideStart = function(){        
        // check start date and make it the same date if it's too
        //cal_endtime.calendars[0].end = new Date( $('repeatend-date').value );
        if(maxDate != ""){           
            cal_repeatend.calendars[0].end = new Date( maxDate );   
        }            
        
        //redraw calendar
        //cal_starttime.navigate(cal_starttime.calendars[0], 'm', 1);
        //cal_starttime.navigate(cal_starttime.calendars[0], 'm', -1);
    }
    
    var setValidDateForEndRepeat = function() {
    	if(maxDate != ""){           
    		var validDays1 = cal_repeatend.values(cal_repeatend.calendars[0]).days;                        
            cal_repeatend.calendars[0].end = new Date( maxDate );
            cal_repeatend.calendars[0].days = validDays1;    

        }
    } 
    
    en4.core.runonce.add(function(){
    	setValidDateForEndRepeat();
    	cal_starttime_onHideStart();
    	cal_endtime_onHideStart();
    });
    
    function isrepeat(obj){
    	$('g_repeat_type').value = obj.value; 
		if(obj.value==0){
			$('repeat_frequency-wrapper').style.display = 'none';
			$('repeatend-wrapper').style.display = 'none';			
		}			
		else{
			$('repeat_frequency-wrapper').style.display = 'block';
			$('repeatend-wrapper').style.display = 'block';			
		}			
	}
    
    if($('g_repeat_type').value == 0 || $('g_repeat_type').value == ""){
			$('repeat_frequency-wrapper').style.display = 'none';
			$('repeatend-wrapper').style.display = 'none';			
		}			
		else{
			$('repeat_frequency-wrapper').style.display = 'block';
			$('repeatend-wrapper').style.display = 'block';			    
		}	
    
	$("repeatend-hour").set('value',12);
    $("repeatend-minute").set('value',50);
   	$("repeatend-ampm").set('value','PM');
    $("repeatend-hour").setStyle("display","none");
    $("repeatend-minute").setStyle("display","none");
    $("repeatend-ampm").setStyle("display","none");
</script>
<script>
	function check(){		
		if($('f_repeat_type').value== '0' ){
			$('ynevent_create_form').submit();
			return true;	
		}
		Smoothbox.open($('ynevent_kind'));	
		return false;
	}
	function myselect(e){		
		$('apply_for_action').value = $$('.form-options-wrapper > li input[name=apply_for]:checked')[1].get('value')	
		$('ynevent_create_form').submit();
	}	
</script>

<div class="" style="display: none;">
<?php 
	echo $this->formcheck;
?>
</div>