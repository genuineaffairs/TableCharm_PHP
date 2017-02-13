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
<?php // echo date('r'); ?>
<?php // echo $this->locale()->toDateTime(date('Y-m-d h:i:s')); ?>
<script type="text/javascript">

var currentOrder = '<?php echo $this->order ?>';
var currentOrderDirection = '<?php echo $this->order_direction ?>';
var changeOrder = function(order, default_direction){
  // Just change direction
  if( order == currentOrder ) {
    $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
  } else {
    $('order').value = order;
    $('order_direction').value = default_direction;
  }
  $('resume_admin_manage_filter').submit();
}


  var delectSelected = function(){

      var checkboxes = $$('input.checkboxes');
      var selecteditems = [];

      checkboxes.each(function(item, index){
        var checked = item.get('checked');
        if (checked) {
          selecteditems.push(item.get('value'));
        }
      });

    if (selecteditems == "") {
      return false;
    }  

    $('ids').value = selecteditems;
    $('delete_selected').submit();
  }
  
function selectAll()
{
  var checkboxes = $$('input.checkboxes');
  var selecteditems = [];

  var chked = $('checkboxes_toggle').get('checked');
  
  checkboxes.each(function(item, index){
    item.set('checked', chked);
  });
}
</script>

<h2><?php echo $this->translate("Resumes Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("This page lists all of the resumes your users have posted. You can use this page to monitor these resumes and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific resumes. Leaving the filter fields blank will show all the resumes on your social network.") ?>
</p>
<br />

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<br />

<div class='admin_results'>
  <div>
    <?php $resumeCount = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%d resume found", "%d resumes found", $resumeCount), ($resumeCount)) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->formValues
    )); ?>  
    
  </div>
</div>
<?php //print_r($this->params)?>
<br />

<?php if( count($this->paginator) ): ?>

<table class='admin_table' id='resume_list_resumes'>
  <thead>
    <tr>
      <th class='admin_table_short'><input onclick="selectAll()" type='checkbox' id='checkboxes_toggle' /></th>
      <th class='admin_table_short'>ID</th>
      <th class='resume_header_title'><?php echo $this->translate("Title") ?></th>
      <th class='resume_header_package'><?php echo $this->translate("Package") ?></th>
      <th class='resume_header_epayment'><?php echo $this->translate("Payment"); ?></th>
      <th class='resume_header_publish'><?php echo $this->translate("Publish"); ?></th>
      <th class='resume_header_status'><?php echo $this->translate("Status") ?></th>
      <th class='resume_header_expires'><?php echo $this->translate("Expires") ?></th>
      <th class='resume_header_icon'><?php echo $this->translate("Icon") ?> [<a href="javascript:void(0);" onclick="Smoothbox.open($('resume_icons_legend')); return false;">?</a>]</th>
      <th class='resume_header_options'><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): // $this->string()->chunk($item->getTitle(), 5) ?>
      <tr>
        <td><input type='checkbox' class='checkboxes' value="<?php echo $item->resume_id ?>"/></td>
        <td><?php echo $item->resume_id ?></td>
        <td><?php echo $this->htmlLink($item->getHref(), $this->radcodes()->text()->truncate($item->getTitle(),32), array('target' => '_blank')) ?>
        	<div class="resume_text_desc">
        		<?php echo $item->location; ?>
        		<br />
                <?php echo $this->locale()->toDate($item->creation_date); ?>
        		<?php echo $this->translate('by %s', $item->getOwner()->toString())?>
            <br />
                <?php echo $this->translate(array("%s view", "%s views", $item->view_count), $this->locale()->toNumber($item->view_count)); ?>
                - <?php echo $this->translate(array("%s comment", "%s comments", $item->comment_count), $this->locale()->toNumber($item->comment_count)); ?>
                - <?php echo $this->translate(array('%1$s like', '%1$s likes', $item->like_count), $this->locale()->toNumber($item->like_count)); ?>
          </div>
        </td>
        <td><?php echo $this->htmlLink(
            array('route' => 'admin_default', 'module' => 'resume', 'controller' => 'manage', 'action' => 'update-package', 'resume_id' => $item->resume_id),
            $item->getPackage()->getTitle(),
            array('class' => 'smoothbox')) ?>
        </td>
        <td>
            <?php $epayment = $item->getRecentEpayment(); ?>
            <?php if ($epayment instanceof Epayment_Model_Epayment): ?>
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'resume', 'controller' => 'epayments', 'action' => 'view', 'epayment_id' => $epayment->getIdentity()),
                $this->translate($epayment->getStatusText())); ?>
              <div class="resume_text_desc">  
                <?php echo $this->locale()->toDate($epayment->creation_date); ?>
                |
                <?php echo $epayment->printAmount(); ?>
                <br />
                <?php $epayment_count = $item->epayments()->getEpaymentCount();
                echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'resume', 'controller' => 'epayments', 'action' => 'index', 'resource_id'=>$item->getIdentity()),
                  $this->translate(array('%d payment', '%d payments', $epayment_count), $epayment_count)
                );?>
                <?php if (!$epayment->processed): ?>
                <br/>
              	<span class="resume_list_epayment_process_<?php echo $epayment->processed ? 'yes' : 'no'; ?>"><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'resume', 'controller' => 'epayments', 'action' => 'process', 'epayment_id' => $epayment->getIdentity()),
                $this->translate($epayment->processed ? 'processed' : 'process now')); ?></span>
                <?php endif;?>
              
              </div>
            <?php else: ?>
		          <?php if ($item->getPackage()->isFree()): ?>
		            <?php echo $this->translate('N/A')?>
		          <?php else: ?>
              	<?php echo $this->translate('not paid yet'); ?>
              <?php endif; ?> 	
            <?php endif; ?>
        </td>
        <td>
          <?php echo $this->translate($item->isPublished() ? 'Live' : 'Draft')?>
        </td>
        <td><?php echo $this->htmlLink(
            array('route' => 'admin_default', 'module' => 'resume', 'controller' => 'manage', 'action' => 'update-status', 'resume_id' => $item->resume_id),
            $this->translate($item->getStatusText()),
            array('class' => 'smoothbox')) ?>
        	<br /><?php echo $this->locale()->toDate($item->status_date); ?>
        </td>
        <td>
          <?php if ($item->hasExpirationDate()): ?>
            <?php $expiration_date = $this->locale()->toDate($item->expiration_date); ?>
          <?php else: ?>
            <?php $expiration_date = $this->translate('Never')?>
          <?php endif; ?>
          
          <?php echo $this->htmlLink(
            array('route' => 'admin_default', 'module' => 'resume', 'controller' => 'manage', 'action' => 'update-expiration', 'resume_id' => $item->resume_id),
            $expiration_date,
            array('class' => 'smoothbox')) ?>
        </td>    
        <td><?php echo $this->htmlImage('./application/modules/Resume/externals/images/'. ($item->isLive() ? 'live' : 'live_off') .'.png',
            array('title' => $this->translate($item->isLive() ? 'Live' : 'NOT Live')))?>
            <?php echo $this->htmlLink(
            array('route' => 'admin_default', 'module' => 'resume', 'controller' => 'manage', 'action' => 'featured', 'resume_id' => $item->resume_id),
            $this->htmlImage('./application/modules/Resume/externals/images/featured'.($item->featured ? "" : "_off").'.png'),
            array('class' => 'smoothbox', 'title' => $this->translate($item->featured ? "Featured" : "Not Featured"))) ?>
            <?php echo $this->htmlLink(
            array('route' => 'admin_default', 'module' => 'resume', 'controller' => 'manage', 'action' => 'sponsored', 'resume_id' => $item->resume_id),
            $this->htmlImage('./application/modules/Resume/externals/images/sponsored'.($item->sponsored ? "" : "_off").'.png'),
            array('class' => 'smoothbox', 'title' => $this->translate($item->sponsored ? "Sponsored" : "Not Sponsored"))) ?>
        </td>
        <td>
          <?php echo $this->htmlLink(array('route'=>'resume_specific', 'action'=>'edit', 'resume_id'=>$item->resume_id), $this->translate('edit'), array('target'=>'_blank'))?>
          |
          <?php echo $this->htmlLink(
            array('route' => 'admin_default', 'module' => 'resume', 'controller' => 'manage', 'action' => 'delete', 'resume_id' => $item->resume_id),
            $this->translate("delete"),
            array('class' => 'smoothbox')) ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<br />

<div class='buttons'>
  <button onclick="javascript:delectSelected();" type='submit'>
    <?php echo $this->translate("Delete Selected") ?>
  </button>
</div>

<form id='delete_selected' method='post' action='<?php echo $this->url(array('action' =>'deleteselected')) ?>'>
  <input type="hidden" id="ids" name="ids" value=""/>
</form>
<br/>

<?php //print_r($this->params)?>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no resumes posted by your members yet.") ?>
    </span>
  </div>
<?php endif; ?>


<div style="display: none">
    
  <ul class="radcodes_admin_icons_legend" id="resume_icons_legend">
    <li><?php echo $this->htmlImage('./application/modules/Resume/externals/images/live.png');?><?php echo $this->translate('Online: (Publish=Live) AND (Status=Approved) AND Not Expired')?></li>
    <li><?php echo $this->htmlImage('./application/modules/Resume/externals/images/live_off.png');?><?php echo $this->translate('Offline: NOT Online')?></li>
  
    <li><?php echo $this->htmlImage('./application/modules/Resume/externals/images/featured.png');?><?php echo $this->translate('Featured')?></li>
    <li><?php echo $this->htmlImage('./application/modules/Resume/externals/images/featured_off.png');?><?php echo $this->translate('Not Featured')?></li>
    <li><?php echo $this->htmlImage('./application/modules/Resume/externals/images/sponsored.png');?><?php echo $this->translate('Sponsored')?></li>
    <li><?php echo $this->htmlImage('./application/modules/Resume/externals/images/sponsored_off.png');?><?php echo $this->translate('Not Sponsored')?></li>  
  
  </ul>
  
</div>
