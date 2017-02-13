<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Mgsl
 * @copyright  Copyright 2001-2013 Technobd Web Solution (Pvt.) Limited.
 * @license    http://www.socialengine-expert.com/
 * @version    $Id: Controller.php 0001 2013-12-2 02:08:08Z aditya $
 * @author     Aditya
 */

/**
 * @category   Application_Core
 * @package    Mgsl
 * @copyright  Copyright 2001-2013 Technobd Web Solution (Pvt.) Limited.
 * @license    http://www.socialengine-expert.com/
 */
class Mgsl_Widget_UserProfileSummaryController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        // Don't render this if not authorized
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }
        // Get subject
        $subject = Engine_Api::_()->core()->getSubject('user');

        // get the subject profile field structured
        $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($subject);

        // nessary profile field contianer
        $profileData = array();

        // get the profile field map
        foreach ($fieldStructure as $map) {
            $field = $map->getChild();
            $value = $field->getValue($subject);
            if (is_object($value)) {
                if ($field->label === 'Primary Sport') {
                    $profileData[$field->label] = $field->getOption($value->getValue())->label;
                }
                if ($field->label === 'Other Sports') {
                    $profileData[$field->label] = $field->getOption($value->getValue())->label;
                }
                if ($field->label === 'Participation Level') {
                    $profileData[$field->label] = $field->getOption($value->getValue())->label;
                } else {
                    $profileData[$field->label] = $value->getValue();
                }
            }
        }
        
        
        // primary Sports
        $this->view->primary_sport = $profileData['Primary Sport'];
        
        // get the participition data for specific user
        $valueTable = Engine_Api::_()->fields()->getTable('user', 'values');
        $optonTable = Engine_Api::_()->fields()->getTable('user', 'options');
        $select = $valueTable->select()
                ->where('item_id = ?', $subject->getIdentity())
                ->where('field_id = ?', 382);
        $valueData = $valueTable->fetchAll($select);
        // get the all option value that the user set
        if (count($valueData) > 0) {
        $optionValue=array();
        foreach ($valueData as $data)
            $optionValue[]=$data->value;
        }
        // get the option name
        $select = $optonTable->select()
                  ->where('option_id IN(?)', $optionValue);
           $optionNames = $optonTable->fetchAll($select);
          
        // get the perticipition label
        $participation_Level = array();
        foreach ($optionNames as $optionName)
            $participation_Level[]=$optionName->label;
        
        // perticipition level in to view
        $this->view->participation_level= implode(', ', $participation_Level);
        
        // get the resident name
        if ($profileData["Country of Residence"] != null) {
            try {
                $locale = new Zend_Locale(Zend_Locale::BROWSER);
                $countries = $locale->getTranslationList('Territory', Zend_Locale::BROWSER, 2);
            } catch (exception $e) {
                $locale = new Zend_Locale('en_US');
                $countries = $locale->getTranslationList('Territory', 'en_US', 2);
            }
            $this->view->residence= $countries[$profileData["Country of Residence"]];
        }
        
    }

}
