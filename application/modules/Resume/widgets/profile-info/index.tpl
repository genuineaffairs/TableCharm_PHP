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
<?php 
$resume = $this->resume;
$category = $resume->getCategory();
?>

<div class='resume_profile_info'>
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

  </ul>
</div>