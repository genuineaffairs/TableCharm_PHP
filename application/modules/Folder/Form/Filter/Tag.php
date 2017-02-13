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
 
 
 
class Folder_Form_Filter_Tag extends Fields_Form_Search
{
  protected $_fieldType = 'folder';
  
  public function init()
  {
    parent::init();

    $this->loadDefaultDecorators();

    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'folders_browse_filters field_search_criteria',
      ))
      ->getDecorator('HtmlTag')
        ->setOption('class', 'browsefolders_criteria folders_browse_filters');
    

    // Add custom elements
    $this->getAdditionalOptionsElement();
    
  }

  public function getAdditionalOptionsElement()
  {
    $i = -5000;
    
    $this->addElement('Hidden', 'page', array(
      'order' => $i++,
    ));


    $this->addElement('Text', 'keyword', array(
      'label' => 'Keywords',
      'order' => $i++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));     
    
    $categories = Engine_Api::_()->folder()->getCategories();
    $categories_prepared = Engine_Api::_()->folder()->convertCategoriesToArray($categories);
    $categories_prepared = array(""=>"") + $categories_prepared;
    
    $this->addElement('Select', 'category', array(
      'label' => 'Category',
      'multiOptions' => $categories_prepared,
      'order' => $i++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));     
   
    
    $this->addElement('Select', 'media', array(
      'label' => 'Media Type',
      'multiOptions' => array("" => "") + Folder_Model_Folder::getMediaTypes(),
      'order' => $i++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));
    
    $this->addElement('Select', 'period', array(
      'label' => 'Time Period',
      'multiOptions' => array(
        '' => '',
        '24hrs' => 'Last 24 Hours',
        'week' => '7 Days',
        'month' => '30 Days',
        'quarter' => '3 Months',
        'year' => '12 Months',
        'all' => 'All Time',
      ),
      'order' => $i++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('folder.listperiod', 'month'),
    ));
    
    
    $this->addElement('Select', 'order', array(
      'label' => 'Sort By',
      'multiOptions' => array(
        '' => '',
        'trending' => 'Trending',
        'recent' => 'Most Recent',
      	'lastupdated' => 'Last Updated',
      	'alphabet' => 'Title',
    
        'mostviewed' => 'Most Viewed',
        'mostcommented' => 'Most Commented',
        'mostliked' => 'Most Liked',
        'mostclicked' => 'Most Clicked',
    
        'mostpoints' => 'Most Points',
        'mostvoted' => 'Most Voted',
    		'mostburied' => 'Most Buried',
      ),
      'order' => $i++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('folder.listorder', 'trending'),
    ));
    
    $this->addElement('Select', 'status', array(
      'label' => 'Status',
      'multiOptions' => array(
        '' => '',
        Folder_Model_Folder::STATUS_POPULAR => 'Popular',
				Folder_Model_Folder::STATUS_QUEUED => 'Upcoming',
      ),
      'order' => $i++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));    
    
    /*
    $seperator1 = $this->getElement('separator1');
    $this->removeElement('separator1');
    $seperator1->setOrder($i++);
    $this->addElement($seperator1);
		*/
    if (count($this->_fieldElements)) {
      $this->_order['separator1'] = $i++;
    }
    else {
      $this->removeElement('separator1');
    }
    
    $j = 10000000;  
    
    $this->addElement('Button', 'done', array(
      'label' => 'Search',
      'type' => 'submit',
      'ignore' => true,
      'order' => $j++,
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));
  }
}