<link type="text/css" href="application/modules/Ynevent/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
<script src="application/modules/Ynevent/externals/scripts/jquery-1.7.1.min.js"></script>
<script src="application/modules/Ynevent/externals/scripts/jquery-ui-1.8.17.custom.min.js"></script>

<script type="text/javascript">
    jQuery.noConflict();
    var ynEventCalendar= {        
            currentText: '<?php echo $this->string()->escapeJavascript($this->translate('Today')) ?>',
            monthNames: ['<?php echo $this->string()->escapeJavascript($this->translate('January')) ?>', 
                '<?php echo $this->string()->escapeJavascript($this->translate('February')) ?>', 
                '<?php echo $this->string()->escapeJavascript($this->translate('March')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('April')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('May')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('June')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('July')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('August')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('September')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('October')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('November')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('December')) ?>',
            ],
            monthNamesShort: ['<?php echo $this->string()->escapeJavascript($this->translate('Jan')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Feb')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Mar')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Apr')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('May')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Jun')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Jul')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Aug')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Sep')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Oct')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Nov')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Dec')) ?>',
            ],
            dayNames: ['<?php echo $this->string()->escapeJavascript($this->translate('Sunday')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Monday')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Tuesday')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Wednesday')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Thursday')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Friday')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Saturday')) ?>',            
            ],
            dayNamesShort: ['<?php echo $this->translate('Su') ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Mo')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Tu')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('We')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Th')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Fr')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Sa')) ?>',
                ],
            dayNamesMin: ['<?php echo $this->string()->escapeJavascript($this->translate('Su')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Mo')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Tu')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('We')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Th')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Fr')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Sa')) ?>',
                ],        
            firstDay: 0,
            //isRTL:yneventIsRightToLeft,
            isRTL: <?php echo $this->layout()->orientation == 'right-to-left'? 'true':'false' ?>,
            showMonthAfterYear: false,
            yearSuffix: ''
    };
	
    jQuery(document).ready(function(){
    	jQuery.datepicker.setDefaults(ynEventCalendar);	
        // Datepicker
        jQuery('#start_date').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Ynevent/externals/images/calendar.png',
            buttonImageOnly: true
        });
        jQuery('#end_date').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Ynevent/externals/images/calendar.png',
            buttonImageOnly: true
        });

    });
     
			
</script>
<ul id="ynevent_search_form_mileof_zipcode" class="form-errors" <?php echo ($this->mile_of_error)? '' : 'style="display: none;"';  ?>><li><?php echo $this->translate('Please enter the <b>Zip/Postal code</b> to search with <b>Mile(s) from Zip/Postal Code</b>'); ?></li></ul>
<?php echo $this->form->render($this); ?>

