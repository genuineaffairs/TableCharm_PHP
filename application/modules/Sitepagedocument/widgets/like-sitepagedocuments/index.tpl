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
          <?php echo $this->translate(array('%s like', '%s likes', $sitepagedocument->like_count), $this->locale()->toNumber($sitepagedocument->like_count)) ?> |
          <?php echo $this->translate(array('%s view', '%s views', $sitepagedocument->views), $this->locale()->toNumber($sitepagedocument->views)) ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>