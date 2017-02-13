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
 
 $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Resume/externals/styles/resume.css');
?>


  <?php if( $this->tag || $this->keyword || $this->location || $this->user):?>
    <div class="resumes_result_filter_details">
      <?php echo $this->translate('Showing resumes posted'); ?>
      <?php if ($this->tag): ?>
        <?php echo $this->translate('tagging #%s', $this->htmlLink(
          $this->url(array('action'=>'browse', 'tag'=>$this->tag), 'resume_general', true),
          $this->tagObject ? $this->tagObject->text : $this->tag
        ));?>
      <?php endif; ?>
      <?php if ($this->keyword): ?>
        <?php echo $this->translate('with %s keyword', $this->htmlLink(
          $this->url(array('action'=>'browse', 'keyword'=>$this->keyword), 'resume_general', true),
          $this->keyword
        ));?>
      <?php endif; ?>
      <?php if ($this->location): ?>
        <?php echo $this->translate('in %s location', $this->htmlLink(
          $this->url(array('action'=>'browse', 'location'=>$this->location), 'resume_general', true),
          $this->location
        ));?>
      <?php endif; ?>
      <?php if ($this->user): ?>
        <?php if ($this->userObject instanceof User_Model_User): ?>
          <?php echo $this->translate('by %s', $this->userObject->toString()); ?>
        <?php else: ?>
          <?php echo $this->translate('by #%s', $this->user); ?>
        <?php endif; ?>
      <?php endif; ?>         
      <?php echo $this->htmlLink(array('action'=>'browse', 'route'=>'resume_general'), $this->translate('(x)'))?>
    </div>
  <?php endif; ?>
  
  <?php if ($this->paginator->getTotalItemCount() > 0): ?>
  
    <script type="text/javascript">
    en4.core.runonce.add(function(){
      $('resume_show_map_trigger').addEvent('click', function(){
    	  $('resumes_browse_list_container').hide();
        $('resumes_browse_map_container').show();
        radcodes_google_map_resumes_browse_map_initialize();
        return false;
      });
      $('resume_show_list_trigger').addEvent('click', function(){
        $('resumes_browse_map_container').hide();
        $('resumes_browse_list_container').show();
        return false;
      });
    });
    </script>   
    <ul class="resume_view_options">
      <li><a href="javascript:void(0);" id="resume_show_list_trigger"><span>List</span></a></li>
      <li><a href="javascript:void(0);" id="resume_show_map_trigger"><span>Map</span></a></li>
    </ul>
  <?php endif; ?>  
  
  <h3 class="sep resume_main_header">
    <span>
      <?php if ($this->categoryObject instanceof Resume_Model_Category): ?>
        <?php echo $this->translate('Browse %s Resumes', $this->translate($this->categoryObject->getTitle())); ?>
      <?php else: ?>  
        <?php echo $this->translate('Browse Resumes'); ?>
      <?php endif; ?>
    </span>
  </h3>    
  
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
 
    
    <div id="resumes_browse_map_container" class="resumes_browse_map_container" style="display: none">
      <?php echo $this->radcodes()->map()->items($this->paginator, array(
              'height' => 480,
              'width'  => 740,
              'google_map' => 'resumes_browse_map',
            )); ?>
    </div>  
    <div id="resumes_browse_list_container">
      <?php $totalCVs = $this->paginator->getTotalItemCount() ?>
      <h3 class='resume_count'><?php echo $this->translate(array('%s CV Profile found.', '%s CV Profiles found.', $totalCVs),$this->locale()->toNumber($totalCVs)) ?></h3>
      <ul class='resumes_rows'>
        <?php foreach ($this->paginator as $resume): ?>
          <?php 
            $recentEpayment = $resume->getRecentEpayment();
            
          ?>
          <li>
            <div class="resume_photo">
              <?php echo $this->htmlLink($resume->getHref(), $this->itemPhoto($resume, 'thumb.normal'));?>
            </div>
            <div class="resume_content">
              <div class="resume_title">
                <?php echo $this->partial('index/_title.tpl', 'resume', array('resume' => $resume))?>
              </div>
              <div class="resume_details">
                <?php echo $this->partial('index/_details.tpl', 'resume', array('resume' => $resume))?>
              </div>
              <div class="resume_description">
                <?php echo $this->partial('index/_description.tpl', 'resume', array('resume' => $resume))?>
              </div>
              <div class="resume_meta">
                <?php echo $this->partial('index/_meta.tpl', 'resume', array('resume' => $resume, 'show_owner' => true))?>
              </div>
            </div>
          </li>
        <?php endforeach; ?>  
      </ul>
    </div>
  <?php elseif( $this->tag || $this->keyword || $this->location || $this->user): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('There are no resumes that match your search criteria.');?>
      </span>
    </div>
  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('There are no resumes posted yet.');?>
        <?php if ($this->can_create): ?>
          <?php echo $this->translate('Get started by <a href=\'%1$s\'>posting</a> a new resume.', $this->url(array('action'=>'create'), 'resume_general'));?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>

    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->formValues
    )); ?>    

