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
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<?php if($this->documentSitepageTotal): ?>
		<div class="sitepagedocument_view_sidebar generic_layout_container">
			<h3><?php echo $this->htmlLink($this->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($this->page_id), 'tab' => $this->tab_selected_id), 'sitepage_entry_view'), $this->sitepage_subject->title, array()) ?><?php echo $this->translate("'s Documents")?></h3>
			<ul class="sitepage_sidebar_list">
				<?php $count = 1;?>
			  	<?php foreach( $this->documentSitepage as $documentSitepage ): ?>
			  		<?php if($count>2):?>
			  			<li class="bold">
				  			<?php echo $this->htmlLink($this->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($this->page_id), 'tab' => $this->tab_selected_id), 'sitepage_entry_view'), $this->translate('More &raquo;'), array('class'=>'fright')) ?>
			  			</li>
			  			<?php break;?>
			  		<?php endif;?>
				    <li>
							<?php if($this->https):?> 
								<?php $documentSitepage->thumbnail = $this->baseUrl().'/'.$this->manifest_path."/ssl?url=".urlencode($documentSitepage->thumbnail);?>
							<?php endif; ?>

				       <?php echo $this->htmlLink($documentSitepage->getHref(), '<img src="'. $documentSitepage->thumbnail .'" class="sitepagedocument_thumb thumb_icon"  />', array('title' => $documentSitepage->sitepagedocument_title) ) ?>
				      <div class='sitepage_sidebar_list_info'>
				       	<div class='sitepage_sidebar_list_title'>
									<?php if(Engine_Api::_()->getApi('settings', 'core')->sitepagedocument_title_truncation): ?>
										<?php echo $this->htmlLink($documentSitepage->getHref(), $documentSitepage->truncateText($documentSitepage->sitepagedocument_title, 13), array('title' => $documentSitepage->sitepagedocument_title)) ?>
									<?php else:?>
										<?php echo $this->htmlLink($documentSitepage->getHref(), $documentSitepage->sitepagedocument_title, array('title' => $documentSitepage->sitepagedocument_title)) ?>
									<?php endif;?>
				        </div>
				        <div class='sitepage_sidebar_list_details'>
				        	<?php echo $this->translate(array('%s comment', '%s comments', $documentSitepage->comment_count), $this->locale()->toNumber($documentSitepage->comment_count)) ?> |
				        	<?php echo $this->translate(array('%s view', '%s views', $documentSitepage->views), $this->locale()->toNumber($documentSitepage->views)) ?>
				        </div>
				        <div class='sitepage_sidebar_list_details'>	
				        	<?php if(( $documentSitepage->rating > 0) && ($this->can_rate == 1)):?>
	          			<?php for($x=1; $x<= $documentSitepage->rating; $x++): ?><span class="rating_star_big_generic rating_star sitepage-rating-star" title="<?php echo $documentSitepage->rating.$this->translate("rating"); ?>"></span><?php endfor; ?><?php if((round( $documentSitepage->rating)- $documentSitepage->rating)>0):?><span class="rating_star_big_generic rating_star_half sitepage-rating-star" title="<?php echo $documentSitepage->rating.$this->translate("rating"); ?>" ></span><?php endif; ?>


	        		<?php endif; ?>
				        </div>
				      </div>
				    </li>
			    <?php $count++ ; ?>
			  <?php endforeach; ?>
			</ul>
		</div>
  <?php endif; ?>