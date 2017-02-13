<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @access	   John
 */
?>

<?php if( $this->paginator->getTotalItemCount() > 0 || $this->canUpload ): ?>
  <div data-role="controlgroup" data-type="horizontal">  
    <?php if( $this->canUpload ): ?>
      <?php echo $this->htmlLink(array(
          'route' => 'sitepageevent_photo_extended',
          'action' => 'upload-sitemobile-photo',
          'subject' => $this->subject()->getGuid(),
          'tab'=>$this->tab_selected_id,
        ), $this->translate('Upload Photos'), array(
          'data-role'=>"button", 'data-icon'=>"picture", "data-iconpos"=>"left", "data-inset" => 'false', 'data-mini'=>"true",'data-corners'=>"true",'data-shadow'=>"true"
      )) ?>
    <?php endif; ?>
  </div>
<?php endif; ?>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
	<ul class="sm-ui-thumbs thumbs thumbs_nocaptions">
		<?php foreach( $this->paginator as $photo ): ?>
			<li>
				<a class="thumbs_photo" href="<?php echo $photo->getHref(). '/tab/' . $this->identity; ?>">
					<span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
				</a>
			</li>
		<?php endforeach;?>
	</ul>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No photos have been uploaded to this event yet.');?>
    </span>
  </div>
<?php endif; ?>