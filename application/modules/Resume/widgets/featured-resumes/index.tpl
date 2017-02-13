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

  <?php $this->headScript()->appendFile('application/modules/Radcodes/externals/scripts/slideshow.js') ?>
  
  <div class="resume_featured_resumes">
    <div class="resume_featured_mask">
      <div id="<?php echo $this->widget_name?>" class="resume_featured_slides">
        <?php foreach ($this->paginator as $resume): ?>
          <div class="resume_featured_slide resume_record_<?php echo $resume->getIdentity();?>">
            <?php if ($this->showphoto && $resume->photo_id): ?>
              <div class="resume_photo">
                <?php echo $this->htmlLink($resume->getHref(), $this->itemPhoto($resume, 'thumb.normal'));?>
              </div>   
            <?php endif; ?>         
            <div class="resume_content">
              <div class="resume_title">
                <?php echo $this->partial('index/_title.tpl', 'resume', array('resume' => $resume, 'max_title_length' => 42))?>
              </div>
              <?php if ($this->showdetails): ?>
                <div class="resume_details">
                  <?php echo $this->partial('index/_details.tpl', 'resume', array('resume' => $resume))?>
                </div>
              <?php endif; ?>
              <?php if ($this->showdescription && $resume->getDescription()): ?>
                <div class="resume_description">
                  <?php echo $this->radcodes()->text()->truncate($resume->getDescription(), 200); ?>
                  <?php //echo $this->partial('index/_description.tpl', 'resume', array('resume' => $resume))?>
                </div>
              <?php endif; ?>  
              <?php if ($this->showmeta): ?>
                <div class="resume_meta">
                  <?php echo $this->partial('index/_meta.tpl', 'resume', array('resume' => $resume))?>
                </div>
              <?php endif; ?>
            </div>
          </div>  
        <?php endforeach; ?>  
      </div>
    </div>
    <?php if ($this->use_slideshow): ?>
      <p class="radcodes_slideshow_buttons" id="<?php echo $this->widget_name?>_buttons">
        <span id="<?php echo $this->widget_name?>_prev" class="radcodes_slideshow_button_prev"><span><?php echo $this->translate('&lt;&lt; Previous'); ?></span></span>
        <span id="<?php echo $this->widget_name?>_play" class="radcodes_slideshow_button_play"><span><?php echo $this->translate('Play &gt;'); ?></span></span>
        <span id="<?php echo $this->widget_name?>_stop" class="radcodes_slideshow_button_stop"><span><?php echo $this->translate('Stop'); ?></span></span>
        <span id="<?php echo $this->widget_name?>_next" class="radcodes_slideshow_button_next"><span><?php echo $this->translate('Next &gt;&gt;'); ?></span></span>
      </p>      
    <?php endif; ?>
  </div>
  <?php if ($this->use_slideshow): ?>
    <script type="text/javascript">
    en4.core.runonce.add(function(){
      var <?php echo $this->widget_name?>_width = $('<?php echo $this->widget_name?>').getSize().x;
      
      $$('#<?php echo $this->widget_name?> div.resume_featured_slide').each(function(el){
        el.setStyle('width', <?php echo $this->widget_name?>_width - 30);
      });
    	var <?php echo $this->widget_name?> = new radcodesNoobSlide({
    		box: $('<?php echo $this->widget_name?>'),
    		items: $$('#<?php echo $this->widget_name?> div.resume_featured_slide'),
    		size: <?php echo $this->widget_name?>_width,
    		autoPlay: true,
    		interval: 8000,
    		addButtons: {
    			previous: $('<?php echo $this->widget_name?>_prev'),
    			play: $('<?php echo $this->widget_name?>_play'),
    			stop: $('<?php echo $this->widget_name?>_stop'),
    			next: $('<?php echo $this->widget_name?>_next')
    		},
    		onWalk: function(currentItem,currentHandle){
    		}
    	});
    });
    </script>
  <?php endif; ?>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no featured resumes.');?>
    </span>
  </div>
<?php endif; ?>