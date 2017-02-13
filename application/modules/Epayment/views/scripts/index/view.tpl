<?php


?>



<div class='layout_right'>
  <div class='epayments_gutter'>
  
    <div class='epayments_gutter_owner'>
      <?php echo $this->htmlLink($this->owner->getHref(), $this->itemPhoto($this->owner, 'thumb.profile'), array('class'=>'epayments_gutter_owner_photo')) ?>
      <div class='epayments_gutter_owner_name'>
        <?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle(), array('class'=>'epayments_gutter_name')); ?>      
      </div>
    </div>  
  
    <ul class='epayments_gutter_options'>

      <?php if ($this->canEdit):?>
      <li>
        <?php echo $this->htmlLink($this->epayment->getEditHref(), $this->translate('Edit This Feed'), array('class'=>'buttonlink icon_epayment_edit')); ?>
      </li>
      <?php endif; ?>  

      <?php if( $this->canDelete ): ?>
      <li>
        <?php echo $this->htmlLink($this->epayment->getDeleteHref(), $this->translate('Delete This Feed'), array('class'=>'buttonlink icon_epayment_delete')); ?>
      </li>
      <?php endif; ?>

    </ul>
    
    <h4><?php echo $this->translate('Feed Details')?></h4>
    <ul class='epayment_gutter_details'>
      <li class='epayment_stats_title'><?php echo $this->epayment->getTitle(); ?></li>
      <?php if (null !== ($category = $this->epayment->getCategory())): ?>
        <li class='epayment_stats_category'><?php echo $this->htmlLink($category->getHref(), $this->translate($category->getTitle())) ?></li>
      <?php endif; ?>
      <?php if ($this->epayment->getDescription()): ?>
        <li class='epayment_stats_description'><?php echo $this->viewMore($this->epayment->getDescription()); ?></li>
      <?php endif;?>
      <li class='epayment_stats_info'>
        <ul>
          <li><?php echo $this->translate(array("%s view", "%s views", $this->epayment->view_count), $this->epayment->view_count); ?></li>
          <li><?php echo $this->translate('Last updated %s', $this->timestamp($this->epayment->modified_date)); ?>
        </ul>
      </li>
      <li class='epayment_stats_source'>
        <?php 
          $uri_source = str_replace('www.','',strtolower(parse_url($this->epayment->uri,PHP_URL_HOST)));
          $uri_source = $this->radcodes()->text()->truncate($uri_source, 20);
          echo $this->translate('Source: %s', 
          $this->htmlLink($this->epayment->uri, $uri_source, array('target'=>'_blank')) 
        );?>
      </li>
    </ul>
    
    <?php $tag_maps = $this->epayment->tags()->getTagMaps(); ?>
    <?php if (count($tag_maps)): ?>
    <h4><?php echo $this->translate('Feed Tags'); ?></h4>
    <ul class='epayment_gutter_tags'>
      <?php foreach ($tag_maps as $tag_map): $tag = $tag_map->getTag();?>
        <li>
          <?php echo $this->htmlLink(array('route' => 'epayment_browse', 'tag' => $tag->tag_id), '#'.$tag->text)?>
        </li>
      <?php endforeach; ?>
    </ul>
    <?php endif;?>


    <?php if (count($this->paginatorEpayments)): ?>
      <h4><?php echo $this->translate('Other Feeds'); ?></h4>
      <ul class='epayment_gutter_others'>
        <?php foreach ($this->paginatorEpayments as $epayment): ?>
          <li>
            <?php echo $this->htmlLink($epayment->getHref(), $epayment->getTitle(), array('class'=>'epayment_title')); ?>
            <span class='epayment_gutter_others_stat'>
            <?php echo $this->timestamp(strtotime($epayment->creation_date)); ?>
            - <?php echo $this->translate(array("%s comment", "%s comments", $epayment->comment_count), $epayment->comment_count); ?>  
            </span>       
          </li>
        <?php endforeach; ?>
      </ul>
      <ul class='epayments_gutter_options'>
        <li>
          <?php echo $this->htmlLink(array('route'=>'epayment_browse','user'=>$this->owner->getIdentity()),
            $this->translate('%s\'s Feeds', $this->owner->getTitle()),
            array('class'=>'buttonlink icon_epayment_list')
          )?>
      </ul>    
    <?php endif;?>
    
    
  </div>
</div>



<div class="layout_middle">

  <h2>
    <?php echo $this->translate('%s\'s Feeds', $this->htmlLink($this->owner, $this->owner->getTitle())) ?>
  </h2>

  <?php if ($this->feed instanceof Zend_Feed_Abstract): ?>
    <div class='epayments_view'>
  
      <div class='epayment_head'>
  
        <?php if ($image = $this->feed->image()): ?>
          <div class='epayment_image'>
            <?php echo $this->htmlLink($this->feed->image->link(), 
              $this->htmlImage($this->feed->image->url(), $this->feed->image->title())
              ); ?>
          </div>
        <?php endif; ?>
        <h3>
          <?php $title = $this->feed->title(); ?>
          <?php echo $title ? $title : $this->epayment->getTitle(); ?>
        </h3>
        <div class="epayment_meta">
          <?php if ($published = $this->feed->pubDate()): ?>
            <span><?php echo $this->translate('Published: %s', $published); ?></span>
          <?php endif; ?>
          <?php if ($copyright = $this->feed->copyright()): ?>
            <span><?php echo $copyright; ?></span>
          <?php endif; ?>
        </div>    
        <div class="epayment_desc">
          <?php echo $this->feed->description(); ?>
        </div>
      </div>
      
      <ul class='epayment_items'>
      <?php foreach ($this->feed as $item): ?>
        <li>
          <div class='epayment_item_title'><?php echo $this->htmlLink($item->link(), $item->title(), array('target'=>'_blank')); ?></div>
          <?php if ($pubDate = $item->pubDate()): ?>
            <div class='epayment_item_pubdate'><?php echo $pubDate ?></div>
          <?php endif; ?>
          <?php if ($description = $item->description()): ?>
            <div class='epayment_item_description'><?php echo $description; ?></div>
          <?php endif;?>
        </li>
      <?php endforeach; ?>
      </ul>
      
      <?php echo $this->action("list", "comment", "core", array("type"=>"epayment", "id"=>$this->epayment->epayment_id)) ?>
      
    </div>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate(($this->feed instanceof Zend_Feed_Exception) ? $this->feed->getMessage() : 'Feed failed to load and/or parsed properly.');?>
      </span>
    </div>
  <?php endif; ?>


</div>
