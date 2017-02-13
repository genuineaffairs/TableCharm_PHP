<?php
class Advgroup_Widget_ProfileGroupAnnouncementsController extends Engine_Content_Widget_Abstract
{
  public function indexAction(){
     // Get paginator
        // Don't render this if not authorized
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }
    $group = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    if($group->is_subgroup && !$group->isParentGroupOwner($viewer)){
       $parent_group = $group->getParentGroup();
        if(!$parent_group->authorization()->isAllowed($viewer , "view")){
          return $this->setNoRender();
        }
        else if(!$group->authorization()->isAllowed($viewer , "view")){
          return $this->setNoRender();
        }
    }
    else if( !$group->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }
    
    $table = Engine_Api::_()->getDbtable('announcements', 'advgroup');
    $select = $table->select()
      ->where('group_id = ?',$group->group_id)
      ->order('modified_date DESC')
            ->limit(1);
      ;

    $announcement = $table->fetchRow($select);

    // Hide if nothing to show
    if( !$announcement ) {
      return $this->setNoRender();
    }

    $this->view->announcement = $announcement;
    if(count($announcement)<=0) {
      return $this->setNoRender();
    }
  }
}
?>