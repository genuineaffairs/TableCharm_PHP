<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.proximity.search.kilometer', 0)) {
    $locationDescription = "Choose the kilometers within which events will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
    $locationLableS = "Kilometer";
    $locationLable = "Kilometers";
} else {
    $locationDescription = "Choose the miles within which events will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
    $locationLableS = "Mile";
    $locationLable = "Miles";
}
?>
<?php if($this->searchRow && $this->searchRow->script_render_file):
 echo $this->render($this->searchRow->script_render_file);
endif;
?>
	<?php $filter = 'filter' ?>
	<?php if (isset($this->params['filter'])) : ?>
		<?php $filter = $this->params['filter']; ?>
	<?php endif; ?>
	
	<?php if ($this->widgetParams['search'] == 2 && ($this->params['module'] != 'messages')): ?>
		<div class="sm-ui-search-show-hide-link">
			<a href="javascript://" <?php if ((!isset($this->params[$this->searchField]) || (isset($this->params[$this->searchField]) && isset($this->params['quickSearch'])))): ?> style="display:none;" <?php endif; ?>  id="hide_advanced_search_<?php echo $this->identity ?>_<?php echo $filter ?>" class="hide_advanced_search" onclick="sm4.core.Module.showAdvancedSearch(1, '<?php echo $this->identity ?>_<?php echo $filter ?>');"><?php echo $this->translate("Hide Advanced Search"); ?></a>
			<a href="javascript://" <?php if (isset($this->params[$this->searchField]) && !isset($this->params['quickSearch'])): ?> style="display:none;" <?php endif; ?>  id="show_advanced_search_<?php echo $this->identity ?>_<?php echo $filter ?>" class="show_advanced_search" onclick="sm4.core.Module.showAdvancedSearch(0, '<?php echo $this->identity ?>_<?php echo $filter ?>');"><?php echo $this->translate("Advanced Search"); ?></a>
		</div>
	<?php endif; ?>
	
	<?php if ($this->widgetParams['search'] < 3) : ?>
		<div id="simple_search_<?php echo $this->identity ?>_<?php echo $filter ?>" <?php if (($this->widgetParams['search'] == 2) && (isset($this->params[$this->searchField])) && (!isset($this->params['quickSearch']))): ?> style="display:none;" <?php endif; ?>>         
			<form class="global_form_box filter_form" role="search" action="<?php echo $this->action; ?>" data-theme="a" data-ajax="true">
              <?php if(empty($this->locationFieldName)):?>
				<input placeholder="<?php echo $this->translate("Search"); ?>"  id="<?php echo $this->searchField; ?>" name="<?php echo $this->searchField; ?>" value="<?php echo $this->search; ?>"  data-mini="true"/>
            <?php else: ?>
        <!--SEARCH FOR EVENT - CATEGORY, LOCATION, MILES FIELD-->
        <?php if($this->pageName == 'siteevent_index_home'):?>
        <?php $categories = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategorieshasevents(0, 'category_id', null, array(), array('category_id', 'category_name'));?>
        <?php $categories_prepared = array();
          if (count($categories) != 0):?>
         <select name="category_id" id="category_id" label="Category" >
              <option id="0" value="0"><?php echo $this->translate("All Categories") ?></option>
              <?php $categories_prepared[0] = "";          
              foreach ($categories as $category): ?>
                  <option id="<?php echo $category->category_id ?>" value="<?php echo $category->category_id ?>" <?php if ($this->category_id == $category->category_id) : echo 'selected';
                  endif;?>><?php echo $category->category_name ?>
                  </option>
              <?php endforeach; ?>
         </select>
         <?php endif;?>
        <input placeholder="<?php echo $this->translate("What are you looking for?"); ?>"  id="<?php echo $this->searchField; ?>" name="<?php echo $this->searchField; ?>" value="<?php echo $this->search; ?>"  data-mini="true"/>              
        <input type="text" placeholder="<?php echo $this->translate("Where are you looking for it?"); ?>" role="search" class="ui-input-text" data-type="search" name="location"  id="location" data-mini="true"/>
         <select name="locationmiles" id="locationmiles" label="Within Miles" >
            <option id="0" value="0"><?php echo $this->translate(" ") ?></option>
            <option id="1" value="1" <?php if ($this->locationmiles == 1) echo "selected"; ?>><?php echo $this->translate("1 %s",$locationLableS) ?></option>
            <option id="2" value="2" <?php if ($this->locationmiles == 2) echo "selected"; ?>><?php echo $this->translate("2 %s",$locationLable) ?></option>
            <option id="5" value="5" <?php if ($this->locationmiles == 5) echo "selected"; ?>><?php echo $this->translate("5 %s",$locationLable) ?></option>
            <option id="10" value="10" <?php if ($this->locationmiles == 10) echo "selected"; ?>><?php echo $this->translate("10 %s",$locationLable) ?></option>
            <option id="20" value="20" <?php if ($this->locationmiles == 20) echo "selected"; ?>><?php echo $this->translate("20 %s",$locationLable) ?></option>
            <option id="50" value="50" <?php if ($this->locationmiles == 50) echo "selected"; ?>><?php echo $this->translate("50 %s",$locationLable) ?></option>
            <option id="100" value="100" <?php if ($this->locationmiles == 100) echo "selected"; ?>><?php echo $this->translate("100 %s",$locationLable) ?></option>
            <option id="250" value="250" <?php if ($this->locationmiles == 250) echo "selected"; ?>><?php echo $this->translate("250 %s",$locationLable) ?></option>
            <option id="500" value="500" <?php if ($this->locationmiles == 500) echo "selected"; ?>><?php echo $this->translate("500 %s",$locationLable) ?></option>
            <option id="750" value="750" <?php if ($this->locationmiles == 750) echo "selected"; ?>><?php echo $this->translate("750 %s",$locationLable) ?></option>
            <option id="1000" value="1000" <?php if ($this->locationmiles == 1000) echo "selected"; ?>><?php echo $this->translate("1000 %s",$locationLable) ?></option>  
          </select>
          <?php 
            $myLocationDetails = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
          
            if (isset($_GET['location'])) {
                Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($myLocationDetails);
            }
            ?>
        <!--END SEARCH FOR EVENT - CATEGORY, LOCATION, MILES FIELD-->
        <?php else: ?>
              <input placeholder="<?php echo $this->translate("What are you looking for?"); ?>"  id="<?php echo $this->searchField; ?>" name="<?php echo $this->searchField; ?>" value="<?php echo $this->search; ?>"  data-mini="true"/>              
              <input placeholder="<?php echo $this->translate("Where are you looking for it?"); ?>" data-type="location"  name="<?php echo $this->locationFieldName; ?>" value="<?php echo $this->location; ?>"  data-mini="true"/>
        <?php endif; ?>
              
              <button type="submit" data-theme="b" data-mini='true' class="fright"><?php echo $this->translate("Search")?></button>
            <?php endif; ?> 
                
        <?php if($this->pageName=='sitereview_review_browse' && isset ($this->params['listingtype_id'])&&$this->params['listingtype_id']>0):?>
        <input type="hidden" name="listingtype_id" value='<?php echo $this->params['listingtype_id']?>' />
        <?php elseif($this->pageName=='forum_index_index'): ?>
        <input type="hidden" name="type" value="forum_topic" />
        <?php endif; ?>
        
        
				<?php if ($this->widgetParams['search'] == 2): ?>
					<input type="hidden" name="quickSearch" value="true" />
				<?php endif; ?>
                  <?php if ($this->form && $this->form->view_selected): ?>
                    <?php echo $this->form->view_selected ?>
                  <?php endif; ?>
			</form>
		</div>
	<?php endif; ?>
	
	<?php if ($this->widgetParams['search'] > 1) : ?>
		<div class="sm-search-form-wrapper" id="advanced_search_<?php echo $this->identity ?>_<?php echo $filter ?>" <?php if ($this->widgetParams['search'] == 2 && (!isset($this->params[$this->searchField]) || (isset($this->params[$this->searchField]) && isset($this->params['quickSearch'])))): ?> style="display:none;" <?php endif; ?> >
			<?php if ($this->form): ?>
				<?php echo $this->form->setAttrib('data-ajax', 'true')->setAction($this->action)->render($this); ?>
			<?php endif ?>
		</div>
	<?php endif; ?>


<?php

echo $this->partial('_jsSwitch.tpl', 'fields', array(
))
?>

<script type="text/javascript">
  sm4.core.runonce.add(function() { 

    $(window).bind('onChangeFields', function() {
      var firstSep = $('li.browse-separator-wrapper');
      var lastSep;
      var nextEl = firstSep;
      var allHidden = true;
      do {
        nextEl = nextEl.next();
        if( nextEl.attr('class') == 'browse-separator-wrapper' ) {
          lastSep = nextEl;
          nextEl = false;
        } else {
          allHidden = allHidden && ( nextEl.css('display') == 'none' );
        }
      } while( nextEl );
      if( lastSep ) {
        lastSep.css('display', (allHidden ? 'none' : ''));
      }
    });
  });
  
    sm4.core.runonce.add(function() {
        if ($('#starttime-minute') && $('#endtime-minute')) {
            $('#starttime-minute').remove();
            $('#endtime-minute').remove();
        }
        if ($('#starttime-ampm') && $('#endtime-ampm')) {
            $('#starttime-ampm').remove();
            $('#endtime-ampm').remove();
        }
        if ($('#starttime-hour') && $('#endtime-hour')) {
            $('#starttime-hour').remove();
            $('#endtime-hour').remove();
        }
    });
</script>
<style type="text/css">
  /*.field_search_criteria li > span {
    display: none;
  }*/
</style>

<!--SEARCH FOR EVENT - CATEGORY, LOCATION, MILES FIELD-->
<?php if($this->pageName == 'siteevent_index_home'):?>
<script type="text/javascript">
//Location related work for siteevent
sm4.core.runonce.add(function(){  
  var autocomplete = new google.maps.places.Autocomplete($('#location').get(0));
    google.maps.event.addListener(autocomplete, 'place_changed', function() {
      var place = autocomplete.getPlace();
      if (!place.geometry) {
        return;
      }

  $.mobile.activePage.find('#latitude').val(place.geometry.location.lat());
  $.mobile.activePage.find('#longitude').val(place.geometry.location.lng());
  });   
  
  <?php if (isset($_GET['location']) && $_GET['location']) :?>
      $.mobile.activePage.find('#location').val('<?php echo $_GET['location']?>');
  <?php elseif(isset($myLocationDetails['location']) && $myLocationDetails['location']) :?> 
      $.mobile.activePage.find('#location').val('<?php echo $myLocationDetails['location']?>');
  <?php endif;?>
   
   <?php if (isset($_GET['locationmiles']) && $_GET['locationmiles']) :?>
      $.mobile.activePage.find('#locationmiles').val('<?php echo $_GET['locationmiles'] ?>');
   <?php elseif(isset($myLocationDetails['locationmiles']) && $myLocationDetails['locationmiles']) :?>  
      $.mobile.activePage.find('#locationmiles').val('<?php echo $myLocationDetails['locationmiles'] ?>');
   <?php endif;?>    
});
</script>
<?php endif;?>