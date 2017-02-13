<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/css.php?request=/application/modules/Sitepagepoll/externals/styles/style_sitepagepoll.css')
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
<div class="sitepage_viewpages_head">
  <?php echo $this->htmlLink($this->sitepage->getHref(), $this->itemPhoto($this->sitepage, 'thumb.icon', '', array('align' => 'left'))) ?>
   <?php if(!empty($this->can_edit)):?>
    <div class="fright">
			<a href='<?php echo $this->url(array('page_id' => $this->sitepage->page_id), 'sitepage_edit', true) ?>' class='buttonlink icon_sitepages_dashboard'><?php echo $this->translate('Dashboard');?></a>
    </div>
	<?php endif;?>
  <h2>	
    <?php echo $this->sitepage->__toString() ?>	
    <?php echo $this->translate('&raquo; '); ?>
    <?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Polls')) ?>
  </h2>
</div>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adpollcreate', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)): ?>
  <div class="layout_right" id="communityad_pollcreate">
    <?php echo $this->content()->renderWidget("sitepage.page-ads", array('limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adpollcreate', 3), 'tab' => 'pollcreate', 'communityadid' => 'communityad_pollcreate', 'isajax' => 0)); ?>
  </div>
<?php endif; ?>
<div class="layout_middle">
  <div class='global_form'>
    <?php echo $this->form->render($this) ?>  
    <a href="javascript: void(0);" onclick="return addAnotherOption();" id="addOptionLink"><?php echo $this->translate("Add another option") ?></a>

    <script type="text/javascript">
      //<!--

      en4.core.runonce.add(function(){

        // check end date and make it the same date if it's too
        cal_end_time.calendars[0].start = new Date( $('end_time-date').value );
        // redraw calendar
        cal_end_time.navigate(cal_end_time.calendars[0], 'm', 1);
        cal_end_time.navigate(cal_end_time.calendars[0], 'm', -1);

      });

      var maxOptions = <?php echo $this->maxOptions ?>;
      var options = <?php echo Zend_Json::encode($this->options) ?>;
  
      window.addEvent('domready', function() {
        if( $type(options) == 'array' && options.length > 0 ) {
          options.each(function(label) {
            addAnotherOption(true, label);
          });
          if( options.length == 1 ) {
            addAnotherOption(true);
          }
        } else {
          // display two boxes to start with
          addAnotherOption(true);
          addAnotherOption(true);
      
        }
        if($('end_settings-1').checked) {
          document.getElementById("end_time-wrapper").style.display = "block";
        }
   
      });

      function addAnotherOption(dontFocus, label) {
        if (maxOptions && $$('input.sitepagepollOptionInput').length >= maxOptions) {
          return !alert(new String('<?php echo $this->string()->escapeJavascript($this->translate("A maximum of %s options are permitted.")) ?>').replace(/%s/, maxOptions));
          return false;
        }
    
        var optionElement = new Element('input', {
          'type': 'text',
          'name': 'optionsArray[]',
          'class': 'sitepagepollOptionInput',
          'value': label,
          'events': {
            'keydown': function(event){
              if (event.key == 'enter') {
                if (this.get('value').trim().length > 0) {
                  addAnotherOption();
                  return false;
                } else
                  return true;
              } else
                return true;
            } // end keypress event
          } // end events
        });
        var optionParent  = $('options').getParent();
        if (dontFocus)
          optionElement.inject(optionParent);
        else
          optionElement.inject(optionParent).focus();
    
        $('addOptionLink').inject(optionParent);

        if (maxOptions && $$('input.sitepagepollOptionInput').length >= maxOptions)
          $('addOptionLink').destroy();

        return false;
      }
      // -->
    </script>
  </div>
</div> 
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js');
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'externals/calendar/styles.css');
?>
<script type="text/javascript">
  var myCalStart = false;
  var myCalEnd = false;

 var endsettingss = 0;
  
  function updateTextFields(value) {
		if (value == 0)
    {
      if($("end_time-wrapper"))
      $("end_time-wrapper").style.display = "none";
    } else if (value == 1)
    { if($("end_time-wrapper"))
      $("end_time-wrapper").style.display = "block";
    }
  }

  en4.core.runonce.add(updateTextFields(endsettingss));

</script>