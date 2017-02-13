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
<?php $document_paginator = Zend_Registry::get('document_paginator'); ?>

<?php if ($this->paginator->count() > 0): ?>

    <form id='filter_form_browse_category' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'browse'), 'document_browse', true) ?>' style='display: none;'>
        <input type="hidden" id="category" name="category"  value=""/>
        <input type="hidden" id="category_id" name="category_id"  value=""/>
    </form>

    <div class="sm-content-list">
        <ul class="seaocore_browse_list" data-role="listview" data-inset="false" >
            <?php foreach ($this->paginator as $document): ?>
                <li data-icon="arrow-r">
                    <a href="<?php echo $document->getHref(); ?>" >
                        <?php if (!empty($document->photo_id)): ?>
                            <?php echo $this->itemPhoto($document, 'thumb.normal'); ?>
                        <?php elseif (!empty($document->thumbnail)): ?>
                            <?php echo '<img src="' . Engine_Api::_()->document()->sslThumbnail($document->thumbnail) . '" class="thumb_normal" />'; ?>
                        <?php else: ?>
                            <?php echo '<img src="application/modules/Document/externals/images/document_thumb.png" class="thumb_normal" />'; ?>
                        <?php endif; ?>

                        <h3><?php echo $document->document_title; ?></h3>
                        <?php if (($document->rating > 0) && ($this->show_rate == 1)): ?>
                            <p>
                                <span class="list_rating_star">
                                    <?php
                                    $currentRatingValue = $document->rating;
                                    $difference = $currentRatingValue - (int) $currentRatingValue;
                                    if ($difference < .5) {
                                        $finalRatingValue = (int) $currentRatingValue;
                                    } else {
                                        $finalRatingValue = (int) $currentRatingValue + .5;
                                    }
                                    ?>						
                                    <?php for ($x = 1; $x <= $document->rating; $x++): ?>
                                        <span class="rating_star_generic rating_star" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
                                        </span>
                                    <?php endfor; ?>
                                    <?php if ((round($document->rating) - $document->rating) > 0): ?>
                                        <span class="rating_star_generic rating_star_half" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>"> </span>
                                    <?php endif; ?>
                                </span>
                            </p>
                        <?php endif; ?>



                        <?php
                        if (empty($document_paginator)) {
                            exit();
                        }
                        ?>
                        <p>
                            <?php echo $this->translate('Created by') ?>
                            <strong><?php echo $document->getOwner()->getTitle(); ?></strong>
                            -
                            <?php echo $this->timestamp($document->creation_date) ?>
                        </p>

                        <?php if ($document->category_id): ?>
                            <p>
                                <?php $category = Engine_Api::_()->getDbtable('categories', 'document')->getCategory($document->category_id); ?>
                                <?php echo $this->translate('Category:'); ?> <?php echo $category->category_name ?> 
                            </p>
                        <?php endif; ?> 
</a>
                </li>

            <?php endforeach; ?>
            <?php
            if (empty($this->current_api)) {
                echo $this->translate($this->document_current_api);
            }
            ?>
        </ul>
    </div>
    <?php
    if (!empty($document_paginator)) {
        echo $this->paginationControl($this->paginator, null, null, array('query' => $this->formValues, 'pageAsQuery' => true,));
    } else {
        echo $this->translate($this->document_current_api);
    }
    ?>
<?php elseif ($this->search || $this->show || $this->category): ?>	
    <div class="tip">
        <span>
            <?php echo $this->translate('No documents were found matching your search criteria.'); ?>
        </span>
    </div>
<?php else: ?>
    <div class="tip">
        <span>
            <?php
            if (empty($document_paginator)) {
                exit();
            } else {
                echo $this->translate('Nobody has created a document yet.');
            }
            ?>
        </span>
    </div>	
<?php endif; ?>
