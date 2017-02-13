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

  <?php $this->headScript()->appendFile('application/modules/Radcodes/externals/scripts/ticker.js') ?>
  
  <div class="resume_sponsored_resumes">
    <ul id="<?php echo $this->widget_name?>" class="resume_sponsored_slides">
      <?php foreach ($this->paginator as $resume): ?>
        <li>
          <?php if ($this->showphoto && $resume->photo_id): ?>
            <div class="resume_photo">
              <?php echo $this->htmlLink($resume->getHref(), $this->itemPhoto($resume, 'thumb.normal'));?>
            </div>   
          <?php endif; ?>         
          <div class="resume_content">
            <div class="resume_title">
              <?php echo $this->partial('index/_title.tpl', 'resume', array('resume' => $resume, 'max_title_length' => 26))?>
            </div>
            <?php if ($this->showdetails): ?>
              <div class="resume_details">
                <?php echo $this->partial('index/_details.tpl', 'resume', array('resume' => $resume))?>
              </div>
            <?php endif; ?>
            <?php if ($this->showdescription && $resume->getDescription()): ?>
              <div class="resume_description">
                <?php echo $this->radcodes()->text()->truncate($resume->getDescription(), 128); ?>
              </div>
            <?php endif; ?>  
            <?php if ($this->showmeta): ?>
              <div class="resume_meta">
                <?php echo $this->partial('index/_meta.tpl', 'resume', array('resume' => $resume, 'show_comments' => false, 'show_likes' => false, 'show_views' => false))?>
              </div>
            <?php endif; ?>
          </div>
        </li>  
      <?php endforeach; ?>  
    </ul>
    
  </div>

  <?php if ($this->use_slideshow): ?>
    <script type="text/javascript">
    en4.core.runonce.add(function(){
    	<?php echo $this->widget_name?>Ticker = new radcodesNewsTicker('<?php echo $this->widget_name?>', {speed:1000,delay:15000,direction:'vertical'});
    });
    </script>
    <div class="resume_sponsored_resumes_action">
      <a href="javascript: void(0);" onclick="<?php echo $this->widget_name?>Ticker.next(); return false;"><?php echo $this->translate("Next &raquo;")?></a>
    </div>    
  <?php endif; ?>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no sponsored resumes.');?>
    </span>
  </div>
<?php endif; ?>