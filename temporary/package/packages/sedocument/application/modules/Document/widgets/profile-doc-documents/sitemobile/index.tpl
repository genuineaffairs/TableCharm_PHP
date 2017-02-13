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
<div class="sm-content-list">
    <ul data-role="listview" data-icon="arrow-r">      
            <li>
                <a href="<?php echo $this->document->getHref(); ?>" >
                    <?php if (!empty($this->document->photo_id)): ?>
                        <?php echo $this->itemPhoto($this->document, 'thumb.normal') ?>
                    <?php else: ?>
                        <?php echo '<img src="' . Engine_Api::_()->document()->sslThumbnail($this->document->thumbnail) . '" class="thumb_normal" />' ?>
                    <?php endif; ?>

                    <h3><?php echo $this->document->document_title; ?></h3>
                    <p>   
                        <?php echo $this->translate('Created by') ?>
                        <strong><?php echo $this->document->getOwner()->getTitle(); ?></strong>
                    -  
                        <?php echo $this->timestamp($this->document->creation_date) ?>
                    </p>
                    <p>
                        <?php if ($this->document->category_id): ?>
                            <?php $category = Engine_Api::_()->getDbtable('categories', 'document')->getCategory($this->document->category_id); ?>
                            <?php echo $this->translate('Category:'); ?> <?php echo $category->category_name ?>
                        <?php endif; ?> 
                    </p>
                </a>
            </li>
    </ul>
</div>