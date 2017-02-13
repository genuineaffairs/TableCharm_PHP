<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Search.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Document_Form_Search extends Fields_Form_Search
{
  protected $_fieldType = 'document';
	protected $_searchForm;
  
  //Changes in onchange event function for mobile mode.
  protected $_hasMobileMode = false;

  public function getHasMobileMode() {
    return $this->_hasMobileMode;
  }

  public function setHasMobileMode($flage) {
    $this->_hasMobileMode = $flage;
    return $this;
  }
  
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'documents_browse_filters field_search_criteria',
      ))
			->setMethod('GET');

		$this->getMemberTypeElement();

    $this->getAdditionalOptionsElement();

		parent::init();

    $this->loadDefaultDecorators();
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();

		if($module == 'document' && $controller == 'index' && $action == 'manage') {
			$this->setAction($view->url(array('action' => 'manage'), 'document_manage', true))->getDecorator('HtmlTag')->setOption('class', 'browsemembers_criteria');
		}
		else {
			$this->setAction($view->url(array('action' => 'browse'), 'document_browse', true))->getDecorator('HtmlTag')->setOption('class', 'browsemembers_criteria');
		}
  }

  public function getMemberTypeElement() {

    $multiOptions = array('' => ' ');
    $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias($this->_fieldType, 'profile_type');
    if( count($profileTypeFields) !== 1 || !isset($profileTypeFields['profile_type']) ) return;
    $profileTypeField = $profileTypeFields['profile_type'];

    $options = $profileTypeField->getOptions();

    foreach( $options as $option ) {
      $multiOptions[$option->option_id] = $option->label;
    }

    $this->addElement('hidden', 'profile_type', array(
      'order' => -1000001,
      'class' =>
        'field_toggle' . ' ' .
        'parent_' . 0 . ' ' .
        'option_' . 0 . ' ' .
        'field_'  . $profileTypeField->field_id  . ' ',
      'onchange' => 'changeFields($(this));',
      'multiOptions' => $multiOptions,
    ));
    return $this->profile_type;
  }

	public function getAdditionalOptionsElement()
	{

    $this->addElement('Hidden', 'page', array(
      'order' => 99990,
    ));

    $this->addElement('Hidden', 'tag', array(
      'order' => 99991,
    ));

    $this->addElement('Hidden', 'start_date', array(
      'order' => 99992,
    ));

    $this->addElement('Hidden', 'end_date', array(
      'order' => 99993,
    ));

    $this->addElement('Hidden', 'category', array(
            'order' => 99994,
    ));

    $this->addElement('Hidden', 'subcategory', array(
            'order' => 99995,
    ));

    $this->addElement('Hidden', 'subsubcategory', array(
            'order' => 99996,
    ));

    $this->addElement('Hidden', 'categoryname', array(
            'order' => 99997,
    ));

    $this->addElement('Hidden', 'subcategoryname', array(
            'order' => 99998,
    ));

    $this->addElement('Hidden', 'subsubcategoryname', array(
            'order' => 99999,
    ));

		$this->_searchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');

    $row = $this->_searchForm->getFieldsOptions('document', 'search');
    if (!empty($row) && !empty($row->display)) {
			$this->addElement('Text', 'search', array(
				'label' => 'Search Documents',
				'order' => $row->order,
				'decorators' => array(
					'ViewHelper',
					array('Label', array('tag' => 'span')),
					array('HtmlTag', array('tag' => 'li'))
				),
			));
		}

    $row = $this->_searchForm->getFieldsOptions('document', 'orderby');
    if (!empty($row) && !empty($row->display)) {
			$this->addElement('Select', 'orderby', array(
				'label' => 'Browse By',
				'multiOptions' => array(
					'document_id' => 'Most Recent',
					'views' => 'Most Viewed',
					'comment_count' => 'Most Commented',
					'like_count' => 'Most Liked',
					'document_title' => 'Alphabetical',
				),
                             'onchange' => $this->gethasMobileMode() ? '' : 'searchDocuments();',
				'order' => $row->order,
				'decorators' => array(
					'ViewHelper',
					array('Label', array('tag' => 'span')),
					array('HtmlTag', array('tag' => 'li'))
				),
			));
		}

    $row = $this->_searchForm->getFieldsOptions('document', 'draft');
    if (!empty($row) && !empty($row->display)) {
			$this->addElement('Select', 'draft', array(
				'label' => 'Show',
				'multiOptions' => array(
					' ' => 'All Documents',
					'0' => 'Published Documents',
					'1' => 'Only Drafts',
				),
				'onchange' => $this->gethasMobileMode() ? '' : 'searchDocuments();',
				'order' => $row->order,
				'decorators' => array(
					'ViewHelper',
					array('Label', array('tag' => 'span')),
					array('HtmlTag', array('tag' => 'li'))
				),
			));
		}

    $row = $this->_searchForm->getFieldsOptions('document', 'show');
    if (!empty($row) && !empty($row->display)) {
      $value_default = 1;
      $multiOptions = array(
					'1' => "Everyone's Documents",
					'2' => "My Friends' Documents",
				);
      
      $settings = Engine_Api::_()->getApi('settings', 'core');
      $enableNetwork = $settings->getSetting('document.network', 0);
      if (empty($enableNetwork)) {
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
        $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer_id));

        if (!empty($viewerNetwork)) {
          $multiOptions["3"] = 'Only My Networks';
          $browseDefaulNetwork = $settings->getSetting('document.default.show', 0);

          if (!isset($_GET['show']) && !empty($browseDefaulNetwork)) {
            $value_default = 3;
          } elseif (isset($_GET['show'])) {
            $value_default = $_GET['show'];
          }
        }
      }
      
			$this->addElement('Select', 'show', array(
				'label' => 'Show',
				'multiOptions' => $multiOptions,
				'onchange' => $this->gethasMobileMode() ? '' : 'searchDocuments();',
				'order' => $row->order,
				'decorators' => array(
					'ViewHelper',
					array('Label', array('tag' => 'span')),
					array('HtmlTag', array('tag' => 'li'))
				),
        'value' => $value_default,  
			));
		}

    $row = $this->_searchForm->getFieldsOptions('document', 'category_id');
    if (!empty($row) && !empty($row->display)) {
			$categories = Engine_Api::_()->getDbTable('categories', 'document')->getCategories(0, 0);
			if (count($categories) != 0) {
				$categories_prepared[0] = "";
				foreach ($categories as $category) {
					$categories_prepared[$category->category_id] = $category->category_name;
				}
                                
                                if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
					$onChangeEvent = " var profile_type = getProfileType($(this).value);
															$('profile_type').value = profile_type;
															changeFields($('profile_type'));
															subcategories(this.value, '', '');";
					$categoryFiles = 'application/modules/Document/views/scripts/_Subcategory.tpl';
				}
				else {
					$onChangeEvent = "sm4.core.category.set(this.value, 'subcategory');";
					$categoryFiles = 'application/modules/Document/views/sitemobile/scripts/_Subcategory.tpl';
				}

				$this->addElement('Select', 'category_id', array(
								'label' => 'Category',
								'order' => $row->order,
								'multiOptions' => $categories_prepared,
								'onchange' => $onChangeEvent,
									'decorators' => array(
												'ViewHelper',
												array('Label', array('tag' => 'span')),
												array('HtmlTag', array('tag' => 'li'))),
				));
			
				$this->addElement('Select', 'subcategory_id', array(
						'RegisterInArrayValidator' => false,
						'order' => $row->order + 1,
						'decorators' => array(array('ViewScript', array(
																		'viewScript' => $categoryFiles,
																		'class' => 'form element')))
					));

				$this->addElement('Select', 'subsubcategory_id', array(
						'RegisterInArrayValidator' => false,
						'order' => $row->order + 1,
						'decorators' => array(array('ViewScript', array(
																		'viewScript' => $categoryFiles,
																		'class' => 'form element')))
				));
			}
		}
                
        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->addElement('Button', 'done', array(
                'label' => 'Search',
                'order' => 999999999,
                'decorators' => array(
                    'ViewHelper',
                    array('HtmlTag', array('tag' => 'li'))
                ),
                'onchange' => $this->gethasMobileMode() ? '' : 'searchDocuments();',
            ));
        } else {
            //SITEMOBILE SUBMIT TYPE BUTTON IN SEARCH FORM
            $this->addElement('Button', 'done', array(
                'label' => 'Search',
                'type' => 'submit',
                'order' => 999999999,
                'ignore' => true,
            ));
        }
	}
}