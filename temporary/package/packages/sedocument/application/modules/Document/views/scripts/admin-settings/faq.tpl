<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo $this->translate('Documents Plugin'); ?></h2>

<?php if( count($this->navigation) ): ?>
	<div class='seaocore_admin_tabs'>
		<?php	echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
	</div>
<?php endif; ?>

<script type="text/javascript">
  function faq_show(id) {
    if($(id).style.display == 'block') {
      $(id).style.display = 'none';
    } else {
      $(id).style.display = 'block';
    }
  }
</script>

<div class="admin_seaocore_files_wrapper">
	<ul class="admin_seaocore_files seaocore_faq">
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("How do I get my Scribd API details ?");?></a>
			<?php if($this->show):?>
				<div class='faq' style='display: block;' id='faq_1'>
			<?php else:?>
				<div class='faq' style='display: none;' id='faq_1'>
			<?php endif;?>
				<ul>
					<li>
						<?php echo $this->translate("First, sign up for a Scribd API account, over here: ");?> <a href=" https://www.scribd.com/login" target="_blank"><?php echo $this->translate(" https://www.scribd.com/login");?></a>
					</li>
					<li>
						<?php echo $this->translate("Then, from the drop-down next to your name in the top-right portion of Scribd, click on 'Settings'.");?>
					</li>
					<li>
						<?php echo $this->translate("Then go to the 'API' tab to see your API key and API secret.");?>
					</li>
					<li>
						<?php echo $this->translate("Now search for the field : 'Require API signature', and make sure that the option selected for this is : 'Don't require signature'."); ?>
					</li>
				</ul>
			</div>
		</li>	
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("How can I change the maximum limit for the document file size ?");?></a>
			<div class='faq' style='display: none;' id='faq_2'>
				<?php echo $this->translate("Go to Member Level Settings and change the Maximum file size for the various member levels there.");?>
			</div>
		</li>
		<li>
			<a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?");?></a>
			<div class='faq' style='display: none;' id='faq_3'>
				<?php echo $this->translate("Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");?></a>
			</div>
		</li>
	  <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("I am uploading documents on my website. Format conversion for most of them is being completed successfully. However, format for some documents are not getting converted. Why would this be happening?"); ?></a>
      <div class='faq' style='display: none;' id='faq_4'>
        <?php echo $this->translate('The documents which are not getting converted might be password protected or copyrighted. So, please check such documents by uploading them at scribd (https://www.scribd.com/) to see if they are getting converted there. Scribd does not allow password protected or copyrighted documents to get converted.'); ?></a>
      </div>
    </li>
	  <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("My website is running under SSL (with https). Will this plugin work fine in this case?"); ?></a>
      <div class='faq' style='display: none;' id='faq_5'>
        <?php echo $this->translate('All pages of this plugin will display fine on your website running under SSL (with https). The main Document view page will give an SSL warning in the browser because of some components of the document viewer which are rendered over http and not https, but the page will display fine. The Scribd document viewer currently does not support https completely. Other pages of this plugin like Documents Home, Browse Documents, etc will display completely fine without any https warning.'); ?></a>
      </div>
    </li>
		<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("I want to change the text 'documents' to 'cardocuments' in the URLs of this plugin. How can I do so ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_6'>
        <?php echo $this->translate('Ans: To do so, please go to the Global Settings section of this plugin. Now, search for the field : Documents pages URL alternate text for "documents"<br />Then, enter the text you want to display in place of \'documents\' in the text box there.'); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("I am not able to find the Suggest to Friends feature for Documents. What can be the reason?"); ?></a>
      <div class='faq' style='display: none;' id='faq_7'>
        <?php echo $this->translate('Ans: The suggestions features are dependent on the %1$sSuggestions / Recommendations / People you may know & Inviter%2$s plugin and require that to be installed.', '<a href="http://www.socialengineaddons.com/socialengine-suggestions-recommendations-plugin" target="_blank">', '</a>'); ?></a>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("I have selected the HTML5 Reader as default viewer for the documents on my site, but some documents are still being shown in Flash Reader. Why is it so?"); ?></a>
      <div class='faq' style='display: none;' id='faq_8'>
        <?php echo $this->translate('Ans: This is happening because secure documents use access-management technology which is available in Flash only. Therefore, secure documents will always be viewed in Flash reader, even if you choose HTML5 reader as default.'); ?></a>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo $this->translate("In the document viewer, links like Download, Share and Print are shown sometimes, whereas other times they are disabled / hidden. Why is this happening?"); ?></a>
      <div class='faq' style='display: none;' id='faq_9'>
        <?php echo $this->translate('Ans: Links like Download, Share and Print will only be shown and enabled in the document viewer for documents which are public on Scribd.com and are not secure.'); ?></a>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo $this->translate("What is the Profile Document functionality? How can it be useful for my website?"); ?></a>
      <div class='faq' style='display: none;' id='faq_10'>
        <?php echo $this->translate("Ans: The Profile Document functionality enables users to showcase a document in a tab on their profile. This feature can have multiple applications for users like showcasing latest resume on profile, recent artwork design, etc. At any time only one document can be made as a Profile Document for a user. For this, you should have placed the 'Member’s Profile Document' widget on 'Member Profile' page. The settings for this functionality are available in Member Level Settings section."); ?></a>
      </div>
    </li>
	</ul>
</div>