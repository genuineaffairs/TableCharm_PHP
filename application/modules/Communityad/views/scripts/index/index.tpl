<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">

	function preview_coupon(height, width, package_type) {
		 var height_width = "width="+width+",height="+height;
		 var child_window = window.open (en4.core.baseUrl + 'sitecoupon/index/previewcoupon/package_type/' + package_type,'mywindow','scrollbars=yes,width=600,height=600');
	}
	
</script>
<?php if(empty($this->is_ajax)): ?>
<?php
  $this->headLink()
  	->prependStylesheet($this->layout()->staticBaseUrl .'application/modules/Communityad/externals/styles/style_communityad.css');
?>
<div class="headline">
  <h2>
    <?php echo $this->translate('Advertising');?>
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
<div>
<div>
<h3 style="margin-bottom:10px;"> 
	<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/ad-icon.png" alt="" class="ad_icon" />
	<?php echo $this->translate("Advertise on ") . Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title') ?></h3>
	<h4 style="margin-left:10px;" class="cmad_step fleft"> <?php echo $this->translate('1. Choose an Ad Package');?></h4>
	
	<?php //start coupon plugin work. ?>
	<?php if (!empty($this->modules_enabled) && in_array("package", $this->modules_enabled)) : ?>
		<h4 class="cmad_step fright"><a href="javascript:void(0);" class=" buttonlink item_icon_coupon"  onclick="javascript:preview_coupon('<?php echo '500' ?>', '<?php echo '500' ?>', '<?php echo 'package' ?>');"><?php echo $this->translate('Discount Coupons') ?></a></h4>
	<?php endif; ?>
	
	<div class='cmad_package_page'>
    <input type="hidden" id="type" name="type" value="default" />   
    	<?php if (count($this->adTypes) > 0): ?>
	    	<ul class="cmad_package_list">
	        <li>          
            <div class="select_package">    
              <label> <?php echo $this->translate('Ad Type'); ?></label>
              <div class="select_package_element">
	              <p class="description"><?php echo $this->translate('Select an Ad Type.') ?></p>
	              <ul class="">
	                <li>
	                	<input id="radio_default" type="radio"  <?php if ($this->package_type == "default"): echo 'checked="checked"';
	        endif; ?>  onclick="javascript:getPackageList(this.value);" value="default" name='type' />
                    
                    <label for="radio_default" class="fleft"><?php echo $this->translate($this->getCommunityadTitle); ?></label>
              			<span class="cmad_show_tooltip_wrapper fleft" style="clear:none;margin-left:5px;">
            					<a href="javascript:void(0);">[?]</a>
											<div class="cmad_show_tooltip" style="margin-left:-8px;">
												<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/tooltip_arrow.png" />
												<?php echo $this->translate("Promote your content from this community, your website, or any online destination. You can specify a custom message, action URL and upload ad image. Ads of content will be likable and relevant actions from the viewer's friends and others will automatically be shown to build word-of-mouth awareness.");?>
											</div>
            				</span>
	        				</li>	                  
	        				<?php foreach ($this->adTypes as $adType): ?>
	                	<li>
	                		<input id="radio_<?php echo $adType->type ?>" type="radio"  <?php if ($this->package_type == $adType->type): echo 'checked="checked"';
	              endif; ?>  onclick="javascript:getPackageList(this.value);" value="<?php echo $adType->type ?>" name='type' />
	              			<label for="radio_<?php echo $adType->type ?>" class="fleft"><?php echo $this->translate($adType->title); ?></label>
	              			<span class="cmad_show_tooltip_wrapper fleft" style="clear:none;margin-left:5px;">
              					<a href="javascript:void(0);">[?]</a>
												<div class="cmad_show_tooltip" style="margin-left:-8px;">
													<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/tooltip_arrow.png" />
													<?php echo $this->translate("Get more distribution and visibility for user interaction with your content from this community. These social ads always include stories of relevant actions by viewer's friends on your content. Example: Viewer's friends Liking your content. With Sponsored Stories, viewers will be able to Like your content inline on-the-spot. Such Like actions will lead to a viral promotion of your content as your Sponsored Story will then also be viewable to the viewer’s friends.");?>
												</div>
              				</span>
	              		</li>
	                  <?php endforeach; ?>
	              </ul>
              </div>                 
            </div>         
	        </li>      
	      </ul>  
       <?php endif; ?>
      
			<ul class="cmad_package_list" id="package_decription" style="border-bottom:none;display: <?php echo count($this->adTypes) > 0 ? 'none':'' ?>;">
				<li>
					<?php echo $this->translate('Select a package for creating your advertisement.'); ?>
				</li>
			</ul>
            
            
    <ul class="cmad_package_list" id="package_list" style="display: <?php echo count($this->adTypes) > 0 ? 'none':'' ?>;">
    <?php endif; ?> 
    <?php if(!empty($this->is_ajax) || count($this->adTypes)==0): ?> 
		<?php if( count($this->paginator) ): ?>						
        <?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?> 				
    <?php foreach ($this->paginator as $item): ?>
      <li>
        <div class="cmad_package_list_title">
          <div class="cmad_hr_link">
            <?php if (!empty($this->type_id) && !empty($this->type)) : ?>
              <a href='<?php echo $this->url(array('id' => $item->package_id, 'type' => $this->type, 'type_id' => $this->type_id), 'communityad_create', true) ?>' ><?php echo $this->translate("COMMUNITYAD_PACKAGE_CREATE_BUTTON_".strtoupper($item->type)); ?> &raquo;</a>
            <?php else : ?>
              <a href='<?php echo $this->url(array('id' => $item->package_id), 'communityad_create', true) ?>' ><?php echo $this->translate("COMMUNITYAD_PACKAGE_CREATE_BUTTON_".strtoupper($item->type)); ?> &raquo;</a>  
            <?php endif; ?>
          </div>
          <h3>
            <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'communityad', 'controller' => 'index', 'action' => 'packge-detail', 'id' => $item->package_id),'default')?>')"  ><?php echo $this->translate(ucfirst($item->title));?></a>
          </h3>  
        </div>
        <div class="cmad_package_stat">
          <span>
            <b><?php echo $this->translate("Price") . ": "; ?> </b>
            <?php
            if (!$item->isFree()):echo $this->locale()->toCurrency($item->price, $currency);
            else: echo $this->translate('FREE');
            endif;
            ?>
          </span>
          <span>
            <b><?php echo $this->translate("Quantity") . ": "; ?> </b>
            <?php
            switch ($item->price_model):
              case "Pay/view":
                if ($item->model_detail != -1): echo $this->translate(array('%s View', '%s Views', $item->model_detail), $this->locale()->toNumber($item->model_detail));
                else: echo $this->translate('UNLIMITED Views');
                endif;

                break;

              case "Pay/click":
                if ($item->model_detail != -1): echo $this->translate(array('%s Click', '%s Clicks', $item->model_detail), $this->locale()->toNumber($item->model_detail));
                else: echo $this->translate('UNLIMITED Clicks');
                endif;

                break;

              case "Pay/period":
                if ($item->model_detail != -1): echo $this->translate(array('%s Day', '%s Days', $item->model_detail), $this->locale()->toNumber($item->model_detail));
                else: echo $this->translate('UNLIMITED  Days');
                endif;

                break;
            endswitch;
            ?>
          </span>
          <?php if($item->type=='default'):?>
          <span>
            <b><?php echo $this->translate("Featured") . ": "; ?> </b>
            <?php
            if ($item->featured == 1)
              echo $this->translate("Yes");
            else
              echo $this->translate("No");
            ?>
          </span>
          <span>
            <b><?php echo $this->translate("Sponsored") . ": "; ?> </b>
            <?php
            if ($item->sponsored == 1)
              echo $this->translate("Yes");
            else
              echo $this->translate("No");
            ?>
          </span>
          <?php endif; ?> 
          <span>
            <b><?php echo $this->translate("Targeting") . ": "; ?> </b>
            <?php
            if ($item->network == 1)
              echo $this->translate("Yes");
            else
              echo $this->translate("No");
            ?>
          </span>
          <span style="clear:both;margin-right:10px;">
            <b><?php echo $this->translate("You can advertise"). ": "; ?> </b>       
            <?php
            $canAdvertise = explode(",", $item->urloption);                       
            if (in_array("website", $canAdvertise)) {
              $canAdvertise[array_search("website", $canAdvertise)] = $this->translate('Custom Ad');
            }
            
            foreach ($canAdvertise as $key => $value):               
              if( strstr($value, "sitereview") ){
                $isReviewPluginEnabled = Engine_Api::_()->getDbtable('modules', 'communityad')->getModuleInfo("sitereview");
                if( !empty($isReviewPluginEnabled) ){
                    $sitereviewExplode = explode("_", $value);
                    $getAdsMod = Engine_Api::_()->getItem("communityad_module", $sitereviewExplode[1]);
                    $modTemTitle = strtolower($getAdsMod->module_title);
                    $modTemTitle = ucfirst($modTemTitle);
                    $canAdvertise[$key] = $modTemTitle;
                }else {
                    unset($canAdvertise[$key]);
                }
              }else {                
                if ($value != 'Custom Ad') {                  
                  $getInfo = Engine_Api::_()->getDbtable('modules', 'communityad')->getModuleInfo($value);
                  if (!empty($getInfo)) {
                    $canAdvertise[$key] = $this->translate($getInfo['module_title']);
                  }else {
                      unset($canAdvertise[$key]);
                  }
                }else {
                  $canAdvertise[$key] = ucfirst($value);
                }
              }
              endforeach;

            $canAdStr = implode(", ", $canAdvertise);
            echo $canAdStr;
            ?>
          </span>     
        </div>

        <div class="cmad_list_details">
      <?php echo $this->translate($this->viewMore($item->desc)); ?>
        </div>
        <div style="clear:both;"></div>
      </li>
    <?php endforeach; ?>       
				
        	<div class="" id="view_more" onclick="getPackageList('')" style="margin-top: 5px; text-align:center; display:<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>" >   <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(           
              'class' => 'buttonlink icon_viewmore'
            )) ?>
            </div>
          <div class="cmad_package_loading" id="loding_image" style="display:none;margin:5px 0;text-align:center;">                   
            <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='margin-right: 5px;' />
            <?php echo $this->translate("Loading ...") ?>
          </div>
		<?php else: ?>   
		 <div class="tip">
		    <span>
		    <?php echo $this->translate("There are no ad packages right now for creating your ad. Please click ". "<a href= '". $this->url(array('page_id' => 4), 'communityad_help', true)."' > here</a>" . " to contact sales team for advertising.") ?>
		    </span>
		  </div>    
		<?php endif; ?>
   <?php endif; ?>      
<?php if(empty($this->is_ajax)): ?>     
      </ul> 
		</div>
	</div>
</div>
<?php endif; ?>
<script type="text/javascript">

  function getNextPage(){
     <?php if ($this->is_ajax || count($this->adTypes) == 0) : ?>
    return <?php echo $this->paginator->getCurrentPageNumber() + 1 ?>;
    <?php endif; ?>
  }
  var getPackageList;
  var package_type_temp='<?php echo $this->package_type ?>';
  en4.core.runonce.add(function() {
    
    getPackageList =function(type){
      if(type !=''){
        if($('type').value==type && $('package_list').style.display=='')
          return;
        $('type').value=type;
      }
      $('package_list').style.display='';
       if($('view_more'))
        $('view_more').destroy();        
      if(package_type_temp !=$('type').value){
        $('package_list').empty();
        	$("package_decription").style.display='none';
       var loadingHTML= '<div id="package_type_loading" style="display: none;" class="cmad_package_loading" >'
				+'<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif" alt="Loading" />'
      +'</div>';
         Elements.from(loadingHTML).inject($('package_list'));
        $('package_type_loading').style.display = '';
        var nextPage=1;
      }else{       
        if($('loding_image'))
          $('loding_image').style.display = '';
        var  nextPage=getNextPage();
      }  
      package_type_temp =$('type').value;
      var request = new Request.HTML({
        url : '<?php echo $this->url(array(), 'communityad_listpackage', true); ?>',
        data : {
          format : 'html',
          'package_type' : $('type').value,       
          'page':nextPage,
          'is_ajax':1,
          'type_id':'<?php echo  $this->type_id?>',
          'type':'<?php echo $this->type ?>'
        },
        evalScripts : true,
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        	$("package_decription").style.display='';
           if($('package_type_loading'))
          $('package_type_loading').destroy(); 
          if(nextPage ==1){
              $('package_list').empty();
          }      
          if($('loding_image'))
            $('loding_image').destroy(); 
          Elements.from(responseHTML).inject($('package_list'));        
          
        }
      });
      request.send();
    }
});

</script>