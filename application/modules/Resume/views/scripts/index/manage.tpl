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


  <?php if( $this->tag || $this->keyword || $this->location):?>
    <div class="resumes_result_filter_details">
      <?php echo $this->translate('Showing resumes posted'); ?>
      <?php if ($this->tag): ?>
        <?php echo $this->translate('tagging #%s', $this->htmlLink(
          $this->url(array('action'=>'manage', 'tag'=>$this->tag), 'resume_general', true),
          $this->tagObject ? $this->tagObject->text : $this->tag
        ));?>
      <?php endif; ?>
      <?php if ($this->keyword): ?>
        <?php echo $this->translate('with %s keyword', $this->htmlLink(
          $this->url(array('action'=>'manage', 'keyword'=>$this->keyword), 'resume_general', true),
          $this->keyword
        ));?>
      <?php endif; ?>
      <?php if ($this->location): ?>
        <?php echo $this->translate('in %s location', $this->htmlLink(
          $this->url(array('action'=>'manage', 'location'=>$this->location), 'resume_general', true),
          $this->location
        ));?>
      <?php endif; ?>
               
      <?php echo $this->htmlLink(array('action'=>'manage', 'route'=>'resume_general'), $this->translate('(x)'))?>
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
        <?php echo $this->translate('My %s Resumes', $this->translate($this->categoryObject->getTitle())); ?>
      <?php else: ?>  
        <?php echo $this->translate('My Resumes'); ?>
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
      <ul class='resumes_rows'>
        <?php foreach ($this->paginator as $resume): ?>
          <?php 
            $recentEpayment = $resume->getRecentEpayment();
            
          ?>
          <li>
            <div class="resume_photo">
              <?php echo $this->htmlLink($resume->getHref(), $this->itemPhoto($resume, 'thumb.normal'));?>
            </div>
            <div class="resume_settings">
              <ul>
                <li>
                  <?php echo $this->translate('Resume ID: %s', $resume->getIdentity())?>
                </li>
                <li>
                  <?php echo $this->translate('Package: %s', $resume->getPackage()->toString()); ?>
                </li>
                <li>
                  <?php echo $this->translate('Publish: %s', $this->translate($resume->isPublished() ? 'Live' : 'Draft')); ?>
                </li>
                <li>
                  <?php echo $this->translate('Status: %1$s - %2$s', $resume->getStatusText(), $this->locale()->toDate($resume->status_date));?>
                </li>
                <li>
                  <?php if ($resume->hasExpirationDate()): ?>
                    <?php $expire_date = $this->timestamp($resume->expiration_date); ?>
                  <?php else: ?>
                    <?php $expire_date =  $this->translate('Never')?>
                  <?php endif; ?>
                  <?php echo $this->translate('Expire: %s', $expire_date); ?>
                </li>
                <li>
                  <?php echo $this->translate('Featured: %s', $this->translate($resume->featured ? 'Yes' : 'No'))?>
                </li>
                <li>
                  <?php echo $this->translate('Sponsored: %s', $this->translate($resume->sponsored ? 'Yes' : 'No'))?>
                </li>
                <li class="resume_settings_epayment">
                  <?php if ($resume->requiresEpayment()): ?>
                    <?php if ($recentEpayment instanceof Epayment_Model_Epayment): ?>
                      <?php echo $this->translate('Payment: received'); ?>
                        <br />
                        #<?php echo $recentEpayment->getIdentity(); ?>
                        <?php echo $this->translate($recentEpayment->getStatusText())?>
                        <?php echo $this->locale()->toDate($recentEpayment->creation_date)?>
                        <?php echo $this->htmlLink($resume->getActionHref('packages'), $this->translate('RENEW / UPGRADE'), array('class' => 'resume_paynow'))?>
                    <?php else: ?>
                      <?php echo $this->translate('Payment: required'); ?>
                      <?php echo $this->htmlLink($resume->getCheckoutHref(), $this->translate('PAY NOW'), array('class' => 'resume_paynow'))?>
                    <?php endif; ?>
                  <?php else: ?>
                    <?php echo $this->translate('Payment: not required'); ?>
                    <?php echo $this->htmlLink($resume->getActionHref('packages'), $this->translate('RENEW / UPGRADE'), array('class' => 'resume_paynow'))?>
                  <?php endif; ?>
                </li>
              </ul>
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
                <?php echo $this->partial('index/_meta.tpl', 'resume', array('resume' => $resume, 'show_owner' => false))?>
              </div>
              <div class="resume_options">
                <?php if ($resume->authorization()->isAllowed($this->viewer(), 'edit')): ?>
                  <?php echo $this->htmlLink(array('route'=>'resume_specific', 'action'=>'edit', 'resume_id'=>$resume->getIdentity()), $this->translate('Edit CV'), array('class'=>'buttonlink icon_resume_edit'))?>
                <?php endif; ?>
                <?php if ($resume->authorization()->isAllowed($this->viewer(), 'delete')): ?>
                  <?php echo $this->htmlLink(array('route'=>'resume_specific', 'action'=>'delete', 'resume_id'=>$resume->getIdentity()), $this->translate('Delete CV'), array('class'=>'buttonlink icon_resume_delete'))?>
                <?php endif; ?>
              </div>
            </div>
          </li>
        <?php endforeach; ?>  
      </ul>
    </div>
  <?php elseif( $this->tag || $this->keyword || $this->location): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any resumes that match your search criteria.');?>
      </span>
    </div>
  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any resumes.');?>
        <?php if ($this->can_create): ?>
          <?php echo $this->translate('Get started by <a href=\'%1$s\'>posting</a> a new resume.', $this->url(array('action'=>'create'), 'resume_general'));?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>

    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->formValues
    )); ?>    

