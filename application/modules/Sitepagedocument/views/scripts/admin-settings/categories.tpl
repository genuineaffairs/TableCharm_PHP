<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: categories.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/pluginLink.tpl'; ?>
<h2><?php echo $this->translate('Directory / Pages - Events Extension') ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>
<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_upgrade_messages.tpl'; ?>

  <div class='clear'>
    <div class='settings'>
    <form class="global_form">
      <div>
        <h3> <?php echo $this->translate("Directory / Pages - Event Categories") ?> </h3>
        <p class="description">
          <?php echo $this->translate("Below, you can add and manage the various categories for the page documents on your site.") ?>
        </p>
          <?php if(count($this->categories)>0):?>

         <table class='admin_table'>
          <thead>
            <tr>
              <th><?php echo $this->translate("Category Name") ?></th>
<?php //              <th># of Times Used</th>?>
              <th><?php echo $this->translate("Options") ?></th>
            </tr>

          </thead>
          <tbody>
            <?php foreach ($this->categories as $category): ?>
                    <tr>
                      <td><?php echo $category->title?></td>
                      <td>
                        <?php echo $this->htmlLink(
																array('route' => 'default', 'module' => 'sitepagedocument', 'controller' => 'admin-settings', 'action' => 'edit-category', 'id' =>$category->category_id),
																$this->translate('edit'),
																array('class' => 'smoothbox',
                        )) ?>
                        |
                        <?php echo $this->htmlLink(
																array('route' => 'default', 'module' => 'sitepagedocument', 'controller' => 'admin-settings', 'action' => 'delete-category', 'id' =>$category->category_id),
																$this->translate('delete'),
																array('class' => 'smoothbox',
                        )) ?>

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

      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepagedocument', 'controller' => 'settings', 'action' => 'add-category'), $this->translate('Add New Category'), array(
        'class' => 'smoothbox buttonlink',
        'style' => 'background-image: url(' . $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/admin/new_category.png);')) ?>

    </div>
    </form>
    </div>
  </div>
     
