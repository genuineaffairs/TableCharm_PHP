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


  <?php if (!empty($this->tags)): ?>
  
      <h3 class="sep">
        <span><?php echo $this->translate('Popular Tags'); ?></span>
      </h3>    
  
      <div class="radcodes_popular_tags resumes_popular_tags">
        <ul>
        <?php foreach ($this->tags as $k => $tag): ?>
          <li><?php echo $this->htmlLink(array(
                      'route' => 'resume_general',
                      'action' => 'browse',
                      'tag' => $tag->tag_id),
            $tag->text, 
            array('class'=> "tag_x tag_$k")
          )?>
          <sup><?php echo $tag->total; ?></sup>
          </li>
        <?php endforeach; ?>
        </ul>
      </div>
  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('There are no resume tags yet.');?>
      </span>
    </div>
  <?php endif; ?>
  