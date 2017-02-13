<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo $this->translate("Documents Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<div class='clear'>
	<div class='settings'>
    <form class="global_form">
      <div>
        <h3><?php echo $this->translate("Category to Document Profile Mapping") ?> </h3>
        <p class="form-description">
          <?php echo $this->translate("This mapping will associate a Document Profile Type with a Category. After such a mapping for a category, document owner of documents belonging to that category will be able to fill profile information fields for that profile type by editing the document. With this mapping, you will also be able to associate a profile type with multiple categories.<br />For information on document profile types, profile fields and to create new profile types or profile fields, please visit the 'Profile Fields' section.<br />An example use case of this feature would be associating category technology with profile type having profile fields related to technology and so on.") ?>
        </p>
        <?php if(count($this->paginator)>0):?>
					<table class='admin_table' width="100%">
						<thead>
							<tr>
								<th align="left"><?php echo $this->translate("Category Name") ?></th>
								<th align="left"><?php echo $this->translate("Associated Profile") ?></th>
								<th align="left"><?php echo $this->translate("Mapping") ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($this->paginator as $category): ?>
								<tr>
									<td><?php echo $this->translate($category->category_name) ?></td>
									<?php if(!empty($category->label)):?>
										<td><?php echo $this->translate($category->label) ?></td>
									<?php else: ?>
										<td>---</td>
									<?php endif; ?>
									<td width="150">
										<?php if(empty($category->profilemap_id)):?>
											<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'document', 'controller' => 'profilemaps', 'action' => 'map', 'category_id' => $category->category_id), $this->translate('Add'), array(
												'class' => 'smoothbox',
											)) ?>
										<?php else: ?>
											<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'document', 'controller' => 'profilemaps', 'action' => 'delete', 'profilemap_id' =>$category->profilemap_id), $this->translate('Remove'), array(
												'class' => 'smoothbox',
											)) ?>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php else:?>
					<br/>
					<div class="tip">
						<span><?php echo $this->translate("There are currently no categories to be mapped.") ?></span>
					</div>
				<?php endif;?>
			</div>
		</form>
	</div>
</div>