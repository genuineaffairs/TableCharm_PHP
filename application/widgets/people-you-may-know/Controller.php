<?php

class Widget_PeopleYouMayKnowController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
   
	$limit =  $this->_getParam('itemPerPage', 4);

	$l = engine_api::_()->user()->getviewer()->getidentity();


	$usersNetworkTable = Engine_Api::_()->getDbTable('membership', 'user' );
	 $db = $usersNetworkTable->getAdapter();


$select = $db->query("

SELECT count(*) as count, u.user_id
	FROM engine4_user_membership m1
	LEFT JOIN engine4_user_membership m2 ON m1.resource_id=m2.user_id AND m2.resource_id NOT IN (SELECT mf.resource_id FROM engine4_user_membership mf WHERE mf.user_id = ".$l.")
	LEFT JOIN engine4_users u ON m2.resource_id=u.user_id

	WHERE u.enabled = 1 AND  
	m1.user_id = ".$l." AND
	m1.resource_id <> m2.resource_id AND
	u.user_id <> ".$l." AND
	u.verified = 1

GROUP BY u.user_id
ORDER BY count DESC
LIMIT ". $limit."



")->fetchAll();


	$this->view->user = $this->view->mutual = array(); 

	for($i=0;$i<count( $select);$i++){	

		$this->view->user[$i] =  engine_api::_()->getitem('user', $select[$i]['user_id']);
		$this->view->mutual[$i] = $select[$i]['count'];
	}
	

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 4));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }
  }

  public function getCacheKey()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $translate = Zend_Registry::get('Zend_Translate');
    return $viewer->getIdentity() . $translate->getLocale();
  }

  public function getCacheSpecificLifetime()
  {
    return 120;
  }
}
