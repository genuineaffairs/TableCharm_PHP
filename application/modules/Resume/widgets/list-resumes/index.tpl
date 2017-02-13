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
<?php if ($this->paginator->getTotalItemCount()): ?>
  
  <?php if ($this->display_style == 'narrow'): ?>
    <ul class='resumes_list'>
      <?php foreach ($this->paginator as $resume): ?>
        <li>
          <?php if ($this->showphoto && $resume->photo_id): ?>
            <div class="resume_photo">
              <?php echo $this->htmlLink($resume->getHref(), $this->itemPhoto($resume, 'thumb.icon'));?>
            </div>
          <?php endif; ?>
          <div class="resume_content">
            <div class="resume_title">
              <?php echo $this->htmlLink($resume->getHref(), $this->radcodes()->text()->truncate($resume->getTitle(), 24))?>
            </div>
            <?php if ($this->showdetails): ?>
              <div class="resume_details">
                <?php echo $this->partial('index/_details.tpl', 'resume', array('resume' => $resume))?>
              </div>
            <?php endif; ?>
            <?php if ($this->showdescription && $resume->getDescription()): ?>
              <div class="resume_description">
                <?php echo $this->partial('index/_description.tpl', 'resume', array('resume' => $resume))?>
              </div>
            <?php endif; ?>
            <?php if ($this->showmeta): ?>
              <div class="resume_meta">
                <?php 
                  $meta_options = array('resume' => $resume,
                    'show_comments' => $this->order == 'mostcommented' ? true : false,
                    'show_views' => $this->order == 'mostviewed' ? true : false,
                    'show_likes' => $this->order == 'mostliked' ? true : false,
                  );
                ?>
                <?php echo $this->partial('index/_meta.tpl', 'resume', $meta_options)?>
              </div>
            <?php endif; ?>
          </div>
        </li>
      <?php endforeach; ?>  
    </ul>

  <?php else: ?>
    <ul class="resumes_rows">
	    <?php foreach( $this->paginator as $resume ): $user = $resume->getOwner(); ?>
	      <li>
          <?php if ($this->showphoto): ?>
            <div class="resume_photo">
              <?php echo $this->htmlLink($resume->getHref(), $this->itemPhoto($resume, 'thumb.normal'));?>
            </div>
          <?php endif; ?>
          <div class="resume_content">
            <div class="resume_title">
              <?php echo $this->partial('index/_title.tpl', 'resume', array('resume' => $resume))?>
            </div>
            <?php if ($this->showdetails): ?>
              <div class="resume_details">
                <?php echo $this->partial('index/_details.tpl', 'resume', array('resume' => $resume))?>
              </div>
            <?php endif; ?>
            <?php if ($this->showdescription && $resume->getDescription()): ?>
              <div class="resume_description">
                <?php echo $this->partial('index/_description.tpl', 'resume', array('resume' => $resume))?>
              </div>
            <?php endif; ?>
            <?php if ($this->showmeta): ?>
              <div class="resume_meta">
                <?php echo $this->partial('index/_meta.tpl', 'resume', array('resume' => $resume))?>
              </div>
            <?php endif; ?>
          </div>
	      </li>
	    <?php endforeach; ?>
    </ul>
  <?php endif;?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no resumes posted yet.');?>
    </span>
  </div>  
<?php endif; ?>