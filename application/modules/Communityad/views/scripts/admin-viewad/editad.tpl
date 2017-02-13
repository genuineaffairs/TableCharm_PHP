<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editad.tpl  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<style type="text/css">
.cmaddis
{
	width:<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.block.width', 150); ?>px;
}
</style>
<?php
$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js');
$this->headLink()
    ->appendStylesheet($this->layout()->staticBaseUrl . 'externals/calendar/styles.css');
?>

<h2><?php echo $this->translate("Community Ads Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='communityad_admin_tabs'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>

  <script type="text/javascript">

    en4.core.runonce.add(function()
    {

      en4.core.runonce.add(function init()
      {
        monthList = [];
        myCal = new Calendar({ 'cads_start_date[date]': 'M d Y', 'cads_end_date[date]' : 'M d Y' }, {
          classes: ['event_calendar'],
          pad: 0,
          direction: 0
        });
      });


      cal_cads_start_date_onHideStart();
      cal_cads_end_date_onHideStart();



    } );

    var cal_cads_start_date_onHideStart = function(){
      // check end date and make it the same date if it's too
      cal_cads_end_date.calendars[0].start = new Date( $('cads_start_date-date').value );
      // redraw calendar
      cal_cads_end_date.navigate(cal_cads_end_date.calendars[0], 'm', 1);
      cal_cads_end_date.navigate(cal_cads_end_date.calendars[0], 'm', -1);
    }
    var cal_cads_end_date_onHideStart = function(){
      // check start date and make it the same date if it's too
      cal_cads_start_date.calendars[0].end = new Date( $('cads_end_date-date').value );
      // redraw calendar
      cal_cads_start_date.navigate(cal_cads_start_date.calendars[0], 'm', 1);
      cal_cads_start_date.navigate(cal_cads_start_date.calendars[0], 'm', -1);
    }
    var cal_cads_end_date_onShowStart = function(){
      if($('validation_cads_end_date-element')){
        document.getElementById("cads_end_date-element").removeChild($('validation_cads_end_date-element'));
      }
    }
    en4.core.runonce.add(function(){


    });
    window.addEvent('domready', function() {

      var endtime_element = document.getElementById("cads_end_date-wrapper");
      endtime_element.style.display = "none";
     
      var value=document.getElementById("end_settings-1").checked;
      if (value)
      {
        endtime_element.style.display = "block";
      }else{

        endtime_element.style.display = "none";
      }


    } );
   
    function endDateField ()
    {
      var endtime_element = document.getElementById("cads_end_date-wrapper");
      endtime_element.style.display = "none";
     
      var value=document.getElementById("end_settings-1").checked; 
      if (value)
      {
        endtime_element.style.display = "block";
      }else{

        endtime_element.style.display = "none";
      }
    }

   
  </script>

  <div>
    <a href='<?php echo $this->url(array('module' => 'communityad', 'controller' => 'viewad'), 'admin_default', true) ?>' class="back buttonlink cmad_icon_back"><?php echo $this->translate('Back to Manage Advertisements') ?></a>
  </div>
  <br />

  <div class="settings" style="float:left;margin-right:10px;">
  <?php echo $this->editform->render($this) ?>
</div>
<?php
  include_once APPLICATION_PATH . '/application/modules/Communityad/views/scripts/admin-viewad/adpreview.tpl';
?>