<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
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

<?php if(!empty($this->faq)) : ?>
	<p><?php echo $this->translate("Browse the different FAQ sections of this plugin by clicking on the corresponding tabs below.") ?><p>
	<br />
	<?php $action = 'faq' ?>
<?php else : ?>
	<?php $action = 'readme' ?>
<?php endif; ?>
<div class='tabs seaocore_faq_tabs'>
		<ul class="navigation">
		  <li class="<?php if($this->faq_type == 'general') { echo "active"; } ?>">
		 	<?php echo $this->htmlLink(array('route'=>'admin_default','module' => 'communityad','controller' => 'settings','action' => $action, 'faq_type' => 'general'), $this->translate('General'), array())
		  ?>
			</li>
			<li class="<?php if($this->faq_type == 'package') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'communityad','controller' => 'settings','action' => $action, 'faq_type' => 'package'), $this->translate('Packages'), array())
		  ?>
			</li>
			<li class="<?php if($this->faq_type == 'blocks') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'communityad','controller' => 'settings','action' => $action, 'faq_type' => 'blocks'), $this->translate('Ad Blocks'), array())
		  ?>
			</li>
			<li class="<?php if($this->faq_type == 'targeting') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'communityad','controller' => 'settings','action' => $action, 'faq_type' => 'targeting'), $this->translate('Targeting'), array())
		  ?>
<!--			<li class="<?php if($this->faq_type == 'ajax') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'communityad','controller' => 'settings','action' => $action, 'faq_type' => 'ajax'), $this->translate('Ajax Based'), array())
		  ?>
			</li>-->
			<li class="<?php if($this->faq_type == 'stats') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'communityad','controller' => 'settings','action' => $action, 'faq_type' => 'stats'), $this->translate('Reports & Statistics'), array())
		  ?>
			</li>
			<li class="<?php if($this->faq_type == 'language') { echo "active"; } ?>">
		   <?php
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'communityad','controller' => 'settings','action' => $action, 'faq_type' => 'language'), $this->translate('Language'), array())
		  ?>
			</li>
		</ul>
	</div>

<?php switch($this->faq_type) : 
	case 'general': ?>
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("Q. How do I go about setting up this plugin on my site?");?></a>
					<div class='faq' style='display: none;' id='faq_1'>
						<?php echo $this->translate('Ans: Below are the steps you should follow to configure the plugin on your site according to your requirements:<br /><br />
					1) If you want paid ads on your site, then configure payment related settings on your site from the "Billing" > "Settings" and "Billing" > "Gateways" sections.<br /><br />
					2) Configure the "Global Settings" of this plugin.<br /><br />
					3) Decide the locations on your site where you want to place the Ad Blocks for Community Ads. Then, go to the "Manage Ad Blocks" section to create new ad blocks, or manage existing ones on your site. For the widgetized pages of your site, it is recommended that you create all your ad blocks from this section, and not place their widgets on the pages from the Layout Editor, to maintain consistency. After creating an ad block for a widgetized page, you can go to the layout editor to adjust the vertical positioning of the ad block widget on that page. For the non-widgetized pages of your site, you can get ad codes from Manage Ad Blocks section, after creating ad blocks, to be inserted at the desired places in the respective template files.<br />
Decide the locations on your site where you want to display Sponsored Stories, if you have enabled them from Global Settings. Then, from the Layout Editor, place the Sponsored Stories Widget at those locations. Edit the settings of those widgets from the Layout Editor as per your requirements.<br /><br />
					4) Manage content modules on your site that can be advertised, from the "Manage Modules" section. With Community Ads, users can either create their custom ads, or advertise their content on the site. Sponsored Stories enable users to virally advertise their content on the site. This advertising system allows you to enable your users to advertise their content from absolutely any content module.<br /><br />
					5) Create ad packages for advertising on your site. Users will have to select an ad package before creating an ad. You can choose settings for ad packages like free/paid, custom ads allowed or not, content items allowed to be advertised, pricing model, etc. Different ad types have different ad packages and package properties.<br /><br />
					6) Configure other settings for advertising on your site like: Member Level Settings, Targeting Settings, Graphs Settings, etc.<br /><br />
					7) Create and manage advertising help pages on your site from the "Manage Help & Learn More" section.');?>
						</div>
				</li>

				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("Q. What are the recommended values for dimensions of Ad elements that should be entered in the Global Settings?");?></a>
					<div class='faq' style='display: none;' id='faq_2'>
						<?php echo $this->translate("Ans: Below are the recommended values for dimensions of Ad elements. You may do minor variations in these according to your site's design/theme, but we highly recommend that you should not change them in large variations, otherwise the ads may not display correctly on your site:<br />
						a) Ad Width: 150px<br />
						b) Ad Image Width: 120px<br />
						c) Ad Image Height: 90px<br />
						d) Ad Title Length: 25 characters<br />
						e) Ad Body Length: 135 characters<br />
						f) Sponsored Story Title Length: 35 characters");?>
						</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("Q I have a content module on my site and I want advertising to be enabled for that content type. How do I go about it?");?></a>
					<div class='faq' style='display: none;' id='faq_8'>
<?php echo '<a = href="http://www.socialengineaddons.com/socialengine-directory-pages-plugin" target="_blank">Directory Items / Pages</a><br />
<a = href="http://www.socialengineaddons.com/socialengine-documents-scribd-ipaper-plugin" target="_blank">Documents</a><br />
<a = href="http://www.socialengineaddons.com/socialengine-listings-catalog-showcase-plugin" target="_blank">Listings / Catalog Items</a><br />
<a = href="http://www.socialengineaddons.com/socialengine-recipes-plugin" target="_blank">Recipes</a><br />'; ?>
						<?php echo $this->translate('This plugin enables you to make content belonging to any module on your site as advertisable. It comes pre-configured with allowing advertising of 9 SocialEngine content modules, Directory Items / Pages, Documents, Listings / Catalog Items and Recipes. You can add additional content modules as advertisable from the "Manage Modules" > "Add New Module" section. After adding this module here, include content from this module in an Ad Package, which users will select to create advertisements of that content.');?>
						</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_9');"><?php echo $this->translate("Q. One of my advertisers is complaining that their ad is getting very less views / impressions. What can I do to make an ad appear more prominently / frequently?");?></a>
					<div class='faq' style='display: none;' id='faq_9'>
						<?php echo $this->translate('Ans: Edit that ad from the "Manage Advertisements" section, and assign a large value to the "Weight" of that ad. This ad will now get higher priority for being displayed. Note: This functionality should only be used for exceptional cases, and you are suggested to change the weight of the ad back to zero after the purpose is achieved.');?>
						</div>
				</li>
					<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_10');"><?php echo $this->translate("Q What types of pages are there in the help and learn more section?");?></a>
					<div class='faq' style='display: none;' id='faq_10'>
						<?php echo $this->translate("Ans: The help and learn more section contains 2 types of pages. One, pages built with rich editor, and two, FAQ pages. You can create new help pages of the first type. If you do not want a page to be shown in the help page, then you can simply disable it.");?>
						</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_11');"><?php echo $this->translate("Q For the content of help and learn more pages of my site, are there any URLs I can refer to?");?></a>
					<div class='faq' style='display: none;' id='faq_11'>
						<?php echo $this->translate("Ans: You may refer to the following URLs for the content of help and learn more pages of your site:<br /><br />

					Overview :-<br />
					http://www.facebook.com/advertising/<br /><br />
					
					Get Started :-<br />
					http://www.facebook.com/adsmarketing/index.php?sk=gettingstarted<br /><br />
					
					Improve your ads :-<br />
					http://www.facebook.com/adsmarketing/index.php?sk=adtypes<br /><br />
					
					General FAQ :<br />
					http://www.facebook.com/help/?page=409<br /><br />
					
					Design FAQ<br />
					http://www.facebook.com/help/?page=861<br /><br />
					
					Targeting FAQ<br />
					http://www.facebook.com/help/?page=863");?>
						</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_13');"><?php echo $this->translate("Q. What are Featured / Sponsored advertisements?");?></a>
					<div class='faq' style='display: none;' id='faq_13'>
						<?php echo $this->translate('Ans: For Community Ads, there are separate widgets available for Featured ads and Sponsored ads. For ad packages of Community Ads, you can choose whether ads belonging to a package should automatically be made featured or sponsored. You can also make any ad as featured or sponsored from the "Manage Advertisements" section.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_14');"><?php echo $this->translate("Q. I want to showcase the advertising feature of my site. How can I do that?");?></a>
					<div class='faq' style='display: none;' id='faq_14'>
						<?php echo '<a = href="http://www.socialengineaddons.com/socialengine-directory-pages-plugin" target="_blank">Directory / Pages Plugin</a><br />'; ?>
						<?php echo $this->translate('Ans: You can do this by using this widget: "Advertise: Create an Ad". This widget tells the users that they can advertise on the site, and also creates a "Create an Ad" button. If you have “Directory / Pages Plugin” on your website, then you can also enable advertising showcase from the “Ad Settings” section in the Admin Panel of that plugin.');?>
						</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_15');"><?php echo $this->translate('Q. Where can I place the main "Advertising" Link?');?></a>
					<div class='faq' style='display: none;' id='faq_15'>
						<?php echo $this->translate("Ans: You can place the main Advertising link on your site at these 4 places, by choosing from the Global Settings of this plugin: Main Navigation Menu, Mini Navigation Menu, Footer Menu, Member Home Page Left side Navigation .");?>
						</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_16');"><?php echo $this->translate("Q: If an ad has been created for a content item and that content item later on gets deleted / disabled, then, will the ad still display?");?></a>
					<div class='faq' style='display: none;' id='faq_16'>
						<?php echo $this->translate("Ans : Yes, the ad will continue to be displayed unless it gets disapproved/paused. However, clicking on the URL of the ad will not open the content item as that item has been removed.");?>
						</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_18');"><?php echo $this->translate("Q: The CSS of this plugin is not coming on my site. What should I do ? ");?></a>
					<div class='faq' style='display: none;' id='faq_18'>
						<?php echo $this->translate("Ans: Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'.");?>
						</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_21');"><?php echo $this->translate("Q: Which account type should I select while creating a paypal account on paypal.com for payment gateways?");?></a>
					<div class='faq' style='display: none;' id='faq_21'>
						<?php echo $this->translate("Ans : When you start creating you paypal account, some account types will be shown to you as an initial step of the sign up process. We recommend you to select ‘Business’ type of paypal account.");?>
						</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_22');"><?php echo $this->translate("Q: Can I delete an advertisement or a campaign I have created?");?></a>
					<div class='faq' style='display: none;' id='faq_22'>
						<?php echo $this->translate("Ans : No, you can not delete an ad permanently. But you can delete an ad on a temporary basis which means that its status would appear as 'Deleted' everywhere on the site. Such an ad would just stop running and not be visible to the viewers on the site. But all the things like the details, statistics, etc of an ad would not be deleted permanently. This is because deleting an ad permanently will affect the consistency of statistics. While you can delete a campaign permanently and hence all the ads belonging to that campaign and their stats will also be deleted.");?>
					</div>
				</li>

				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_23');"><?php echo $this->translate("Q: How are the number of clicks on a Sponsored Story calculated?");?></a>
					<div class='faq' style='display: none;' id='faq_23'>
						<?php echo $this->translate("Ans : The number of clicks on a Sponsored Story are calculated on the basis of viewer’s clicks on the story subject. Thus, clicks count is incremented when a viewer clicks on the content title, content photo or when he likes the content.");?>
					</div>
				</li>

				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_24');"><?php echo $this->translate("Q: Where can I find more about Sponsored Stories?");?></a>
					<div class='faq' style='display: none;' id='faq_24'>
						<?php echo $this->translate("Ans : You can find more about this in 'Help and Learn More' section.");?>
					</div>
				</li>

			</ul>
		</div>
	<?php break; ?>

	<?php case 'package': ?>
			<div class="admin_seaocore_files_wrapper">
				<ul class="admin_seaocore_files seaocore_faq">
					<li>
							<a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("Q What are Ad Packages?");?></a>
							<div class='faq' style='display: none;' id='faq_5'>
								<?php echo $this->translate("Ans: Before creating an ad on your site, users will have to choose a package for it. Ad packages in this advertising system are very flexible and create many settings as mentioned below, to suit your advertising needs:<br />
					- Paid / Free package<br />
					- Package cost<br />
					- Content types to be advertisable<br />
					- Allow / disallow advertisers to create custom ads in this package<br />
					- Ad blocks on the site where ads of this package should appear<br />
					- Pricing Model (clicks / views / days)<br />
					- Expiry limit for ads of this package<br />
					- Make ads of package sponsored<br />
					- Make ads of package featured<br />
					- Allow targeting of ads of this package<br />
					- Show package's ads to non-logged-in visitors<br />
					- Require / not-require admin approval for ads of this package<br />
					- Enable advertisers to renew their ads of this package before expiry");?>
								</div>
					</li>
					<li>
						<a href="javascript:void(0);" onClick="faq_show('faq_23');"><?php echo $this->translate("Q: Why am I not able to delete an Ad Package?");?></a>
						<div class='faq' style='display: none;' id='faq_23'>
							<?php echo $this->translate("Ans : You can not delete an Ad Package once you have created it. This is because the consistency of the already created ads in that package would get affected in that case. But if you wish, you can disable an ad package from Manage Packages section and hence it would not be displayed in the list of packages during the initial step of the ad creation.");?>
							</div>
					</li>
					<li>
						<a href="javascript:void(0);" onClick="faq_show('faq_26');"><?php echo $this->translate("Q: I want to let some advertisers to create Ads under the PAID package(like Pay for Clicks or Views) but I don't want them to pay for this as a trial. How can I do so ?");?></a>
						<div class='faq' style='display: none;' id='faq_26'>
							<?php echo $this->translate("Ans: In that case, you would have to first make that Ad as 'Approved' from 'Manage Advertisements' section of this plugin. Then, you would have to 'Renew' it by clicking on 'Renew' link in the rightmost 'Options' column.<br />If you would not Renew the Ad, it would get expired before the limit of clicks/views/days gets completed. In this way, you(site admin) can manually make an advertisement run for FREE under the paid package.");?>
							</div>
					</li>
					<li>
						<a href="javascript:void(0);" onClick="faq_show('faq_27');"><?php echo $this->translate('Q: I have created an Ad in the paid package and then approved it manually from "Manage Advertisements" section without making the payment. But it is showing only 5 views in the "Remaining" column while the package this Ad belongs to says 100,000 views. What can be the reason ?');?></a>
						<div class='faq' style='display: none;' id='faq_27'>
							<?php echo $this->translate("Ans: In this case, you have actually approved the Ad from admin side without paying for it. While plugin provides '5 views' in case Ad belongs to package 'Pay for views' and '1 click' in case Ad belongs to package 'Pay for clicks' as for a trial to the advertiser and after those 5 views are done and payment is still not made, the ad gets expired automatically.<br /><br />So, now if you want to continue that advertisement without payment, then after making it 'Approved' from 'Manage Advertisements' section, you will also have to renew it by clicking on 'Renew' link in the right most 'Options' column. It will renew the Ad and will give it 100,000 Views.");?>
							</div>
					</li>
					<li>
						<a href="javascript:void(0);" onClick="faq_show('faq_28');"><?php echo $this->translate("Q: Is it possible to let users create free trial Ads on my site valid only for a limited period of time?");?></a>
						<div class='faq' style='display: none;' id='faq_28'>
							<?php echo $this->translate('Ans: Yes, it is possible to do so by doing the following things:<br />1) You can disable the existing free package from the "Manage Ad Packages" section at the admin panel of this plugin.<br />2) Now, create a new free package from there and you can set the specifications of that package according to what features you want to give users in that and then in the field "Pricing Model", select "Pay For Days" option and do not select the checkbox for "Enable Ad Renewal => Ad creators will be able to renew their ads of this package before expiry." so that users will not be able renew their Ad and use this package for more than the no. of days you have set there.<br />3) Also, in future when you do not want users to create free Ads anymore at your site, you can disable all the FREE packages from "Manage Ad Packages" section.');?>
							</div>
					</li>
				</ul>
		</div>
	<?php break; ?>

	<?php case 'blocks': ?>
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("Q. How can I place an 'Ad Block' on a Non-widgetized page?");?></a>
					<div class='faq' style='display: block;' id='faq_3'>
                      <p>To do so, please follow the steps mentioned below:</p>
                      <p>Step 1: Open the desired file.</p>
                       <p>Step 2: Copy and paste the below code at desired position in the file:</p><br> <div class ="code">
						<?php echo '&lt;?php  $cmad_show_type="all";  &nbsp;&nbsp;<b>// Select the any one type of Ad - all, sponsored, featured</b> <br>
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;$cmad_itemCount = 10;   &nbsp;&nbsp; <b>// Enter the number of Ads</b> <br> 
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; $cmad_packageIds = array();      <b>// Packages Ids in Array Like array(id1,id2,....,idn)</b><br><br>
 echo $this->content()->renderWidget("communityad.ads", array( "show_type" => $cmad_show_type, "itemCount"=>$cmad_itemCount,"packageIds"=>$cmad_packageIds)); ?&gt;'?>
						</div></div>
				</li>

<!--				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php //echo $this->translate("Q How do I add an Ad Block on a page?");?></a>
					<div class='faq' style='display: none;' id='faq_4'>
						<?php //echo $this->translate('Ans: Please refer to the above question : "On which all pages of my site can I show ads?"');?>
						</div>
				</li>-->
			</ul>
		</div>
	<?php break; ?>

	<?php case 'targeting': ?>
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("Q What is Ad Targeting?");?></a>
					<div class='faq' style='display: none;' id='faq_6'>
						<?php echo $this->translate("Ans: Ad targeting enables advertisers to reach their target audience which is more likely to be interested in their ads. If an ad package allows targeting, then advertisers using it will be able to configure targeting for their ad based on user profile fields. Only users having profiles matching with the targeting criteria will be shown the targeted ad. You can configure settings for ads targeting from the Targeting Settings section. Basic targeting occurs on specific profile fields (gender, city, country, education, interests, etc). Advanced targeting occurs such that advertiser will be able to select the Profile Type to which the ad should be targeted and the generic profile fields for that profile type on which targeting should be done.");?>
						</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("Q What are specific profile fields and generic profile fields?");?></a>
					<div class='faq' style='display: none;' id='faq_7'>
						<?php echo $this->translate('Ans: To better understand this, please go to the "Settings" > "Profile Questions" section of this Admin Panel. Once there, click on "edit" for a field. In the lightbox, click on the "Question Type" field. You will see the Generic and Specific fields in the dropdown. Fields of type: gender, city, country, education, interests, etc are specific profile fields and fields of type single line text input, select box, etc are generic profile fields.');?>
						</div>
				</li>
			</ul>
		</div>
	<?php break; ?>

<?php case 'ajax': ?>
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_17');"><?php echo $this->translate("Q. How do I get Ajax-Based display of Ad Blocks on my site?");?></a>
					<div class='faq' style='display: none;' id='faq_17'>
						<?php echo $this->translate('Ans: To get Ajax-based Ad Blocks on your site, please go through the following points:<br /><br />1) In "Global Settings", you can "Yes" for the field "Default Ajax Based Display of Ad Blocks" to make the display of all the ad blocks as "Ajax-based" by default.<br />2) When you create a new block from the "Manage Blocks" section at admin panel, you can choose to make that block as ajax-based by selecting "Yes" corresponding to the field "Ajax Based Display".<br />3) If you place an ad block on a page from "Layout" > "Layout Editor" section and do not add it from "Manage Blocks" section, in that case it will be ajax-based if "Default Ajax Based Display of Ad Blocks" in Global Settings has been selected as "Yes".<br /><br />In this way, you can select for every single Ad block on your site to be displayed as Ajax-based or not on the basis of the criticality of that page on which it has been rendered.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_24');"><?php echo $this->translate("Q. What are Ajax based Ads?");?></a>
					<div class='faq' style='display: none;' id='faq_24'>
						<?php echo $this->translate("Ans: Ajax based Ads means that the Ad blocks containing Ads will be dispalyed through ajax.<br /><br />Now, the choice is yours that you want to make the ads blocks display on your site as ajax based or not.You can enable it from the 'Default Ajax Based Display of Ad Blocks' field in the Global Settings section of this plugin. And then you can customize this setting for each and every ad block displaying at your site from 'Manage Ad Blocks' section.<br />It just depend on the criticality of the page on which the ad block is being rendered. For ex-<br /><br />1) If you feel like that your page is too heavy and normal ads will make it even more heavy. In that case you can make the ad blocks for that page as 'Ajax based' from Manage Blocks section of this plugin.<br />2) If you think that a particular page has to be loaded fast and you don't want Ads to slow down its execution or display even for a little, in that case you can make the Ad blocks for that page as ajax based.<br /><br />Ajax based ad blocks are displayed on the page after it is rendered to the user. But there might be some places at your site where advertisement display is very important or the main priority. In that case you would not like it if the ad blocks are being displayed slowly after the full render of the whole page. So, in such a case you don't need to make them as ajax-based.");?>
					</div>
				</li>
			</ul>
		</div>
	<?php break; ?>

	<?php case 'stats': ?>
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_12');"><?php echo $this->translate("Q. Can advertisers see the performance of their Ads?");?></a>
					<div class='faq' style='display: none;' id='faq_12'>
						<?php echo $this->translate('Ans: Yes, advertisements can see both graphical as well as statistical reports of their ads and campaigns. Graphical statistics are shown in the "My Campaigns" section to users for all their ads and campaigns, whereas tabular statistics can be seen in the "Reports" section. Advertisers can see the clicks, views and CTR (clickthrough rate) for their ads. You can also configure settings for the graphs from the "Graphs Settings" section in the admin panel.');?>
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_25');"><?php echo $this->translate("Q. What are clicks, views and CTR ?");?></a>
					<div class='faq' style='display: none;' id='faq_25'>
						<?php echo $this->translate("Ans: In this plugin, there are 3 types of stats related to an Ad ie. Views, Clicks and CTR.<br /><br />1) Views get incremented as the page is refreshed from the browser. Yes, it means that if you refresh the browser 10 times in a row, it is counted as 10 views.<br />2) Clicks get incremented after being filtered on the basis of IP addresses. More than 1 clicks in a row from the same IP are counted as only 1 click.<br />3) CTR stands for Clicks to Views ratio.<br /><br />Please remember that statistics measured at daily intervals may be based on different time zones, resulting in different daily totals.");?>
					</div>
				</li>
			</ul>
		</div>
	<?php break; ?>

	<?php case 'language': ?>
		<div class="admin_seaocore_files_wrapper">
			<ul class="admin_seaocore_files seaocore_faq">
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_19');"><?php echo $this->translate("Q: There are multiple languages on my site. How should this plugin be used for non-English languages?");?></a>
					<div class='faq' style='display: none;' id='faq_19'>
						<?php echo $this->translate("Ans : This plugin only comes with English language by default. For other languages, you need to copy the 'communityad.csv' language file from the directory: '/application/languages/en/' of your site, to the directory '/application/languages/LANGUAGE_PACK_DIRECTORY/'. Then, go to the section 'Layout' > 'Language Manager' in the Admin Panel and edit phrases for the desired language.");?>
						</div>
				</li>
				<li>
					<a href="javascript:void(0);" onClick="faq_show('faq_20');"><?php echo $this->translate("Q: In the Ad creation step, the error: 'exception 'Engine_Exception' with message 'No subject translation available for system email 'communityad_userad_disapproved'' in /var/www/application/modules/Core/Api/Mail.php:395' is coming. Why is this coming and what should be done to resolve this?");?></a>
					<div class='faq' style='display: none;' id='faq_20'>
						<?php echo $this->translate("Ans : This error comes if you are using a language other than English on your site, and have not done the required language file settings. For resolving this, you need to copy the 'communityad.csv' language file from the directory: '/application/languages/en/' of your site, to the directory '/application/languages/LANGUAGE_PACK_DIRECTORY/'. Then, go to the section 'Layout' > 'Language Manager' in the Admin Panel and edit phrases for the desired language.");?>
					</div>
				</li>
			</ul>
		</div>
	<?php break; ?>
<?php endswitch; ?>