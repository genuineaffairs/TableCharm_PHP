<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headLink()->appendStylesheet($this->seaddonsBaseUrl()
  	              . '/application/modules/Seaocore/externals/styles/styles.css');
?>
<script language="javascript" type="text/javascript">
  var urlhome="<?php echo $this->url(array(),"document_homesponsored",true); ?>";
</script>
<?php
$this->headScript()
        ->appendFile($this->seaddonsBaseUrl() . '/application/modules/Document/externals/scripts/slideitmoo-1.1_full_source.js');
?>
<a id="group_profile_members_anchor" class="pabsolute"></a>
<script language="javascript" type="text/javascript"> 
  var total = '<?php echo $this->totalSponsoredDocuments; ?>';

  var forward_link;
  var fwdbck_click  = 1;
 
  var limit='<?php echo $this->limit * 2; ?>';
  var curnt_limit='<?php echo $this->limit; ?>';
  var category_id='<?php echo $this->category_id; ?>';
  var titletruncation='<?php echo $this->titletruncation; ?>';
  var startindex = -1;
  var call_count = 1;
</script>
<script language="javascript" type="text/javascript">
  var slideshow;
  window.addEvents ({
    'domready': function() {
      slideshow = new SlideItMoo({
        overallContainer: 'SlideItMoo_outer',
        elementScrolled: 'SlideItMoo_inner',
        thumbsContainer: 'SlideItMoo_items',
        itemsVisible:curnt_limit,
        elemsSlide:curnt_limit,
        duration:8000,
        itemsSelector: '.SlideItMoo_element',
        itemWidth:132,
        showControls:1,
        startIndex:1,
        transition: Fx.Transitions.linear, /* transition */
        onChange: function(index) { call_count = 1;
        }
      });

      $('SlideItMoo_back').addEvent('click', function () {slideshow.sendajax(-1,slideshow);
        call_count = 1;

      });

      $('SlideItMoo_forward').addEvent('click', function () { slideshow.sendajax(1,slideshow);
        call_count = 1;
      });

       
      if((total-curnt_limit)<=0){
        // hidding forward button
        document.getElementById('SlideItMoo_forward').style.display= 'none';
        document.getElementById('SlideItMoo_back_dis').style.display= 'none';
      }
    }
  });
  var obj_sliditmoo;
  function custom_sliditmoo (obj, direction) {
    obj_sliditmoo = new SlideItMoo({
      overallContainer: 'SlideItMoo_outer',
      elementScrolled: 'SlideItMoo_inner',
      thumbsContainer: 'SlideItMoo_items',
      itemsVisible:curnt_limit,
      elemsSlide:curnt_limit,
      duration:<?php echo $this->interval; ?>,
      itemsSelector: '.SlideItMoo_element',
      itemWidth:132,
      showControls:1,
      startIndex:1,
      onChange: function(index) { call_count = 1;
      }
    });

    obj_sliditmoo.slide(direction, obj_sliditmoo);

    if(startindex<=0 && direction== -1){
      // hidding back button
      document.getElementById('SlideItMoo_back').style.display= 'none';
      document.getElementById('SlideItMoo_back_dis').style.display= 'block';
    }else{
      // vissible back button
      document.getElementById('SlideItMoo_back').style.display= 'block';
      document.getElementById('SlideItMoo_back_dis').style.display= 'none';
    }

    if(((startindex>(total-limit)|| (startindex>=(total-limit))) && direction== 1) || ((startindex>=(total-curnt_limit)) && direction == -1)){
      // hidding forward button
      document.getElementById('SlideItMoo_forward').style.display= 'none';
      document.getElementById('SlideItMoo_forward_dis').style.display= 'block';

    }else{
      // vissible forward button
      document.getElementById('SlideItMoo_forward').style.display= 'block';
      document.getElementById('SlideItMoo_forward_dis').style.display= 'none';
    }
    fwdbck_click = 1;
  }
</script>
<ul class="seaocore_sponsored_widget">
  <li>
    <div id="SlideItMoo_outer" class="seaocore_sponsored_carousel">
      <div class="seaocore_sponsored_carousel_back" id="SlideItMoo_back" style="display:none;">
        <?php echo $this->htmlImage('application/modules/Document/externals/images/document_carousel_up.png', '', array('align' => '', 'onMouseOver' => 'this.src="application/modules/Document/externals/images/document_carousel_up_hover.png";', 'onMouseOut' => 'this.src="application/modules/Document/externals/images/document_carousel_up.png";', 'border' => '0')) ?>
      </div>
       <div class="seaocore_sponsored_carousel_back" id="SlideItMoo_back_loding" style="display:none;">
        <?php echo $this->htmlImage("application/modules/Core/externals/images/loading.gif", '', array('align'=>'', 'border'=>'0','class'=>''));  ?>
      </div>
      <div class="seaocore_sponsored_carousel_back_dis" id="SlideItMoo_back_dis" style="display:block;">
        <?php 
          echo $this->htmlImage('application/modules/Document/externals/images/document_carousel_up_dis.png', '', array('align' => '', 'border' => '0'));
          ?>
      </div>
      <div id="SlideItMoo_inner" class="seaocore_sponsored_carousel_inner">
        <div id="SlideItMoo_items" class="seaocore_sponsored_carousel_item_list">
          <?php foreach ($this->documents as $document): ?>
            <div class="SlideItMoo_element seaocore_sponsored_carousel_items">
              <div class="seaocore_sponsored_carousel_items_thumb">

								<?php if(!empty($document->photo_id)):?>
									<?php echo $this->htmlLink($document->getHref(), $this->itemPhoto($document, 'thumb.icon'), array('title' => $document->document_title, 'rel' => 'lightbox[galerie]') ); ?>
								<?php else: ?>
									<?php echo $this->htmlLink($document->getHref(), '<img src="'. Engine_Api::_()->document()->sslThumbnail($document->thumbnail) .'" class="thumb_icon" />', array('title' => $document->document_title, 'rel' => 'lightbox[galerie]') ); ?>
								<?php endif; ?>

              </div>
              <div class="seaocore_sponsored_carousel_items_info">
                <div class="seaocore_sponsored_carousel_items_title seaocore_txt_light">
									<?php
									$item_title = Engine_Api::_()->document()->truncateText($document->getTitle(), $this->titletruncation);
									echo $this->htmlLink(Engine_Api::_()->document()->getHref($document->document_id, $document->owner_id, $document->getSlug()), $item_title, array('title' => $document->getTitle()))
									?>
                </div>                   		
                <div class="seaocore_sponsored_carousel_items_stat seaocore_txt_light">
									<?php echo $this->translate('posted by'); ?>
									<?php echo $this->htmlLink($document->getOwner()->getHref(), Engine_Api::_()->document()->truncateText($document->getOwner()->getTitle(), 100), array('title' => $document->getOwner()->getTitle())) ?>
                </div>											
              </div>
            </div>	                  
          <?php endforeach; ?>
        </div>
      </div>
      <div class="seaocore_sponsored_carousel_forward" id ="SlideItMoo_forward">
        <?php echo $this->htmlImage('application/modules/Document/externals/images/document_carousel_down.png', '', array('align' => '', 'onMouseOver' => 'this.src="application/modules/Document/externals/images/document_carousel_down_hover.png";', 'onMouseOut' => 'this.src="application/modules/Document/externals/images/document_carousel_down.png";', 'border' => '0')) ?>
      </div>

      <div class="seaocore_sponsored_carousel_forward" id="SlideItMoo_forward_loding"  style="display: none;">
        <?php echo $this->htmlImage("application/modules/Core/externals/images/loading.gif", '', array('align'=>'', 'border'=>'0','class'=>''));  ?>
      </div>
      <div class="seaocore_sponsored_carousel_forward_dis" id="SlideItMoo_forward_dis" style="display:none;">
        <?php 
          echo $this->htmlImage('application/modules/Document/externals/images/document_carousel_down_dis.png', '', array('align' => '', 'border' => '0'));
         ?>
      </div>
    </div>
    <div class="clr"></div>
  </li>
</ul>