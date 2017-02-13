<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: export-webpage.tpl  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<style type="text/css">
	table.admin_table thead tr th,
	table.admin_table tbody tr td
{
	text-align:center;
}
</style>
	<div class="headline">
	  <h2><?php echo $this->translate("Community Ads Plugin") ?></h2>
	  <?php if (count($this->navigation)) { ?>
	      <div class='communityad_admin_tabs'>
	    <?php
	      // Render the menu
	      //->setUlClass()
	      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
	    ?>
	    </div>
	  <?php } ?>
	</div>
	
		<h3 style="margin-bottom:5px;float:left;"><?php echo $this->translate('View Advertising Report') ?></h3>
		<div style="float:right;margin-top:3px;">
			<a href='<?php echo $this->url(array('module' => 'communityad', 'controller' => 'statistics', 'action' => 'export-report'), 'admin_default', true) ?>' class="buttonlink cmad_icon_back"><?php echo $this->translate('Generate Another Report') ?></a>
		</div>	
	

	<div class="cmad_admin_vr_table">
	<table>
		<thead>
			<tr>
				<th><?php echo $this->translate('Summarize By') ?></th>
				<th><?php echo $this->translate('Time Summary') ?></th>
				<th><?php echo $this->translate('Duration') ?></th>
			</tr>	
		</thead>
		<tbody>
			<tr>
				<td>
					<?php if($this->values['ad_subject'] == 'ad') { 
						echo $this->translate('Ad');
					      }
					      elseif($this->values['ad_subject'] == 'campaign') {
						echo $this->translate('Campaign');
					      }
					?>
				</td>
				<td>
					<?php echo $this->translate($this->values['time_summary']); ?>
				</td>
				<td>
					<?php
					    $startTime = $endTime = date('Y-m-d');
					    if(!empty($this->values['time_summary'])) {
					      if($this->values['time_summary'] == 'Monthly') {
						$startTime = date('M d, Y', mktime(0, 0, 0, $this->values['month_start'], date('d'), $this->values['year_start']));//echo $startTime;die;
						$endTime = date('M d, Y', mktime(0, 0, 0, $this->values['month_end'], date('d'), $this->values['year_end']));//echo $endTime;die;
					      }
					      else {
									if (!empty($this->values['start_daily_time'])) {
										$start = $this->values['start_daily_time'];
									}
									if (!empty($this->values['start_daily_time'])) {
									$end = $this->values['end_daily_time'];
									}
									$startTime = date('M d, Y', $start);
									$endTime = date('M d, Y', $end);
					      }
					    }
					    echo $startTime. $this->translate(" to "). $endTime;
					?>
				</td>
			</tr>
		</tbody>
	</table>
	</div>

<?php if(count($this->rawdata)) : ?>
 <?php 
      switch($this->values['time_summary']) {
	
				case 'Monthly':
				$date_label = 'Month';
				break;

				case 'Daily':
				$date_label = 'Date';
				break;
      }
  ?>
	
  	<div id='stat_table' style="clear:both;">
			<div class="cadva_total_reports">
			  <div><?php echo $this->translate(array("<span> %s </span> View", "<span> %s </span> Views", $this->totalViews), $this->locale()->toNumber($this->totalViews)) ?></div>
			  <div><?php echo $this->translate(array("<span> %s </span> Click", "<span> %s </span> Clicks", $this->totalClicks), $this->locale()->toNumber($this->totalClicks)) ?></div>
			  <div><?php echo  "<span>" . number_format($this->totalCtr, 4) . "%" . "</span> " . $this->translate("CTR")?></div>
			</div>
	    <?php if($this->values['ad_subject'] == 'ad') :?>
	      <table class="admin_table" style="width:100%;">
	      	<thead>
						<tr>
							<th><?php echo $this->translate($date_label); ?></th>
						  <th><?php echo $this->translate("Ad Id") ?></th>
						  <th style="text-align:left;"><?php echo $this->translate("Advertiser") ?></th>
						  <th style="text-align:left;"><?php echo $this->translate("Campaign Name") ?></th>
						  <th style="text-align:left;"><?php echo $this->translate("Ad Title") ?></th>
						  <th><?php echo $this->translate("Views") ?></th>
						  <th><?php echo $this->translate("Clicks") ?></th>
						  <th><?php echo $this->translate("CTR (%)") ?></th>
						</tr>
					</thead>
					<tbody> 	
					  <?php foreach($this->rawdata as $data) : ?>
				     <?php 
				      $response_date = explode(' ', $data->response_date);
				      $date_array = explode('-', $response_date[0]);
				      switch($this->values['time_summary']) {
								case 'Monthly':
								$date_value = date('F, Y', mktime(0, 0, 0, $date_array[1], date('d'), date('Y')));
								break;
						
								case 'Daily':
								$response_time = strtotime($data->response_date);
								$labelDate = new Zend_Date();
								$labelDate->set($response_time);
								$date_value = $this->locale()->toDate($labelDate, array('size' => 'long')); 
								break;
							}
						  ?>
					    <tr>
					      <td><?php echo $date_value; ?></td>
					      <td><?php echo $data->userad_id ?></td>
					      <td style="text-align:left;" title="<?php echo $data->getOwner()->getTitle() ?>">
							<?php echo Engine_Api::_()->communityad()->truncation($data->getOwner()->getTitle(), 25); ?>
					      </td>
					      <td  style="text-align:left;" title="<?php echo ucfirst($data->name) ?>">
							<?php echo ucfirst(Engine_Api::_()->communityad()->truncation($data->name, 40)) ?>
					      </td>
					      <td style="text-align:left;">
							<?php echo $this->htmlLink(array('route' => 'communityad_userad', 'ad_id' => $data->userad_id),ucfirst($this->translate(Engine_Api::_()->communityad()->truncation($data->cads_title, 30))),
						array( 'title' => ucfirst($data->cads_title), 'target' => '_blank')) ?>
					      </td>
					      <td title='<?php echo $this->translate("Views") ?>'>
					      	<?php 
								    if(!empty($data->views)) {
								      echo number_format($data->views);
								    }
								    else echo "0";
								  ?> 
								</td>
					      <td title='<?php echo $this->translate("Clicks") ?>'>
					      	<?php 
					      		if(!empty($data->clicks)) {
						      		echo number_format($data->clicks);
						    		}
						    		else echo "0";
						  		?> 
						  	</td>
					      <td title='<?php echo $this->translate("CTR (%)") ?>'>
					      	<?php 
						    		if(!empty($data->views)) {
						    			echo number_format(round(($data->clicks/$data->views)*100, 4), 4);
						    		}
						    		else echo number_format("0", 4); 
						  		?> 
						  	</td>
					    </tr>
		  			<?php endforeach; ?>
		  		</tbody>	
	      </table>
		  <?php elseif($this->values['ad_subject'] == 'campaign') :?>
	      <table class="admin_table" style="width:100%;">
	      	<thead>
						<tr>
						  <th><?php echo $this->translate($date_label); ?></th>
							<th><?php echo $this->translate("Campaign Id") ?></th>
						  <th style="text-align:left;"><?php echo $this->translate("Advertiser") ?></th>
						  <th style="text-align:left;"><?php echo $this->translate("Campaign Name") ?></th>
						  <th><?php echo $this->translate("Views") ?></th>
						  <th><?php echo $this->translate("Clicks") ?></th>
						  <th><?php echo $this->translate("CTR (%)") ?></th>
						</tr>
					</thead>
					<tbody>	
				 		<?php foreach($this->rawdata as $data) : ?>
					  	<?php 
					      $response_date = explode(' ', $data->response_date);
					      $date_array = explode('-', $response_date[0]);
					      switch($this->values['time_summary']) {
						
									case 'Monthly':
									$date_value = date('F, Y', mktime(0, 0, 0, $date_array[1], date('d'), date('Y')));
									break;
							
									case 'Daily':
									$response_time = strtotime($data->response_date);
									$labelDate = new Zend_Date();
									$labelDate->set($response_time);
									$date_value = $this->locale()->toDate($labelDate, array('size' => 'long')); 
									break;

								}
					   	?>
					    <tr>
					      <td><?php echo $date_value; ?></td>
					      <td><?php echo $data->adcampaign_id ?></td>
					      <td style="text-align:left;" title="<?php echo $data->getOwner()->getTitle() ?>"><?php echo Engine_Api::_()->communityad()->truncation($data->getOwner()->getTitle(), 25); ?></td>
					      <td  style="text-align:left;" title="<?php echo ucfirst($data->name) ?>">
							<?php echo ucfirst(Engine_Api::_()->communityad()->truncation($data->name, 70)) ?>
					      </td>
					      <td title='<?php echo $this->translate("Views") ?>'>
					      	<?php 
								    if(!empty($data->views)) {
								      echo number_format($data->views);
								    }
								    else echo "0";
								  ?> 
								</td>
					      <td title='<?php echo $this->translate("Clicks") ?>'>
					      	<?php 
					      		if(!empty($data->clicks)) {
						      		echo number_format($data->clicks);
						    		}
						    		else echo "0";
						  		?> 
						  	</td>
					      <td title='<?php echo $this->translate("CTR (%)") ?>'>
					      	<?php 
						    		if(!empty($data->views)) {
						      		echo number_format(round(($data->clicks/$data->views)*100, 4), 4);
						    		}
						    		else echo number_format("0", 4); 
						  		?> 
						  	</td>
					    </tr>
					  <?php endforeach; ?>
					</tbody>  
     		</table>
    	<?php endif; ?>
		</div>
<?php elseif(!count($this->rawdata) && $this->post == 1) :?>
	<div class="tip">
  	<span>
    	<?php echo $this->translate("There are no activities found in the selected date range.") ?>
    </span>
  </div>
<?php endif; ?>