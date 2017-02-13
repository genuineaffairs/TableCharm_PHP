<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class SitepageDocument_Widget_SocialshareSitepagedocumentsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//SHARING IS ALLOWED OR NOT
    $sharingAllow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.share', 1);

		$social_share_default_code = '<div class="addthis_toolbox addthis_default_style ">
		<a class="addthis_button_preferred_1"></a>
		<a class="addthis_button_preferred_2"></a>
		<a class="addthis_button_preferred_3"></a>
		<a class="addthis_button_preferred_4"></a>
		<a class="addthis_button_preferred_5"></a>
		<a class="addthis_button_compact"></a>
		<a class="addthis_counter addthis_bubble_style"></a>
		</div>
		<script type="text/javascript">
		var addthis_config = {
							services_compact: "facebook, twitter, linkedin, google, digg, more",
							services_exclude: "print, email"
		}
		</script>
		<script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js"></script>';

		//GET CODE FROM CORE SETTING
    $this->view->code = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.code.share', $social_share_default_code);

    //GET DOCUMENT ITEM
    $document = Engine_Api::_()->getItem('sitepagedocument_document', Zend_Controller_Front::getInstance()->getRequest()->getParam('document_id'));

		//DON'T RENDER IF DOCUMENT IS NOT AUTHORIZED
		if($document->status != 1 || $document->approved != 1 || $document->draft == 1) {
      return $this->setNoRender();
		}

		//DONT RENDER THIS IF NOT AUTHORIZED OR DISABLE SHARING BY ADMIN
    if (!$document || empty($sharingAllow) || empty($this->view->code)) {
      return $this->setNoRender();
    }
  }
}
?>