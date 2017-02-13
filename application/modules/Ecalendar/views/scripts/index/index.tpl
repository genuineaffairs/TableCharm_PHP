<link rel="stylesheet" type="text/css" href="<?php echo($this->baseUrl() . '/application/modules/Ecalendar/externals/styles/main.css'); ?>" />
<script language="javascript" type="text/javascript" src="<?php echo($this->baseUrl() . '/application/modules/Ecalendar/externals/scripts/mecPHPPlugin.js'); ?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo($this->baseUrl() . '/application/modules/Ecalendar/externals/scripts/mooECal.js'); ?>"></script>


<?php echo $this->content()->renderWidget('event.browse-menu');?> 
 <div class='layout_right'>

</div> 
 <div class='layout_middle'>
<div id="calBody"></div>
<script  language="javascript" type="text/javascript">

		var feedPlugin = 	new mecPHPPlugin();
		feedPlugin.initialize({url:"<?php echo $this->url(array('action' => 'events','controller' => 'index'));?>"})
		var today = new Date();	
        new Calendar({
				calContainer:'calBody',
				newDate:today,
				feedPlugin:feedPlugin,
				feedSpan:2
			});

</script>
</div>