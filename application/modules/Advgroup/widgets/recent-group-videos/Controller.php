<?php
class Advgroup_Widget_RecentGroupVideosController extends Engine_Content_Widget_Abstract{
  public function indexAction(){
     // Don't render this if not authorized
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }
    if(!Engine_Api::_()->advgroup()->checkYouNetPlugin('ynvideo'))
    {
      return $this->setNorender();
    }
    // Get subject and check auth
    $this->view->group = $subject = Engine_Api::_()->core()->getSubject('group');
    if($subject->is_subgroup && !$subject->isParentGroupOwner($viewer)){
       $parent_group = $subject->getParentGroup();
        if(!$parent_group->authorization()->isAllowed($viewer , "view")){
          return $this->setNoRender();
        }
        else if(!$subject->authorization()->isAllowed($viewer , "view")){
          return $this->setNoRender();
        }
    }
    else if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    //Get number of videos display
    $max = $this->_getParam('itemCountPerPage');
    if(!is_numeric($max) | $max <=0) $max = 5;

    $marginLeft = $this->_getParam('marginLeft', '');
        if (!empty($marginLeft)) {
            $this->view->marginLeft = $marginLeft;
        }

    $params = array();
    $params['parent_type'] = 'group';
    $params['parent_id'] = $subject->getIdentity();
    $params['orderby'] = 'creation_date';
    $params['page'] = $this->_getParam('page',1);
    $params['limit'] = $max;

    $this->view->paginator = $paginator = Engine_Api::_()->ynvideo()->getVideosPaginator($params);

    if($paginator->getTotalItemCount() <= 0){
      return $this->setNoRender();
    }
  }
}
?>
