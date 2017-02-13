<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenotes
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  function faq_show(id) {
    if($(id).style.display == 'block') {
      $(id).style.display = 'none';
    } else {
      $(id).style.display = 'block';
    }
  }
</script>
<div class="admin_sitepage_files_wrapper">
	<ul class="admin_sitepage_files sitepage_faq">
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("I want Notes to be available to only certain directory items / pages on my site. How can this be done?");?></a>
			<div class='faq' style='display: none;' id='faq_1'>
				<?php echo $this->translate("Ans: You can enable packages for pages on your site, and make Notes available to only certain packages. If you have not enabled packages, then from Member Level Settings, you can make Notes to be available for pages of only certain member levels.");?>
			</div>
		</li>	
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("What kinds of displays are available for Notes Photos?");?></a>
			<div class='faq' style='display: none;' id='faq_2'>
				<?php echo $this->translate("Ans: There are 2 types of photo displays available. Both of them are AJAX based displays. The first is a simple AJAX based display, and the second one is an advanced lightbox display. The Advanced Lightbox Display for notes photos can be enabled from Global Settings of Page Notes Extension.");?>
			</div>
		</li>
		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("I had not selected to 'Enable Notes Module for Default Package' in 'Global Settings' section before activating this Plugin. But after activation, I am not able to view this option. Does this mean that this module is permanently disabled for Default Package?"); ?></a>
      <div class='faq' style='display: none;' id='faq_7'>
        <?php echo $this->translate("Ans: No, it does not mean so. You can still enable this module for Default Package from the 'Manage Packages' section of 'Directory/Pages Plugin' by editing the Default Package and selecting the 'Notes' checkbox in the 'Modules / Apps' field."); ?>
      </div>
    </li>
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("I want to use this plugin for directory of car notes. How can I change the word: 'page notes' to 'car notes' in this plugin?");?></a>
			<div class='faq' style='display: none;' id='faq_3'>
				<?php echo $this->translate("Ans: You can easily use this plugin for creation of any type of directories. You can change the word 'page notes' to your directory type from the 'Layout' > 'Language Manager' section in the Admin Panel.");?>
			</div>
		</li>
		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("I want to change the text 'pagenotes' to 'carnotes' in the URLs of this plugin. How can I do so ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_8'>
        <?php echo $this->translate('Ans: To do so, please go to the Global Settings section of this plugin. Now, search for the field : Page Notes URL alternate text for "pagenotes"<br />Then, enter the text you want to display in place of \'pagenotes\' in the text box there.'); ?>
      </div>
    </li>
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?");?></a>
			<div class='faq' style='display: none;' id='faq_4'>
				<?php echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");?>
			</div>
		</li>	
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("I am not able to find the Suggest to Friends feature for Page Notes. What can be the reason?");?></a>
			<div class='faq' style='display: none;' id='faq_5'>
				<?php echo $this->translate('Ans: The suggestions features are dependent on the %1$sSuggestions / Recommendations / People you may know & Inviter%2$s plugin and require that to be installed.', '<a href="http://www.socialengineaddons.com/socialengine-suggestions-recommendations-plugin" target="_blank">', '</a>');?>
			</div>
		</li>	
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("I want to enhance the Pages on my site and provide more features to my users. How can I do it?");?></a>
			<div class='faq' style='display: none;' id='faq_6'>
				<?php echo $this->translate('Ans: There are various apps / extensions available for the "Directory / Pages Plugin" which can enhance the Pages on your site, by adding valuable functionalities to them. To view the list of available extensions, please visit: %s.', '<a href="http://www.socialengineaddons.com/catalog/directory-pages-extensions" target="_blank">http://www.socialengineaddons.com/catalog/directory-pages-extensions</a>');?>
			</div>
		</li>
	</ul>
</div>