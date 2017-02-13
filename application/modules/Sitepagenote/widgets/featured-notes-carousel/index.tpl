<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
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
    . 'application/modules/Sitepagenote/externals/styles/style_sitepagenote.css');
     
  $this->headScript()
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/sitepageslideitmoo-1.1_full_source.js');
?>

<a id="group_profile_members_anchor" style="position:absolute;"></a>
<script language="javascript" type="text/javascript">

  var module = 'Sitepagenote';
</script>
<script language="javascript" type="text/javascript">
  var slideshownote;
  window.addEvents ({
    'domready': function() {
      slideshownote = new SocialengineSlideItMoo({
        fwdbck_click:1,
        slide_element_limit:1,
        startindex:-1,
        in_one_row:<?php echo  $this->inOneRow_note?>,
        no_of_row:<?php echo  $this->noOfRow_note?>,
        curnt_limit:<?php echo $this->totalItemShownote;?>,
        category_id:<?php echo $this->category_id;?>,
        total:<?php echo $this->totalCount_note; ?>,
        limit:<?php echo $this->totalItemShownote*2;?>,
        module : 'Sitepagenote',
        call_count:1,
        foward:'Sitepagenote_SlideItMoo_forward',
        bck:'Sitepagenote_SlideItMoo_back',
        overallContainer: 'Sitepagenote_SlideItMoo_outer',
        elementScrolled: 'Sitepagenote_SlideItMoo_inner',
        thumbsContainer: 'Sitepagenote_SlideItMoo_items',
        slideVertical: <?php echo $this->vertical?>,
        itemsVisible:1,
        elemsSlide:1,
        duration:<?php echo  $this->interval;?>,
        itemsSelector: '.Sitepagenote_SlideItMoo_element',
        itemWidth:<?php echo 146 * $this->inOneRow_note?>,
        itemHeight:<?php echo 146 * $this->noOfRow_note?>,
        showControls:1,
        startIndex:1,
        navs:{ /* starting this version, you'll need to put your back/forward navigators in your HTML */
				fwd:'.Sitepagenote_SlideItMoo_forward', /* forward button CSS selector */
				bk:'.Sitepagenote_SlideItMoo_back' /* back button CSS selector */
				},
        transition: Fx.Transitions.linear, /* transition */
        onChange: function(index) { slideshownote.options.call_count = 1;
        }
      });

      $('Sitepagenote_SlideItMoo_back').addEvent('click', function () {slideshownote.sendajax(-1,slideshownote,'Sitepagenote',"<?php echo $this->url(array('module' => 'sitepagenote','controller' => 'index','action'=>'featured-notes-carousel'),'default',true); ?>");
        slideshownote.options.call_count = 1;

      });

      $('Sitepagenote_SlideItMoo_forward').addEvent('click', function () { slideshownote.sendajax(1,slideshownote,'Sitepagenote',"<?php echo $this->url(array('module' => 'sitepagenote','controller' => 'index','action'=>'featured-notes-carousel'),'default',true); ?>");
        slideshownote.options.call_count = 1;
      });
     
      if((slideshownote.options.total -slideshownote.options.curnt_limit)<=0){
        // hidding forward button
       document.getElementById('Sitepagenote_SlideItMoo_forward').style.display= 'none';
       document.getElementById('Sitepagenote_SlideItMoo_back_disable').style.display= 'none';
      }
    }
  });
</script>
<?php
$noteSettings=  array();
$noteSettings['class'] = 'thumb';

?>
<ul class="Sitepagecontent_featured_slider">
  <li>
		<?php
    $module = 'Sitepagenote';
    $extra_width=0;
    $extra_height=0;    
        if (empty($this->vertical)):
        $typeClass='horizontal';
         if ($this->totalCount_note > $this->totalItemShownote):
          $extra_width = 60;
          endif;
          $prev='back';
          $next='forward';
        else:
        	$typeClass='vertical';
        if ($this->totalCount_note > $this->totalItemShownote):
          $extra_height=50;
          endif;
          $prev='up';
          $next='down';
        endif;
     ?>
    <div id="Sitepagenote_SlideItMoo_outer" class="Sitepagecontent_SlideItMoo_outer Sitepagecontent_SlideItMoo_outer_<?php echo $typeClass;?>" style="height:<?php echo 146*$this->heightRow+$extra_height;?>px; width:<?php echo (146*$this->inOneRow_note)+$extra_width;?>px;">
      <div class="Sitepagecontent_SlideItMoo_back" id="Sitepagenote_SlideItMoo_back" style="display:none;">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/photo/slider-$prev.png", '', array('align'=>'', 'onMouseOver'=>'this.src="application/modules/Sitepage/externals/images/photo/slider-'.$prev.'-active.png";','onMouseOut'=>'this.src="application/modules/Sitepage/externals/images/photo/slider-'.$prev.'.png";', 'border'=>'0')) ?>
      </div>
      <div class="Sitepagecontent_SlideItMoo_back" id="Sitepagenote_SlideItMoo_back_loding" style="display:none;">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Core/externals/images/loading.gif", '', array('align'=>'', 'border'=>'0','class'=>'Sitepagecontent_SlideItMoo_loding'));  ?>
      </div>      
       <div class="Sitepagecontent_SlideItMoo_back_disable" id="Sitepagenote_SlideItMoo_back_disable" style="display:block;cursor:default;">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/photo/slider-$prev-disable.png", '', array('align'=>'', 'border'=>'0'));  ?>
      </div>
      <div id="Sitepagenote_SlideItMoo_inner" class="Sitepagecontent_SlideItMoo_inner">
        <div id="Sitepagenote_SlideItMoo_items" class="Sitepagecontent_SlideItMoo_items" style="height:<?php echo 146*$this->heightRow;?>px;">
          <div class="Sitepagecontent_SlideItMoo_element Sitepagenote_SlideItMoo_element" style="width:<?php echo 146*$this->inOneRow_note;?>px;">
              <div class="Sitepagecontent_SlideItMoo_contentList">
               <?php  $i=0; ?>
                  <?php foreach ($this->featuredNotes as $note):?>
                       <?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $note->page_id);?>
                       <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
												$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagenote.profile-sitepagenotes', $note->page_id, $layout);?>
                      	<div class="featured_thumb_content">
													<?php if($note->photo_id == 0):?>
														<?php 
														if($sitepage_object->photo_id == 0):?>
															<a class="thumb_img" href="<?php echo $note->getHref(array( 'page_id' => $note->page_id, 'note_id' => $note->note_id,'slug' => $note->getSlug(), 'tab' => $tab_id)); ?>">
																<span><?php echo $this->itemPhoto($note, 'thumb.profile', $note->getTitle()) ?></span>
															</a>
														<?php else:?>
															<a class="thumb_img" href="<?php echo $note->getHref(array( 'page_id' => $note->page_id, 'note_id' => $note->note_id,'slug' => $note->getSlug(), 'tab' => $tab_id)); ?>">
																<span style="background-image: url(<?php echo $sitepage_object->getPhotoUrl('thumb.normal'); ?>);"></span>
														</a>
													  <?php endif;?>
													<?php else:?>
														<a class="thumb_img" href="<?php echo $note->getHref(array( 'page_id' => $note->page_id, 'note_id' => $note->note_id,'slug' => $note->getSlug(), 'tab' => $tab_id)); ?>">
																<span style="background-image: url(<?php echo $note->getPhotoUrl('thumb.normal'); ?>);"></span>
														</a>
													<?php endif;?>
                          <span class="show_content_des">
                            <?php
								              $owner = $note->getOwner();
								              echo
								                  $this->htmlLink($note->getHref(array('tab' => $tab_id)), $this->string()->chunk($this->string()->truncate($note->getTitle(), 45), 10),array('title' => $note->getTitle()));
														?>
														<?php
														$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
														$tmpBody = strip_tags($sitepage_object->title);
														$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
														?>
														<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($note->page_id, $note->owner_id, $note->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?>
                            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1)):?> 
															<?php echo $this->translate('by ').
															$this->htmlLink($owner->getHref(), $this->string()->truncate($owner->getTitle(),25),array('title' => $owner->getTitle()));?>   
                            <?php endif;?>   
                          </span>
                      </div>
                   <?php  $i++; ?>
                  <?php endforeach; ?>
                <?php for($i; $i<($this->heightRow *$this->inOneRow_note);$i++):?>
                <div class="featured_thumb_content"></div>
                <?php endfor; ?>
              </div>
           </div>
        </div>
      </div>
      <?php $module = 'Sitepagenote';?>
      <div class="Sitepagecontent_SlideItMoo_forward" id ="Sitepagenote_SlideItMoo_forward">
      	<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/photo/slider-$next.png", '', array('align'=>'', 'onMouseOver'=>'this.src="application/modules/Sitepage/externals/images/photo/slider-'.$next.'-active.png";','onMouseOut'=>'this.src="application/modules/Sitepage/externals/images/photo/slider-'.$next.'.png";', 'border'=>'0')) ?>
      </div>
      <div class="Sitepagecontent_SlideItMoo_forward" id="Sitepagenote_SlideItMoo_forward_loding"  style="display: none;">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Core/externals/images/loading.gif", '', array('align'=>'', 'border'=>'0','class'=>'Sitepagecontent_SlideItMoo_loding'));  ?>
      </div>
      <div class="Sitepagecontent_SlideItMoo_forward_disable" id="Sitepagenote_SlideItMoo_forward_disable" style="display:none;cursor:default;">
      	<?php  echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/photo/slider-$next-disable.png", '', array('align'=>'', 'border'=>'0'));  ?>
      </div>
    </div>
    <div class="clear"></div>
  </li>
</ul>
