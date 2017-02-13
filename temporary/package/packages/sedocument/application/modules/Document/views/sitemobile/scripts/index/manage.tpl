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
    <?php if ($this->paginator->count() > 0): ?> 
        <div class="sm-content-list">
            <ul class="seaocore_browse_list" data-role="listview" data-inset="false" >
                <?php foreach ($this->paginator as $document): ?>
                    <li data-icon="arrow-r">
                        <a href="<?php echo $document->getHref(); ?>" data-ajax="false">
                            <?php if (!empty($document->photo_id)): ?>
                                <?php echo $this->itemPhoto($document, 'thumb.normal') ?>
                            <?php elseif (!empty($document->thumbnail)): ?>
                                <?php echo '<img src="' . Engine_Api::_()->document()->sslThumbnail($document->thumbnail) . '" class="thumb_normal" />' ?>
                            <?php else: ?>
                                <?php '<img src="application/modules/Document/externals/images/document_thumb.png" class="thumb_normal" />' ?>
                            <?php endif; ?>
                            <h3><?php echo $document->document_title; ?></h3>
                            <?php if (($document->rating > 0) && ($this->show_rate == 1)): ?>
                                <?php
                                $currentRatingValue = $document->rating;
                                $difference = $currentRatingValue - (int) $currentRatingValue;
                                if ($difference < .5) {
                                    $finalRatingValue = (int) $currentRatingValue;
                                } else {
                                    $finalRatingValue = (int) $currentRatingValue + .5;
                                }
                                ?>
                                <span class="list_rating_star">
                                    <?php for ($x = 1; $x <= $document->rating; $x++): ?>
                                        <span class="rating_star_generic rating_star" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
                                        </span>
                                    <?php endfor; ?>
                                    <?php if ((round($document->rating) - $document->rating) > 0): ?>
                                        <span class="rating_star_generic rating_star_half" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
                                        </span>
                                    <?php endif; ?>
                                </span>	
                            <?php endif; ?>

                            <?php if ($document->status == 0): ?>
                                <div class="document_alert-message">
                                    <?php echo $this->htmlImage('application/modules/Document/externals/images/document_wait.gif', '', array('class' => 'icon')) ?>
                                    <?php echo $this->translate("Document format conversion in progress.") ?>
                                </div>
                            <?php elseif ($document->status == 2): ?>
                                <div class="document_alert-message">
                                    <?php echo $this->htmlImage('application/modules/Document/externals/images/document_alert16.gif', '', array('class' => 'icon')) ?>
                                    <?php echo $this->translate("Format conversion for this document failed.") ?>
                                </div>
                            <?php elseif ($document->status == 3): ?>
                                <?php if (empty($this->can_edit) || empty($this->can_delete)): ?>
                                    <div class="document_alert-message">
                                        <?php echo $this->htmlImage('application/modules/Document/externals/images/document_alert16.gif', '', array('class' => 'icon')) ?>
                                        <?php echo $this->translate("This document has been deleted at Scribd") ?>
                                    </div>
                                <?php else: ?>
                                    <div class="document_alert-message">
                                        <?php echo $this->htmlImage('application/modules/Document/externals/images/document_alert16.gif', '', array('class' => 'icon')) ?>
                                        <?php echo $this->translate('This document has been deleted at Scribd.'); ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <p>
                                <?php echo $this->translate('Created by') ?>
                                <strong><?php echo $document->getOwner()->getTitle(); ?></strong>
                                -
                                <?php echo $this->timestamp($document->creation_date) ?>
                            </p>
                            <?php if ($document->category_id): ?>
                                <p>
                                    <?php $category = Engine_Api::_()->getDbtable('categories', 'document')->getCategory($document->category_id); ?>
                                    <?php echo $this->translate('Category:'); ?><?php echo $category->category_name ?> 
                                </p>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif ($this->search || $this->category || $this->draft): ?>
        <div class="tip">
            <span>
                <?php echo $this->translate('You do not have any documents matching your search criteria.'); ?>
            </span>
        </div>
    <?php else: ?>
        <div class="tip">
            <span>
                <?php echo $this->translate('You do not have any documents.'); ?>
            </span>
        </div>
    <?php endif; ?>
    <div>
        <?php echo $this->paginationControl($this->paginator, null, null, array('query' => $this->formValues, 'pageAsQuery' => true,)); ?>
    </div>
