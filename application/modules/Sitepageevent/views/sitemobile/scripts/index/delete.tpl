<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php echo $this->content()->renderWidget("sitepageevent.sitemobile-breadcrumb",array('noShowTitle'=> 1,'tab_id'=>$this->tab_selected_id,'icon'=>"arrow-d")); ?>
<div class="layout_middle">

  <div class='global_form'>
    <form method="post" class="global_form">
      <div>
        <div>
          <h3><?php echo $this->translate('Delete Page Event ?'); ?></h3>
          <p>
<?php echo $this->translate('Are you sure that you want to delete the Page event titled "%1$s" last modified %2$s? It will not be recoverable after being deleted.', $this->sitepageevent->title, $this->timestamp($this->sitepageevent->modified_date)) ?>
          </p>
          <br />
          <p>
            <input type="hidden" name="confirm" value="true"/>
            <button type='submit' data-theme="b"><?php echo $this->translate('Delete'); ?></button>
						<div style="text-align: center"><?php echo $this->translate('or'); ?> </div>
            <a href="#" data-rel="back" data-role="button">
              <?php echo $this->translate('Cancel') ?>
            </a>
          </p>
        </div>
      </div>
    </form>
  </div>
</div>	