<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<ul class="sitepage_sidebar_list">
  <?php foreach ($this->paginator as $sitepagedocument): ?>
    <?php  $this->partial()->setObjectKey('sitepagedocument');
        echo $this->partial('application/modules/Sitepagedocument/views/scripts/partialWidget.tpl', $sitepagedocument);
		?>
          <?php echo $this->translate(array('%s view', '%s views', $sitepagedocument->views), $this->locale()->toNumber($sitepagedocument->views)) ?>
        </div>
        <div class='sitepage_sidebar_list_details'>	
          <?php if (($sitepagedocument->rating > 0) && ($this->show_rate == 1)): ?>

            <?php
            $currentRatingValue = $sitepagedocument->rating;
            $difference = $currentRatingValue - (int) $currentRatingValue;
            if ($difference < .5) {
              $finalRatingValue = (int) $currentRatingValue;
            } else {
              $finalRatingValue = (int) $currentRatingValue + .5;
            }
            ?>

            <?php for ($x = 1; $x <= $sitepagedocument->rating; $x++): ?><span class="rating_star_big_generic rating_star sitepage-rating-star" title="<?php echo $finalRatingValue ?> rating"></span><?php endfor; ?><?php if ((round($sitepagedocument->rating) - $sitepagedocument->rating) > 0): ?><span class="rating_star_big_generic rating_star_half sitepage-rating-star" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>"></span><?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>