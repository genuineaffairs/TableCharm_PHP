<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
  $this->headLink()
     ->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/styles/sitepage_featured_carousel.css');

	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitepageevent/externals/styles/style_sitepageevent.css');
     
  $this->headScript()
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/sitepageslideitmoo-1.1_full_source.js');
?>

<a id="group_profile_members_anchor" style="position:absolute;"></a>
<script language="javascript" type="text/javascript">
     var module = 'Sitepageevent';
</script>
<script language="javascript" type="text/javascript">
  var slideshowevent;
  window.addEvents ({
    'domready': function() {
      slideshowevent = new SocialengineSlideItMoo({
        fwdbck_click:1,
        slide_element_limit:1,
        startindex:-1,
        in_one_row:<?php echo  $this->inOneRow_event?>,
        no_of_row:<?php echo  $this->noOfRow_event?>,
        curnt_limit:<?php echo $this->totalItemShow_event;?>,
        category_id:<?php echo $this->category_id;?>,
        total:<?php echo $this->totalCount_event; ?>,
        limit:<?php echo $this->totalItemShow_event*2;?>,
        module : 'Sitepageevent',
        call_count:1,
        foward:'Sitepageevent_SlideItMoo_forward',
        bck:'Sitepageevent_SlideItMoo_back',
        overallContainer: 'Sitepageevent_SlideItMoo_outer',
        elementScrolled: 'Sitepageevent_SlideItMoo_inner',
        thumbsContainer: 'Sitepageevent_SlideItMoo_items',
        slideVertical: <?php echo $this->vertical?>,
        itemsVisible:1,
        elemsSlide:1,
        duration:<?php echo  $this->interval;?>,
        itemsSelector: '.Sitepageevent_SlideItMoo_element',
        itemWidth:<?php echo 146 * $this->inOneRow_event?>,
        itemHeight:<?php echo 146 * $this->noOfRow_event?>,
        showControls:1,
        startIndex:1,
        navs:{ /* starting this version, you'll need to put your back/forward navigators in your HTML */
				fwd:'.Sitepageevent_SlideItMoo_forward', /* forward button CSS selector */
				bk:'.Sitepageevent_SlideItMoo_back' /* back button CSS selector */
				},
        transition: Fx.Transitions.linear, /* transition */
        onChange: function(index) { slideshowevent.options.call_count = 1;
        }
      });

      $('Sitepageevent_SlideItMoo_back').addEvent('click', function () {slideshowevent.sendajax(-1,slideshowevent,'Sitepageevent',"<?php echo $this->url(array('module' => 'sitepageevent','controller' => 'index','action'=>'featured-events-carousel'),'default',true); ?>");
        slideshowevent.options.call_count = 1;

      });

      $('Sitepageevent_SlideItMoo_forward').addEvent('click', function () { slideshowevent.sendajax(1,slideshowevent,'Sitepageevent',"<?php echo $this->url(array('module' => 'sitepageevent','controller' => 'index','action'=>'featured-events-carousel'),'default',true); ?>");
        slideshowevent.options.call_count = 1;
      });
     
      if((slideshowevent.options.total -slideshowevent.options.curnt_limit)<=0){
        // hidding forward button
       document.getElementById('Sitepageevent_SlideItMoo_forward').style.display= 'none';
       document.getElementById('Sitepageevent_SlideItMoo_back_disable').style.display= 'none';
      }
    }
  });
</script>
<?php
$eventSettings=  array();
$eventSettings['class'] = 'thumb';

?>
<ul class="Sitepagecontent_featured_slider">
  <li>
		<?php
    $extra_width=0;
    $extra_height=0;    
        if (empty($this->vertical)):
        $typeClass='horizontal';
         if ($this->totalCount_event > $this->totalItemShow_event):
          $extra_width = 60;
          endif;
          $prev='back';
          $next='forward';
        else:
        	$typeClass='vertical';
        if ($this->totalCount_event > $this->totalItemShow_event):
          $extra_height=50;
          endif;
          $prev='up';
          $next='down';
        endif;
     ?>
    <div id="Sitepageevent_SlideItMoo_outer" class="Sitepagecontent_SlideItMoo_outer Sitepagecontent_SlideItMoo_outer_<?php echo $typeClass;?>" style="height:<?php echo 146*$this->heightRow+$extra_height;?>px; width:<?php echo (146*$this->inOneRow_event)+$extra_width;?>px;">
      <div class="Sitepagecontent_SlideItMoo_back" id="Sitepageevent_SlideItMoo_back" style="display:none;">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/photo/slider-$prev.png", '', array('align'=>'', 'onMouseOver'=>'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/photo/slider-'.$prev.'-active.png";','onMouseOut'=>'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/photo/slider-'.$prev.'.png";', 'border'=>'0')) ?>
      </div>
      <div class="Sitepagecontent_SlideItMoo_back" id="Sitepageevent_SlideItMoo_back_loding" style="display:none;">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Core/externals/images/loading.gif", '', array('align'=>'', 'border'=>'0','class'=>'Sitepagecontent_SlideItMoo_loding'));  ?>
      </div>      
       <div class="Sitepagecontent_SlideItMoo_back_disable" id="Sitepageevent_SlideItMoo_back_disable" style="display:block;cursor:default;">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/photo/slider-$prev-disable.png", '', array('align'=>'', 'border'=>'0'));  ?>
      </div>
      <div id="Sitepageevent_SlideItMoo_inner" class="Sitepagecontent_SlideItMoo_inner">
        <div id="Sitepageevent_SlideItMoo_items" style="height:<?php echo 146*$this->heightRow;?>px;">
          <div class="Sitepagecontent_SlideItMoo_element Sitepageevent_SlideItMoo_element" style="width:<?php echo 146*$this->inOneRow_event;?>px;">
              <div class="Sitepagecontent_SlideItMoo_contentList">
               <?php  $i=0; ?>
                  <?php foreach ($this->featuredEvents as $event):?>
                       	<div class="featured_thumb_content">								
													<?php if($event->photo_id == 0)	:?>
														<a class="thumb_img" href="<?php echo $event->getHref(array( 'page_id' => $event->page_id, 'event_id' => $event->event_id,'slug' => $event->getSlug())); ?>">
															<span><?php echo $this->itemPhoto($event, 'thumb.profile', $event->getTitle()) ?></span>
														</a>
													<?php else :?>
														<a class="thumb_img" href="<?php echo $event->getHref(array( 'page_id' => $event->page_id, 'event_id' => $event->event_id,'slug' => $event->getSlug())); ?>">
															<span style="background-image: url(<?php echo $event->getPhotoUrl('thumb.normal'); ?>);"></span>
														</a>
													<?php endif; ?>	
                          <span class="show_content_des">
                            <?php
								              $owner = $event->getOwner();
								              echo 
								                  $this->htmlLink($event->getHref(), $this->string()->truncate($event->getTitle(),25),array('title'=> $event->getTitle()));
														?>
														<?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $event->page_id);?>
														<?php
														$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
														$tmpBody = strip_tags($sitepage_object->title);
														$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
														?>
														<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($event->page_id, $event->owner_id, $event->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?> 
                            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1)):?>  
															<?php echo $this->translate('by ').
																		$this->htmlLink($owner->getHref(), $this->string()->truncate($owner->getTitle(),25),array('title'=> $owner->getTitle()));?> 
                            <?php endif;?>    
                          </span>
                      </div>
                   <?php  $i++; ?>
                  <?php endforeach; ?>
                <?php for($i; $i<($this->heightRow *$this->inOneRow_event);$i++):?>
                <div class="featured_thumb_content"></div>
                <?php endfor; ?>
              </div>
           </div>
        </div>
      </div>
      <div class="Sitepagecontent_SlideItMoo_forward" id ="Sitepageevent_SlideItMoo_forward">
      	<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/photo/slider-$next.png", '', array('align'=>'', 'onMouseOver'=>'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/photo/slider-'.$next.'-active.png";','onMouseOut'=>'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/photo/slider-'.$next.'.png";', 'border'=>'0')) ?>
      </div>
      <div class="Sitepagecontent_SlideItMoo_forward" id="Sitepageevent_SlideItMoo_forward_loding"  style="display: none;">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Core/externals/images/loading.gif", '', array('align'=>'', 'border'=>'0','class'=>'Sitepagecontent_SlideItMoo_loding'));  ?>
      </div>
      <div class="Sitepagecontent_SlideItMoo_forward_disable" id="Sitepageevent_SlideItMoo_forward_disable" style="display:none;cursor:default;">
      	<?php  echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/photo/slider-$next-disable.png", '', array('align'=>'', 'border'=>'0'));  ?>
      </div>
    </div>
    <div class="clear"></div>
  </li>
</ul>
