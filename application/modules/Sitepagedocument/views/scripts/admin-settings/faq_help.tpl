<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
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
      <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("I want Documents to be available to only certain directory items / pages on my site. How can this be done?"); ?></a>
      <div class='faq' style='display: none;' id='faq_1'>
        <?php echo $this->translate("Ans: You can enable packages for pages on your site, and make Documents available to only certain packages. If you have not enabled packages, then from Member Level Settings, you can make Documents to be available for pages of only certain member levels."); ?>
      </div>
    </li>	
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("How do I get my Scribd API details ?"); ?></a>
      <?php if ($this->show): ?>
        <div class='faq' style='display: block;' id='faq_2'>
      <?php else: ?>
        <div class='faq' style='display: none;' id='faq_2'>
      <?php endif; ?>
          <ul>
            <?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_getScribdKeys.tpl';?>
          </ul>
        </div>
    </li>	

    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("How can I change the maximum limit for the document file size ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_3'>
        <?php echo $this->translate('Ans: Go to Global Settings and change the value of "Maximum file size" field.'); ?>
      </div>
    </li>	

    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("Can I create custom fields for Page Documents on my site?"); ?></a>
      <div class='faq' style='display: none;' id='faq_4'>
        <?php echo $this->translate('Ans: Yes, you can do so from the "Page Document Questions" section.'); ?></a>
      </div>
    </li>
		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo $this->translate("I had not selected to 'Enable Documents Module for Default Package' in 'Global Settings' section before activating this Plugin. But after activation, I am not able to view this option. Does this mean that this module is permanently disabled for Default Package?"); ?></a>
      <div class='faq' style='display: none;' id='faq_9'>
        <?php echo $this->translate("Ans: No, it does not mean so. You can still enable this module for Default Package from the 'Manage Packages' section of 'Directory/Pages Plugin' by editing the Default Package and selecting the 'Documents' checkbox in the 'Modules / Apps' field."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("I want to use this plugin for directory of car documents. How can I change the word: 'page documents' to 'car documents' in this plugin?"); ?></a>
      <div class='faq' style='display: none;' id='faq_5'>
        <?php echo $this->translate("Ans: You can easily use this plugin for creation of any type of directories. You can change the word 'page documents' to your directory type from the 'Layout' > 'Language Manager' section in the Admin Panel."); ?>
      </div>
    </li>
		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo $this->translate("I want to change the text 'pagedocuments' to 'cardocuments' in the URLs of this plugin. How can I do so ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_10'>
        <?php echo $this->translate('Ans: To do so, please go to the Global Settings section of this plugin. Now, search for the field : Page Documents URL alternate text for "pagedocuments"<br />Then, enter the text you want to display in place of \'pagedocuments\' in the text box there.'); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_6'>
        <?php echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'."); ?></a>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("I am not able to find the Suggest to Friends feature for Page Documents. What can be the reason?"); ?></a>
      <div class='faq' style='display: none;' id='faq_7'>
        <?php echo $this->translate('Ans: The suggestions features are dependent on the %1$sSuggestions / Recommendations / People you may know & Inviter%2$s plugin and require that to be installed.', '<a href="http://www.socialengineaddons.com/socialengine-suggestions-recommendations-plugin" target="_blank">', '</a>'); ?></a>
      </div>
    </li>	

    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("I want to enhance the Pages on my site and provide more features to my users. How can I do it?"); ?></a>
      <div class='faq' style='display: none;' id='faq_8'>
        <?php echo $this->translate('Ans: There are various apps / extensions available for the "Directory / Pages Plugin" which can enhance the Pages on your site, by adding valuable functionalities to them. To view the list of available extensions, please visit: %s.', '<a href="http://www.socialengineaddons.com/catalog/directory-pages-extensions" target="_blank">http://www.socialengineaddons.com/catalog/directory-pages-extensions</a>'); ?></a>
      </div>
    </li>
	  <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_11');"><?php echo $this->translate("I am uploading documents on my website. Format conversion for most of them is being completed successfully. However, format for some documents are not getting converted. Why would this be happening?"); ?></a>
      <div class='faq' style='display: none;' id='faq_11'>
        <?php echo $this->translate('Ans: The documents which are not getting converted might be password protected or copyrighted. So, please check such documents by uploading them at scribd (https://www.scribd.com/) to see if they are getting converted there. Scribd does not allow password protected or copyrighted documents to get converted.'); ?></a>
      </div>
    </li>
	  <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_12');"><?php echo $this->translate("My website is running under SSL (with https). Will this plugin work fine in this case?"); ?></a>
      <div class='faq' style='display: none;' id='faq_12'>
        <?php echo $this->translate('All pages of this plugin will display fine on your website running under SSL (with https). The main Document view page will give an SSL warning in the browser because of some components of the document viewer which are rendered over http and not https, but the page will display fine. The Scribd document viewer currently does not support https completely.'); ?></a>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_13');"><?php echo $this->translate("I have selected the HTML5 Reader as default viewer for the documents on my site, but some documents are still being shown in Flash Reader. Why is it so?"); ?></a>
      <div class='faq' style='display: none;' id='faq_13'>
        <?php echo $this->translate('Ans: This is happening because secure documents use access-management technology which is available in Flash only. Therefore, secure documents will always be viewed in Flash reader, even if you choose HTML5 reader as default.'); ?></a>
      </div>
    </li>
  </ul>
</div>