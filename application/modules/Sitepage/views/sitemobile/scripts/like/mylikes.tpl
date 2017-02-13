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
<?php
	$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/styles/sitepage-tooltip.css');
	$viewer = Engine_Api::_()->user()->getViewer()->getIdentity();
	$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	$MODULE_NAME = 'sitepage';
	$RESOURCE_TYPE = 'sitepage_page';
	$enableBouce = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.map.sponsored', 1);
	$currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
?>
	<div class="sitepage_view_select">
	<h3 class="sitepage_mypage_head"><?php echo $this->translate('Pages I Like'); ?></h3>
  </div>
<?php if ($this->paginator->count() > 0): ?>
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1);?>	 
<?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
<div class="sm-content-list">  
<ul data-role="listview" data-inset="false">
			<?php foreach ($this->paginator as $sitepage): ?>
				<li>
          <a href="<?php echo $sitepage->getHref();?>">
          <!--ADD PARTIAL VIEWS -->
            <?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitemobile_partial_views.tpl';?>
          
            <p><?php echo $this->translate(array('%s like', '%s likes', $sitepage->like_count), $this->locale()->toNumber($sitepage->like_count)) ?></p>
		      </a>
	      </li>
			<?php endforeach; ?>
</ul>
</div>
 <?php else: ?>
    <ul class="p_list_grid">
      <?php foreach ($this->paginator as $sitepage): ?>
        <li style="height:<?php echo $this->columnHeight ?>px;">
          <a href="<?php echo $sitepage->getHref(); ?>" class="ui-link-inherit">
            <div class="p_list_grid_top_sec">
              <div class="p_list_grid_img">
                <?php
                $url = $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/nophoto_page_thumb_profile.png';
                $temp_url = $sitepage->getPhotoUrl('thumb.profile');
                if (!empty($temp_url)): $url = $sitepage->getPhotoUrl('thumb.profile');
                endif;
                ?>
                <span style="background-image: url(<?php echo $url; ?>);"> </span>
              </div>
              <div class="p_list_grid_title">
                <span><?php echo $this->string()->chunk($this->string()->truncate($sitepage->getTitle(), 45), 10); ?></span>
              </div>
            </div>
          </a>
          <div class="p_list_grid_info">	                 
            <span class="fleft">
              <?php echo $this->timestamp(strtotime($sitepage->creation_date)) ?>
            </span>
              <?php if ($postedBy): ?>
            <span class="fright">
              <?php echo $this->translate('by ') . '<b>' . $sitepage->getOwner()->getTitle() . '</b>'; ?>
            </span> 
              <?php endif; ?>
            <span class="p_list_grid_stats">
              <?php echo $this->translate(array('%s like', '%s likes', $sitepage->like_count), $this->locale()->toNumber($sitepage->like_count)) ?>
            </span>                  
          </div>   
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
<?php if( $this->paginator->count() > 1 ): ?>
		<?php echo $this->paginationControl($this->paginator, null, null, array(
			'query' => $this->formValues,
		)); ?>
	<?php endif; ?>
  <?php else: ?>

  <div class="tip">
  		<span>
			<?php $translatebusiness = "<a href=".$this->url(array('action' => 'index'), 'sitepage_general', true).">" . $this->translate("Explore pages") . "</a>";
			echo $this->translate("You have not liked any pages yet.");?>
		</span>
	</div>
  <?php endif; ?>


