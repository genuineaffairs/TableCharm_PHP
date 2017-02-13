<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     Jung
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Communityad_Form_Admin_Widget_Ads extends Core_Form_Admin_Widget_Standard {

  public function init() {
    parent::init();

    // Set form attributes
    $this
            ->setTitle('Display Advertisements')
            ->setDescription('Display advertisements on your site. Multiple settings available in the Edit Settings of this widget.')
            ->setAttrib('id', 'form-display-advertisements');



    $this->addElement('Select', 'show_type', array(
        'label' => 'Show Ads',
        'multiOptions' => array(
            'all' => 'All',
            'sponsored' => 'Only Sponsored',
            'featured' => 'Only Featured'
        ),
        'value' => 'all'));

    $this->addElement('Radio', 'loaded_by_ajax', array(
        'label' => 'AJAX Based Display',
        'description' => 'Do you want the content of this widget to be loaded via AJAX after page load (this can be good for the overall webpage loading speed)?',
        'multiOptions' => array(
            '1' => 'Yes',
            '0' => 'No'
        ),
        'value' => 0));

    $this->addElement('Text', 'itemCount', array(
        'label' => 'Count',
        'description' => '(number of Ads to show)',
        'value' => 3));

    $this->addElement('Radio', 'showOnAdboard', array(
        'label' => 'Show on Ad Board',
        'description' => 'Do you want to show this widget on Ad Board page? [Note: Choose \'No\', if you place this wiget in Footer and do not not want to display ads in this widget on Adboard page.]',
        'multiOptions' => array(
            '1' => 'Yes',
            '0' => 'No'
        ),
        'value' => 1));
    $packageList = Engine_Api::_()->getDbtable('packages', 'Communityad')->getEnabledPackageList('default');
    $this->addElement('MultiCheckbox', 'packageIds', array(
        'label' => 'Package',
        'description' => 'Choose packages from below belonging to which ads will be displayed in this widget. [Note: If you want to show ads from all packages, then simply do not select any package.]',
        'multiOptions' => $packageList,
        'value' => 0));
  }

}
