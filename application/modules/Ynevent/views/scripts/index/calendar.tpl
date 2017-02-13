<script src="application/modules/Ynevent/externals/scripts/jquery-1.4.4.min.js"></script>

<div class="headline">
    <h2>
        <?php echo $this->translate('Events'); ?>
    </h2>
    <div class="tabs">
        <?php
        // Render the menu
        echo $this->navigation()
                ->menu()
                ->setContainer($this->navigation)
                ->render();
        ?>
    </div>
</div>

<script type="text/javascript">
	    jQuery.noConflict();
	    <?php if ($this->enableTooltip) : ?>
		    jQuery(document).ready(function(){
		        jQuery(document).delegate(".ynevent", "mouseenter", function() {
		            if (!jQuery(this).data("tooltip")) {
		                tip = jQuery(this);                  
		                tip.tooltip();
		                tip.trigger("mouseenter");
		            }
		        });
		    });
   		<?php endif;?>
</script>

<?php
/* date settings */

$month = $this->month;

$year = $this->year;

/* select month control */
$select_month_control = '<select name="month" id="ynevent_month">';
for ($x = 1; $x <= 12; $x++) {
    $m = $this->translate(date('F', mktime(0, 0, 0, $x, 1, $year)));
    $select_month_control.= '<option value="' . $x . '"' . ($x != $month ? '' : ' selected="selected"') . '>' . $m . '</option>';
}

$select_month_control.= '</select>';

/* select year control */
$year_range = 7;
$select_year_control = '<select name="year" id="ynevent_year">';
for ($x = ($year - floor($year_range / 2)); $x <= ($year + floor($year_range / 2)); $x++) {
    $select_year_control.= '<option value="' . $x . '"' . ($x != $year ? '' : ' selected="selected"') . '>' . $x . '</option>';
}
$select_year_control.= '</select>';

/* "next month" control */
$next_month_link = '<a href="' . $this->url(array('action' => 'calendar'), 'event_general') . '?month=' . ($month != 12 ? $month + 1 : 1) . '&amp;year=' . ($month != 12 ? $year : $year + 1) . '" class="control"><img class="ynevent_arrow" src="application/modules/Ynevent/externals/images/next_rtl.png" /></a>';
$previous_month_link = '<a href="' . $this->url(array('action' => 'calendar'), 'event_general') . '?month=' . ($month != 1 ? $month - 1 : 12) . '&amp;year=' . ($month != 1 ? $year : $year - 1) . '" class="control"><img src="application/modules/Ynevent/externals/images/previous-ltr.png" /></a>';

/* bringing the controls together */
$label = $this->translate("Go");
$controls = '<form class="ynevent_mycalendar_form" method="get">' . $select_month_control . $select_year_control . '&nbsp;<button onclick="getData()" name="submit" value="Go">'.$label.'</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $previous_month_link . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $next_month_link . ' </form>';


/* draws a calendar */

$events = $this->events;
$m =$this->translate( date('F',mktime(0,0,0,$month,1,$year)));
echo '<h3 style="float:left; padding-right:15px;">' . $m . ' ' . $year . '</h3>';
echo '<div style="float:left;">' . $controls . '</div>';
echo '<div style="clear:both;"></div>';



echo $this->htmlLink(
				$this->url(array('action' => 'promote-calendar', 'month' => $this->month, 'year' => $this->year), 'event_general'),
				$this->translate('Promote This Calendar'),
				array(
					'class' => 'buttonlink smoothbox ynevent_promote_calendar'
				)
);


echo '<div id="ynevent_myCalendar">';
echo $this->calendar; //draw_calendar($month, $year, $events);
echo '</div>';
echo '<br /><br />';
?>


