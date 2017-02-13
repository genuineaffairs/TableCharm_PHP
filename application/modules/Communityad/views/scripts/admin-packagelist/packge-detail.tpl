<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: package-detail.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<style type="text/css">
  td{
    padding:3px;
  }
  td b{
    font-weight:bold;
  }

.advancedclassified_package_details{
	width:600px;
	margin:10px 0 0 10px;
}
.advancedclassified_package_details table{
	margin-top:15px;
	clear:both;
}
.advancedclassified_package_details th,
.advancedclassified_package_details td
{
	font-weight:normal;
	border-top-width:1px;
	border-top-style:solid;
	border-top-color:#CCCCCC;
	padding:5px;
	text-align:center;
	font-size:11px;
}
.advancedclassified_package_details_title{
	font-size:13px !Important;
	font-weight:bold !Important;
	text-align:left !important;
	width:150px;
}
</style>
<?php foreach ($this->package as $item):
   ?>


		<div class="advancedclassified_package_details">
			<h3 style="float:left;"><?php echo $this->translate('Package Details'); ?></h3>
		
      <?php $enable_plugin_video= 0; ?>
			<table width="100%">
				<thead>
					<tr valign="top">
						<th style="text-align:left;"><?php echo $this->translate('Name'); ?></th>
						<th><?php echo $this->translate('Price'); ?> </th>
           	<th><?php echo $this->translate('Quantity'); ?> </th>
           	<?php if($item['type']=='default'):?>
            <th><?php echo $this->translate('Featured'); ?> </th>
            <th><?php echo $this->translate('Sponsored'); ?> </th>
            <?php endif;?>
            <th><?php echo $this->translate('Targeting'); ?> </th>


					</tr>
				</thead>
				<tbody>

					<tr valign="top">
						<td class="advancedclassified_package_details_title">
							<?php echo $item['title']; ?>&nbsp;&nbsp;

        		</td>
					 <?php if(!$item['price']!=0):?>
						<td><?php echo $this->locale()->toCurrency($item['price'], Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')); ?></td>

            <?php else:?>
            <td><?php echo $this->translate('FREE'); ?></td>

            <?php endif;?>
             <td>


             <?php  switch ($item["price_model"]):

              case "Pay/view":
                 if($item["model_detail"] != -1):echo $this->translate(array('%s View', '%s Views', $item["model_detail"]), $this->locale()->toNumber($item["model_detail"])); else: echo $this->translate('UNLIMITED Views'); endif ;

                 break;

              case "Pay/click":
                 if($item["model_detail"] != -1):echo $this->translate(array('%s Click', '%s Clicks', $item["model_detail"]), $this->locale()->toNumber($item["model_detail"])); else: echo $this->translate('UNLIMITED Clicks'); endif ;

                 break;

              case "Pay/period":
                 if($item["model_detail"] != -1):echo $this->translate(array('%s Day', '%s Days', $item["model_detail"]), $this->locale()->toNumber($item["model_detail"])); else: echo $this->translate('UNLIMITED Days'); endif ;

              break;
                   endswitch;?>

            </td>
            <?php if($item['type']=='default'):?>
             <td>
							<?php
		            if ($item['featured'] == 1)
		              echo $this->translate("Yes");
		            else
		              echo $this->translate("No");
							?>
						</td>
            <td>
							<?php
		            if ($item['sponsored'] == 1)
		              echo $this->translate("Yes");
		            else
		              echo $this->translate("No");
							?>
						</td>
           <?php endif;?> 
             <td>
							<?php
		            if ($item['network'] == 1)
		              echo $this->translate("Yes");
		            else
		              echo $this->translate("No");
							?>
						</td>


					</tr>
          <tr>
            <td colspan="12" style="text-align:left;">

           <b><?php echo  $this->translate("You can advertise"). ": "; ?> </b>
           <?php
              $canAdvertise=explode(",",$item['urloption']);
              if(in_array("website",$canAdvertise)){
               $canAdvertise[array_search("website",$canAdvertise)]= $this->translate('custom Ad');
              }

              foreach ($canAdvertise as $key=>$value):
                if( strstr($value, "sitereview") ){
                $isReviewPluginEnabled = Engine_Api::_()->getDbtable('modules', 'communityad')->getModuleInfo("sitereview");
                if( !empty($isReviewPluginEnabled) ){
                    $sitereviewExplode = explode("_", $value);
                    $getAdsMod = Engine_Api::_()->getItem("communityad_module", $sitereviewExplode[1]);
                    $modTemTitle = strtolower($getAdsMod->module_title);
                    $modTemTitle = ucfirst($modTemTitle);
                    $canAdvertise[$key] = $modTemTitle;
                }else {
                    unset($canAdvertise[$key]);
                }
                }else {
                  if( $value != 'Custom Ad' ) {
                    $getInfo = Engine_Api::_()->getDbtable('modules', 'communityad')->getModuleInfo( $value );
                    if( !empty($getInfo) ) {
                      $canAdvertise[$key] = $getInfo['module_title'];
                    }else {
                        unset($canAdvertise[$key]);
                    }
                  }else {
                    $canAdvertise[$key] = ucfirst($value);
                  }
                }
                endforeach;
              $canAdStr=implode(", ",$canAdvertise);

              echo $canAdStr;

           ?>

            </td>
          </tr>
					<tr>
						<td colspan="12" style="text-align:left;">

							<?php echo  $item['desc']; ?>
						</td>
					</tr>

				</tbody>
			</table>
			<br />
      <button onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('Close'); ?></button>
    </div>
  <?php endforeach;?>
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>