<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  en4.core.runonce.add(function(){$$('th.admin_table_short input[type=checkbox]').addEvent('click', function(){ $$('input[type=checkbox]').set('checked', $(this).get('checked', false)); })});

  var viewad =function(id){  
    
    $('package').value = id;
    $('view_selected').submit();
  }


     var saveFlag=false;
		var origOrder;
		var changeOptionsFlag = false;

		function saveOrder(value){
			saveFlag=value;
    	var finalOrder = [];
			var li = $('order-element').getElementsByTagName('li');
			for (i = 1; i <= li.length; i++)
        finalOrder.push(li[i]); 
      $("order").value=finalOrder;

      	$('saveorder_form').submit();
		}
  window.addEvent('domready', function(){
				//         We autogenerate a list on the fly
				var initList = [];
				var li = $('order-element').getElementsByTagName('li');
				for (i = 1; i <= li.length; i++)
						initList.push(li[i]);
				origOrder = initList;
				var temp_array = $('order-element').getElementsByTagName('ul');
				temp_array.innerHTML = initList;
				new Sortables(temp_array);
		});

		window.onbeforeunload = function(event){
			var finalOrder = [];
			var li = $('order-element').getElementsByTagName('li');
			for (i = 1; i <= li.length; i++)
				finalOrder.push(li[i]);

      

			for (i = 0; i <= li.length; i++){
				if(finalOrder[i]!=origOrder[i])
				{
					changeOptionsFlag = true;
					break;
				}
			}

			if(changeOptionsFlag == true && !saveFlag){
				var answer=confirm("<?php echo $this->string()->escapeJavascript($this->translate("A change in the order of the packages has been detected. If you click Cancel, all unsaved changes will be lost. Click OK to save change and proceed.")); ?>");
				if(answer) {
          $('order').value=finalOrder;
					$('saveorder_form').submit();

				}
			}
		}
  function setTypeBasePackage(value){
  window.location.href='<?php echo $this->url(array('module' => 'communityad', 'controller' => 'packagelist', 'action' => 'index'), 'admin_default', true); ?>'+'/index/type/'+value;
  }  
</script>

<h2><?php echo $this->translate("Community Ads Plugin") ?></h2>

<?php if (count($this->navigation)) {
?>
  <div class='communityad_admin_tabs'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php } ?>
<h3><?php echo $this->translate('Manage Ad Packages') ?></h3>
 <p><?php echo $this->translate('This powerful advertising system enables you to create highly configurable and effective ad packages, enabling you to get the best results. You can create both Free and Paid ad packages. Different pricing models can be chosen for different packages. Packages can enable advertisers to showcase their content in ads, or to create custom ads. You can also allow only privileged packages to have custom ads. Packages and their properties are dependent on the Ad Type chosen for them.<br />
Below, you can manage existing ad packages on your site, or create new ones. You can create different packages for the different Ad Types. You can also set the sequence of ad packages in the order in which they should appear to advertisers on the package listing page in the first step of ad creation. To do so, drag-and-drop the packages vertically and click on "Save Order" to save the sequence.') ?>
</p>

 <br style="clear:both;" />
<div>
  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'packagelist', 'action' => 'create'), $this->translate('Create New Ad Package'), array('class'=>'cmad_icon_package_add buttonlink')) ?> 
<?php if (count($this->adTypes) > 0): ?>
  <label style="font-weight:bold;"><?php echo $this->translate('Ad Type: '); ?></label>
  <select onchange="javascript:setTypeBasePackage(this.value);" id="type" name="type">
    <option  <?php if ($this->type == "default"): echo 'selected="selected"';
  endif; ?> label="Community Ads" value="default"><?php echo $this->translate($this->getCommunityadTitle); ?></option>
    <?php foreach ($this->adTypes as $adType): ?>
      <option <?php if ($this->type == $adType->type): echo 'selected="selected"';
      endif; ?> label="<?php echo $this->translate($adType->title); ?>" value="<?php echo $adType->type ?>"><?php echo $this->translate($adType->title); ?></option>
  <?php endforeach; ?>  
  </select> 
  <?php if($this->type!='default' &&!$this->getAdTypeStatus ):?> 
  <div class="tip">
    <br/>
    <span>
      <?php echo $this->translate('You have disabled the extension: "Advertisements / Community Ads - Sponsored Stories Extension". Please enable it if you want to use the Sponsored Stories Ad Type.')?>
    </span>
  </div>
  <?php endif;?>
<?php endif; ?>
   </div>
<br />
<?php if (count($this->paginator)) {
?>
<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
    ?>
    <table class='admin_table admin_table_packagelist' style="width:100%;">
      <thead>
        <tr>
       		<th style="padding:7px 0;">
	          <div style="width:2%;" class="admin_table_centered"><?php echo $this->translate("ID") ?></div>
	          <div style="width:10%;"><?php echo $this->translate("Name") ?></div>
	          <div style="width:10%;"><?php echo $this->translate("Model") ?></div>
	          <div style="width:8%;" class="admin_table_centered"><?php echo $this->translate("Detail") ?></div>        
	          <div style="width:5%;"><?php echo $this->translate("Price") ?></div>
            <?php if($this->type == "default") : ?>   
	          <div style="width:10%;" class="admin_table_centered"><?php echo $this->translate("Custom Ads") ?></div>
            <?php endif; ?>
	          <div style="width:10%;" class="admin_table_centered"><?php echo $this->translate("Member Levels") ?></div>
	          <div style="width:8%;" class="admin_table_centered"><?php echo $this->translate("Total Ads") ?></div>
	          <div style="width:6%;" class="admin_table_centered"><?php echo $this->translate("Enabled") ?></div>
	          <div style="width:9%;"><?php echo $this->translate("Created On") ?></div>
	          <div style="width:10%;" class="admin_table_centered"><?php echo $this->translate("Options") ?></div>
	        </th>  
        </tr>
      </thead>
   </table>
   <form id='saveorder_form' method='post' action='<?php echo $this->url(array('action' =>'update')) ?>'>
     	<input type='hidden'  name='order' id="order" value=''/>
    <div id='order-element'>
      <ul>
      <?php foreach ($this->paginator as $item) :
      ?>
        <li class="package-list">
          <input type='hidden'  name='order[]' value='<?php echo $item->package_id; ?>'>
          <table class='admin_table admin_table_packagelist' width='100%'>
						<tbody>
							<tr>
                <td style="padding:7px 0;">
                	<div style="width:2%;" class="admin_table_centered"><?php echo $item->package_id ?></div>
                	<div style="width:10%;" title= "<?php echo ucfirst($item->title) ?>">
                		<?php echo ucfirst(Engine_Api::_()->communityad()->truncation($item->title,30)) ?>
                	</div>
                	<div style="width:10%;">
                		<?php switch($item->price_model): case "Pay/view": echo $this->translate("Pay for Views"); break; case "Pay/click":echo $this->translate("Pay for Clicks"); break; case "Pay/period":echo $this->translate("Pay for Days"); break; endswitch; ?>
                	</div>
	                <?php if($item->model_detail != -1): ?>
	                	<div style="width:8%;" class="admin_table_centered"><?php echo $item->model_detail ?></div>
	                <?php else:?>
	                  <div style="width:8%;" class="admin_table_centered"><?php echo $this->translate('UNLIMITED'); ?></div>
	                <?php endif; ?>
	                
	                <div style="width:5%;"><?php if ($item->isFree()) {
	                echo "Free"; ?>
	                <?php
	                  } else {
	                    echo $this->locale()->toCurrency($item->price, $currency);
	                  }
	                ?>
	                </div>
                 <?php if($this->type == "default") : ?>   
	                <div style="width:10%;" class="admin_table_centered">
	                  <?php  $flage=stripos($item->urloption, "website");
	                  if ($flage!== false) :
	                    echo $this->translate("Yes");
	                  else:
	                     echo $this->translate("No");
	                  endif; ?>
	                </div>
                  <?php endif;?>  
	                <div style="width:10%;" class="cmad_package_show_tooltip_wrapper admin_table_centered">
	                  <?php if(empty($item->level_id)):?>
	                  <?php echo $this->translate("All Levels"); ?>
	                  <?php else: ?>
	                  <?php echo $this->translate("See Levels"); ?>
	                  <div class="cmad_package_show_tooltip">
	                  <?php echo $item->getLevelString(); ?>
	                  </div>
	                  <?php endif;?>
	                </div>
	                <div style="width:8%;" class="admin_table_centered" >
	                  <?php echo $item->total_ad ?>
	                </div>
	              <?php if ($item->enabled == 1) {?>
	                <div style="width:6%;" class="admin_table_centered"> 
	                	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'packagelist', 'action' => 'enabled', 'id' => $item->package_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/images/enabled1.gif', '', array('title' => $this->translate('Disable Package')))) ?>
	                </div>
	              <?php } else {?>
	                <div style="width:6%;" class="admin_table_centered"> 
	                	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'packagelist', 'action' => 'enabled', 'id' => $item->package_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/images/enabled0.gif', '', array('title' => $this->translate('Enable Package')))) ?>
	                </div>
              	<?php } ?>
              	<div style="width:9%;"><?php echo $this->translate('%s', gmdate("M d, Y", strtotime($item->creation_date))); ?></div>
	              <div style="width:10%;" class="admin_table_centered">
	                <?php
	                echo $this->htmlLink(
	                        array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'packagelist', 'action' => 'edit', 'id' => $item->package_id),
	                        $this->translate('Edit')
	                ) ?>
	                |
                  <a href="javascript:void(0);" onclick="viewad(<?php echo $item->package_id ?>)" ><?php echo  $this->translate('View Ads');  ?></a>
	
	              </div>
	            </td>  
            </tr>
					</tbody>
				</table>
      </li>
    <?php endforeach; ?>
   </ul>
  </div>

</form>

  <br />
  
   <button onClick="javascript:saveOrder(true);" type='submit'>
    <?php echo $this->translate("Save Order") ?>
  </button>
  <form id='view_selected' method='post'  action='<?php  echo $this->url(array('module' => 'communityad', 'controller' => 'viewad', 'action' => 'index'),'admin_default',true);         
?>'>
    <input type="hidden" id="package" name="package" value=""/>
    <input type="hidden" id="type" name="ad_type" value="<?php echo $this->type;?>"/>
    <input type="hidden" id="search" name="search" value="1"/>
  </form>
  <br/>
  <div>
  <?php echo $this->paginationControl($this->paginator); ?>
    </div>
<?php
    } else {
?>
      <div class="tip">
        <span>
    <?php echo $this->translate("There are no packages yet.") ?>
    </span>
  </div>
<?php } ?>


<style type="text/css">
.cmad_package_show_tooltip{
	display:none;
	position: absolute;
	background: #222;
	color: #fff;
	text-align: left;
	padding: 5px;
	margin-left: 0px;
	margin-top:5px;
	font-weight:normal;
	font-size:11px !important;
}
.cmad_package_show_tooltip_wrapper:hover .cmad_package_show_tooltip{
	display: block;
}
#order-element li.package-list{
	cursor: move;
}
#order-element li.package-list:hover{
	background:#fbfbfb;
}
</style>