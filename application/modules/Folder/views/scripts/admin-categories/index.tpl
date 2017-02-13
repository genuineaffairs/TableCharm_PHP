<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Folder
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
?>


<script type="text/javascript">

  var SortablesInstance;

  window.addEvent('load', function() {
    SortablesInstance = new Sortables('category_list', {
      clone: false,
      constrain: true,
      handle: 'td.move-me',
      onComplete: function(e) {
        reorder(e);
      }
    });
  });

  var reorder = function(e) {

	     var categoryitems = e.parentNode.childNodes;
	     var ordering = {};
	     var i = 1;
	     for (var categoryitem in categoryitems)
	     {
	       var child_id = categoryitems[categoryitem].id;

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



<h2><?php echo $this->translate("Folder / File Sharings Plugin") ?></h2>

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
    <div class='settings'>
    <form class="global_form">
      <div>
        <h3><?php echo $this->translate("Folder Categories") ?> </h3>
        <p class="description">
          <?php echo $this->translate("Below are categories which folders can be assigned under.") ?>
        </p>
      <?php if(count($this->categories)>0):?>

         <table class='admin_table'>
          <thead>
            <tr>
              <th>&nbsp;</th>
              <th><?php echo $this->translate("Category Name") ?></th>
              <th><?php echo $this->translate("Number of Times Used") ?></th>
              <th><?php echo $this->translate("Options") ?></th>
            </tr>
          </thead>
          <tbody id='category_list'>
            <?php foreach ($this->categories as $category): ?>
              <tr id='admin_category_item_<?php echo $category->category_id; ?>'>
                <td class='move-me'><img src="application/modules/Core/externals/images/admin/sortable.png" width="16" height="16"/></td>
                <td><?php echo $this->htmlLink($category->getHref(), $category->getTitle(), array('target'=>'_blank'))?></td>
                <td><?php echo $category->getUsedCount()?></td>
                <td>
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'folder', 'controller' => 'categories', 'action' => 'edit', 'category_id' =>$category->category_id), $this->translate('edit'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  |
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'folder', 'controller' => 'categories', 'action' => 'delete', 'category_id' =>$category->category_id), $this->translate('delete'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  |
                  <?php if ($category->photo_id): ?>
                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'folder', 'controller' => 'categories', 'action' => 'icon', 'category_id' =>$category->category_id), $this->translate('icon'), array(
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
      <span><?php echo $this->translate("There are currently no categories.") ?></span>
      </div>
      <?php endif;?>
        <br/>
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'folder', 'controller' => 'categories', 'action' => 'add'), $this->translate('Add Folder Category'), array(
          'class' => 'smoothbox buttonlink icon_radcodes_category_new',
          )) ?>
          
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'folder', 'controller' => 'categories', 'action' => 'move'), $this->translate('Move Folder Category'), array(
          'class' => 'smoothbox buttonlink icon_radcodes_category_move',
          )) ?>
    </div>
    </form>
    </div>
  </div>
     