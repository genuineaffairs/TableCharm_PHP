<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if ($this->paginator->getTotalItemCount() > 0): ?>
<div class="sm-content-list" id ="profile_documents">
    <ul data-role="listview" data-icon="arrow-r">
        <?php foreach ($this->paginator as $document): ?>       
            <li>
                <a href="<?php echo $document->getHref(); ?>" >
                    <?php if (!empty($document->photo_id)): ?>
                        <?php echo $this->itemPhoto($document, 'thumb.normal') ?>
                    <?php else: ?>
                        <?php echo '<img src="' . Engine_Api::_()->document()->sslThumbnail($document->thumbnail) . '" class="thumb_normal" />' ?>
                    <?php endif; ?>

                    <h3><?php echo $document->document_title; ?></h3>
                    <p>   
                        <?php echo $this->translate('Created by') ?>
                        <strong><?php echo $document->getOwner()->getTitle(); ?></strong>
                    -  
                        <?php echo $this->timestamp($document->creation_date) ?>
                    </p>
                    <p>
                        <?php if ($document->category_id): ?>
                            <?php $category = Engine_Api::_()->getDbtable('categories', 'document')->getCategory($document->category_id); ?>
                            <?php echo $this->translate('Category:'); ?> <?php echo $category->category_name ?>
                        <?php endif; ?> 
                    </p>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    
   <?php if ($this->paginator->getTotalItemCount() > $this->items_per_page): ?>
			<?php
			echo $this->paginationAjaxControl(
							$this->paginator, $this->identity, 'profile_documents');
			?>
		<?php endif; ?>
    
</div>
<?php endif;?>