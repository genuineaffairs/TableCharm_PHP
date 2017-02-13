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
<div class='resume_profile_related_resumes'>
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
      <ul class='resumes_rows'>
        <?php foreach ($this->paginator as $resume): ?>
          <li class="resume_record_<?php echo $resume->getIdentity();?>">
            <div class="resume_score">
              <?php echo $this->partial('index/_vote_points.tpl', 'resume', array('resume' => $resume))?>
            </div>
            <?php if ($resume->photo_id): ?>
              <div class="resume_photo">
                <?php echo $this->htmlLink($resume->getHref(), $this->itemPhoto($resume, 'thumb.icon'));?>
              </div>
            <?php endif;?>            
            <div class="resume_content">
              <div class="resume_title">
                <?php echo $this->partial('index/_title.tpl', 'resume', array('resume' => $resume))?>
              </div>
              <div class="resume_description">
                <?php echo $this->partial('index/_description.tpl', 'resume', array('resume' => $resume))?>
              </div>
              <div class="resume_details">
                <?php echo $this->partial('index/_details.tpl', 'resume', array('resume' => $resume))?>
              </div>
              <div class="resume_meta">
                <?php echo $this->partial('index/_meta.tpl', 'resume', array('resume' => $resume))?>
              </div>

            </div>
          </li>
        <?php endforeach; ?>  
      </ul>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('There are no tagging related resumes for this resume.');?>
      </span>
    </div>
  <?php endif; ?>    
</div>