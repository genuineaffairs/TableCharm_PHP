<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagelikebox
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: LikeBox.php 2011-10-10 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagelikebox_Form_LikeBox extends Engine_Form {

  public $_error = array ( ) ;
  protected $_item ;

  public function getItem() {
    return $this->_item ;
  }

  public function setItem( Core_Model_Item_Abstract $item ) {
    $this->_item = $item ;
    return $this ;
  }

  public function init() {
    parent::init() ;
    $sitepage = $this->getItem() ;
    $hasPackageEnable = Engine_Api::_()->sitepage()->hasPackageEnable() ; 
    $paramaName = Engine_Api::_()->sitepagelikebox()->getWidgteParams();
    foreach( $paramaName as $order => $infoArray ) {
			switch($infoArray['name']) {
				case 'advancedactivity.home-feeds':
				case 'activity.feed':
				case 'seaocore.feed':
				$flag = 1;
				break;
				case 'sitepage.info-sitepage':
				$flag1 = 1;
				break;
				case 'sitepage.location-sitepage':
				$flag2 = 1;
				break;
				case 'sitepage.discussion-sitepage':
				$flag3 = 1;
				break;			
				case 'sitepage.photos-sitepage':
				$flag4 = 1;
				break;
				case 'sitepageevent.profile-sitepageevents':
				$flag5 = 1;
				break;
				case 'sitepagepoll.profile-sitepagepolls':
				$flag6 = 1;
				break;
				case 'sitepagenote.profile-sitepagenotes':
				$flag7 = 1;
				break;
				case 'sitepageoffer.profile-sitepageoffers':
				$flag8 = 1;
				break;
				case 'sitepagevideo.profile-sitepagevideos':
				$flag9 = 1;
				break;			
				case 'sitepagemusic.profile-sitepagemusic':
				$flag10 = 1;
				break;
				case 'sitepagereview.profile-sitepagereviews':
				$flag11 = 1;
				break;
				case 'sitepagedocument.profile-sitepagedocuments':
				$flag12 = 1;
				break;			
			}
		}
    $this
        //->setTitle('Like Box Settings')
        // ->setDescription('Overview enables you to create a rich profile for your Page using the editor below. Compose the overview and click "Save Overview" to save it.')
        ->setAttrib( 'name' , 'sitepages_likebox' ) ;

    //VALUE FOR URL
    $this->addElement( 'Text' , 'url' , array (
      'label' => 'Your Page URL' ,
      'decorators' => array ( array ( 'ViewScript' , array (
            'viewScript' => '_formurlField.tpl' ,
            'class' => 'form element'
          ) ) )
        ) ) ;

		$apiSettings = Engine_Api::_()->getApi( 'settings' , 'core' );

    if ( $apiSettings->getSetting( 'likebox.width' , 1 ) ) {

      //VALUE FOR WIDTH.
      $this->addElement( 'Text' , 'widht' , array (
        'label' => Zend_Registry::get( 'Zend_Translate' )->_( "Badge Width (px)" ) . " <a href='javascript:void(0);' class='sitepagelikebox_show_tooltip_wrapper'> [?] <span class='sitepagelikebox_show_tooltip'><img src='application/modules/Sitepage/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Width of the embeddable badge in pixels.' ) . "</span> </a>" ,
        'attribs' => array ( 'style' => 'width:80px; max-width:80px;' ) ,
        'onblur' => "setLikeBox()" ,
        'value' => "300" ,
          ) ) ;
      $this->getElement( 'widht' )->getDecorator( 'Label' )->setOptions( array ( 'placement' => 'PREPEND' , 'escape' => false ) ) ;
    }

    if ( $apiSettings->getSetting( 'likebox.hight' , 1 ) ) {

      //VALUE FOR HEIGHT.
      $this->addElement( 'Text' , 'height' , array (
        'label' => Zend_Registry::get( 'Zend_Translate' )->_( "Badge Height (px)" ) . " <a href='javascript:void(0);' class='sitepagelikebox_show_tooltip_wrapper'> [?] <span class='sitepagelikebox_show_tooltip'><img src='application/modules/Sitepage/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Height of the embeddable badge in pixels.' ) . "</span> </a>" ,
        'attribs' => array ( 'style' => 'width:80px; max-width:80px;' ) ,
        'onblur' => "setLikeBox()" ,
        'value' => "660" ,
          ) ) ;
      $this->getElement( 'height' )->getDecorator( 'Label' )->setOptions( array ( 'placement' => 'PREPEND' , 'escape' => false ) ) ;
    }

    if ( $apiSettings->getSetting( 'likebox.colorschme' , 1 ) ) {

      //VALUE FOR COLOR SCHEME.
      $this->addElement( 'Select' , 'colorscheme' , array (
        'label' => Zend_Registry::get( 'Zend_Translate' )->_( "Color Scheme" ) . "<a href='javascript:void(0);' class='sitepagelikebox_show_tooltip_wrapper'> [?] <span class='sitepagelikebox_show_tooltip'><img src='application/modules/Sitepage/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Color scheme of the embeddable badge.' ) . "</span></a>" ,
        'multiOptions' => array ( 'light' => "Light" , 'dark' => "Dark" ) ,
        'onchange' => "setLikeBox()"
          ) ) ;
      $this->getElement( 'colorscheme' )->getDecorator( 'Label' )->setOptions( array ( 'placement' => 'PREPEND' , 'escape' => false ) ) ;
    }

    if ( $apiSettings->getSetting( 'likebox.faces' , 1 ) ) {

      //VALUE FOR FACES.
      $this->addElement( 'checkbox' , 'faces' , array (
        'label' => 'Show profile photos' ,
        'description' => Zend_Registry::get( 'Zend_Translate' )->_( "Profile Photos for Likes" ) . " <a href='javascript:void(0);' class='sitepagelikebox_show_tooltip_wrapper'> [?] <span class='sitepagelikebox_show_tooltip'><img src='application/modules/Sitepage/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show profile photos of users who like the page in the embeddable badge.' ) . "</span></a>" ,
        'value' => "1" ,
        'onchange' => "setLikeBox()" ,
          ) ) ;
      $this->getElement( 'faces' )->getDecorator( 'Description' )->setOptions( array ( 'placement' => 'PREPEND' , 'escape' => false ) ) ;
    }

    if ( $apiSettings->getSetting( 'likebox.bordercolor' , 1 ) ) {

      //VALUE FOR BORDER COLOR.
      $this->addElement( 'Text' , 'border_color' , array (
        'label' => 'Color Scheme Code:' ,
        'decorators' => array ( array ( 'ViewScript' , array (
              'viewScript' => '_formBorderColor.tpl' ,
              'class' => 'form element' ) ) )
          ) ) ;
    }

    //VALUE FOR TITLE TRUNCATION.
    $this->addElement( 'Text' , 'titleturncation' , array (
      'label' => Zend_Registry::get( 'Zend_Translate' )->_( "Title Truncation Limit" ) . "<a href='javascript:void(0);' class='sitepagelikebox_show_tooltip_wrapper'> [?] <span class='sitepagelikebox_show_tooltip'><img src='application/modules/Sitepage/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Number of characters to be shown in the Title.' ) . "</span></a>" ,
      'attribs' => array ( 'style' => 'width:80px; max-width:80px;' ) ,
      'onblur' => "setLikeBox()" ,
      'value' => "50" ,
        ) ) ;
    $this->getElement( 'titleturncation' )->getDecorator( 'Label' )->setOptions( array ( 'placement' => 'PREPEND' , 'escape' => false ) ) ;

    if ( $apiSettings->getSetting( 'likebox.header' , 1 ) ) {

      //VALUE FOR HEADER.
      $this->addElement( 'checkbox' , 'header' , array (
        'label' => 'Show header' ,
        'description' => Zend_Registry::get( 'Zend_Translate' )->_( "Header" ) . " <a href='javascript:void(0);' class='sitepagelikebox_show_tooltip_wrapper'> [?] <span class='sitepagelikebox_show_tooltip'><img src='application/modules/Sitepage/externals/images/tooltip_arrow.png'/>" . sprintf( Zend_Registry::get( 'Zend_Translate' )->_( 'Show the \'Find us on %s\' bar at top of the embeddable badge.' ) , $apiSettings->getSetting( 'core.general.site.title' ) ) . "</span></a>" ,
        'value' => "1" ,
        'onchange' => "setLikeBox()" ,
          ) ) ;
      $this->getElement( 'header' )->getDecorator( 'Description' )->setOptions( array ( 'placement' => 'PREPEND' , 'escape' => false ) ) ;
    }


    //VALUE FOR STREAM.
    $this->addElement( 'checkbox' , 'stream' , array (
      'label' => 'Show page data and content' ,
      'description' => Zend_Registry::get( 'Zend_Translate' )->_( "Page Data and Content" ) . " <a href='javascript:void(0);' class='sitepagelikebox_show_tooltip_wrapper'> [?] <span class='sitepagelikebox_show_tooltip'><img src='application/modules/Sitepage/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show data and content of page in the embeddable badge.<br/> Below, you can further choose what all to show.' ) . "</span></a>" ,
      'value' => "1" ,
      //'onchange' => "setLikeBox()" ,
      'onchange' => "showOptions(this.value)" ,
        ) ) ;
    $this->getElement( 'stream' )->getDecorator( 'Description' )->setOptions( array ( 'placement' => 'PREPEND' , 'escape' => false ) ) ;

    if ( !empty($flag) ) {
    //VALUE FOR UPDATES.
    $this->addElement( 'checkbox' , 'streamupdatefeed' , array (
      'label' => 'Show Updates' ,
      'description' => Zend_Registry::get( 'Zend_Translate' )->_( "Updates" ) . " <a href='javascript:void(0);' class='sitepagelikebox_show_tooltip_wrapper'> [?] <span class='sitepagelikebox_show_tooltip'><img src='application/modules/Sitepage/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show page updates in the embeddable badge.' ) . "</span></a>" ,
      'value' => "1" ,
      'onchange' => "setLikeBox()" ,
        ) ) ;
    $this->getElement( 'streamupdatefeed' )->getDecorator( 'Description' )->setOptions( array ( 'placement' => 'PREPEND' , 'escape' => false ) ) ;
    }

    if ( $apiSettings->getSetting( 'likebox.info' , 1 )  && ( !empty($flag1) )) {

      //VALUE FOR INFO.
      $this->addElement( 'checkbox' , 'streaminfo' , array (
        'label' => 'Show Info' ,
        'description' => Zend_Registry::get( 'Zend_Translate' )->_( "Info" ) . " <a href='javascript:void(0);' class='sitepagelikebox_show_tooltip_wrapper'> [?] <span class='sitepagelikebox_show_tooltip'><img src='application/modules/Sitepage/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show page information in the embeddable badge.' ) . "</span></a>" ,
        'value' => "1" ,
        'onchange' => "setLikeBox()" ,
          ) ) ;
      $this->getElement( 'streaminfo' )->getDecorator( 'Description' )->setOptions( array ( 'placement' => 'PREPEND' , 'escape' => false ) ) ;
    }

		//VALUE FOR MAP.
		if ( !empty($sitepage->location) && $apiSettings->getSetting( 'likebox.map' , 1 ) && ( !empty($flag2) ) ) {
			$this->addElement( 'checkbox' , 'streammap' , array (
				'label' => 'Show Map' ,
				'description' => Zend_Registry::get( 'Zend_Translate' )->_( "Map" ) . " <a href='javascript:void(0);' class='sitepagelikebox_show_tooltip_wrapper'> [?] <span class='sitepagelikebox_show_tooltip'><img src='application/modules/Sitepage/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show page location map in the embeddable badge.' ) . "</span></a>" ,
				'value' => "1" ,
				'onchange' => "setLikeBox()" ,
					) ) ;
			$this->getElement( 'streammap' )->getDecorator( 'Description' )->setOptions( array ( 'placement' => 'PREPEND' , 'escape' => false ) ) ;
		}


    $moduleNmae = $apiSettings->getSetting( 'modules_likebox' ) ;
    $moduleNmae = unserialize( $moduleNmae ) ;
    $moduleNmae_temp = array ( ) ;

    foreach ( $moduleNmae as $key => $values ) {

      if ( $values != 'review' ) {

        if ( !Engine_Api::_()->sitepagelikebox()->allowModule( $sitepage , $values , $hasPackageEnable ) )
          continue ;
      } else {
// 					if(Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitepagereview' ) && (Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'sitepagereview.isActivate' , 0 ))) {
						$values = 'sitepagereview' ;
// 					} else {
// 						$values = '' ;
// 					}
      }
			$elementName=null;
			switch ($values) {
				case 'sitepagealbum':
				if (!empty($flag4)) {
					$elementName =  'streamalbum';
					$label = 'Show Photos';
					$description = Zend_Registry::get( 'Zend_Translate' )->_( "Photos" ) . " <a href='javascript:void(0);' class='sitepagelikebox_show_tooltip_wrapper'> [?] <span class='sitepagelikebox_show_tooltip'><img src='application/modules/Sitepage/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show page photos in the embeddable badge.' ) . "</span></a>" ;
				}
				break;

				case 'sitepagemusic':
				if (!empty($flag10)) {
					$elementName =   'streammusic';
					$label = 'Show Music';
					$description =  Zend_Registry::get( 'Zend_Translate' )->_( "Music" ) . " <a href='javascript:void(0);' class='sitepagelikebox_show_tooltip_wrapper'> [?] <span class='sitepagelikebox_show_tooltip'><img src='application/modules/Sitepage/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show page music in the embeddable badge.' ) . "</span></a>";
				}
				break;

				case 'sitepageevent':
				if (!empty($flag5)) {
					$elementName =   'streamevent';
					$label = 'Show Events';
					$description =  Zend_Registry::get( 'Zend_Translate' )->_( "Events" ) . "<a href='javascript:void(0);' class='sitepagelikebox_show_tooltip_wrapper'> [?] <span class='sitepagelikebox_show_tooltip'><img src='application/modules/Sitepage/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show page events in the embeddable badge.' ) . "</span></a>";
				}
				break;

				case 'sitepagereview':
				if (!empty($flag11)) {
					if(Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitepagereview' ) && (Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'sitepagereview.isActivate' , 0 ))){
					$elementName =   'streamreview';
					$label = 'Show Reviews & Ratings';
					$description = Zend_Registry::get( 'Zend_Translate' )->_( "Reviews" ) . " <a href='javascript:void(0);' class='sitepagelikebox_show_tooltip_wrapper'> [?] <span class='sitepagelikebox_show_tooltip'><img src='application/modules/Sitepage/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show page reviews and ratings in the embeddable badge.' ) . "</span></a>" ;}
				}
				break;

				case 'sitepagepoll':
				if (!empty($flag6)) {
					$elementName =   'streampoll';
					$label = 'Show Polls';
					$description =  Zend_Registry::get( 'Zend_Translate' )->_( "Polls" ) . "<a href='javascript:void(0);' class='sitepagelikebox_show_tooltip_wrapper'> [?] <span class='sitepagelikebox_show_tooltip'><img src='application/modules/Sitepage/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show page polls in the embeddable badge.' ) . "</span></a>";
				}
				break;

				case 'sitepagediscussion':
				if (!empty($flag3)) {
					$elementName =   'streamdiscussion';
					$label = 'Show Discussions';
					$description = Zend_Registry::get( 'Zend_Translate' )->_( "Discussions" ) . "<a href='javascript:void(0);' class='sitepagelikebox_show_tooltip_wrapper'> [?] <span class='sitepagelikebox_show_tooltip'><img src='application/modules/Sitepage/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show page discussions in the embeddable badge.' ) . "</span></a>" ; 
				}
				break;

				case 'sitepagenote':
				if (!empty($flag7)) {
					$elementName =   'streamnote';
					$label = 'Show Notes';
					$description =  Zend_Registry::get( 'Zend_Translate' )->_( "Notes" ) . " <a href='javascript:void(0);' class='sitepagelikebox_show_tooltip_wrapper'> [?] <span class='sitepagelikebox_show_tooltip'><img src='application/modules/Sitepage/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show page notes in the embeddable badge.' ) . "</span></a>";
				}
				break;

				case 'sitepagevideo':
				if (!empty($flag9)) {
					$elementName =   'streamvideo';
					$label = 'Show Videos';
					$description =  Zend_Registry::get( 'Zend_Translate' )->_( "Videos" ) . " <a href='javascript:void(0);' class='sitepagelikebox_show_tooltip_wrapper'> [?] <span class='sitepagelikebox_show_tooltip'><img src='application/modules/Sitepage/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show page videos in the embeddable badge.' ) . "</span></a>";
				}
				break;

				case 'sitepageoffer':
				if (!empty($flag8)) {
					$elementName =   'streamoffer';
					$label = 'Show Offers';
					$description =  Zend_Registry::get( 'Zend_Translate' )->_( "Offers" ) . " <a href='javascript:void(0);' class='sitepagelikebox_show_tooltip_wrapper'> [?] <span class='sitepagelikebox_show_tooltip'><img src='application/modules/Sitepage/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show page offers in the embeddable badge.' ) . "</span></a>";
				}
				break;

				case 'sitepagedocument':
				if (!empty($flag12)) {
					$elementName =   'streamdocument';
					$label = 'Show Documents';
					$description =  Zend_Registry::get( 'Zend_Translate' )->_( "Documents" ) . " <a href='javascript:void(0);' class='sitepagelikebox_show_tooltip_wrapper'> [?] <span class='sitepagelikebox_show_tooltip'><img src='application/modules/Sitepage/externals/images/tooltip_arrow.png'/>" . Zend_Registry::get( 'Zend_Translate' )->_( 'Show page documents in the embeddable badge.' ) . "</span></a>" ;
				}
				break;
			}
			if(!empty($elementName)){
			$this->addElement( 'checkbox' , $elementName , array (
          'label' => $label ,
          'description' => $description ,
          'value' => "1" ,
          'onchange' => "setLikeBox()" ,
            ) ) ;
      $this->getElement( $elementName )->getDecorator( 'Description' )->setOptions( array ( 'placement' => 'PREPEND' , 'escape' => false ) ) ;
		}
	}

    //ADD FOR BUTTON.
    $this->addElement( 'Button' , 'save' , array (
      'label' => 'Get Code' ,
      'href' => 'javascript:void(0);' ,
      'link' => true ,
      'decorators' => array ( 'ViewHelper' ) ,
      'onclick' => "getCode()" ,
        ) ) ;
  }
}
?>