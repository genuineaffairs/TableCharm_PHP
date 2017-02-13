<b class="advertise_your_page_title"><?php echo $this->translate('Advertise your %s', $this->translate($this->info['module_title'])) ?></b>
<div class="advertise_your_page">
	<?php $site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title'); ?>
	<?php echo $this->translate('Get more audience to visit and like your %1$s with %2$s Ads!', $this->info['module_title'], $site_title) ?>
	<?php $promote_Ad_path = $this->url(array(), 'communityad_listpackage', true); ?>
	<?php echo $this->htmlLink(array('route'=>'communityad_listpackage','type' => $this->module_type, 'type_id' => $this->module_type_id), $this->translate("Create an Ad"), array(		'class' => 'buttonlink icon_sitepage_ad_create')); ?>

</div>