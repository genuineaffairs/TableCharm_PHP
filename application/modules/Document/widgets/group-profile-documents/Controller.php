<?php
class Document_Widget_GroupProfileDocumentsController extends Engine_Content_Widget_Abstract
{
  const maxDocumentsPerPage = 8;

  protected $_childCount;

  public function indexAction()
  {
     // don't render this if not authorized
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if(!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    // get subject and check auth
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

    // just remove the title decorator
    $this->getElement()->removeDecorator('Title');

    // get number of documents to display
    $max = $this->_getParam('itemCountPerPage');
    if(!is_numeric($max) | $max <=0) $max = $this::maxDocumentsPerPage;

    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('document')->getDocumentPaginator(array(
      'parent_type' => 'group',
      'parent_id' => $subject->getIdentity(),
      'order' => 'creation_date',
      'page' => $this->_getParam('page', 1),
      'limit' => $max
    ));

    if($paginator->getTotalItemCount() <= 0){
      return $this->setNoRender();
    }

    // add count to title if configured
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
