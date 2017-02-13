<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */
?>

<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl . 'externals/mootools/mootools-core-1.4.5-full-compat-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js?c=1'; ?>"></script>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>externals/autocompleter/Autocompleter.js"></script>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>externals/autocompleter/Autocompleter.Request.js"></script>
<script type="text/javascript">
  var url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'utility', 'action' => 'advertisement'), 'default', true) ?>';
  var processClick = window.processClick = function(adcampaign_id, ad_id) {
    (new Request.JSON({
      'format': 'json',
      'url' : url,
      'data' : {
        'format' : 'json',
        'adcampaign_id' : adcampaign_id,
        'ad_id' : ad_id
      }
    })).send();
  }
</script>

<style type="text/css">
    body {
      margin: 0;
      padding: 0;
    }
    img {
      width: 100%;
    }
</style>

<div onclick="javascript:processClick(<?php echo $this->campaign->adcampaign_id.", ".$this->ad->ad_id?>)">
  <?php echo $this->ad->html_code; ?>
</div>
