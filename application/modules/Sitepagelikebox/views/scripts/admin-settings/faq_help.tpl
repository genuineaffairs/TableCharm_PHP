<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagelikebox
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.tpl 2011-10-10 9:40:21Z SocialEngineAddOns $
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
      <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("Where will the users see the option to generate code for Embeddable Page Badges / Like Boxes?"); ?></a>
      <div class='faq' style='display: none;' id='faq_1'>
        <?php echo $this->translate("Ans: Users will be able to configure and generate code for Embeddable Page Badges / Like Boxes from the “Marketing” section of their Page Dashboard."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("I want the embeddable badges / like box for Pages to match with the theme of my site. How can I do this?"); ?></a>
      <div class='faq' style='display: none;' id='faq_2'>
        <?php echo $this->translate("Ans. You can configure the 2 available color schemes (light and dark) for embeddable badges to match with your site’s theme. To do so, please go to the Color Scheme section and configure the color schemes using the style editor."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("The logo of my site is not being shown in “Powered By” on Embeddable Page Badges / Like Boxes. What would be the reason?"); ?></a>
      <div class='faq' style='display: none;' id='faq_3'>
        <?php echo $this->translate("Ans. Please use these 3 fields in Global Settings to enable “Powered By” and upload the logo of your website: “Powered By”, “Logo or Title”, and “Upload Logo”."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("How can I set the default height and width of the Embeddable Page Badges / Like Boxes?"); ?></a>
      <div class='faq' style='display: none;' id='faq_4'>
        <?php echo $this->translate("Ans. Use the “Badge Width” and “Badge Height” fields in Global Settings. If you select “No” for “Badge Width”, you will be able to specify the “Default Badge Width”, and similarly for Badge Height."); ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_5');"><?php echo $this->translate("I want Embeddable Page Badges / Like Boxes to be available to only certain directory items / pages on my site. How can this be done?"); ?></a>
      <div class='faq' style='display: none;' id='faq_5'>
        <?php echo $this->translate("Ans. You can enable packages for pages on your site from Global Settings, and make “External Badge” App available to only certain packages. If you have not enabled packages, then from Member Level Settings, you can make External Embeddable Badge / Like Box to be available for pages of only certain member levels."); ?>
      </div>
    </li>


    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_6');"><?php echo $this->translate("I do not want all the Tabs / Page Apps to be shown in the Embeddable Page Badges / Like Boxes. I just want selected tabs to be available. What should I do?"); ?></a>
      <div class='faq' style='display: none;' id='faq_6'>
        <?php echo $this->translate("You can choose the tabs that should be available for display in embeddable page badges / like boxes from Global Settings section of this plugin using the “Tabs from Apps” field and the fields above it. There are various extensions available for Directory / Pages Plugin for additional functionalities and tabs. To see the complete list of available extensions, please visit: <a href='http://www.socialengineaddons.com/catalog/directory-pages-extensions' target='_blank' >http://www.socialengineaddons.com/catalog/directory-pages-extensions.</a>"); ?>
      </div>
    </li>

    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_7');"><?php echo $this->translate("How are the sequence of tabs and tab names in Embeddable Page Badges / Like Boxes decided?"); ?></a>
      <div class='faq' style='display: none;' id='faq_7'>
        <?php echo $this->translate("Ans. The sequence of tabs and their names in Embeddable Page Badges / Like Boxes is the same as that on the main Page Profile."); ?>
      </div>
    </li>

    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_8');"><?php echo $this->translate("The CSS of this plugin is not coming on my site. What should I do?"); ?></a>
      <div class='faq' style='display: none;' id='faq_8'>
        <?php echo $this->translate("Ans. Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'."); ?>
      </div>
    </li>

  </ul>
</div>