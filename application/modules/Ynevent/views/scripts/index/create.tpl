<!--<script type="text/javascript">
    window.addEvent('domready', function(){
        //        var elSel = new Array();
        //        elSel.combine($$('#ynevent_create_form #category_id'));
        //        elSel.combine($$('#ynevent_create_form #sub_category_id'));
        
        //         en4.core.runonce.add(function() {
        $$('#ynevent_create_form #category_id').each(
        function(el){       
            el.removeEvents().addEvent('change', function(){             
               
                //  alert(el.get('id'));           
                //console.log(el.get('id'));
                var category_id = el.value;
                //var remain_time = el.value;
                           
                (new Request.JSON({
   
                    'method' : 'get',
                    'url' : '<?php //echo $this->url(array('module' => 'ynevent', 'controller' => 'index', 'action' => 'getcategories'), 'default', true) ?>',
                    'data' : {
                        'format' : 'json',
                        'parent_id' : category_id                      
                    },
                    onComplete: function(childs)
                    {  
                        var divCat = $('sub_category_id-wrapper') ;
                        if(childs.categories.length> 0)
                        {
                            var subCat = $('sub_category_id');
                            if(!subCat)
                            {
                                //console.log("dsad");
                                var subCat = document.createElement('select');
                                subCat.setAttribute('id', 'sub_category_id');
                                //console.log(subCat.get('id'));
                                //subCat.id = "sub_category_id";
                            }    
                            else{
                                //console.log("dsad");
                                while (subCat.length> 0) {
                                    subCat.remove(0);
                                } 
                            }                            
                            
                            for(var i=0 ; i <childs.categories.length; i++)
                            {
                                var elOptNew = document.createElement('option');
                                elOptNew.text = childs.categories[i].title;
                                elOptNew.value = childs.categories[i].id;
                                subCat.options[subCat.length] = elOptNew;
                                //console.log(subCat.length);
                                
                            }
                           
                            divCat.style.display='block';

                        }
                        else
                        {
                            divCat.style.display='none';
                        }
                     
                          
                    }
                            
                })).send();
            });
        });
    });
</script>-->
<script>      
	function check(){
        $('ynevent_create_form').submit();
	}	
</script>


<?php echo $this->form->render() ?>
<script>

	function isrepeat(obj){
		$('g_repeat_type').value = obj.value; 
		if(obj.value == 0){
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
		
	//Set dropdown list of event hidden
			
	$("repeatend-hour").set('value',12);
	$("repeatend-minute").set('value',50);
	$("repeatend-ampm").set('value','PM');
	$("repeatend-hour").setStyle("display","none");
	$("repeatend-minute").setStyle("display","none");
	$("repeatend-ampm").setStyle("display","none");   	
		
</script>
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
        if ($('endtime-date').value != '') {
            var repeatEndDate = null;
            if ($('repeatend-date').value != '') {
            	repeatEndDate = new Date($('repeatend-date').value); 
            }
            var endTimeDate = new Date( $('endtime-date').value );
            var d = endTimeDate;
            if (repeatEndDate != null && repeatEndDate < d) {
                d = repeatEndDate;
            } 
            
	        cal_starttime.calendars[0].end = new Date( $('endtime-date').value );
	        // redraw calendar
	        cal_starttime.navigate(cal_starttime.calendars[0], 'm', 1);
	        cal_starttime.navigate(cal_starttime.calendars[0], 'm', -1);
        }
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
            cal_repeatend.calendars[0].end = new Date( maxDate );
            var validDays1 = cal_repeatend.values(cal_repeatend.calendars[0]).days;                        
            cal_repeatend.calendars[0].days = validDays1;
                
            /*cal_starttime.calendars[0].end = new Date( maxDate );
            var validDays2 = cal_starttime.values(cal_starttime.calendars[0]).days;                        
            cal_starttime.calendars[0].days = validDays2;*/
        }
    } 
    
    /*en4.core.runonce.add(function(){
    	setValidDateForEndRepeat();
    });*/
    window.addEvent('domready', function() {
    	setValidDateForEndRepeat();
    });
</script>