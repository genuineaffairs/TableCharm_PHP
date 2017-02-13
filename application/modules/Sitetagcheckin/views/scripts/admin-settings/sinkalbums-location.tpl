<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sinkalbum-location.tpl 2012-01-12 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">

  function getLoading() {
    document.getElementById('global_form').style.display = 'none';
    document.getElementById('add_progress_bar').style.display = 'block';
  }

</script>
<?php if( empty($this->error) ): ?>
  <div id="add_progress_bar" style="display:none;margin:30px 10px 10px;">
    <div class="settings"><form style="width:450px;"><div style="padding:15px;"><div style="font-weight:bold;">Please do not refresh or close this page, until the process is running.<br /><br /><center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/progress-bar.gif" alt="Loading.." /></center></div></div></form></div>
  </div>
<?php endif; ?>
<?php if( empty($this->error) ): ?>
<div id="global_form">
<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate('Sync Album Locations with Google Places.'); ?></h3>
    <p><?php echo $this->translate("Google processes only 2000 requests from an IP address in a day. Thus, you will only be able to sync upto 2000 Albums in a day. Make sure that you sync rest of the Albums later by visiting this setting again."); ?></p>
    <br />
    
    <?php $albumCount = $this->row;
    if (empty($albumCount)) : ?>
			<div class="tip">
				<span> 
					<?php echo $this->translate('There are currently no Albums on your site to be synced with Google Places.'); ?>
				</span>
			</div>			
			<button type='submit' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('Close'); ?></button>			
    <?php endif; ?>
    
    <?php if (!empty($albumCount)) : ?>
			<p>
				<input type="hidden" name="confirm" value=""/>
				<button type='submit' onClick="getLoading()"><?php echo $this->translate('Sync Album Locations'); ?></button>
				or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
			</p>
			<?php endif; ?>
  </div>
</form>
</div>
<?php else: ?>
  <script type="text/javascript">
    window.addEvent('domready', function() {
    parent.window.location.reload();
      parent.Smoothbox.close ();
    });
  </script>
<?php endif; ?>
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>