<?php 
$viewer = Engine_Api::_()->user()->getViewer();
$addableCheck = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'addable.integration');
$createPrivacy = 1;
$sitepageintegrationEnabled = Engine_Api::_()->getDbtable('modules',
'core')->isModuleEnabled('sitepageintegration');
if(!empty($sitepageintegrationEnabled)) :
	$getHost = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
	$getPageType = Engine_Api::_()->sitepageintegration()->getPageType($getHost);
	if( !empty($getPageType)):
		$mixSettingsResults = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitepageintegration')->getIntegrationItems();

		foreach($mixSettingsResults as $modNameValue):
			if($addableCheck == 1) :
			  $Params = Engine_Api::_()->sitepageintegration()->integrationParams($modNameValue["resource_type"], $modNameValue['listingtype_id']);
				$createPrivacy =  $Params['create_privacy'] ;
			endif;
			if (Engine_Api::_()->sitepage()->hasPackageEnable()) :
				if($createPrivacy) :
					if (Engine_Api::_()->sitepage()->allowPackageContent($this->subject->package_id,
					"modules", $modNameValue["resource_type"] . '_' . $modNameValue['listingtype_id'])) :  ?>
						<li> <?php $canShowMessage = false;?>
							<div class="sitepage_getstarted_num">
								<div>
									<?php echo $i; $i++;?>
								</div>
							</div>
							<div class="sitepage_getstarted_des">
								<?php 
								if ($modNameValue["resource_type"] == 'sitereview_listing') : 
									$listingType = Engine_Api::_()->getItem('sitereview_listingtype', 	$modNameValue['listingtype_id'])->toarray(); ?>
									<b><?php echo $this->translate("%s New %s %s", ucfirst($listingType['language_phrases']['text_post']), $modNameValue["item_title"], ucfirst($listingType['language_phrases']['text_listings'])); ?>	</b>
								<?php else: ?>
									<b><?php echo $this->translate($modNameValue["item_title"]); ?></b>
								<?php endif; ?>
								<p>
									<?php $item_title = strtolower($modNameValue["item_title"]);
									if ($modNameValue["resource_type"] == 'sitereview_listing') :
										echo $this->translate("%s new %s %s to this page.", ucfirst($listingType['language_phrases']['text_post']), $item_title, strtolower($listingType['language_phrases']['text_listings'])); ?>
									<?php
									else:
										echo $this->translate("Add %s to this page.", $item_title); ?>
									<?php endif; ?>
								</p>
								<div class="sitepage_getstarted_btn">
									<?php if ($modNameValue["resource_type"] == 'sitereview_listing') : ?>
										<a href='<?php echo $this->url(array('action' => 'index','resource_type' => $modNameValue["resource_type"], 'page_id' => $this->page_id, 'listingtype_id' => $modNameValue["listingtype_id"] ),'sitepageintegration_create', true) ?>'><?php echo $this->translate("%s %s %s", ucfirst($listingType['language_phrases']['text_post']),$modNameValue["item_title"], ucfirst($listingType['language_phrases']['text_listing']));?></a>
									<?php else: ?>
										<a href='<?php echo $this->url(array('action' => 'index','resource_type' => $modNameValue["resource_type"], 'page_id' => $this->page_id, 'listingtype_id' => $modNameValue["listingtype_id"] ),'sitepageintegration_create', true) ?>'><?php echo $this->translate("Add %s", $modNameValue["item_title"]);?></a>
									<?php endif; ?>
								</div>
							</div>
						</li>
					<?php endif; ?>
	      <?php	endif;  ?>
			<?php else : ?>
				<?php
				if($createPrivacy) :
					$isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($this->subject,
					$modNameValue["resource_type"] . '_' . $modNameValue['listingtype_id']);
					if (!empty($isPageOwnerAllow)) : ?>
						<li> <?php $canShowMessage = false;?>
							<div class="sitepage_getstarted_num">
								<div>
									<?php echo $i; $i++;?>
								</div>
							</div>
							<div class="sitepage_getstarted_des">
								<?php 
								if ($modNameValue["resource_type"] == 'sitereview_listing') :
									$listingType = Engine_Api::_()->getItem('sitereview_listingtype', $modNameValue['listingtype_id'])->toarray(); ?>
									<b><?php echo $this->translate("%s New %s %s", ucfirst($listingType['language_phrases']['text_post']), $modNameValue["item_title"], ucfirst($listingType['language_phrases']['text_listings'])); ?></b>
								<?php else: ?>
									<b><?php echo $this->translate($modNameValue["item_title"]); ?></b>
								<?php endif; ?>
								<p>
									<?php $item_title = strtolower($modNameValue["item_title"]);
									if ($modNameValue["resource_type"] == 'sitereview_listing') :
									echo $this->translate("%s new %s %s to this page.", ucfirst($listingType['language_phrases']['text_post']), $item_title, strtolower($listingType['language_phrases']['text_listings'])); ?>
									<?php
									else:
										echo $this->translate("Add %s to this page.", $item_title); ?><?php
									endif;
									?>
								</p>
								<div class="sitepage_getstarted_btn">
									<?php if ($modNameValue["resource_type"] == 'sitereview_listing') : ?>
										<a href='<?php echo $this->url(array('action' => 'index','resource_type' => $modNameValue["resource_type"], 'page_id' => $this->page_id, 'listingtype_id' => $modNameValue["listingtype_id"] ),'sitepageintegration_create', true) ?>'><?php echo $this->translate("%s %s %s", ucfirst($listingType['language_phrases']['text_post']),$modNameValue["item_title"], ucfirst($listingType['language_phrases']['text_listing'])); ?></a>
									<?php else: ?>
										<a href='<?php echo $this->url(array('action' => 'index','resource_type' => $modNameValue["resource_type"], 'page_id' => $this->page_id, 'listingtype_id' => $modNameValue["listingtype_id"] ),'sitepageintegration_create', true) ?>'><?php echo 	$this->translate("Add %s", $modNameValue["item_title"]);?></a>
									<?php endif; ?>
								</div>
							</div>
						</li>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif;?>
		<?php	endforeach;  ?>
	<?php endif; ?>
<?php endif;	?>