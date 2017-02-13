<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @package   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
?>


<script type="text/javascript">

  var SortablesInstance;

  window.addEvent('load', function() {
    SortablesInstance = new Sortables('package_list', {
      clone: false,
      constrain: true,
      handle: 'td.move-me',
      onComplete: function(e) {
        reorder(e);
      }
    });
  });

  var reorder = function(e) {

	     var packageitems = e.parentNode.childNodes;
	     var ordering = {};
	     var i = 1;
	     for (var packageitem in packageitems)
	     {
	       var child_id = packageitems[packageitem].id;

	       if ((child_id != undefined) && (child_id.substr(0, 5) == 'admin'))
	       {
	         ordering[child_id] = i;
	         i++;
	       }
	     }
	    ordering['format'] = 'json';

	    // Send request
	    var url = '<?php echo $this->url(array('action' => 'order')) ?>';
	    var request = new Request.JSON({
	      'url' : url,
	      'method' : 'POST',
	      'data' : ordering,
	      onSuccess : function(responseJSON) {
	      }
	    });

	    request.send();

	  }

  function ignoreDrag()
  {
    event.stopPropagation();
    return false;
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

  <div class='clear'>
    <form class="global_form">
      <div>
        <h3><?php echo $this->translate("Resume Packages") ?> </h3>
        <p class="description">
          <?php echo $this->translate("You can define various of packages for resume listings, as well as their costs, duration for posting days.") ?>
        </p>
        
        <br/>
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'resume', 'controller' => 'packages', 'action' => 'create'), $this->translate('Add New Package'), array(
          'class' => 'buttonlink icon_radcodes_category_new',
          )) ?>
          
        <?php /* echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'resume', 'controller' => 'packages', 'action' => 'move'), $this->translate('Move Resume Package'), array(
          'class' => 'smoothbox buttonlink icon_radcodes_category_move',
          )) */ ?>
          <br /><br/>
      <?php if(count($this->packages)>0):?>

         <table class='admin_table'>
          <thead>
            <tr>
              <th>&nbsp;</th>
                <th><?php echo $this->translate("Package Name") ?></th>
                <th><?php echo $this->translate("Resumes") ?></th>
                <th><?php echo $this->translate("Epayments") ?></th>
                <th><?php echo $this->translate("Featured")?></th>
                <th><?php echo $this->translate("Sponsored")?></th>
                <th style="white-space: normal;"><?php echo $this->translate("Auto Process")?></th>
                <th style="white-space: normal;"><?php echo $this->translate("Allow Upgrade")?></th>
                <th style="white-space: normal;"><?php echo $this->translate("Allow Renew")?></th>
                <th><?php echo $this->translate("Enabled") ?></th>
                <th><?php echo $this->translate("Options") ?></th>
            </tr>
          </thead>
          <tbody id='package_list'>
            <?php foreach ($this->packages as $package): ?>
              <tr id='admin_package_item_<?php echo $package->package_id; ?>'>
                <td class='move-me'><img src="application/modules/Core/externals/images/admin/sortable.png" width="16" height="16"/></td>
                <td><?php echo $this->htmlLink($package->getHref(), $package->getTitle(), array('target'=>'_blank'))?>
                  <br/><?php echo $package->getTerm() ?>  
                </td>
                <td><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'resume', 'controller' => 'manage', 'action' => 'index', 'package' =>$package->package_id), 
                    $this->locale()->toNumber($package->getResumeCount())) ?>
                </td>
                <td><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'resume', 'controller' => 'epayments', 'action' => 'index', 'package_id' =>$package->package_id), 
                    $this->locale()->toNumber($package->getEpaymentCount())) ?>
                </td>
                <td>
                  <?php echo $this->translate($package->featured ? 'Yes' : 'No');?>
                </td>
                <td>
                  <?php echo $this->translate($package->sponsored ? 'Yes' : 'No');?>
                </td>
                <td>
                  <?php echo $this->translate($package->auto_process ? 'Yes' : 'No');?>
                </td>
                <td>
                  <?php echo $this->translate($package->allow_upgrade ? 'Yes' : 'No');?>
                </td>
                <td>
                  <?php echo $this->translate($package->allow_renew ? 'Yes' : 'No');?>
                </td>
                <td>
                  <?php echo $this->translate($package->enabled ? 'Yes' : 'No');?>
                </td> 
                <td>
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'resume', 'controller' => 'packages', 'action' => 'edit', 'package_id' =>$package->package_id), $this->translate('edit'), array(
                    //'class' => 'smoothbox',
                  )) ?>
                  |
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'resume', 'controller' => 'packages', 'action' => 'delete', 'package_id' =>$package->package_id), $this->translate('delete'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  |
                  <?php if ($package->photo_id): ?>
                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'resume', 'controller' => 'packages', 'action' => 'icon', 'package_id' =>$package->package_id), $this->translate('icon'), array(
                      'class' => 'smoothbox',
                    )) ?>
                  <?php else: ?>
                    <?php echo $this->translate('no icon'); ?>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else:?>
        <br/>
        <div class="tip">
        <span><?php echo $this->translate("There are currently no packages.") ?></span>
        </div>
      <?php endif;?>

    </div>
    </form>
  </div>
     