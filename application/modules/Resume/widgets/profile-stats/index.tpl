<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
?>
<?php $resume = $this->resume; 
$category = $resume->getCategory();
?>
<div class='resume_profile_stats'>
  <ul>
    <li><?php echo $this->translate('Category: %s', $this->htmlLink($category->getHref(), $this->translate($category->getTitle())))?></li>
    <?php if (count($tagMaps = $this->resume->tags()->getTagMaps()) > 0): ?>
      <?php $tagResumes = array(); ?>
      <?php foreach ($tagMaps as $tagMap): $tag = $tagMap->getTag()?>
        <?php if (!empty($tag->text)):?>
          <?php $tagResumes[] = '#'.$this->htmlLink(array('route'=>'resume_general', 'action'=>'browse', 'tag'=>$tag->tag_id), $tag->text); ?>
        <?php endif; ?>
      <?php endforeach; ?>
      <li><?php echo $this->translate('Tags: %s', join(" ", $tagResumes));?></li>
    <?php endif; ?>  
    <li><?php echo $this->translate('By: %s', $this->resume->getOwner()->toString())?></li>
    <li><?php echo $this->translate('Posted: %s', $this->timestamp($this->resume->creation_date))?></li>
    <?php if ($this->resume->creation_date != $this->resume->modified_date): ?>
      <li><?php echo $this->translate('Updated: %s', $this->timestamp($this->resume->modified_date))?></li>
    <?php endif; ?>
    <!--<li><?php echo $this->translate(array("%s comment", "%s comments", $this->resume->comment_count), $this->locale()->toNumber($this->resume->comment_count)); ?></li>-->
    <!--<li><?php echo $this->translate(array('%1$s like', '%1$s likes', $this->resume->like_count), $this->locale()->toNumber($this->resume->like_count)); ?></li>-->
    <li><?php echo $this->translate(array('%s view', '%s views', $this->resume->view_count), $this->locale()->toNumber($this->resume->view_count)); ?></li>
  </ul>
</div>