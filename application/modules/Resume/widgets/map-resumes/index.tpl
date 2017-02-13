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

<?php //if ($this->paginator->getTotalItemCount()): ?>
  
  <?php 
    $map_options = array(
      'height' => 400,
      'width'  => '100%', // 540
      'google_map' => 'resume_widget_map_resumes',
    );
  ?>
  
  <script type="text/javascript">
  en4.core.runonce.add(function(){
    $$('li.tab_layout_resume_map_resumes').addEvent('click', function(){
      radcodes_google_map_resume_widget_map_resumes_initialize();
    });
  });
  </script>  
  
  <div class="resume_widget_map_resumes_container">
    <?php echo $this->radcodes()->map()->items($this->paginator, $map_options); ?>
  </div>  
  
<?php // endif; ?>
