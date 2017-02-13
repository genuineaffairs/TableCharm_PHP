<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: guidelines.tpl 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
  <?php echo $this->translate('Mobile / Tablet Plugin') ?>
</h2>
<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<div class='clear'>
  <a href="<?php echo $this->url(array('module' => 'sitemobile', 'controller' => 'themes'), 'admin_default', true) ?>" class="buttonlink seaocore_icon_back"><?php echo $this->translate("Back to Mobile / Tablet Theme Editor"); ?></a>
    
<div class='' style="margin-top:15px;" id="create-theme">
    <h3 class="sm-page-head">
       <?php echo $this->translate("Guidelines for creating a new theme") ?>
    </h3>
    <p><?php echo $this->translate('Please follow the steps below to create a new theme. After following these guidelines for creating a new theme, it will be listed in the "Available Themes" block of the Mobile / Tablet Theme Editor section. You will need to follow these guidelines to create a new theme:'); ?>
      <br /> 
      </p><br />
    <div class="admin_seaocore_files_wrapper" >
      <ul class="admin_seaocore_guidelines">
        <li>	
          <div class="steps">
            <a></a>
            <div id="guidline2-step-1">
              <p>
                <?php echo $this->translate('a) Click on "Clone" link available in the top right corner.') ?>
              </p>            
              <p>
                <?php echo $this->translate('b)  Now, in the "Theme Manager" form, fill the Theme Title, Theme Description and Theme Author\'s name. 
') ?>
              </p>            
              <p>
                <?php echo $this->translate("c) From the \"Theme to Clone\" drop-down field, you need to select the theme which you want to clone and edit to create your own theme.") ?>
              </p>            
              <p>
                <?php echo $this->translate('d) After filling up the form, click on "Clone" button to create new theme.') ?>
              </p>
              <p><?php echo $this->translate('e) The new theme will be displayed in the "Available Themes" block.'); ?><br />
             </p>
              <p><?php echo $this->translate('f)  From "Available Themes" block, you can easily activate the new theme by clicking on "Activate Theme" button and edit it.') ?>	
              </p>
              <br />
              <p><?php echo $this->translate('- Note: we recommend you to create new theme to configure the look and feel of your mobile site because if you do changes in the default themes provided with this plugin, then the changes made by you will be overwritten after the plugin upgrade. 

') ?>	
              </p>
            </div>
          </div>
        </li>
      </ul>
    </div>
</div>
    
<!--  </div> -->
  <br />
  <div class='' style="margin-top:15px;" id="edit-theme">
    <h3 class="sm-page-head">
      <?php echo $this->translate("Guidelines for editing active theme") ?>
    </h3>
    <p><?php echo $this->translate("Please follow the steps below to edit a theme:"); ?>
       <br /> 
      </p><br />
      
    <div class="admin_seaocore_files_wrapper" >
      <ul class="admin_seaocore_guidelines">
        <li>	
          <div class="steps">
            <a></a>
            <div id="guidline2-step-1">
              <p>
                <?php echo $this->translate('a) Activate the theme from the "Available Themes" block, which you want to edit.') ?>
              </p>            
              <p>
                <?php echo $this->translate('b) Select the css file which you want to edit from the "Editing File" pull-down from the "Active Theme" block.') ?>
              </p>            
              <p>
                <?php echo $this->translate('c) Select "constants.css" file, to change the widgets\' border colors, border colors of activity feed foreground and background color, etc. Here, you can make your custom changes in the editor.
') ?>
              </p>            
              <p>
                <?php echo $this->translate('d) Select "structure.css" file, to change the structure of the elements like header, footer, buttons, etc of your site. Here, you can make your custom changes in the editor. ') ?>
              </p>
              <p><?php echo $this->translate('e) After making your custom changes in constants.css and structure.css files, save your changes by clicking on "Save Changes" button available in the bottom of the editor.'); ?><br />
             </p>
              <p><?php echo $this->translate('f) Now, if you want to revert your changes, then you can do that by clicking on the "revert" link available in the top-right corner.
') ?>	
              </p>
              <p>
                 <?php echo $this->translate('g) Select "theme.css" file, to make color changes in the elements of your site. When you select this file, a Theme Roller will open in the place of editor. Here, you will see five standard themes (A,B,C,D,E).'); ?>
              </p>
              <p>
                 <?php echo $this->translate('h) Configure your theme settings from the Theme Settings Form which is available in the left hand side of the Theme Roller. Please follow the below steps to configure five tabs available in this form:
'); ?>
              </p>
                <p style="margin-left:5em" >
                  <?php echo $this->translate('i) Select "Global" Tab, to globally change colors of fonts, Active state, corner radii, etc.') ?></p>
                 <p style="margin-left:5em" >
                  <?php echo $this->translate('ii) Select "A" tab, to change the color in header, footer, dialog box background color and loading image color, etc. You can view the changes in right side "A" theme Layout.') ?></p>
                 <p style="margin-left:5em" >
                  <?php echo $this->translate('iii) Select "B" tab to change the colors of submit buttons, background of status box, etc. You can view the changes in right side "B" theme Layout.') ?></p>
                 <p style="margin-left:5em" >
                  <?php echo $this->translate('iv) Select "C" tab, to change the color of all listing elements and main content of pages, etc. You can view the changes in right side "C" theme Layout.
') ?>
                </p>
                <p style="margin-left:5em" >
                  <?php echo $this->translate('v) Select "D" tab to change the colors of create new account button, etc. You can view the changes in right side "D" theme Layout.
') ?>
                </p>
                <p style="margin-left:5em" >
                  <?php echo $this->translate('vi) Select "E" tab, to change the color of notification page, message page, tip, etc. You can view the changes in right side "E" theme Layout.

') ?>
                </p>
                <p>
                   <?php echo $this->translate('i) You can also change the color easily by dragging and dropping desired colors from the color bar into to any theme layout of your choice.'); ?>
</p>
<p>
                   <?php echo $this->translate('j) Once you are done with your changes, click on "Save theme" button available in the top of the Theme Roller.'); ?>
</p>
 <br />
              <p><?php echo $this->translate('- Note: we recommend you to create new theme to configure the look and feel of your mobile site because if you do changes in the default themes provided with this plugin, then the changes made by you will be overwritten after the plugin upgrade.') ?>	
              </p>
            </div>
          </div>
        </li>
      </ul>
    </div>
</div>

