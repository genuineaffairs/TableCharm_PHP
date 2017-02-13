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

<?php if ($this->paginator->getTotalItemCount()): ?>
  <form id='filter_form_page' class='global_form_box' method='get' action='<?php echo $this->url(array(), 'sitepagedocument_browse', true) ?>' style='display: none;'>
    <input type="hidden" id="page" name="page"  value=""/>
  </form>

<div class="sm-content-list">
  <ul class="seaocore_browse_list" data-role="listview" data-inset="false" >
    <?php foreach ($this->paginator as $sitepagedocument): ?>
      <li data-icon="arrow-r">
        <a href="<?php echo $sitepagedocument->getHref(); ?>" >
          <?php if(false):?>
              <?php if(!empty($sitepagedocument->thumbnail)): ?>
              <?php if($this->https):?>
              <?php $sitepagedocument->thumbnail = $this->baseUrl().'/'.$this->manifest_path."/ssl?url=".urlencode($sitepagedocument->thumbnail); ?>
              <?php endif; ?>
              <?php echo '<img src="'. $sitepagedocument->thumbnail .'" />' ?>
              <?php else: ?>
              <?php echo '<img src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitepagedocument/externals/images/sitepagedocument_thumb.png" />' ?>
              <?php endif;?>
          <?php endif;?>       
          <h3><?php echo $sitepagedocument->sitepagedocument_title; ?></h3>
          <p><?php echo $this->translate("in ") ?>
            <strong><?php echo $sitepagedocument->page_title ?> </strong>             
          </p>
          <p>
          <span class="list_rating_star">
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

              <?php for ($x = 1; $x <= $sitepagedocument->rating; $x++): ?><span class="rating_star_generic rating_star" title="<?php echo $finalRatingValue ?> <?php echo $this->translate('rating'); ?>"></span><?php endfor; ?><?php if ((round($sitepagedocument->rating) - $sitepagedocument->rating) > 0): ?><span class="rating_star_generic rating_star_half" title="<?php echo $finalRatingValue . $this->translate("rating"); ?>"></span><?php endif; ?>
            <?php endif; ?>
          </span>
          </p>
          <p>
            <?php echo $this->translate('Created by') ?>
            <strong><?php echo $sitepagedocument->getOwner()->getTitle(); ?></strong>
            -
          <?php echo $this->timestamp($sitepagedocument->creation_date) ?>
          </p>
          
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
  </div>
<?php if( $this->paginator->count() > 1 ): ?>
		<?php echo $this->paginationControl($this->paginator, null, null, array(
			'query' => $this->formValues,
		)); ?>
	<?php endif; ?>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no search results to display.'); ?>
    </span>
  </div>
<?php endif; ?> 

<script type="text/javascript">
//  var pageAction = function(page){
//    var form;
//    if($('#filter_form')) {
//      form=document.getElementById('filter_form');
//    }else if($('#filter_form_page')){
//      form=$('#filter_form_page');
//    }
//    form.elements['page'].value = page;
//    
//    form.submit();
//  } 
</script>