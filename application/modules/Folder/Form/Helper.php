<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Folder
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Folder_Form_Helper
{

  public static function getContentField($name, $options = array())
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
                'label' => 'Max Items',
                'value' => 5,
              ),
            ),
      'parent_type' => array(
              'Select',
              'parent_type',
              array(
                  'label' => 'Parent Type',
                  'multiOptions' => array(
                    '' => 'Everything',
                  ) + self::getAvailableTypes(),
                  'value' => '',
                ),
              ),   
      'parent_id' => array(
              'Text',
              'parent_id',
              array(
                  'label' => 'Parent ID',
                ),
              ),
      'user_type' => array(
              'Radio',
              'user_type',
              array(
                  'label' => 'User',
                  'multiOptions' => array(
                    'owner' => 'OWNER - folder\'s created by owner of the current viewing page',
                    'viewer' => 'VIEWER - folder\'s created by the current active logged in member',
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
      'category' => array(
              'Select', 
              'category',
              array(
                'label' => 'Category',
                'multiOptions' => array(""=>"") + Engine_Api::_()->folder()->convertCategoriesToArray(Engine_Api::_()->folder()->getCategories()),
              )
            ),       
      'order' => array(
              'Select', 
              'order',
              array(
                'label' => 'Sort By',
                'multiOptions' => array(
                
                  'recent' => 'Most Recent',
                  'lastupdated' => 'Last Updated',
              
                  'alphabet' => 'Folder Name',
              
                  'mostviewed' => 'Most Viewed',
                  'mostcommented' => 'Most Commented',
                  'mostliked' => 'Most Liked',
             
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
                'label' => 'Show only featured folders?',
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
                'label' => 'Show only sponsored folders?',
                'multiOptions' => array(
                  1 => 'Yes',
                  0 => 'No'
                ),
                'value' => 0,
              )
            ),     
      'showemptyresult' => array(
          'Select', 
          'showemptyresult',
          array(
            'label' => 'Show Empty Result',
            'multiOptions' => array(
              0 => 'No',
              1 => 'Yes',
            ),
            'value' => 0,
          )
        ),
      'showmemberitemlist' => array(
          'Select', 
          'showmemberitemlist',
          array(
            'label' => 'Show Member\'s Folder Link',
            'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
            ),
            'value' => 1,
          )
        ),
      'showlinkall' => array(
          'Select', 
          'showlinkall',
          array(
            'label' => 'Show Link All',
            'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
            ),
            'value' => 1,
          )
        ),
    );
    
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
  
  
  public static function getAvailableTypes()
  {
  	$options = array();

    $availableTypes = Engine_Api::_()->folder()->getAvailableParentTypes();
    if( is_array($availableTypes) && count($availableTypes) > 0 ) {
      foreach( $availableTypes as $index => $type ) {
        $options[$type] = strtoupper('ITEM_TYPE_' . $type);
      }
    }
    
    return $options;
  }
  
  public static function getDefaultAllowedFileExtensions()
  {
    return 'doc, docx, log, txt, csv, pps, ppt, pptx, xml, vcf, mp3, wav, wma, avi, flv, mov, mp4, mpg, rm, swf, vob, wmv, bmp, gif, jpg, jpeg, png, psd, tif, wks, xls, xlsx, db, sql, mdb, pdf, css, htm, html, js, xhtml, ttf, dll, bin, cue, pkg, rar, sit, zip, iso';
  }  
}