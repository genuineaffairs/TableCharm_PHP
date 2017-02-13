<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
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
    . 'application/modules/Sitepagedocument/externals/styles/style_sitepagedocument.css');
     
  $this->headScript()
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/sitepageslideitmoo-1.1_full_source.js');
?>

<a id="group_profile_members_anchor" style="position:absolute;"></a>
<script language="javascript" type="text/javascript">
 
  var module = 'Sitepagedocument';
//   var forward_link_document;
//   var fwdbck_click_document  = 1;
//   var total_document = '<?php echo $this->totalCount_document; ?>';
//   var limit_documnet='<?php echo $this->totalItemShow_document*2;?>';
//   var curnt_limit_document=<?php echo $this->totalItemShow_document;?>;
//   var slide_element_limit_document=1; 
//   var no_of_row_document=<?php echo  $this->noOfRow_document?>;
//   var in_one_row_document=<?php echo  $this->inOneRow_document?>;
//   var startindex_document = -1;
</script>
<script language="javascript" type="text/javascript">
  var slideshowdocument;
  window.addEvents ({
    'domready': function() {
      slideshowdocument = new SocialengineSlideItMoo({
        fwdbck_click:1,
        slide_element_limit:1,
        startindex:-1,
        in_one_row:<?php echo  $this->inOneRow_document?>,
        no_of_row:<?php echo  $this->noOfRow_document?>,
        curnt_limit:<?php echo $this->totalItemShow_document;?>,
        category_id:<?php echo $this->category_id;?>,
        total:<?php echo $this->totalCount_document; ?>,
        limit:<?php echo $this->totalItemShow_document*2;?>,
        module : 'Sitepagedocument',
        call_count:1,
        foward:'Sitepagedocument_SlideItMoo_forward',
        bck:'Sitepagedocument_SlideItMoo_back',
        overallContainer: 'Sitepagedocument_SlideItMoo_outer',
        elementScrolled: 'Sitepagedocument_SlideItMoo_inner',
        thumbsContainer: 'Sitepagedocument_SlideItMoo_items',
        slideVertical: <?php echo $this->vertical?>,
        itemsVisible:1,
        elemsSlide:1,
        duration:<?php echo  $this->interval;?>,
        itemsSelector: '.Sitepagedocument_SlideItMoo_element',
        itemWidth:<?php echo 146 * $this->inOneRow_document?>,
        itemHeight:<?php echo 146 * $this->noOfRow_document?>,
        showControls:1,
        startIndex:1,
        navs:{ /* starting this version, you'll need to put your back/forward navigators in your HTML */
				fwd:'.Sitepagedocument_SlideItMoo_forward', /* forward button CSS selector */
				bk:'.Sitepagedocument_SlideItMoo_back' /* back button CSS selector */
				},
        transition: Fx.Transitions.linear, /* transition */
        onChange: function(index) { slideshowdocument.options.call_count = 1;
        }
      });

      $('Sitepagedocument_SlideItMoo_back').addEvent('click', function () {slideshowdocument.sendajax(-1,slideshowdocument,'Sitepagedocument',"<?php echo $this->url(array('module' => 'sitepagedocument','controller' => 'index','action'=>'featured-documents-carousel'),'default',true); ?>");
        slideshowdocument.options.call_count = 1;

      });

      $('Sitepagedocument_SlideItMoo_forward').addEvent('click', function () { slideshowdocument.sendajax(1,slideshowdocument,'Sitepagedocument',"<?php echo $this->url(array('module' => 'sitepagedocument','controller' => 'index','action'=>'featured-documents-carousel'),'default',true); ?>");
        slideshowdocument.options.call_count = 1;
      });
     
      if((slideshowdocument.options.total -slideshowdocument.options.curnt_limit)<=0){
        // hidding forward button
       document.getElementById('Sitepagedocument_SlideItMoo_forward').style.display= 'none';
       document.getElementById('Sitepagedocument_SlideItMoo_back_disable').style.display= 'none';
      }
    }
  });
</script>
<?php
$documentSettings=  array();
$documentSettings['class'] = 'thumb';

?>
<ul class="Sitepagecontent_featured_slider">
  <li>
		<?php
    $extra_width=0;
    $extra_height=0;    
        if (empty($this->vertical)):
        $typeClass='horizontal';
         if ($this->totalCount_document > $this->totalItemShow_document):
          $extra_width = 60;
          endif;
          $prev='back';
          $next='forward';
        else:
        	$typeClass='vertical';
        if ($this->totalCount_document > $this->totalItemShow_document):
          $extra_height=50;
          endif;
          $prev='up';
          $next='down';
        endif;
     ?>
    <div id="Sitepagedocument_SlideItMoo_outer" class="Sitepagecontent_SlideItMoo_outer Sitepagecontent_SlideItMoo_outer_<?php echo $typeClass;?>" style="height:<?php echo 146*$this->heightRow+$extra_height;?>px; width:<?php echo (146*$this->inOneRow_document)+$extra_width;?>px;">
      <div class="Sitepagecontent_SlideItMoo_back" id="Sitepagedocument_SlideItMoo_back" style="display:none;">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/photo/slider-$prev.png", '', array('align'=>'', 'onMouseOver'=>'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/photo/slider-'.$prev.'-active.png";','onMouseOut'=>'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/photo/slider-'.$prev.'.png";', 'border'=>'0')) ?>
      </div>
      <div class="Sitepagecontent_SlideItMoo_back" id="Sitepagedocument_SlideItMoo_back_loding" style="display:none;">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Core/externals/images/loading.gif", '', array('align'=>'', 'border'=>'0','class'=>'Sitepagecontent_SlideItMoo_loding'));  ?>
      </div>      
       <div class="Sitepagecontent_SlideItMoo_back_disable" id="Sitepagedocument_SlideItMoo_back_disable" style="display:block;cursor:default;">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/photo/slider-$prev-disable.png", '', array('align'=>'', 'border'=>'0'));  ?>
      </div>
      <div id="Sitepagedocument_SlideItMoo_inner" class="Sitepagecontent_SlideItMoo_inner">
        <div id="Sitepagedocument_SlideItMoo_items" class="Sitepagecontent_SlideItMoo_items" style="height:<?php echo 146*$this->heightRow;?>px;">
          <div class="Sitepagecontent_SlideItMoo_element Sitepagedocument_SlideItMoo_element" style="width:<?php echo 146*$this->inOneRow_document;?>px;">
              <div class="Sitepagecontent_SlideItMoo_contentList">
               <?php  $i=0; ?>
                  <?php foreach ($this->featuredDocuments as $document):?>
                       <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
												$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagedocument.profile-sitepagedocuments', $document->page_id, $layout);?>
                       <div class="featured_thumb_content">
                          <a href="<?php echo $this->url(array('user_id' => $document->owner_id, 'document_id' =>  $document->document_id,'tab' => $tab_id,'slug' => $document->getSlug()),'sitepagedocument_detail_view', true)?>">
														<?php
														//SSL WORK
														$this->https = 0;
														if (!empty($_SERVER["HTTPS"]) && 'on' == strtolower($_SERVER["HTTPS"])) {
														$this->https = 1;
														}

														if($this->https) {
														$this->manifest_path = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.manifestUrl', "page-documents");
														echo $document->thumbnail = $this->baseUrl().'/'.$this->manifest_path."/ssl?url=".urlencode($document->thumbnail);
														}
														?>
												  </a>
                          <?php echo $this->htmlLink($document->getHref(), '<span><img src="'. $document->thumbnail .'" alt="" /></span>', array('class'=>'thumb_img')) ?>
                          <span class="show_content_des">
                            <?php
								              $owner = $document->getOwner();
								              echo $this->htmlLink($document->getHref(array('tab' => $tab_id)), $this->string()->truncate($document->getTitle(),25),array('title'=> $document->getTitle()));
														?>
														<?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $document->page_id);?>
														<?php
														$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
														$tmpBody = strip_tags($sitepage_object->title);
														$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
														?>
														<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($document->page_id, $document->owner_id, $document->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?> 
                            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1)):?>  
															<?php echo $this->translate('by ').
																		$this->htmlLink($owner->getHref(), $this->string()->truncate($owner->getTitle(),25),array('title'=> $owner->getTitle()));?> 
                            <?php endif;?>  
                          </span>
                      </div>
                   <?php  $i++; ?>
                  <?php endforeach; ?>
                <?php for($i; $i<($this->heightRow *$this->inOneRow_document);$i++):?>
                <div class="featured_thumb_content"></div>
                <?php endfor; ?>
              </div>
           </div>
        </div>
      </div>
      <div class="Sitepagecontent_SlideItMoo_forward" id ="Sitepagedocument_SlideItMoo_forward">
      	<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/photo/slider-$next.png", '', array('align'=>'', 'onMouseOver'=>'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/photo/slider-'.$next.'-active.png";','onMouseOut'=>'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/photo/slider-'.$next.'.png";', 'border'=>'0')) ?>
      </div>
      <div class="Sitepagecontent_SlideItMoo_forward" id="Sitepagedocument_SlideItMoo_forward_loding"  style="display: none;">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Core/externals/images/loading.gif", '', array('align'=>'', 'border'=>'0','class'=>'Sitepagecontent_SlideItMoo_loding'));  ?>
      </div>
      <div class="Sitepagecontent_SlideItMoo_forward_disable" id="Sitepagedocument_SlideItMoo_forward_disable" style="display:none;cursor:default;">
      	<?php  echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/photo/slider-$next-disable.png", '', array('align'=>'', 'border'=>'0'));  ?>
      </div>
    </div>
    <div class="clear"></div>
  </li>
</ul>
