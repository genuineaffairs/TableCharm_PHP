<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view-ad.tpl  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (empty($this->is_ajax) && empty($this->ajax_filter)) : ?>
	<a id="classified_review_anchor" style="position:absolute;"></a>
	<style type="text/css">
	<?php
	$this->headScript()
					->appendFile($this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js');
	$this->headLink()
					->appendStylesheet($this->layout()->staticBaseUrl . 'externals/calendar/styles.css');
	?>
	.global_form div.form-element
	{
		min-width:0px;
	}
	</style>
	<script type="text/javascript">
var showMarkerInDate="<?php echo $this->showMarkerInDate ?>";
		en4.core.runonce.add(function()
		{    
			en4.core.runonce.add(function init()
			{
				monthList = [];
				myCal = new Calendar({ 'start_cal[date]': 'M d Y', 'end_cal[date]' : 'M d Y' }, {
					classes: ['event_calendar'],
					pad: 0,
					direction: 0
				});
			}); 
		});

			var cal_start_cal_onHideStart = function(){        
       if(showMarkerInDate == 0) return;
			// check end date and make it the same date if it's too
			cal_end_cal.calendars[0].start = new Date( $('start_cal-date').value );
			// redraw calendar
			cal_end_cal.navigate(cal_end_cal.calendars[0], 'm', 1);
			cal_end_cal.navigate(cal_end_cal.calendars[0], 'm', -1);
		}
		var cal_end_cal_onHideStart = function(){
       if(showMarkerInDate == 0) return;
			// check start date and make it the same date if it's too
			cal_start_cal.calendars[0].end = new Date( $('end_cal-date').value );
			// redraw calendar
			cal_start_cal.navigate(cal_start_cal.calendars[0], 'm', 1);
			cal_start_cal.navigate(cal_start_cal.calendars[0], 'm', -1);
		}

		en4.core.runonce.add(function(){

			cal_start_cal_onHideStart();
			cal_end_cal_onHideStart();
				if($('start_cal-minute'))
			$('start_cal-minute').style.display= 'none';	
			if($('start_cal-hour'))
			$('start_cal-hour').style.display= 'none';
			if($('end_cal-minute'))
			$('end_cal-minute').style.display= 'none';
			if($('end_cal-hour'))		
			$('end_cal-hour').style.display= 'none';
			if($('start_cal-ampm'))
			$('start_cal-ampm').style.display= 'none';
			if($('end_cal-ampm'))
			$('end_cal-ampm').style.display= 'none';
		});

	</script>
<?php endif; ?>

<script type="text/javascript">
 
var communityadPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
var ad_id = <?php echo sprintf('%d', $this->ad_id) ?>;
  function paginateCommunityadListing(page) {

    $('table_content').innerHTML = "<center><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='margin:10px 0;' /></center>";
    var url = '<?php echo $this->url(array('module' => 'communityad', 'controller' => 'statistics', 'action' => 'view-ad'), 'default', true) ?>';

    en4.core.request.send(new Request.HTML({
      'url' : url,
      'method' : 'post',
      'data' : {
        'format' : 'html',
        'ad_subject' : 'ad',
				'ad_id' : ad_id,
        'page' : page,
				'is_ajax' : '1',
        'start_cal':$("start_cal-date").value,
        'end_cal':$('end_cal-date').value
      }
    }), {
       'element' : $('table_content')
    });
	}
 
  window.addEvent('domready', function(){

  $$('.global_form').addEvent('submit', function(e) {
		  $('table_content').innerHTML = "<center><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='margin:10px 0;' /></center>";
		  //Prevents the default submit event from loading a new page.
		  e.stop();
		  this.set('send', {'format':'html',onComplete: function(response) { 
			  $('table_content').set('html', response);
		  }});
		  //Send the form.
		  this.send();
	  });
  });

 function filterDropdown(element) {
    var optn1 = document.createElement("OPTION");
		optn1.text = '<?php echo $this->translate("By Week") ?>';
		optn1.value = '<?php echo Zend_Date::WEEK; ?>';
    var optn2 = document.createElement("OPTION");
		optn2.text = '<?php echo $this->translate("By Month") ?>';
		optn2.value = '<?php echo Zend_Date::MONTH; ?>';

    switch(element.value) {
      case 'ww':
			removeOption('ww');
			removeOption('MM');
      break;

      case 'MM':
			addOption(optn1,'ww' );
			removeOption('MM');
      break;

      case 'y':
			addOption(optn1,'ww' );
			addOption(optn2,'MM' );
      break;
    }
  }

  function addOption(option,value )
  {
    var addoption = false;
		for (var i = ($('chunk').options.length-1); i >= 0; i--) {
			var val = $('chunk').options[ i ].value; 
			if (val == value) {
				addoption = true;
				break; 
			}
		}
		if(!addoption) {
			$('chunk').options.add(option);
		}
  }

   function removeOption(value) 
  {
    for (var i = ($('chunk').options.length-1); i >= 0; i--) 
    { 
      var val = $('chunk').options[ i ].value; 
      if (val == value) {
				$('chunk').options[i] = null;
				break; 
      }
    } 
  }
</script>

<?php if (empty($this->is_ajax) && empty($this->ajax_filter)) : ?>
<div class="cadcomp_page">
	<div class="headline">
	  <h2>
	    <?php echo $this->translate('Advertising'); ?>
	  </h2>
	 <?php if (count($this->navigation)): ?>
		<!-- NAVIGATION TABS START-->
  <div class = "tabs">
		 <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<!-- NAVIGATION TABS END-->
	  <?php endif; ?>
  </div>

	<div class="breadcrumb">
		<a href='<?php echo $this->url(array(), 'communityad_campaigns', true) ?>'><?php echo $this->translate('My Campaigns') ?></a> &nbsp; &raquo; &nbsp; <a href='<?php echo $this->url(array('adcampaign_id' => $this->communityads_array['campaign_id']), 'communityad_ads', true) ?>'><?php echo $this->translate("Campaign:"). "\t". ucfirst($this->communityads_array['name']) ?></a> &nbsp; &raquo; &nbsp; <b><?php echo ucfirst($this->communityads_array['cads_title']) ?></b>
	</div>
  <?php if(!empty($this->saved) && $this->saved== "saved"):?>
  <ul class="form-notices" style="clear:both;margin-bottom:0px;">
    <li style="margin:0px;">
    	<b style="text-transform:none;">
      	<?php echo $this->translate("COMMUNITYAD_CREATE_SUSSEC_HEADING"); ?>
      </b>
      <div style="text-transform:none;">
      	<?php echo $this->translate("COMMUNITYAD_CREATE_SUSSEC_MESSAGE"); ?>
      </div>
    </li>
  </ul>
  <?php endif; ?>

  <?php if(!empty($this->saved) && $this->saved== "edit"):?>
  <ul class="form-notices" style="clear:both;margin-bottom:0px;">
    <li style="margin:0px;">
    	<b style="text-transform:none;">
      	<?php echo $this->translate("COMMUNITYAD_EDIT_SUSSEC_HEADING"); ?>
      </b>
    </li>
  </ul>
  <?php endif; ?>

  <?php
if(empty($this->communityads_array['declined']) && $this->communityads_array['status'] !=4):
	$renewFlage=0;$renewFlageValue=0;
	switch ($this->communityads_array['price_model']):

		case "Pay/view":
			if ($this->communityads_array['limit_view'] != -1) {

				$renewFlageValue=$this->communityads_array['limit_view'];
				$renewFlage=1;
			}
		break;

		case "Pay/click":
			if ($this->communityads_array['limit_click'] != -1){

				$renewFlageValue=$this->communityads_array['limit_click'];
				$renewFlage=1;
			}
		break;
		case "Pay/period":
			if (!empty($this->communityads_array['expiry_date'])) {
					if ($this->communityads_array['expiry_date'] !== '2250-01-01'){
					$diff_days = round((strtotime($this->communityads_array['expiry_date']) - strtotime(date('Y-m-d'))) / 86400);
          if($diff_days <=0)
            $diff_days=0;
					$renewFlageValue=$diff_days;


					$renewFlage=1;
				}
			}
		break;
		endswitch;

		if(($this->communityads_array['payment_status'] !='active' && $this->communityads_array['payment_status'] !='pending') && $this->communityads_array['price'] != 0 &&  empty($this->communityads_array['approve_date'])):
				?> <div class="tip"><span>
		<?php echo $this->translate('You have not completed the payment for this ad. %1$sMake your payment%2$s for this ad.', '<a href="javascript:void(0);" title="'. $this->translate('Make your payment'). '" onclick="setSession('. $this->communityads_array['userad_id']. ')" >', '</a>') ?>
						</span></div>
		<?php
					endif;
			?>
		<?php if(!empty($this->communityads_array['renew']) && !empty($this->communityads_array['approve_date']) && !empty($renewFlage) && $renewFlageValue <= $this->communityads_array['renew_before'] && $renewFlageValue > 0):?>
			<?php if($this->communityads_array['price'] != 0):?> <div class="tip"><span>
			<?php echo $this->translate('Your ad is about to expire. %1$sRenew your ad%2$s now.', '<a href="javascript:void(0);"  title="'. $this->translate('Renew your ad'). '" onclick="setSession('. $this->communityads_array['userad_id']. ')" >', '</a>'); ?>
				</span></div>
			<?php else:?>
			<div class="tip"><span>
			<?php echo $this->translate('Your ad is about to expire. %1$sRenew your ad%2$s now.', '<a href="'. $this->url(array('id' =>  $this->communityads_array['userad_id']),  'communityade_renew', true). '" class = "smoothbox" title = "'. $this->translate('Renew your ad'). '" >', '</a>') ?> </span></div>
			<?php  endif; ?>                                       
		<?php  endif; ?>

		<?php if(!empty($this->communityads_array['renew']) && !empty($this->communityads_array['approve_date']) && !empty($renewFlage) && $renewFlageValue <= $this->communityads_array['renew_before'] && $renewFlageValue <= 0):?>
				<?php if($this->communityads_array['price'] !=0 ):?> <div class="tip"><span>
				<?php echo $this->translate('Your ad has expired. %1$sRenew your ad%2$s now.', '<a href="javascript:void(0);"  title="'. $this->translate('Renew your ad'). '" onclick="setSession('. $this->communityads_array['userad_id']. ')" >', '</a>'); ?>
					</span></div>
				<?php else:?>
				<div class="tip"><span>
			<?php echo $this->translate('Your ad has expired. %1$sRenew your ad%2$s now.', '<a href="'. $this->url(array('id' =>  $this->communityads_array['userad_id']),  'communityade_renew', true). '" class = "smoothbox" title = "'. $this->translate('Renew your ad'). '" >', '</a>') ?> </span></div>
			<?php  endif; ?>                                       
		<?php  endif; ?>
<?php  endif; ?>


	<div class="cadcomp_vad_header">
		<h3>
			<?php if( !empty($this->communityads_array['cads_title']) ){ echo "<span>". $this->translate('Ad:') . "</span> ". ucfirst($this->translate($this->communityads_array['cads_title'])); } ?>
      <?php if( !empty($this->communityads_array['ad_type']) ){ echo "<span style='margin-left:50px;'>". $this->translate('Ad Type:') . "</span> ". $this->translate($this->list->getAdTypeTitle($this->list->ad_type)); } ?>
		</h3>
		<div class="cmad_hr_link">
			<?php if($this->can_create):?>
			<a href='<?php echo $this->url(array(), 'communityad_listpackage', true) ?>' style="margin-left:5px;">
				<?php echo $this->translate('Create an Ad'); ?> &raquo;
			</a> <?php endif; ?>
		</div>
	</div>  
    
	<div class="cadva_detail_table">
	<?php $totalSpend= Engine_Api::_()->communityad()->paymentSpend(array('source_type'=>'userads','source_id'=> $this->communityads_array['userad_id'])); ?>
	<table>
		<thead>
			<tr>
				<th><?php echo $this->translate('Campaign Name') ?></th>
				<th><?php echo $this->translate('Package Name') ?></th>
				<th><?php echo $this->translate('Start Date') ?></th>
				<th><?php echo $this->translate('End Date') ?></th>
        <?php if(!empty($this->enableTarget)):?>
        <th><?php echo $this->translate('Targeting') ?></th>
        <?php endif; ?>
				<th><?php echo $this->translate("Remaining") ?></th>       
				<th><?php echo $this->translate('Status') ?></th>
				<th><?php echo $this->translate('Payment') ?></th>
				<th><?php echo $this->translate('Total Likes') ?></th>
        <?php if(!empty($totalSpend)):?>
				<th><?php echo $this->translate('Total Spend') ?></th>
        <?php endif; ?>
			</tr>	
		</thead>
		<tbody>
			<tr>
				<td>
					<?php echo $this->htmlLink(array('route' => 'communityad_ads', 'adcampaign_id' => $this->communityads_array['adcampaign_id'] ), ucfirst(Engine_Api::_()->communityad()->truncation($this->communityads_array['name'], 25)), array('title' => ucfirst($this->communityads_array['name']))) ?>
				</td>
				<td>
					<?php echo $this->htmlLink(
				      array('route' => 'default', 'module' => 'communityad', 'controller' => 'index', 'action' => 'packge-detail', 'id' => $this->communityads_array['package_id']),
				      ucfirst($this->translate(Engine_Api::_()->communityad()->truncation($this->communityads_array['package_name']))), array('class' => 'smoothbox', 'title' => ucfirst($this->communityads_array['package_name'])))
					?>
				</td>
				<td><?php 	$labelDate = new Zend_Date();	
           $startDate = strtotime($this->communityads_array['cads_start_date']);
             $oldTz = date_default_timezone_get();
              date_default_timezone_set($this->viewer()->timezone);   
							echo $this->locale()->toDate($labelDate->set($startDate), array('size' => 'long'));
              date_default_timezone_set($oldTz);
						?></td>
				<td><?php if(!empty($this->communityads_array['cads_end_date'])) {
						$labelDate = new Zend_Date();
             $endDate = strtotime($this->communityads_array['cads_end_date']);
             $oldTz = date_default_timezone_get();
              date_default_timezone_set($this->viewer()->timezone);   
							echo $this->locale()->toDate($labelDate->set($endDate), array('size' => 'long'));
              date_default_timezone_set($oldTz);
					
					  } else {
						  echo $this->translate('Never ends');
					  } ?>
				</td>
        <?php if(!empty($this->enableTarget)):?>
        <td>
           <?php if(empty($this->linkTarget)):?>
         <?php echo $this->translate("No") ?>
          <?php else:?>
         <?php	echo $this->htmlLink(
					               array('route' => 'communityad_targetdetails', 'id' => $this->communityads_array['userad_id']),
					                $this->translate(ucfirst('yes')), array('class' => 'smoothbox', 'title'=> $this->translate(ucfirst('View targeting parameters'))));
           ?>
          <?php endif; ?>
        </td>
        <?php endif; ?>
				<td><?php
					   if(!empty($this->communityads_array['approve_date'])):
					    switch ($this->communityads_array['price_model']):

					    case "Pay/view":

					      $limit_view = $this->communityads_array['limit_view'];
					      if ($limit_view == -1) {
									echo  $this->translate('UNLIMITED Views');
					      } else{
									$renewFlageValue = $limit_view;
									$renewFlage = 1;
									echo  $this->translate(array('%s View', '%s Views', $limit_view), $this->locale()->toNumber($limit_view));
					      }
					    break;

					    case "Pay/click":
					      $limit_click = $this->communityads_array['limit_click'];
					      if ($limit_click == -1){
									echo  $this->translate('UNLIMITED Clicks');
					      }else{
									$renewFlageValue = $limit_click;
									$renewFlage = 1;
									echo  $this->translate(array('%s Click', '%s Clicks', $limit_click), $this->locale()->toNumber($limit_click));
					      }
					    break;
					    case "Pay/period": 
					      if (!empty($this->communityads_array['expiry_date'])) {
									if ($this->communityads_array['expiry_date'] !== '2250-01-01'){
									$diff_days = round((strtotime($this->communityads_array['expiry_date']) - strtotime(date('Y-m-d'))) / 86400);
									if($diff_days<=0)
                    $diff_days=0;
                  $renewFlageValue=$diff_days;
									$renewFlage=1;
									echo  $this->translate(array('%s Day', '%s Days', $diff_days), $this->locale()->toNumber($diff_days));
								}
								else {
									echo  $this->translate('UNLIMITED Days');
								}
					      }else {
									echo  $this->translate('-');
					      }
					    break;
					    endswitch;
						else:
							echo  $this->translate('-');
						endif;
				      ?> </td>
				<td>
            <?php if($this->communityads_array['approved'] == 1 && $this->communityads_array['status'] <=3 && $this->communityads_array['declined'] !=1):?>
					<?php switch($this->communityads_array['status']) {
							    case 0:
							    echo $this->translate("Approval Pending");
							    break;
					      
							    case 1:
							    echo $this->translate("Running");
							    break;
							
							    case 2:
							    echo $this->translate("Paused");
							    break;
					      
							    case 3:
							    echo $this->translate("Completed");
							    break;
						      
							    case 4:
							    echo "<span style='color:red;'>".  $this->translate('Deleted'). "</span>";
							    break;

							    case 5:
							    echo $this->translate("Declined");
							    break;
						}?>
             <?php elseif($this->communityads_array['status']==4): ?>
                <span style='color:red;'><?php echo $this->translate("Deleted"); ?></span>
           <?php elseif($this->communityads_array['status']==3): ?>
                <?php echo $this->translate("Completed"); ?>
           <?php elseif($this->communityads_array['declined']==1): ?>
                <span style='color:red;'><?php echo $this->translate("Declined"); ?></span>
                <?php else: ?>
               <?php if(empty($this->communityads_array['approve_date'])): ?>
                 <?php echo $this->translate("Approval Pending"); ?>
                <?php else:?>
                <?php echo $this->translate("Dis-Approved"); ?>
                <?php endif;?>
               <?php endif; ?>
					
                                </td>
				<td align="center" class="admin_table_centered"> 	   
					<?php if($this->communityads_array['payment_status'] == 'active' && $this->communityads_array['price']!=0) : ?>
						<?php echo $this->translate('Yes') ?>
					<?php elseif($this->communityads_array['payment_status'] == 'initial' && $this->communityads_array['price']!=0): ?>
						<?php echo $this->translate('No') ?>
					<?php elseif($this->communityads_array['payment_status'] == 'pending' && $this->communityads_array['price']!=0): ?>
						<?php echo $this->translate('Pending') ?>
					<?php elseif($this->communityads_array['payment_status'] == 'overdue' && $this->communityads_array['price']!=0): ?>
						<?php echo $this->translate('Overdue') ?>
					<?php elseif($this->communityads_array['payment_status']  == 'refunded' && $this->communityads_array['price']!=0): ?>
						<?php echo $this->translate('Refunded') ?>
					<?php elseif($this->communityads_array['payment_status'] == 'cancelled' && $this->communityads_array['price']!=0): ?>
						<?php echo $this->translate('Cancelled') ?>
					<?php elseif($this->communityads_array['payment_status']  == 'expired' && $this->communityads_array['price']!=0): ?>
						<?php echo $this->translate('Expired') ?>
					<?php elseif( $this->communityads_array['price']==0): ?>
						<?php  echo $this->translate('FREE') ?>
					<?php endif; ?>
				</td>
				<?php if(!empty($this->communityads_array['resource_type'])) : ?>
					<td>
						<?php 
							if(!empty($this->communityads_array['count_like'])) {
								echo $this->communityads_array['count_like'];
							}
							else echo "0"; 
						?> 
					</td>
				<?php else :?>
					<td title="<?php echo $this->translate('Not Applicable') ?>">
						<?php 
							echo $this->translate("NA"); 
						?> 
					</td>
				<?php endif; ?>
          <?php if(!empty($totalSpend)):?>
				<td>
					<?php echo $this->locale()->toCurrency( $totalSpend, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')); ?>
				</td>
        <?php endif; ?>
			</tr>	
		</tbody>
	</table>
</div>

<div class="cadva_dsform">
	<?php echo $this->filter_form->render($this) ?>
	<div id="table_content" class="cadva_table">
<?php endif; ?>
		<table border='0'>
			<thead>
			  <tr>
			    <th><?php echo $this->translate("Date") ?></th>
			    <th><?php echo $this->translate("Views") ?></th>
			    <th><?php echo $this->translate("Clicks") ?></th>
			    <th><?php echo $this->translate("CTR (%)") ?></th>
		      </tr>
		    </thead>
				<tbody>
					<?php if ($this->total_count > 0) { ?>
					<?php foreach ($this->paginator as $item) { ?>
				  	<tr>
							<td><?php 
										$response_time = strtotime($item->response_date);
										$labelDate = new Zend_Date();
										$labelDate->set($response_time);
										$date_value = $this->locale()->toDate($labelDate, array('size' => 'long')); 
										echo $date_value; 
									?></td>
							<td>
								<?php 
									if(!empty($item->views)) {
										echo number_format($item->views);
									}
									else echo "0";
								?> 
							</td>
							<td>
								<?php  
									if(!empty($item->clicks)) {
										echo number_format($item->clicks);
									}
					      else echo "0";
					    	?> 
					   	</td>
							<td>
								<?php 
					      if($item->views != 0) {
									echo number_format(round(($item->clicks/$item->views)*100, 4), 4);
					      }
					      else echo number_format("0", 4); 
					    	?> 
					    </td>
						</tr>
				  <?php } ?>
		  	</tbody>
			</table>
  		<br/>
 <?php if ($this->paginator->count() > 1): ?>
   <div>
    <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
      <div id="user_group_members_previous" class="paginator_previous">
      <?php
                      echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                              'onclick' => 'paginateCommunityadListing(communityadPage - 1)',
                              'class' => 'buttonlink icon_previous'
                      )); ?>
			</div>
    <?php endif; ?>
    <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
       <div id="user_group_members_next" class="paginator_next">
      <?php  echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                                'onclick' => 'paginateCommunityadListing(communityadPage + 1)',
                                'class' => 'buttonlink_right icon_next'
                        ));?>
				</div>
    <?php endif; ?>
  </div>
<?php endif; ?>
<?php
    } else {
?>
     <?php if ($this->viewer()->getIdentity())?>
    <tr>
	    <td><?php echo $this->translate('Lifetime') ?></td>
	    <td>0</td>
	    <td>0</td>
	    <td>0.0000</td>
    </tr>
  </tbody>
</table>
<?php } ?>

<?php if (empty($this->is_ajax) && empty($this->ajax_filter)) : ?>
	</div>
</div>



<?php 
if( empty($this->communityads_array['story_type']) ) {

?>
    <!-- Start Preview Code -->
    <div class="cmaddis_preview_wrapper">
	    <b><?php echo $this->translate('Preview') ?></b>
	    <div class="cadcp_preview">
	    	<div class="cmaddis">
					<div class="cmad_addis">
						<!--tital code start here for both-->
						<div class="cmaddis_title">
							<?php // Title if has existence on site then "_blank" not work else work.

								if ( !empty($this->communityads_array['resource_type']) && !empty($this->communityads_array['resource_id']) ) {
									$set_target = '';
								} else {
									$set_target = 'target="_blank"';
								}
								echo '<a href="'. $this->communityads_array['cads_url']  .'" '.$set_target.'>' . ucfirst($this->communityads_array['cads_title']) . "</a>";
							?>
						</div>
						<?php
							if ( !empty($this->communityads_array['resource_type']) && !empty($this->communityads_array['resource_id']) ) { ?>
								<div class="cmaddis_adinfo">
								<?php
                  $getResourceType = $this->communityads_array['resource_type'];
									$resource_url = Engine_Api::_()->communityad()->resourceUrl( $getResourceType, $this->communityads_array['resource_id'] );
									if( !empty($resource_url['status']) ) {
										echo '<a href="'. $resource_url['link']  .'" >' . $resource_url['title'] . "</a>";
									}else {
										echo $resource_url['title'];
									} ?>
								</div>
							<?php } else if( !empty($this->hideCustomUrl) ) {
								$ad_url = Engine_Api::_()->communityad()->adSubTitle( $this->communityads_array['cads_url'] );
								echo '<div class="cmaddis_adinfo"><a title="'. $this->communityads_array['cads_url'] .'"href="'. $this->communityads_array['cads_url']  .'" target="_blank" >' . $this->translate(Engine_Api::_()->communityad()->truncation($ad_url, 25)) . "</a></div>";
							}
						?>
						<!--image code start here for both-->
						<?php
						// Display image if 'Advertisment' is the content of the site then show the content image.
							$community_ad_image = $this->itemPhoto($this->list, '', '');
						?>
						<div class="cmaddis_image">
						<?php 
							echo '<a href="'. $this->communityads_array['cads_url']  .'" '.$set_target.'>' .  $community_ad_image . "</a>";
						?>
						</div>
						<!--image code end here for both-->
						
						<!--description code start here for both-->
						<div class="cmaddis_body">	
							<?php
								echo '<a href="'. $this->communityads_array['cads_url']  .'" '.$set_target.'>' .  $this->communityads_array['cads_body'] . "</a>"; 
							?>
						</div>
						<!-- Like option only show in the case of if existence on the site -->
						<?php if ( !empty($this->communityads_array['resource_type']) && !empty($this->communityads_array['resource_id']) ) { ?>
							<div class="cmad_show_tooltip_wrapper">
								<?php echo '<div class="cmaddis_cont"><a href="javascript:void(0);" class="cmad_like_button"><i class="like_thumbup_icon"></i><span>'. $this->translate("Like"). '</span></a><span class="cmad_like_un">&nbsp;&middot;&nbsp;<a href="javascript:void(0);">' . $this->viewer->getTitle() . '</a>' . $this->translate(' likes this.') . '</span></div>'; ?>
								<div class="cmad_show_tooltip">
									<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/tooltip_arrow.png" />
									<?php echo $this->translate("Viewers will be able to like this ad and its content. They will also be able to see how many people like this ad, and which friends like this ad.");?>
								</div>
							</div>
						<?php } ?>

						<!--description code end here for both-->
					</div>
				</div>		
	    </div>
	    <div class="cadva_buttons">
			<?php if($this->communityads_array['status'] != 4 && $this->communityads_array['declined'] != 1 ) {
         if($this->can_edit):
				echo $this->htmlLink(
					array('route' => 'communityad_edit', 'id' => $this->communityads_array['userad_id']),
					$this->translate('Edit Ad')
				);
      endif;
			      }
			?>
			<br />
      <?php if($this->can_create && $this->viewer_id == $this->communityads_array['owner_id']):?>
			<?php   echo $this->htmlLink(
					array('route' => 'communityad_copyad', 'copy'=>'copy', 'id' => $this->communityads_array['userad_id']),
					$this->translate('Create a Similar Ad')
				); 
			?> <?php endif; ?>
	    </div>
	   </div> 
    <!-- End Preview Code -->
<?php }else {

	if( $this->communityads_array['story_type'] == 1 ) {
	
	$mainObj = Engine_Api::_()->user()->getViewer();
	$getTitleLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('story.char.title', 35);
	$rootTitleLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.title', 25);
	$getModInfo = Engine_Api::_()->getDbTable('modules', 'communityad')->getModuleType($this->communityads_array['resource_type']);
	$contentObj = Engine_Api::_()->getItem($getModInfo['table_name'], $this->communityads_array['resource_id']);
	?>
	    <!-- Start Preview Code -->
    <div class="cmaddis_preview_wrapper">
	    <b><?php echo $this->translate('Preview') ?></b>
	    <div class="cadcp_preview">
				<div class="cmad_sdab">
					<div class="cmad_sdab_sp">
						<?php echo $this->htmlLink($mainObj->getHref(), $this->itemPhoto($mainObj, 'thumb.icon')) ?>
					</div>
					
					<div class="cmad_sdab_body">
						<div class="cmad_sdab_title" style="clear:none;">
							<?php 
							  $getMainStrTitle = $this->translate('<b>%s</b> likes %s.', $this->htmlLink($mainObj->getHref(), Engine_Api::_()->communityad()->truncation($mainObj->getTitle(), $rootTitleLimit), array('title' => $mainObj->getTitle())), $this->htmlLink($contentObj->getHref(), Engine_Api::_()->communityad()->truncation($contentObj->getTitle(), $getTitleLimit), array('title' => $contentObj->getTitle())));
							  //$getMainStrTitle = str_replace(' ', '&nbsp;', $getMainStrTitle);  
							  echo $getMainStrTitle;
							?>
						</div>
						<div class="cmad_sdab_cont">
							<div class="cmad_sdab_cont_img">
								<?php 
								  $getResourceImage = $this->htmlLink($contentObj->getHref(), $this->itemPhoto($contentObj, 'thumb.profile')) ;
								  if( !empty($this->communityads_array['resource_type']) && (($this->communityads_array['resource_type'] == 'blog') || ($this->communityads_array['resource_type'] == 'music')) ){
								    $getResourceImage = $this->htmlLink($contentObj->getHref(), $this->itemPhoto($contentObj, 'thumb.icon')) ;
								  }
								  echo $getResourceImage;
								?>
							</div>
							<div class="cmad_sdab_cont_body" style="clear:none;">
								<?php echo $this->htmlLink($contentObj->getHref(), Engine_Api::_()->communityad()->truncation($contentObj->getTitle(), $getTitleLimit), array('title' => $contentObj->getTitle())) ?>
							</div>
						</div>
			
 						<?php if ( !empty($this->communityads_array['resource_type']) && !empty($this->communityads_array['resource_id']) ) { ?>
							<div class="cmad_show_tooltip_wrapper">
								<?php echo '<div class="cmaddis_cont" style="display:block;"><a href="javascript:void(0);" class="cmad_like_button" style="display:block;"><i class="like_thumbup_icon"></i><span>'. $this->translate("Like This %s", $getModInfo['module_title']). '</span></a></div>'; ?>
								<div class="cmad_show_tooltip">
									<img src="<?php echo  $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/tooltip_arrow.png" />
									<?php echo $this->translate("_sponsored_like_tooltip");?>
								</div>
							</div>
						<?php } ?>
						</div>
					</div>
				</div>

				<div class="cadva_buttons">
					<?php if($this->communityads_array['status'] != 4 && $this->communityads_array['declined'] != 1 ) {
		         if($this->can_edit):
						echo $this->htmlLink(
							array('route' => 'communityad_edit', 'id' => $this->communityads_array['userad_id']),
							$this->translate('Edit Sponsored Story')
						);
		      endif;
			      }
					?>
				</div>
			</div>
						<?php
	
	}

} ?>





	<div style="clear:both;height:15px;"></div>
	<div class="cadmc_statistics">
		<div>
	    <p>
	      <?php echo $this->translate("Use the below filter to observe various metrics of your ad over different time periods.") ?>
          <span style="font-weight: normal;">
           <?php echo $this->translate(array('(for last %s year)', '(for last %s years)', Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.statistics.limit',3)), $this->locale()->toNumber(Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.statistics.limit',3))) ?>
        </span>
	    </p>
        <br>
			<div class="cadmc_statistics_search">
				<?php echo $this->filterForm->render($this) ?>
			</div>
			
		  <div class="cadmc_statistics_nav">
		    <a id="admin_stats_offset_previous"  class='buttonlink icon_previous' onclick="processStatisticsPage(-1);" href="javascript:void(0);" style="float:left;"><?php echo $this->translate("Previous") ?></a>
		    <a id="admin_stats_offset_next" class='buttonlink_right icon_next' onclick="processStatisticsPage(1);" href="javascript:void(0);" style="display:none;float:right;"><?php echo $this->translate("Next") ?></a>
		  </div>

  
		  
		  <script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>externals/swfobject/swfobject.js"></script>
		  <script type="text/javascript">
		    var prev = '<?php echo $this->prev_link ?>';
		    var currentArgs = {};
		    var processStatisticsFilter = function(formElement) {
		      var vals = formElement.toQueryString().parseQueryString();
		      vals.offset = 0;
		      buildStatisticsSwiff(vals);
		      return false;
		    }
		    var processStatisticsPage = function(count) {
		      var args = $merge(currentArgs);
		      args.offset += count;
		      buildStatisticsSwiff(args);
		    }
		    var buildStatisticsSwiff = function(args) {

		      var earliest_date = '<?php echo $this->earliest_ad_date ?>';
		      var startObject = '<?php echo $this->startObject ?>';

		      if(args.offset < 0) {
						switch(args.period) {
							case 'ww':
							startObject = startObject - (Math.abs(args.offset)*7*86400);
							break;
							
							case 'MM':
							startObject = startObject - (Math.abs(args.offset)*31*86400);
							break;

							case 'y':
							startObject = startObject - (Math.abs(args.offset)*366*86400);
							break;
						}
						$('admin_stats_offset_previous').setStyle('display', (startObject > earliest_date ? '' : 'none'));
		      }
		      else if(args.offset > 0) {
						$('admin_stats_offset_previous').setStyle('display', 'block');
		      }
		      else if(args.offset == 0) {
						switch(args.period) {
							case 'ww':
							if (typeof args.prev_link != 'undefined') {
									$('admin_stats_offset_previous').setStyle('display', (args.prev_link >= 1 ? '' : 'none')); 
							}
							else {
									$('admin_stats_offset_previous').setStyle('display', (startObject > earliest_date ? '' : 'none'));
							}
							break;
							
							case 'MM':
								startObject = '<?php echo mktime(0, 0, 0, date('m', $this->startObject), 1, date('Y', $this->startObject)) ?>';
								$('admin_stats_offset_previous').setStyle('display', (startObject > earliest_date ? '' : 'none'));
								break;

							case 'y':
								startObject = '<?php echo mktime(0, 0, 0, 1, 1, date('Y', $this->startObject)) ?>';
								$('admin_stats_offset_previous').setStyle('display', (startObject > earliest_date ? '' : 'none'));
								break;
						}
		      }
		  
		      currentArgs = args;

		      $('admin_stats_offset_next').setStyle('display', (args.offset < 0 ? '' : 'none'));

		      var url = new URI('<?php echo $this->url(array('action' => 'chart-data')) ?>');
		      url.setData(args);
		      
		      //$('my_chart').empty();
		      swfobject.embedSWF(
						"<?php echo $this->baseUrl() ?>/externals/open-flash-chart/open-flash-chart.swf",
						"my_chart",
						"850",
						"400",
						"9.0.0",
						"expressInstall.swf",
						{
							"data-file" : escape(url.toString()),
							'id' : 'mooo'
						}
		      );
		    }	    

		    window.addEvent('load', function() {
		      buildStatisticsSwiff({
					'type' : 'all',
					'mode' : 'normal',
					'chunk' : 'dd',
					'period' : 'ww',
					'start' : 0,
					'offset' : 0,
					'ad_subject' : 'ad',
					'prev_link' : prev
					});
		    });
		  </script>

				<div id="my_chart">
					<center><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='margin:10px 0;' /></center>
				</div>
			</div>	
			</div>
		</div>	
		<?php endif; ?>
		<div>
		  <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array('module' => 'communityad', 'controller' => 'index', 'action' => 'set-session'), 'default', true) ?>">
		    <input type="hidden" name="ad_ids_session" id="ad_ids_session">
		  </form>
		</div>

		<script type="text/javascript">
		function setSession(id){

		    document.getElementById("ad_ids_session").value=id;
		    document.getElementById("setSession_form").submit();
		}
		</script>