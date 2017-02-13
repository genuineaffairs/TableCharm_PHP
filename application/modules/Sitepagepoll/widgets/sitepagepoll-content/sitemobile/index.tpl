<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
?>


<?php 

$breadcrumb = array(
    array("href" => $this->sitepage->getHref(),"title" => $this->sitepage->getTitle(),"icon" => "arrow-r"),
    array("href" => $this->sitepage->getHref(array('tab' => $this->tab_selected_id)),"title"=>"Polls","icon" => "arrow-r"),
    array("title" => $this->sitepagepoll->getTitle(),"icon" => "arrow-d","class" => "ui-btn-active ui-state-persist"));

echo $this->breadcrumb($breadcrumb);
?>

<div class="ui-page-content">
  <div class="sm-ui-cont-head">	
    <div class="sm-ui-cont-cont-info">
      <div class="sm-ui-cont-author-name">
        <?php echo $this->sitepagepoll->getTitle()?>
            <?php if( $this->sitepagepoll->closed ): ?>
            <p class="ui-li-aside">
            <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/close.png' alt="<?php echo $this->translate('Closed') ?>" />
            </p>
            <?php endif;?>
      </div>
      <div class="sm-ui-cont-cont-date">
		<?php echo $this->translate('Created by %s', $this->htmlLink($this->sitepagepoll->getOwner(), $this->sitepagepoll->getOwner()->getTitle())) ?>
        -
		<?php echo $this->timestamp($this->sitepagepoll->creation_date) ?>
      </div>

      </div>
    </div>
  <div class="sm-ui-cont-cont-des">
		<?php echo nl2br($this->sitepagepoll->description); ?>
	</div>
	
	<?php  
		echo $this->render('_sitemobile_sitepagepoll.tpl')
	?>

</div>