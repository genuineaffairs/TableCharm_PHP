<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
?>
<?php 
$resume = $this->resume;
$location = $this->location;

$google_map = Engine_Api::_()->getApi('map', 'radcodes')->factory('radcodes_map_'.$resume->getGuid());
$map_options = array(
  'height'=>460,
  'width'=>'100%',
  'google_map'=>$google_map,

  'init_open_infowindow'=>true,
);

?>

<script type="text/javascript">
window.addEvent("load", function(){
  $$('li.tab_layout_resume_profile_location').addEvent('click', function(){
      <?php echo $google_map->getInitializeJsFunctionName();?>();
  });
});
</script>

<?php echo $this->radcodes()->map()->item($resume, $map_options); ?>


<div class="resume_profile_fields">
  <div class="profile_fields">
    <h4><span><?php echo $this->translate('Full Address')?></span></h4>
    <ul>
      <li>
        <?php echo $location->formatted_address; ?>
      </li>
    </ul>
  
    <h4><span><?php echo $this->translate('Location Information')?></span></h4>
    <ul>
      <?php if ($location->street_address): ?>
        <li class="">
          <span><?php echo $this->translate("Street Address")?></span>
          <span><?php echo $location->street_address; ?></span>
        </li>
      <?php endif; ?>
      <?php if ($location->city): ?>
        <li class="">
          <span><?php echo $this->translate("City")?></span>
          <span><?php echo $this->htmlLink(array('route'=>'resume_general', 'action'=>'browse', 'location'=>$location->city.($location->state ? ', '.$location->state : '').($location->country ? ', '.$location->country : '')), $location->city); ?></span>
        </li>
      <?php endif; ?>
      <?php if ($location->state): ?>
        <li class="">
          <span><?php echo $this->translate("State")?></span>
          <span><?php echo $this->htmlLink(array('route'=>'resume_general', 'action'=>'browse', 'location'=>$location->state.($location->country ? ', '.$location->country : '')), $location->state); ?></span>
        </li>
      <?php endif; ?>
      <?php if ($location->zip): ?>
        <li class="">
          <span><?php echo $this->translate("Zip Code")?></span>
          <span><?php echo $this->htmlLink(array('route'=>'resume_general', 'action'=>'browse', 'location'=>$location->zip), $location->zip); ?></span>
        </li>
      <?php endif; ?>
      <?php if ($location->country): ?>
        <li class="">
          <span><?php echo $this->translate("Country")?></span>
          <span><?php echo $this->htmlLink(array('route'=>'resume_general', 'action'=>'browse', 'location'=>$location->getCountryName()), $location->getCountryName()); ?></span>
        </li>
      <?php endif; ?>
    </ul>
  </div>
</div>
