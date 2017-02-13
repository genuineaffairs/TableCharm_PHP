<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _cardpictureimage.tpl  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
 
  window.addEvent('load', function(){
    var title_count;
    var body_count;
    $('name').addEvent('keyup', function()
    {
      nameTitle(this);
    });

    $('name').addEvent('blur', function()
    {
      nameTitle(this);
    });

    function nameTitle(thisValue){
      if($('validation_name')){
        document.getElementById("name-element").removeChild($('validation_name'));
      }

      if( thisValue.value != '' ){
        title = thisValue.value;
         var maxSizeTitle= <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.title', 25); ?>;
          if(title.length>maxSizeTitle)
          { title = title.substring(0,maxSizeTitle);
            thisValue.value=title.substring(0,maxSizeTitle);
          }
       $('ad_title').innerHTML = '<a href="javascript:void(0);" >'+title +'</a>';
      }
      else
      { 
       $('ad_title').innerHTML = title = '<a href="javascript:void(0);" >'+'<?php echo $this->string()->escapeJavascript($this->translate("Example Ad Title")) ?>'+'</a>';
      }
     
    }

    
   
    $('cads_body').addEvent('keyup', function(){
      var body='';
      if($('validation_cads_body')){
        document.getElementById("cads_body-element").removeChild($('validation_cads_body'));
      }
      if( this.value != '' ){
        body = this.value;
      }
      else{
        body =  '<?php echo $this->string()->escapeJavascript($this->translate("Example ad body text.")) ?>';
      }
      var maxSize= <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.body', 135); ?>;
      if(body.length<=maxSize)
        $('ad_body').innerHTML = body;
      else
      { $('ad_body').innerHTML = body.substring(0,maxSize);
        this.value=body.substring(0,maxSize);}
    });
	$('cads_body').addEvent('blur', function(){
      var body=''; 
      if($('validation_cads_body')){
        document.getElementById("cads_body-element").removeChild($('validation_cads_body'));
      }
      if( this.value != '' ){
        body = this.value;
      }
      else{
        body =  '<?php echo $this->string()->escapeJavascript($this->translate("Example ad body text.")) ?>';
      }
      var maxSize= <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.body', 135); ?>;
      if(body.length<=maxSize)
        $('ad_body').innerHTML = body;
      else
      { $('ad_body').innerHTML = body.substring(0,maxSize);
        this.value=body.substring(0,maxSize);}
    });

  });



</script>

<div class="cmaddis_preview_wrapper">
	<b><?php echo $this->translate("Preview Your Ad"); ?></b>
	<div class="cadcp_preview">
	  <?php
	  $ad_body = $this->translate("Example ad body text.");
	
	  $ad_title = '<a href="javascript:void(0);">'. $this->translate('Example Ad Title'). '</a>';
	  ?>
	  
	 	<div class="cmaddis">
	    <div class="cmad_addis">
	    	<div class="cmad_show_tooltip_wrapper">
		      <div class='cmaddis_title'id="ad_title">
		        <?php echo $ad_title; ?>
		      </div>
		    	<div class="cmad_show_tooltip">
						<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/tooltip_arrow.png" />
		      	<?php echo $this->translate("Ad title linked to the ad destination URL.");?>
		      </div>
	      </div>
	      <div class="cmad_show_tooltip_wrapper">
		      <div class="cmaddis_image" id="ad_photo">
		        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/blankImage.png" />
		      </div>
	      	<div class="cmad_show_tooltip">
	      		<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/tooltip_arrow.png" />
	      		<?php echo $this->translate("Ad image linked to the ad destination URL.");?>
	      	</div>
		    </div>  
	      <div class="cmad_show_tooltip_wrapper">
	      	<div class="cmaddis_body cmad_show_tooltip_wrapper" id="ad_body">
	        	<?php echo $ad_body; ?>
	        </div>
	      	<div class="cmad_show_tooltip">
						<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/tooltip_arrow.png" />
	      		<?php echo $this->translate("Ad body text linked to the ad destination URL.");?>
	      	</div>
	      </div>
	      <div class="cmad_show_tooltip_wrapper">
					<div id="ad_like">
					</div>
	      	<div class="cmad_show_tooltip">
						<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/tooltip_arrow.png" />
	      		<?php echo $this->translate("Viewers will be able to like this ad and its content. They will also be able to see how many people like this ad, and which friends like this ad.");?>
	      	</div>
	      </div>
	    </div>
	  </div>
	</div>
</div>

