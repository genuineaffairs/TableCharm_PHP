<?php if($this->fullAddress) : ?>
	<?php echo $this->translate("Address")?>: <?php echo $this->fullAddress; ?><br /><br />
<?php endif; ?>
<?php if ( !($this->event->address || $this->event->latitude) ) : ?>
	<div class="tip"><span><?php echo $this->translate('No location was set'); ?></span></div>
<?php else :?>
	<?php if ($this->event->latitude && $this->event->longitude ): ?>
		<div id="ynevent_google_map_component">
			<iframe id="ynevent_profile_event_gmap" name="ynevent_profile_event_gmap" width="450" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="">
			</iframe>
		</div>
		<script type="text/javascript">
		var src = "";
		$(window).addEvent('domready', function() {
			src = "https://maps.google.com/?q=<?php echo $this->event->latitude ?>,<?php echo $this->event->longitude ?>&t=m&ie=UTF8&z=15&output=embed";
			$("ynevent_profile_event_gmap").set("src",src);
		});
		</script>
	<?php endif;?>
<?php endif;?>

