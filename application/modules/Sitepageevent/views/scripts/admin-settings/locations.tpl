<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: locations.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?><?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/pluginLink.tpl'; ?>
<h2><?php echo $this->translate("Directory / Pages - Events Extension") ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>
<div class="seaocore_settings_form">
    <div class="tip">
  <span>  
      <?php $eventCount = count($this->row);
      echo 'To enable Proximity and Geo-location search for Page Events on your site, you need to sync the locations of all the page events on your site with Google Places. Thus,<a href="' . $this->url(array('action' => 'sink-location')) . '" class="smoothbox"> click here</a> to sync '.$eventCount.' page events on your site with Google Places. This search is similar to the location based searching and browsing of Directory Items / Pages from “<a href="http://www.socialengineaddons.com/socialengine-directory-pages-plugin" target="_blank">Directory / Pages Plugin</a>”.';
      
      //echo $this->translate('Your have %s page events in your database associated from the location but not sink from the proximity searchfurther there plugins. Please sink to such location with proximity search., <a href="' . $this->url(array('action' => 'sink-location')) . '" class="smoothbox">Click here to sink the location</a>.', $eventCount); ?>
      </span>
</div>
</div>