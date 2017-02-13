<?php
class Mgsl_Widget_GroupProfileVideosController extends Engine_Content_Widget_Abstract
{
  const maxVideosPerPage = 8;

  protected $_childCount;

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

    // Just remove the title decorator
    $this->getElement()->removeDecorator('Title');

    //Get number of videos display
    $max = $this->_getParam('itemCountPerPage');
    if(!is_numeric($max) | $max <=0) $max = $this::maxVideosPerPage;

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

    // Add count to title if configured
    if($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}
?>
