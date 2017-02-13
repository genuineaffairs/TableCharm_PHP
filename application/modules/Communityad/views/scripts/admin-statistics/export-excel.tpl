<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: export-excel.tpl  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if(count($this->rawdata)) :?>
  <?php if($this->values['format_report'] == '1') : ?>
    <?php
      header("Expires: 0");
      header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
      header("Cache-Control: no-store, no-cache, must-revalidate");
      header("Cache-Control: post-check=0, pre-check=0", false);
      header("Pragma: no-cache");
      header("Content-type: application/vnd.ms-excel;charset:UTF-8");
      header("Content-Disposition: attachment; filename=Report.xls"); 
      print "\n"; // Add a line, unless excel error..
    ?>
  <?php endif; ?>
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
 
  <div id='stat_table'>
    <?php if($this->values['ad_subject'] == 'ad') :?>
      <table border="2">
	<tr>
	  <th><?php echo $this->translate($date_label); ?></th>
	  <th><?php echo $this->translate("Ad Id") ?></th>
	  <th><?php echo $this->translate("Advertiser") ?></th>
	  <th><?php echo $this->translate("Campaign Name") ?></th>
	  <th><?php echo $this->translate("Ad Title") ?></th>
	  <th><?php echo $this->translate("Views") ?></th>
	  <th><?php echo $this->translate("Clicks") ?></th>
	  <th><?php echo $this->translate("CTR (%)") ?></th>
	</tr>
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
	      <td><?php echo $data->getOwner()->getTitle(); ?></td>
	      <td><?php echo $data->name; ?></td>
	      <td><?php echo $data->cads_title; ?></td>
	      <td><?php 
		    if(!empty($data->views)) {
		      echo number_format($data->views);
		    }
		    else echo "0";
		  ?> </td>
	      <td><?php  if(!empty($data->clicks)) {
		      echo number_format($data->clicks);
		    }
		    else echo "0";
		  ?> </td>
	      <td><?php 
		    if(!empty($data->views)) {
		      echo number_format(round(($data->clicks/$data->views)*100, 4), 4);
		    }
		    else echo number_format("0", 4);
		  ?> </td>
	    </tr>
	  <?php endforeach; ?>
      </table>
      <?php elseif($this->values['ad_subject'] == 'campaign') :?>
      <table border="2">
	<tr>
	  <th><?php echo $this->translate($date_label); ?></th>
	  <th><?php echo $this->translate("Campaign Id") ?></th>
	  <th><?php echo $this->translate("Advertiser") ?></th>
	  <th><?php echo $this->translate("Campaign Name") ?></th>
	  <th><?php echo $this->translate("Views") ?></th>
	  <th><?php echo $this->translate("Clicks") ?></th>
	  <th><?php echo $this->translate("CTR (%)") ?></th>
	</tr>
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
	      <td><?php echo $data->getOwner()->getTitle(); ?></td>
	      <td><?php echo $data->name; ?></td>
	      <td><?php 
		    if(!empty($data->views)) {
		      echo number_format($data->views);
		    }
		    else echo "0";
		  ?> </td>
	      <td><?php  if(!empty($data->clicks)) {
		      echo number_format($data->clicks);
		    }
		    else echo "0";
		  ?> </td>
	      <td><?php 
		    if(!empty($data->views)) {
		      echo number_format(round(($data->clicks/$data->views)*100, 4), 4);
		    }
		    else number_format("0", 4);
		  ?> </td>
	    </tr>
	  <?php endforeach; ?>
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