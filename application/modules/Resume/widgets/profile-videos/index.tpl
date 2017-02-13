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

<?php $uid = md5(time() . rand(1, 1000)) ?>

<script type="text/javascript">
  en4.core.runonce.add(function(){
    var uid = '<?php echo $uid ?>';
    //var hasTitle = Boolean($$('.profile_videos_' + uid)[0].getParent().getElement('h3'));
    
    <?php if( !$this->renderOne ): ?>
    var wrapper = $('resume_videos_wrapper');
    //var anchor = $$('.profile_videos_' + uid)[0].getParent();
    $$('.profile_videos_previous_' + uid)[0].style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $$('.profile_videos_next_' + uid)[0].style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $$('.profile_videos_previous_' + uid)[0].removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/mod/resume/name/profile-videos',
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>,
          ajax : 1
        }
      }), {
        'element' : wrapper
      })
    });

    $$('.profile_videos_next_' + uid)[0].removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/mod/resume/name/profile-videos',
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>,
          ajax : 1
        }
      }), {
        'element' : wrapper
      })
      //en4.core.runonce.add(function() {
      //  if( !hasTitle ) {
      //    anchor.getElement('h3').destroy();
      //  }
      //});
    });
    <?php endif; ?>
  });
</script>

<?php if(empty($this->isAjax)) : ?>
<div id="resume_videos_wrapper" class='wrapper_padding no_top'>
<?php endif; ?>
  
  <div class="resume_album_options">
    <?php /* if( $this->paginator->getTotalItemCount() > 0 ): ?>
      <?php echo $this->htmlLink(array(
          'route' => 'resume_extended',
          'controller' => 'photo',
          'action' => 'list',
          'subject' => $this->subject()->getGuid(),
        ), $this->translate('View All Videos'), array(
          'class' => 'buttonlink icon_resume_photo_view'
      )) ?>
    <?php endif; */ ?>

    <?php if( $this->canUpload ): ?>
      <?php echo $this->htmlLink(array(
          'route' => 'resume_video_create',
          'resume_id' => $this->resume->resume_id,
          'tab' => $this->tab,
        ), $this->translate('Upload Videos'), array(
          'class' => 'buttonlink icon_video_new'
      )) ?>
    <?php endif; ?>
  </div>

  <br />

  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <ul class="thumbs">
    <?php foreach( $this->paginator as $video ): ?>
      <li>
        <a class="thumbs_photo" href="<?php echo $video->getHref(); ?><?php if(!empty($this->tab)) : ?>/tab/<?php echo $this->tab ?><?php endif; ?>">
          <span style="background-image: url(<?php echo $video->getPhotoUrl('thumb.normal'); ?>);"></span>
        </a>
        <p class="thumbs_info">
          <?php echo $this->translate('By');?>
          <?php echo $this->htmlLink($video->getOwner()->getHref(), $video->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
          <br />
          <?php echo $this->timestamp($video->creation_date) ?>
        </p>
      </li>
    <?php endforeach;?>
  </ul>

  <div class='overflow_hidden'>
    <div id="profile_videos_previous" class="paginator_previous profile_videos_previous_<?php echo $uid ?>">
      <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
        'onclick' => '',
        'class' => 'buttonlink icon_previous'
      )); ?>
    </div>
    <div id="profile_videos_next" class="paginator_next profile_videos_next_<?php echo $uid ?>">
      <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
        'onclick' => '',
        'class' => 'buttonlink_right icon_next'
      )); ?>
    </div>
  </div>
  <?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No videos have been uploaded to this resume yet.');?>
    </span>
  </div>
  <?php endif; ?>
  
<?php if(empty($this->isAjax)) : ?>
</div>
<?php endif; ?>
