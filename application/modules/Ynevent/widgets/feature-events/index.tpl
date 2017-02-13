<?php
//$this->headLink()
        //->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Ynevent/externals/styles/slideshow.css');
$this->headScript()
		->appendFile('http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places')
    ->appendFile($this->baseUrl() . '/application/modules/Ynevent/externals/scripts/Navigation.js')
	  ->appendFile($this->baseUrl() . '/application/modules/Ynevent/externals/scripts/Loop.js')
	  ->appendFile($this->baseUrl() . '/application/modules/Ynevent/externals/scripts/SlideShow.js');
   
?>

<div id="ynevent_navigation" class="demo">
	<div id="ynevent_navigation_slideshow" class="ynevent_slideshow">
		<ul id = "ynevent_slideshow_left">
			<?php foreach($this->items as $event): ?>
				<li id="lp<?php echo $event->event_id; ?>" class = "ynevent_slideshow_slide">
			    	<div class="ynevent_album_photo">
			    		<?php 
                            
                            echo $this->htmlLink($event->getHref(), $this->itemPhoto($event, 'thumb.featured'))
                                              
                                
                        ?>
			       </div>
							<div class="ynevent_albumfeatured_info">
								<div class="ynevent_album_info ynevent_album_title" style="font-weight: bold;">
										<?php echo $this->htmlLink($event->getHref(), $event->title); ?>
								</div>
								<p class="ynevent_album_info"><?php echo $this->string()->truncate($event->description, 100); ?></p>
								<p class="ynevent_album_info"><?php echo $this->locale()->toDate( $event->creation_date, array('size' => 'short'))?></p>
								<div class="ynevent_album_info ynevent_view_more"> <?php echo $this->htmlLink($event->getHref(), $this->translate("View more"));?></div>
							</div>
			    </li> 
			<?php endforeach; ?> 	 
		</ul>
		<ul class="ynevent_slideshow_pagination nav" id="ynevent_pagination">
			<?php foreach($this->items as $event): ?>
			<li><a class="current" href="#lp<?php echo $event->event_id; ?>"></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
