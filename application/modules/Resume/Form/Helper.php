<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Resume_Form_Helper
{

  public static function getContentField($name, $options = array())
  {
    static $content_fields = null;
    
    if ($content_fields === null)
    {
      $content_fields = array(
      
        'title' => array(
                'Text',
                'title',
                array(
                  'label' => 'Title',
                )
              ),
        'max' => array(
                'Text',
                'max',
                array(
                  'label' => 'Max Resumes',
                  'value' => 5,
                ),
              ),
        'user_type' => array(
                'Radio',
                'user_type',
                array(
                    'label' => 'User',
                    'multiOptions' => array(
                      'owner' => 'OWNER - resume\'s created by owner of the current viewing page',
                      'viewer' => 'VIEWER - resume\'s created by the current active logged in member',
                    ),
                    'value' => 'owner',
                  ),
                ),
        'user' => array(
                'Text',
                'user',
                array(
                  'label' => 'User',
                )
              ),
        'keyword' => array(
                'Text',
                'keyword',
                array(
                  'label' => 'Keywords',
                )
              ),
        'location' => array(
                'Text',
                'location',
                array(
                  'label' => 'Location',
                )
              ),
        'distance' => array(
                'Text',
                'distance',
                array(
                  'label' => 'Within Distance',
                  'multiOptions' => Resume_Form_Helper::getDistanceOptions(),
                )
              ),    
        'category' => array(
                'Select', 
                'category',
                array(
                  'label' => 'Category',
                  'multiOptions' => array(""=>"") + Engine_Api::_()->resume()->getCategoryOptions(),
                )
              ),      
        'order' => array(
                'Select', 
                'order',
                array(
                  'label' => 'Sort By',
                  'multiOptions' => self::getOrderOptions() + array(
                    'random' => 'Randomized',
                  ),
                  'value' => 'recent',
                )
              ),
        'period' => array(
                'Select', 
                'period',
                array(
                  'label' => 'Time Period',
                  'multiOptions' => array(
                    'all' => 'All Time',
                    '24hrs' => 'Last 24 Hours',
                    'week' => '7 Days',
                    'month' => '30 Days',
                    'quarter' => '3 Months',
                    'year' => '12 Months',
                  ),
                )
              ),

        'display_style' => array(
                'Radio',
                'display_style',
                array(
                  'label' => 'Display Style',
                  'multiOptions' => array(
                    'wide' => "Wide (main middle column)",
                    'narrow' => "Narrow (left / side side column)",
                  ),
                  'value' => 'wide',
                )
              ),
        'showphoto' => array(
                'Select', 
                'showphoto',
                array(
                  'label' => 'Show Photo',
                  'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                  ),
                  'value' => 1,
                )
              ),  
        'showdetails' => array(
                'Select', 
                'showdetails',
                array(
                  'label' => 'Show Details',
                  
                  'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                  ),
                  'value' => 1,
                )
              ), 
        'showmeta' =>  array(
                'Select', 
                'showmeta',
                array(
                  'label' => 'Show Meta',
                  
                  'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                  ),
                  'value' => 1,
                )
              ), 
        'showdescription' =>  array(
                'Select', 
                'showdescription',
                array(
                  'label' => 'Show Description',
                  'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                  ),
                  'value' => 1,
                )
              ),  
        'featured' => array(
                'Select', 
                'featured',
                array(
                  'label' => 'Show only featured resumes?',
                  'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                  ),
                  'value' => 0,
                )
              ),
        'sponsored' => array(
                'Select', 
                'sponsored',
                array(
                  'label' => 'Show only sponsored resumes?',
                  'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                  ),
                  'value' => 0,
                )
              ),     
  
        'showmemberitemlist' => array(
            'Select', 
            'showmemberitemlist',
            array(
              'label' => 'Show Member\'s Resume Link',
              'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
              ),
              'value' => 1,
            )
          ),
     
      );
    
    }
    
    if (array_key_exists($name, $content_fields)) {
      $field = $content_fields[$name];
    }
    else {
      $field = array(
        'Text',
        $name,
        array(
          'label' => $name
        ),
      );
    }
    
    $keys = array('value', 'label', 'multiOptions');
    foreach ($options as $key => $value) {
      if (in_array($key, $keys)) {
        $field[2][$key] = $value;
      }
    }
    
    return $field;
  }
  
  public static function getOrderOptions()
  {
    $options = array(
        'recent' => 'Most Recent',
      	'lastupdated' => 'Last Updated',
    
        'alphabet' => 'Alphabetically',
        /*'pricehighest' => 'Highest Price',
        'pricelowest' => 'Lowest Price',
*/
        'mostviewed' => 'Most Viewed',
  /*      'mostcommented' => 'Most Commented',
        'mostliked' => 'Most Liked',
    *//*
        'status' => 'Status',
        'expiration_date' => 'Expiration Date',
       */
    );
    
    return $options;
  }
  
  public static function getDistanceOptions()
  {
    $unit = Engine_Api::_()->getApi('settings', 'core')->getSetting('resume.distanceunit', Radcodes_Lib_Helper_Unit::UNIT_MILE);
    
    $distances = array(''=>"");
    $distance_ranges = array(5,10,25,50,100,250,500);
    foreach ($distance_ranges as $distance) {
      $distances[$distance] = "$distance $unit";
    }
    
    return $distances;
  }  
  
  public static function getSectionChildTypeOptions()
  {
    $options = array(
      Resume_Model_Section::CHILD_TYPE_TEXT => 'Text',
      Resume_Model_Section::CHILD_TYPE_EDUCATION => 'Education',
      Resume_Model_Section::CHILD_TYPE_EMPLOYMENT => 'Experience',
      Resume_Model_Section::CHILD_TYPE_SPORTING_HISTORY => 'Sporting History',
      Resume_Model_Section::CHILD_TYPE_AWARD => 'Honours & Awards',
      Resume_Model_Section::CHILD_TYPE_QUALIFICATION => 'Qualifications',
      Resume_Model_Section::CHILD_TYPE_COACHING_HISTORY => 'Coaching History',
    );
    
    return $options;
  }
}