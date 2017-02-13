<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo $this->translate("Community Ads Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
 <div class='communityad_admin_tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<h3><?php echo $this->translate('Manage Advertisements') ?></h3>
<p>
  <?php echo $this->translate('This page lists all of the Ads your users have posted. You can use this page to monitor these ads and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific ads. Leaving the filter fields blank will show all the ads on your social network. Here, you can monitor ads, delete them, make ads featured / un-featured, sponsored / unsponsored and also approved / dis-approved. If you want to pause an ad, or modify more parameters of it like priority-weight, minimum CTR, etc, then click on the "Edit" link for it.');?>
</p>
<br/>

<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction){  

    if( order == currentOrder ) {
      $('order').value = order;
      $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else { 
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }
  
  en4.core.runonce.add(function(){$$('th.admin_table_short input[type=checkbox]').addEvent('click', function(){ $$('input[type=checkbox]').set('checked', $(this).get('checked', false)); })});


  var delectSelected =function(){
    var checkboxes = $$('input[type=checkbox]');
    var selecteditems = [];

    checkboxes.each(function(item, index){
      var checked = item.get('checked', false);
      var value = item.get('value', false);
      if (checked == true && value != 'on'){
	selecteditems.push(value);
      }
    });

    $('ids').value = selecteditems;
    $('delete_selected').submit();
  }
</script>


<div class="admin_search" style="max-width:950px;">
  <div class="search">
    <form method="post" class="global_form_box" action="">
      <div>
	      <label>
	      	<?php echo $this->translate("Advertisement Title") ?>
	      </label>
	      <?php if( empty($this->title)):?>
	      	<input type="text" name="title" /> 
	      <?php else: ?>
	      	<input type="text" name="title" value="<?php echo $this->translate($this->title)?>"/>
	      <?php endif;?>
      </div>
      <div>
	      <label>
	      	<?php echo $this->translate("Campaign Name") ?>
	      </label>
	      <?php if( empty($this->campaign_name)):?>
	      	<input type="text" name="campaign_name" /> 
	      <?php else: ?>
	      	<input type="text" name="campaign_name" value="<?php echo $this->translate($this->campaign_name)?>"/>
	      <?php endif;?>
      </div>
      <div>
      	<label>
      		<?php echo $this->translate("Advertiser") ?>
      	</label>
      	<?php if( empty($this->owner)):?>
      		<input type="text" name="owner" /> 
      	<?php else: ?>
      		<input type="text" name="owner" value="<?php echo $this->translate($this->owner)?>" />
      	<?php endif;?>
      </div>

      <div>
	 <label>
	  <?php echo  $this->translate("Type") ?>	
	</label>
       <select id="ad_type" name="ad_type" onchange="javascript:$('package').value=0;" >
          <option value="" ><?php echo $this->translate("Select") ?></option>
          <option value="default" <?php if( $this->ad_type == 'default') echo $this->translate("selected");?> ><?php echo $this->translate($this->getCommunityadTitle) ?></option>
          <option value="sponsored_stories" <?php if( $this->ad_type == 'sponsored_stories') echo $this->translate("selected");?> ><?php echo $this->translate("Sponsored Stories") ?></option>
       </select>
      </div>

      <div>
	    	<label>
	      	<?php echo  $this->translate("Package") ?>
	      </label>
        <select id="package" name="package" >
          <option value="0" ><?php echo $this->translate("Select") ?></option>
          <?php  foreach($this->packageList as $package):?>
          <option value="<?php echo "$package->package_id" ?>" title ="<?php echo $this->translate(ucfirst($package->title)) ?>" <?php if( $this->package == $package->package_id) echo $this->translate("selected");?>  style="background:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/<?php echo $package->type ?>.png) no-repeat left;padding-left:20px;"><?php echo Engine_Api::_()->communityad()->truncation($package->title,20) ?></option>

            <?php endforeach;?>
        </select>
      </div>
      <div>
	    	<label>
	      	<?php echo  $this->translate("Status Filter") ?>	
	      </label>
        <select id="status" name="status">
          <option value="100" ><?php echo $this->translate("Select") ?></option>
          <option value="0" <?php if( $this->status == '0') echo $this->translate("selected");?> ><?php echo $this->translate("Pending") ?></option>
          <option value="1" <?php if( $this->status == '1') echo $this->translate("selected");?> ><?php echo $this->translate("Running") ?></option>
          <option value="2" <?php if( $this->status == '2') echo $this->translate("selected");?> ><?php echo $this->translate("Paused") ?></option>
          <option value="3" <?php if( $this->status == '3') echo $this->translate("selected");?> ><?php echo $this->translate("Completed") ?></option>
          <option value="4" <?php if( $this->status == '4') echo $this->translate("selected");?> ><?php echo $this->translate("Deleted") ?></option>
          <option value="5" <?php if( $this->status == '5') echo $this->translate("selected");?> ><?php echo $this->translate("Declined") ?></option>
       </select>
      </div>
     
      <div style="margin-top:10px;">
        <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
      </div>
    </form>
  </div>
</div>
<br />



<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>
 <?php $counter=$this->paginator->getTotalItemCount(); ?>
 
<div class='admin_members_results'>
 <?php if(!empty($counter)): ?>
  <div class="">
    <?php  echo $this->translate(array('%s Advertisement found.', '%s Advertisements found.', $counter), $this->locale()->toNumber($counter)) ?>
  </div>
  <?php else:?>
  <div class="tip"><span>
    <?php if(!empty($this->view->post)) : ?>
    <?php  echo $this->translate("No results were found.") ?>
    <?php else : ?>
     <?php  echo $this->translate("There are no Advertisements.") ?>
    <?php endif; ?></span>
  </div>
  <?php  endif; ?>
  <br />
  <?php  echo $this->paginationControl($this->paginator); ?>
</div>

 <?php  if( $this->paginator->getTotalItemCount()>0):?>

  <table class='admin_table' width="100%">
    <thead>
      <tr>
          <th class="admin_table_short"><input type='checkbox' class='checkbox'></th>
            <?php $class = ( $this->order == 'userad_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th style='width: 1%;' class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('userad_id', 'DESC');"><?php echo $this->translate('ID'); ?></a></th>
             <?php $class = ( $this->order == 'cads_title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th align="left" class="<?php echo $class ?>"><a href="javascript:void(0);"  title='<?php echo $this->translate('Ad Title') ?>' onclick="javascript:changeOrder('cads_title', 'ASC');"><?php echo $this->translate('Title'); ?></a></th>
            <?php $class = ( $this->order == 'username' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
	  <th class="admin_table_centered"><?php echo $this->translate("Type") ?></th>
          <th align="left"  class="<?php echo $class ?>"><a href="javascript:void(0);"  title='<?php echo $this->translate('Owner Name') ?>' onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate('Advertiser');?></a></th>
          <th class="admin_table_centered"><?php echo $this->translate("Status") ?></th>
            <?php $class = ( $this->order == 'approved' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class='admin_table_centered <?php echo $class ?>'><a href="javascript:void(0);" onclick="javascript:changeOrder('approved', 'DESC');" title="<?php echo $this->translate('Approved') ?>"><?php echo $this->translate('A'); ?></a></th>
             <?php $class = ( $this->order == 'featured' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class='admin_table_centered <?php echo $class ?>'><a href="javascript:void(0);" onclick="javascript:changeOrder('featured', 'DESC');" title="<?php echo $this->translate('Featured')?>"><?php echo $this->translate('F'); ?></a></th>
             <?php $class = ( $this->order == 'sponsored' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="admin_table_centered <?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('sponsored', 'DESC');" title="<?php echo $this->translate('Sponsored') ?>"><?php echo $this->translate('S'); ?></a></th>
              <?php $class = ( $this->order == 'count_view' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class='admin_table_centered <?php echo $class ?>'><a href="javascript:void(0);"  title='<?php echo $this->translate('Total Views') ?>' onclick="javascript:changeOrder('count_view', 'DESC');"><?php echo $this->translate('Views'); ?></a></th>
              <?php $class = ( $this->order == 'count_click' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="admin_table_centered <?php echo $class ?>"><a href="javascript:void(0);"  title='<?php echo $this->translate('Total Clicks') ?>' onclick="javascript:changeOrder('count_click', 'DESC');"><?php echo $this->translate('Clicks'); ?></a></th>
              <?php $class = ( $this->order == 'CTR' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="admin_table_centered <?php echo $class ?>"><a href="javascript:void(0);"  title='<?php echo $this->translate('Click Through Rate') ?>' onclick="javascript:changeOrder('CTR', 'DESC');"><?php echo $this->translate('CTR (%)'); ?></a></th>
              <?php $class = ( $this->order == 'count_like' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="admin_table_centered <?php echo $class ?>"><a href="javascript:void(0);"  title='<?php echo $this->translate('Total Likes') ?>' onclick="javascript:changeOrder('count_like', 'DESC');"><?php echo $this->translate('Likes'); ?></a></th>						
							<?php $class = ( $this->order == 'weight' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th class="admin_table_centered <?php echo $class ?>"><a href="javascript:void(0);"  title='<?php echo $this->translate('Higher the weight, more is the ad’s priority and chance to be shown.') ?>' onclick="javascript:changeOrder('weight', 'DESC');"><?php echo $this->translate('Weight'); ?></a></th>

              <?php $class = ( $this->order == 'package_name' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th align="left" class="<?php echo $class ?>"><a href="javascript:void(0);"  title='<?php echo $this->translate('Package Name') ?>' onclick="javascript:changeOrder('package_name', 'ASC');"><?php echo $this->translate('Package'); ?></a></th>
              <?php $class = ( $this->order == 'create_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
          <th align="left" <?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('create_date', 'DESC');"><?php echo $this->translate('Creation'); ?></a></th>
          <th align="left"><?php echo $this->translate('Remaining'); ?></th>
          <th class="admin_table_centered" title="Payment Status"><?php echo $this->translate('Payment'); ?></th>       
          <th align="left"><?php echo $this->translate('Options'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php if( count($this->paginator) ): ?>
        <?php foreach( $this->paginator as $item ): ?>
         <tr>
					<td>	
						<?php if($item->status!=4 && $item->status!=5): ?>
							<input name='delete_<?php echo $item->userad_id;?>' type='checkbox' class='checkbox' value="<?php echo $item->userad_id ?>"/>
						<?php endif; ?>	
					</td>
					<td><?php echo $item->userad_id ?></td>
					<td style="white-space:normal;" title="<?php echo ucfirst($item->cads_title) ?>">
						<?php echo $this->htmlLink(
							array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'viewad', 'action' => 'adpreview', 'ad_id' => $item->userad_id), $this->translate(ucfirst(Engine_Api::_()->communityad()->truncation($item->cads_title, 14))),
							array('class' => 'smoothbox', 'title'=>$this->translate(ucfirst($item->cads_title))))
						?>
					</td>
          <td title="<?php echo $item->getAdTypeTitle($item->ad_type)?>" > 
<?php  echo  $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Communityad/externals/images/$item->ad_type.png"); ?>
</td>					
					<td title="<?php echo $item->getOwner()->getTitle() ?>"> 
						<?php echo $this->htmlLink($item->getOwner()->getHref(), Engine_Api::_()->communityad()->truncation($item->getOwner()->getTitle())) ?>
					</td>
					<td align="center" class='admin_table_centered'>
	           <?php if($item->approved == 1 && $item->status <=3 && $item->declined!=1):?>
						  <?php switch($item->status) {
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
						
						    }
						  ?>
              <?php elseif($item->status==4): ?>
                <?php echo "<span style='color:red;'>" . $this->translate("Deleted") . "</span>"; ?>
             <?php elseif($item->status==3): ?>
                <?php echo $this->translate("Completed"); ?>
             <?php elseif($item->declined==1): ?>
                <?php echo "<span style='color:red;'>" . $this->translate("Declined") . "</span>"; ?>
             <?php else: ?>
                  <?php if(empty($item->approve_date)): ?>
               <?php echo $this->translate("Approval Pending"); ?>
                  <?php else:?>
              <?php echo $this->translate("Dis-Approved"); ?>
                  <?php endif;?>
            <?php endif; ?>
					</td>
	 				<?php if($item->status == 4 || $item->declined == 1): ?>
       			<!--Approved--> <?php if($item->approved == 1):?>
						<td align="center" class="admin_table_centered"> 
							    <?php
								    echo  $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/images/communityad_approved1.gif');
							    ?>
						</td>
					<?php else: ?>
						<td align="center" class="admin_table_centered">
					    <?php 
						    echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/images/communityad_approved0.gif');
					    ?>
					  </td>
					<?php endif; ?>
      
		      <!--Featured-->
			   	<?php if($item->featured == 1): ?>
						<td align="center" class="admin_table_centered">
				    <?php 				      
				      if( $item->ad_type != 'sponsored_stories' ) {
					echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/images/communityad_goldmedal1.gif');
				      }else {
					echo '-';
				      }
				    ?>
					<?php else: ?>
						<td align="center" class="admin_table_centered"> 
						    <?php 
						      if( $item->ad_type != 'sponsored_stories' ) {
							echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/images/communityad_goldmedal0.gif');
						      }else {
						      echo '-';
						      }
						    ?>
						</td>
					<?php endif; ?>

    <!--sponsored-->
   <?php if($item->sponsored == 1):?>
		<td align="center" class="admin_table_centered"> 
			    <?php 
			      if( $item->ad_type != 'sponsored_stories' ) {
				echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/images/sponsored.png'); 
			      }else {
				echo '-';
			      }
			    ?>
			    <?php else: ?>
		<td align="center" class="admin_table_centered"> 
			    <?php  
			      if( $item->ad_type != 'sponsored_stories' ) {
				echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/images/unsponsored.png');
			      }else {
				echo '-';
			      } 
			    ?>
		</td>
		<?php endif; ?>
 
	<?php else : ?>
    <?php if($item->approved == 1):?>
		<td align="center" class="admin_table_centered"> 
			    <?php
				    echo $this->htmlLink(array('route' => 'default', 'module' => 'communityad', 'controller' => 'admin', 'action' => 'approved', 'id' => $item->userad_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/images/communityad_approved1.gif', '', array('title'=> $this->translate('Make Dis-Approved'))))
			    ?>
		</td>
		<?php else: ?>
		<td align="center" class="admin_table_centered">
			    <?php 
				    echo $this->htmlLink(array('route' => 'default', 'module' => 'communityad', 'controller' => 'admin', 'action' => 'approved', 'id' => $item->userad_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/images/communityad_approved0.gif', '', array('title'=> $this->translate('Make Approved'))))
			    ?>
	      </td>
		<?php endif; ?>
      

     <?php if($item->featured == 1): ?> 
		<td align="center" class="admin_table_centered">
			    <?php 
				    if( $item->ad_type != 'sponsored_stories' ) {
				      echo $this->htmlLink(array('route' => 'default', 'module' => 'communityad', 'controller' => 'admin', 'action' => 'featured', 'id' => $item->userad_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/images/communityad_goldmedal1.gif', '', array('title'=> $this->translate('Make Un-featured')))); }else { echo '-'; }?>
		<?php else: ?>
		<td align="center" class="admin_table_centered"> 
			    <?php 
				    if( $item->ad_type != 'sponsored_stories' ) {
				      echo $this->htmlLink(array('route' => 'default', 'module' => 'communityad', 'controller' => 'admin', 'action' => 'featured', 'id' => $item->userad_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/images/communityad_goldmedal0.gif', '', array('title'=> $this->translate('Make Featured'))));
				    }else {
				      echo '-';
				    }
			    ?>
		</td>
		<?php endif; ?>

    <?php if($item->sponsored == 1):?>
		<td align="center" class="admin_table_centered"> 
			    <?php 
				  if( $item->ad_type != 'sponsored_stories' ) {
				    echo $this->htmlLink(array('route' => 'default', 'module' => 'communityad', 'controller' => 'admin', 'action' => 'sponsored', 'id' => $item->userad_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/images/sponsored.png', '', array('title'=> $this->translate('Make Unsponsored'))));}else { echo '-'; } ?>
			    <?php else: ?>
		<td align="center" class="admin_table_centered"> 
			    <?php  
				  if( $item->ad_type != 'sponsored_stories' ) {
				    echo $this->htmlLink(array('route' => 'default', 'module' => 'communityad', 'controller' => 'admin', 'action' => 'sponsored', 'id' => $item->userad_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/images/unsponsored.png', '', array('title'=> $this->translate('Make Sponsored')))); }else { echo '-'; } ?>
		</td>
		<?php endif; ?>

	<?php endif; ?>

		<td align="center" class="admin_table_centered">
			<?php 
				if(!empty($item->count_view)) {
					echo number_format($item->count_view);
				}
				else echo "0";
			?> 
		</td>
		<td align="center" class="admin_table_centered">
			<?php  
				if(!empty($item->count_click )) {
					echo number_format($item->count_click );
				}
				else echo "0";
			?> 
		</td>
		<td align="center" class="admin_table_centered">
			<?php 
				if(!empty($item->CTR)) {
					echo number_format(round(($item->CTR)*100, 4), 4);
				}
				else echo number_format("0", 4); 
			  ?> 
		</td>
		
		<?php if(!empty($item->resource_type)) : ?>
			<td align="center" class="admin_table_centered">
				<?php 
					if(!empty($item->count_like)) {
						echo $item->count_like;
					}
					else echo "0"; 
				?> 
			</td>
		<?php else :?>
			<td align="center" class="admin_table_centered" title="<?php echo $this->translate('Not Applicable') ?>">
				<?php 
					echo $this->translate("NA"); 
				?> 
			</td>
		<?php endif; ?>

		<td align="center" class="admin_table_centered" >
						<?php 
							if(!empty($item->weight)) {
								echo $item->weight; 
							}
							else echo "0";
						?> 
		</td>

               <td style="white-space:normal;" title="<?php echo $this->translate(ucfirst($item->package_name))?>">
			<?php
				echo $this->htmlLink(
				array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'packagelist', 'action' => 'packge-detail', 'id' => $item->package_id), $this->translate(ucfirst(Engine_Api::_()->communityad()->truncation($item->package_name, 14))), array('class' => 'smoothbox')
				) ?>
		</td>

		<td align="left">
			<?php echo $this->translate(date('M d, Y',strtotime($item->cads_start_date))) ?>
		</td>

<!--REMAINING-->
		<td><?php
       if(!empty($item->approve_date)):
          switch ($item->price_model):

          case "Pay/view":


            if ($item->limit_view == -1) {
            echo  $this->translate('UNLIMITED Views');
            } else{
              $renewFlageValue=$item->limit_view;
              $renewFlage=1;
            echo  $this->translate(array('%s View', '%s Views', $item->limit_view), $this->locale()->toNumber($item->limit_view));
            }

          break;

          case "Pay/click":
            if ($item->limit_click == -1){
            echo  $this->translate('UNLIMITED Clicks');
            }else{

             $renewFlageValue=$item->limit_click;
             $renewFlage=1;
            echo  $this->translate(array('%s Click', '%s Clicks', $item->limit_click), $this->locale()->toNumber($item->limit_click));
            }
          break;
          case "Pay/period":
            if (!empty($item->expiry_date)) {

               if ($item->expiry_date !== '2250-01-01'){
              $diff_days = round((strtotime($item->expiry_date) - strtotime(date('Y-m-d'))) / 86400);
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
              ?>
    </td>

 <!--PAYMENT--> 

		<td align="center" class="admin_table_centered"> 	   
    <?php if($item->payment_status == 'active' && $item->price!=0) : ?>
       <?php echo $this->translate('Yes') ?>
    <?php elseif($item->payment_status == 'initial' && $item->price!=0): ?>
      <?php echo $this->translate('No') ?>
    <?php elseif($item->payment_status == 'pending' && $item->price!=0): ?>
      <?php echo $this->translate('Pending') ?>
    <?php elseif($item->payment_status == 'overdue' && $item->price!=0): ?>
      <?php echo $this->translate('Overdue') ?>
    <?php elseif($item->payment_status == 'refunded' && $item->price!=0): ?>
      <?php echo $this->translate('Refunded') ?>
    <?php elseif($item->payment_status == 'cancelled' && $item->price!=0): ?>
      <?php echo $this->translate('Cancelled') ?>
    <?php elseif($item->payment_status == 'expired' && $item->price!=0): ?>
      <?php echo $this->translate('Expired') ?>
    <?php elseif( $item->price==0): ?>
      <?php  echo $this->translate('FREE') ?>
    <?php endif; ?>
  </td>
	   
          <td class='admin_table_options' style="white-space: normal;">
    <?php echo $this->htmlLink(
			array('route' => 'communityad_userad', 'ad_id' => $item->userad_id),
			$this->translate('Details'), array('target' => '_blank', 'title' => $this->translate('Details'))
                    )
		?>

      <?php if($item->status!=4 && $item->declined !=1): ?>
		 
                          |
		<?php echo $this->htmlLink(
			array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'viewad', 'action' => 'editad', 'id' => $item->userad_id),
			$this->translate('Edit'), array('title' => $this->translate('Edit'))
                    )
		?>
                          |
		<?php  if(!empty($item->approved) && $item->status<=2) {
		      if($item->enable==1):
		    echo $this->htmlLink(
		      array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'viewad', 'action' => 'enabled', 'id' => $item->userad_id),
		      $this->translate('Pause'), array('title' => $this->translate('Pause'))
		    );
		  else:
		      echo $this->htmlLink(
		      array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'viewad', 'action' => 'enabled', 'id' => $item->userad_id),
		      $this->translate('Active'), array('title' => $this->translate('Active'))
		    );
		  endif;
		?>
    | 

    <?php if($item->expiry_date !== '2250-01-01' && $item->limit_click != -1 && $item->limit_view != -1):

        echo $this->htmlLink(array('route' => 'default', 'module' => 'communityad', 'controller' => 'admin', 'action' => 'renew', 'id' => $item->userad_id), $this->translate('Renew'), array(
          'class' => 'smoothbox', 'title' => $this->translate('Renew')
        )); ?>  | 
      <?php endif; ?>
             
                     
		<?php } ?>
		      <?php
		      echo $this->htmlLink(
		  array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'viewad', 'action' => 'deletead', 'id' => $item->userad_id),
			      $this->translate('Delete'),
			      array('class' => 'smoothbox', 'title' => $this->translate('Delete'))
		      );
	endif;  ?>

          </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
  <br />
  <div class='buttons'>
	<button onclick="javascript:delectSelected();" type='submit'>
			<?php echo $this->translate("Delete Selected") ?>
	</button>
  </div>
	<form id='delete_selected' method='post' action='<?php echo $this->url(array('action' => 'deleteselectedad')) ?>'>
			<input type="hidden" id="ids" name="ids" value=""/>
	</form>
<?php endif;?>
<style type="text/css">
table.admin_table thead tr th,
table.admin_table tbody tr td{
	padding:5px 3px;
	font-size:11px;
}
.paginationControl{
	margin-bottom:15px;
}
.search div input, div.search div select{
	width:125px;
}
</style>
