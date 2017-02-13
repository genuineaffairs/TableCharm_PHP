<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1);?>
<div class="sm-content-list">
	<ul id="profile_pages" data-role="listview" data-icon="arrow-r">
		<?php foreach ($this->paginator as $item): ?>
			<li>
				<a href="<?php echo $item->getHref(); ?>">
					<?php echo $this->itemPhoto($item, 'thumb.icon'); ?>
					<h3><?php echo $item->getTitle() ?></h3>
          <p>
            <?php if ($this->ratngShow): ?>
              <?php if (($item->rating > 0)): ?>
                <?php
                $currentRatingValue = $item->rating;
                $difference = $currentRatingValue - (int) $currentRatingValue;
                if ($difference < .5) {
                  $finalRatingValue = (int) $currentRatingValue;
                } else {
                  $finalRatingValue = (int) $currentRatingValue + .5;
                }
                ?>
                <span title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
                  <?php for ($x = 1; $x <= $item->rating; $x++): ?>
                    <span class="rating_star_generic rating_star" ></span>
                  <?php endfor; ?>
                  <?php if ((round($item->rating) - $item->rating) > 0): ?>
                    <span class="rating_star_generic rating_star_half" ></span>
                  <?php endif; ?>
                </span>
              <?php endif; ?>
            <?php endif; ?>
          </p>
          <p>
            <?php if($postedBy):?><?php echo $this->translate('posted by'); ?>
								<strong><?php echo $item->getOwner()->getTitle() ?></strong> - 
						<?php endif; ?>
					 <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
          </p>
					<p class="ui-li-aside">
            <?php if ($item->closed): ?>
               <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/close.png', '', array('class' => 'icon', 'title' => $this->translate('Closed'))) ?>
            <?php endif; ?>
						<?php if ($item->sponsored == 1): ?>
							<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
						<?php endif; ?>
						<?php if ($item->featured == 1): ?>
							<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
						<?php endif; ?>
					</p>
          <p>
						<?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?> - 
						<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) : ?>
							<?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.member.title' , 1);
							if ($item->member_title && $memberTitle) : ?>
									<?php if ($item->member_count == 1) : ?>
										<?php echo $item->member_count . ' member'; ?> - 
									<?php  else: ?>
										<?php echo $item->member_count . ' ' . $item->member_title; ?> -
									<?php endif; ?>
							<?php else : ?>
									<?php echo $this->translate(array('%s member', '%s members', $item->member_count), $this->locale()->toNumber($item->member_count)) ?> -
							<?php endif; ?>
						<?php endif; ?>
						<?php $sitepagereviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview'); ?>
						<?php if ($sitepagereviewEnabled): ?>
							<?php echo $this->translate(array('%s review', '%s reviews', $item->review_count), $this->locale()->toNumber($item->review_count)) ?> - 
						<?php endif; ?>
            <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?> - 
            <?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>
          </p>
          <p>
						<?php if (!empty($item->page_owner_id)) : ?>
							<?php if ($item->page_owner_id == $item->owner_id) : ?>
								<i class="icon_sitepages_page-owner"><?php echo $this->translate("PAGEMEMBER_OWNER"); ?></i>
							<?php  else: ?>
								<i class="icon_sitepage_member"><?php echo $this->translate("PAGEMEMBER_MEMBER"); ?></i>
							<?php endif; ?>
						<?php endif; ?>
          </p>
				</a> 
			</li>
		<?php endforeach; ?>
	</ul>
	<?php if ($this->paginator->count() > 1): ?>
		<?php
		echo $this->paginationAjaxControl(
						$this->paginator, $this->identity, 'profile_pages', array('pageAdmin' => $this->pageAdmin, "category_id" => $this->category_id));
		?>
	<?php endif; ?>
</div>