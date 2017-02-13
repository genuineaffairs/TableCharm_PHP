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

<?php 
  include APPLICATION_PATH . '/application/modules/Sitepageevent/views/scripts/_page_eventheader.tpl';
?>
  <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adeventdelete', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)): ?>
  <div class="layout_right" id="communityad_eventdelete">
		<?php
		echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adeventdelete', 3),"loaded_by_ajax"=>0,'widgetId'=>'page_eventdelete')); 			 
		?>
  </div>
<?php endif; ?>
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
            <button type='submit'><?php echo $this->translate('Delete'); ?></button>
						<?php echo $this->translate('or'); ?> <?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>$this->tab_selected_id)),$this->translate('cancel')) ?>
          </p>
        </div>
      </div>
    </form>
  </div>
</div>	