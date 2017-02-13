<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminViewadController.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_AdminViewadController extends Core_Controller_Action_Admin {

  public function indexAction() {
    if (date('Y-m-d', strtotime(Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.update.approved'))) < date('Y-m-d')) {
      Engine_Api::_()->getDbtable('userads', 'communityad')->updateApproved();
    }
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('communityad_admin_main', array(), 'communityad_admin_view_advertisment');
    // Make filter form
    $this->view->formFilter = $formFilter = new Communityad_Form_Admin_Filter();
    $this->view->getCommunityadTitle = Engine_Api::_()->communityad()->getCommunityadTitle();

    $this->view->getCommunityadTitle = Engine_Api::_()->communityad()->getCommunityadTitle();
    // Get page number
    $page = $this->_getParam('page', 1);
    // user table
    $tableUser = Engine_Api::_()->getItemTable('user')->info('name');
    // advertiesment table
    $table = Engine_Api::_()->getDbtable('userads', 'communityad');
    $rName = $table->info('name');
    // adstatistcs table
    $statTable = Engine_Api::_()->getDbtable('adstatistics', 'communityad');
    $statName = $statTable->info('name');
    // campaigns table
    $campTable = Engine_Api::_()->getDbtable('adcampaigns', 'communityad');
    $campName = $campTable->info('name');
    // package table
    $packTable = Engine_Api::_()->getDbtable('packages', 'communityad');
    $packName = $packTable->info('name');
    $this->view->packageList = $packTable->fetchAll();


    $select = $table->select()
            ->setIntegrityCheck(false)
            ->from($rName, array('*', "(case when count_view <> 0 and  SUM(count_click) <>0  then  ROUND((count_click / count_view), 7)  else 0 end)   AS CTR"))
            ->joinLeft($tableUser, "$rName.owner_id = $tableUser.user_id", 'username')
            ->join($packName, $packName . '.package_id = ' . $rName . '.package_id', array('title as package_name', 'price'))
            ->join($campName, $campName . '.adcampaign_id = ' . $rName . '.campaign_id', array('name'))
            // ->where($rName . '.	story_type =?', 0)
            ->group($rName . '.userad_id');

    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    // searching
    $this->view->post = 0;
    $this->view->title = '';
    $this->view->campaign_name = '';
    $this->view->owner = '';
    $this->view->package = '';
    $this->view->status = '';

    if (isset($_POST['search'])) {
      $this->view->post = 1;

      if (!empty($_POST['title'])) {
        $this->view->title = $_POST['title'];
        $select->where($rName . '.cads_title  LIKE ?', '%' . $_POST['title'] . '%');
      }
      if (!empty($_POST['campaign_name'])) {
        $this->view->campaign_name = $_POST['campaign_name'];
        $select->where($campName . '.name  LIKE ?', '%' . $_POST['campaign_name'] . '%');
      }
      if (!empty($_POST['owner'])) {
        $this->view->owner = $_POST['owner'];
        $select->where($tableUser . '.displayname  LIKE ?', '%' . $_POST['owner'] . '%');
      }
      if (!empty($_POST['package'])) {
        $this->view->package = $_POST['package'];
        $select->where($rName . '.package_id = ? ', $_POST['package']);
        $_POST['ad_type'] = $packTable->fetchRow(array('package_id = ?' => $_POST['package']))->type;
      }
      if (isset($_POST['status']) && $_POST['status'] != 100) {
        $this->view->status = $_POST['status'];
        $select->where($rName . '.status = ? ', $_POST['status']);
      }
      if (!empty($_POST['ad_type'])) {
        $this->view->ad_type = $_POST['ad_type'];
        $select->where($rName . '.ad_type  = ?', $_POST['ad_type']);
      }
    }
    $values = array_merge(array(
        'order' => 'userad_id',
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);

    $select->order((!empty($values['order']) ? $values['order'] : 'userad_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    //MAKE PAGINATOR
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $items_per_page = 25;
    $this->view->paginator->setItemCountPerPage($items_per_page);
    $this->view->paginator = $paginator->setCurrentPageNumber($page);
  }

  public function editadAction() {

    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('communityad_admin_main', array(), 'communityad_admin_view_advertisment');

    $this->view->get_title = Engine_Api::_()->user()->getViewer()->getTitle();
    $ad_id = $this->_getParam('id');
    $useradsTable = Engine_Api::_()->getDbtable('userads', 'communityad');
    $useradsName = $useradsTable->info('name');

    $campTable = Engine_Api::_()->getDbtable('adcampaigns', 'communityad');
    $campName = $campTable->info('name');

    $packTable = Engine_Api::_()->getDbtable('packages', 'communityad');
    $packName = $packTable->info('name');

    // To find the details of this ad
    $select = $useradsTable->select();
    $select
            ->setIntegrityCheck(false)
            ->from($useradsName)
            ->join($packName, $packName . '.package_id = ' . $useradsName . '.package_id', array('package_id', 'title as package_name', 'price'))
            ->join($campName, $campName . '.adcampaign_id = ' . $useradsName . '.campaign_id', array('name'))
            ->where($useradsName . '.userad_id = ?', $ad_id)
            ->limit(1);

    $result = $useradsTable->fetchRow($select);
    $this->view->result = $result;

    $this->view->editform = $editform = new Communityad_Form_Admin_Editad(array('item' => $result));
    $editform->ad_type->setDescription($result->getAdTypeTitle($result->ad_type));
    $result = $result->toarray();
    if (empty($result['resource_type'])) {
      $editform->ad_content->setLabel('Url');
      $editform->ad_content->setDescription($result['cads_url']);
    } elseif (!empty($result['resource_type'])) {
      $editform->ad_content->setLabel('Resource Type');
      $editform->ad_content->setDescription('Module : ' . $result['resource_type']);
    }
    $editform->ad_title->setDescription($result['cads_title']);
    $editform->campaign_title->setDescription($result['name']);
    $package_link = '<a class ="smoothbox" href="'. $this->view->url(array('module'=>'communityad','controller'=>'packagelist','action' => 'packge-detail','id'=>$result['package_id']),'admin_default',true).'">' . $result['package_name'] . '</a>';

    $editform->package_name->setDescription($package_link);
    $approved = $result['approved'];
    $notPause = $result['enable'];
    if ($approved && $result['status'] <= 2 && $result['declined'] != 1) {
      switch ($result['status']) {
        case 0:
          $status = "Approval Pending";
          break;

        case 1:
          $status = "Running";
          break;

        case 2:
          $status = "Paused";
          break;

        case 3:
          $status = "Completed";
          break;

        case 4:
          $status = "Deleted";
          break;

        case 5:
          $status = "Declined";
          break;
      }
    } elseif ($result['status'] == 4) {
      $status = "Deleted";
    } elseif ($result['status'] == 3) {
      $status = "Completed";
    } elseif ($result['declined'] == 1) {
      $status = "Declined";
    } else {
      if (empty($result['approve_date']))
        $status = "Approval Pending";
      else
        $status = "Dis-Approved";
    }
    $editform->status->setDescription($status);
    if (empty($result['cads_end_date'])) {
      $date = (string) date('Y-m-d') . ' 00:00:00';
      $result['cads_end_date'] = $date;
    } else {
      $result['end_settings'] = 1;
    }

    $editform->populate($result);

    // Code Start for Preview.
    $fetch_community_ads = Engine_Api::_()->communityad()->adpreview($ad_id);
    if (!empty($fetch_community_ads)) {
      $this->view->communityads_array = $fetch_community_ads;
    } else {
      return $this->setNoRender();
    }

    if ($this->getRequest()->isPost() && $editform->isValid($this->getRequest()->getPost())) {
      $values['weight'] = 0;
      $values = $editform->getValues();

      if ( isset($values['limit_view']) && ($values['limit_view'] < -1 || $values['limit_view'] == 0 ) &&  $values['limit_view'] != $result['limit_view']) {
        return $editform->addError("Please enter a non-zero value for Total Views Allowed. If you do not want this ad to be shown, then you can disable it.");
      }
      if (isset($values['limit_click']) && ($values['limit_click'] < -1 || $values['limit_click'] == 0 ) && $values['limit_click'] != $result['limit_click']) {
        return $editform->addError("Please enter a non-zero value for Total Clicks Allowed. If you do not want this ad to be shown, then you can disable it.");
      }
      if (isset($values['limit_click']) && empty($values['limit_click'])) {
        unset($values['limit_click']);
      }

      if (isset($values['limit_view']) &&  empty($values['limit_view'])) {
        unset($values['limit_view']);
      }
      unset($values['ad_type']);
      unset($values['ad_content']);
      unset($values['ad_title']);
      unset($values['campaign_title']);
      unset($values['package_name']);
      unset($values['status']);


      if (empty($values['end_settings'])) {
        $values['cads_end_date'] = null;
        unset($values['cads_end_date']);
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = "UPDATE  `engine4_communityad_userads` SET  `cads_end_date` = NULL WHERE  `engine4_communityad_userads`.`userad_id` =" . $ad_id;
        $db->query($sql);
      }

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $ad = Engine_Api::_()->getItem('userads', $ad_id);

        if ($ad->declined != 1) {

          if (!empty($approved) && $result['status'] == 3 && !empty($result['cads_end_date']) && date('Y-m-d H:i:s', strtotime($result['cads_end_date'])) < date('Y-m-d H:i:s')) {

            if ($result['enable'] == 1)
              $values['status'] = 1;
            else
              $values['status'] = 2;
          }
          if (empty($approved)) {
            unset($values["enable"]);
          }

          if (empty($result['price'])) {
            unset($values["payment_status"]);
          }

          if (isset($values['enable']) && empty($values['enable']) && $result['status'] == 1) {
            $values['status'] = 2;
          } elseif (isset($values['enable']) && $result['status'] == 2 && !empty($values['enable'])) {
            $values['status'] = 1;
          }

          $ad->setFromArray($values);

          include_once(APPLICATION_PATH . "/application/modules/Communityad/controllers/license/license2.php");
        }
        $this->_helper->redirector->gotoRoute(array('action' => 'index'));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }

  public function deleteadAction() {
    $id = $this->_getParam('id');
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $userads = Engine_Api::_()->getItem('userads', $id);
        $userads->enable = !$userads->enable;

        $userads->status = 4;

        $userads->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('')
      ));
    }
    $this->renderScript('admin-viewad/deletead.tpl');
  }

  public function deleteselectedadAction() {
    $this->view->ids = $ids = $this->_getParam('ids', null);
    $confirm = $this->_getParam('confirm', false);
    $this->view->count = count(explode(",", $ids));

    // Save values
    if ($this->getRequest()->isPost() && $confirm == true) {
      $ids_array = explode(",", $ids);
      foreach ($ids_array as $id) {
        $subject = Engine_Api::_()->getItem('userads', $id);
        $subject->enable = !$subject->enable;
        $subject->status = 4;
        $subject->save();
      }

      $this->_redirect('admin/communityad/viewad');
    }
  }

  // FOR ACTIVE/PAUSE
  public function enabledAction() {
    $id = $this->_getParam('id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $userads = Engine_Api::_()->getItem('userads', $id);
      $userads->enable = !$userads->enable;
      if ($userads->enable)
        $userads->status = 1;
      else
        $userads->status = 2;
      $userads->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->_redirect('admin/communityad/viewad');
  }

  // SHOW ADVERTIESMENT PREVIEW
  public function adpreviewAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;
    $this->view->get_title = Engine_Api::_()->user()->getViewer()->getTitle();
    $ad_id = $this->_getParam('ad_id');
    $fetch_community_ads = Engine_Api::_()->communityad()->adpreview($ad_id);
    if (!empty($fetch_community_ads)) {
      $this->view->communityads_array = $fetch_community_ads;
      $this->view->hideCustomUrl = Engine_Api::_()->communityad()->hideCustomUrl();
    } else {
      return $this->setNoRender();
    }
  }

  //ACTION FOR MAKE APPROVE/DIS-APPROVE
  public function approvedEnable($id) {

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      $userads = Engine_Api::_()->getItem('userads', $id);
      // for approved
      if (!empty($userads->approved)) {

        if (empty($userads->status) || $userads->status == 3) {
          $userads->status = 1;
          $userads->enable = 1;
        }
        // Package for advertiesment
        $package = Engine_Api::_()->getItem('package', $userads->package_id);
        if (empty($userads->approve_date) && $package->isFree())
          $userads->approve_date = date('Y-m-d H:i:s');
        if (empty($userads->approve_date) && !$package->isFree() && $userads->payment_status == 'active')
          $userads->approve_date = date('Y-m-d H:i:s');
        switch ($userads->price_model) {
          case "Pay/view":
            if (empty($userads->limit_view)) {
              // for price package
              if (!$package->isFree()) {
                // give trial 5 views
                if ($userads->payment_status != "active")
                  $userads->limit_view = 5;
                else
                  $userads->limit_view = $package->model_detail;
              }else {
                $userads->limit_view = $package->model_detail;
              }
            }
            break;

          case "Pay/click":
            if (empty($userads->limit_click)) {
              if (!$package->isFree()) {
                if ($userads->payment_status != "active")
                  $userads->limit_click = 1;
                else
                  $userads->limit_click = $package->model_detail;
              }else {
                $userads->limit_click = $package->model_detail;
              }
            }
            break;
          case "Pay/period":

            $diff_days = 0;
            if (!empty($userads->expiry_date) && date('Y-m-d', strtotime($userads->expiry_date)) > date('Y-m-d')) {
              $diff_days = round((strtotime($userads->expiry_date) - strtotime(date('Y-m-d'))) / 86400);
            }

            if (($diff_days <= 0 && $userads->expiry_date !== '2250-01-01') || empty($userads->expiry_date)) {
              if ($package->model_detail == -1) {
                if (!$package->isFree()) {
                  if ($userads->payment_status != "active")
                    $userads->expiry_date = Engine_Api::_()->communityad()->getExpiryDate(1);
                  else
                    $userads->expiry_date = '2250-01-01';
                }else {
                  $userads->expiry_date = '2250-01-01';
                }
              } else {
                $days = $package->model_detail;

                if (!$package->isFree()) {
                  if ($userads->payment_status != "active")
                    $userads->expiry_date = Engine_Api::_()->communityad()->getExpiryDate(1);
                  else
                    $userads->expiry_date = Engine_Api::_()->communityad()->getExpiryDate($days);
                }else {
                  $userads->expiry_date = Engine_Api::_()->communityad()->getExpiryDate($days);
                }
              }
            }
            break;
        }
        // SEND APPROVED MAIL
        Engine_Api::_()->communityad()->sendMail("APPROVED", $userads->userad_id);
      } else {
        // SEND DISAPPROVED MAIL
        Engine_Api::_()->communityad()->sendMail("DISAPPROVED", $userads->userad_id);
      }
      $userads->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

}
