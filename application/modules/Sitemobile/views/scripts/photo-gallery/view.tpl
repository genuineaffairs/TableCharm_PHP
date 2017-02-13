<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<ul class="gallery" >
<?php $count=0;?>  
<?php foreach ($this->paginator as $photo):?>
  <?php $title='';?>
  <?php if(isset($this->album->owner_id) &&  !empty($this->album)) :?>
		<?php $title= $this->translate('%1$s\'s Album: %2$s',
			$this->album->getOwner()->getTitle(),
			( '' != trim($this->album->getTitle()) ? $this->album->getTitle() : '<em>' . $this->translate('Untitled') . '</em>')
		);
				if($photo->getTitle()):
					$title .=" - ".$photo->getTitle();
				endif;
		?>
  <?php endif;?>
  <li <?php if($photo->photo_id==$this->photo->photo_id):?> class="active" <?php endif; ?> >
    <a  href="<?php echo $photo->getPhotoUrl() ?>" data-related-url="<?php echo $photo->getHref();?>" data-subject="<?php echo $photo->getGuid(); ?>" <?php if($photo->photo_id==$this->photo->photo_id):?> class="active_photo" <?php endif; ?> rel="external" <?php if($this->canComment): ?> data-liked="<?php echo $photo->likes()->isLike($this->viewer) ? "1": "0";?>"<?php endif; ?> data-caption="<?php echo $title;?>" data-count-caption="<?php echo ++$count." - ".$this->paginator->getTotalItemCount();?>"></a>
    </li>
<?php endforeach; ?>
</ul>