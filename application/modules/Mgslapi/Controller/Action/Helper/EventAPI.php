<?php

class Mgslapi_Controller_Action_Helper_EventAPI extends Zend_Controller_Action_Helper_Abstract
{

  /**
   * Get list of upcoming or past events
   * @return Zend_Paginator
   */
  public function getPersonalEvents()
  {
    // Prepare
    $viewer = Engine_Api::_()->user()->getViewer();
    $request = Zend_Controller_Front::getInstance()->getRequest();

    $filter = $request->getParam('filter', 'future');
    if ($filter != 'past' && $filter != 'future') {
      $filter = 'future';
    }

    // Create form
    $formFilter = new Event_Form_Filter_Browse();
    $defaultValues = $formFilter->getValues();

    if (!$viewer || !$viewer->getIdentity()) {
      $formFilter->removeElement('view');
    }

    // Populate options
    foreach (Engine_Api::_()->getDbtable('categories', 'event')->select()->order('title ASC')->query()->fetchAll() as $row) {
      $formFilter->category_id->addMultiOption($row['category_id'], $row['title']);
    }
    if (count($formFilter->category_id->getMultiOptions()) <= 1) {
      $formFilter->removeElement('category_id');
    }

    // Populate form data
    if ($formFilter->isValid($request->getParams())) {
      $values = $formFilter->getValues();
    } else {
      $formFilter->populate($defaultValues);
      $values = array();
    }

    // Prepare data
    $values = $formFilter->getValues();

    if ($viewer->getIdentity() && @$values['view'] == 1) {
      $values['users'] = array();
      foreach ($viewer->membership()->getMembersInfo(true) as $memberinfo) {
        $values['users'][] = $memberinfo->user_id;
      }
    }

    $values['search'] = 1;

    if ($filter == "past") {
      $values['past'] = 1;
    } else {
      $values['future'] = 1;
    }

    // check to see if request is for specific user's listings
    if (($user_id = $request->getParam('user'))) {
      $values['user_id'] = $user_id;
    }

    // Get paginator
    $paginator = Engine_Api::_()->getItemTable('event')
            ->getEventPaginator($values);

    return $paginator;
  }

  /**
   * Get list of my events
   * @return Zend_Paginator
   */
  public function getMyEvents()
  {
    // Create form
    if (!$this->getActionController()->getHelper('requireAuth')->setAuthParams('event', null, 'edit')->isValid()) {
      $this->getActionController()->jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    $request = Zend_Controller_Front::getInstance()->getRequest();

    $formFilter = new Event_Form_Filter_Manage();
    $defaultValues = $formFilter->getValues();

    // Populate form data
    if ($formFilter->isValid($request->getParams())) {
      $values = $formFilter->getValues();
    } else {
      $formFilter->populate($defaultValues);
      $values = array();
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbtable('events', 'event');
    $tableName = $table->info('name');

    // Only mine
    if (@$values['view'] == 2) {
      $select = $table->select()
              ->where('user_id = ?', $viewer->getIdentity());
    }
    // All membership
    else {
      $membership = Engine_Api::_()->getDbtable('membership', 'event');
      $select = $membership->getMembershipsOfSelect($viewer);
    }

    if (!empty($values['search_text'])) {
      $values['text'] = $values['search_text'];
    }
    if (!empty($values['text'])) {
      $select->where("`{$tableName}`.title LIKE ?", '%' . $values['text'] . '%');
    }

    $select->order('starttime ASC');
    //$select->where("endtime > FROM_UNIXTIME(?)", time());

    $paginator = Zend_Paginator::factory($select);

    return $paginator;
  }

  public function getJoinStatusOfUser($event, $user)
  {
    // Get membership row, if existing
    $row = $event->membership()->getRow($user);
    // Not yet associated at all
    $join_status = 0;

    if ($row !== null) {
      if ($row->active) {
        // Already a member
        $join_status = 2;
      } elseif (!$row->resource_approved && $row->user_approved) {
        // Membership request sent
        $join_status = 1;
      } elseif (!$row->user_approved && $row->resource_approved) {
        // Event owner has invited the current user to the event
        $join_status = 3;
      }
    }
    return $join_status;
  }

}
