<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Poll.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagepoll_Model_Poll extends Core_Model_Item_Abstract {

  protected $_parent_type = 'user';
  protected $_parent_is_owner = true;

  public function getMediaType() {
    return 'poll';
  }

  /**
   * Return page object
   *
   * @return page object
   * */
  public function getParent($recurseType = null) {

        if ($recurseType == null)
            $recurseType = 'sitepage_page';
    return Engine_Api::_()->getItem($recurseType, $this->page_id);
  }

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array()) {
    $pageid = $this->page_id;
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
    $tab_id = '';
    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      $tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagepoll.sitemobile-profile-sitepagepolls', $pageid, $layout);
    } else {
      $tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagepoll.profile-sitepagepolls', $pageid, $layout);
    }

    $params = array_merge(array(
        'route' => 'sitepagepoll_detail_view',
        'reset' => true,
        'user_id' => $this->owner_id,
        'poll_id' => $this->poll_id,
        'slug' => $this->getSlug(),
        'tab' => $tab_id
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble($params, $route, $reset);
  }

  /**
   * Return a poll trunacte description
   *
   * @return truncate description
   * */
  public function getDescription() {
    // @todo decide how we want to handle multibyte string functions
    $tmpBody = strip_tags($this->description);
    return ( Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody );
  }

  /**
   * Return a poll owner trunacte name
   *
   * @return truncate description
   * */
  public function truncateOwner($owner_name) {
    $tmpBody = strip_tags($owner_name);
    return ( Engine_String::strlen($tmpBody) > 10 ? Engine_String::substr($tmpBody, 0, 10) . '..' : $tmpBody );
  }

  /**
   * Make format for activity feed
   *
   * @return activity feed content
   */
  public function getRichContent() {
    $view = Zend_Registry::get('Zend_View');
    $view = clone $view;
    $view->clearVars();
    $view->addScriptPath('application/modules/Sitepagepoll/views/scripts/');
    $tmpBody = $this->getDescription();
    $poll_description = Engine_String::strlen($tmpBody) > 70 ? Engine_String::substr($tmpBody, 0, 70) . '...' : $tmpBody;
    $content = '';
    $content .= '
      <div class="feed_sitepagepoll_rich_content">
        <div class="feed_item_link_title">
          ' . $view->htmlLink($this->getHref(), $this->getTitle()) . '
        </div>
        <div class="feed_item_link_desc">
          ' . $view->viewMore($poll_description) . '
        </div>
    ';

    //RENDER THE THINGY
    $view->sitepagepoll = $this;
    $view->owner = $owner = $this->getOwner();
    $view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $view->sitepagepollOptions = $this->getOptions();
    $view->hasVoted = $this->viewerVoted();
    $view->showPieChart = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.showPieChart', false);
    $view->canChangeVote = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.canchangevote', false);
    $view->hideLinks = true;

    if (!empty($viewer_id) && $this->approved == 1 && $this->search == 1) {
      $view->canVote = $view->can_vote = 1;
    } else {
      $view->canVote = $view->can_vote = 0;
    }
    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      $content .= $view->render('_sitepagepoll.tpl');
    } else {
      $view->hideStats = true;
      $content .= $view->render('_sitemobile_sitepagepoll.tpl');
    }

    $content .= '
      </div>
    ';
    return $content;
  }

  /**
   * Return poll options
   *
   * @return poll options
   * */
  public function getOptions() {
    return Engine_Api::_()->getDbtable('options', 'sitepagepoll')->fetchAll(array(
                'poll_id = ?' => $this->getIdentity(),
            ));
  }

  /**
   * Return query for user has voted or not
   *
   * @param $user:user model
   * @return Zend_Db_Table_Select
   * */
  public function hasVoted(User_Model_User $user) {
    $voteTable = Engine_Api::_()->getDbtable('votes', 'sitepagepoll');
    return (bool) $voteTable
                    ->select()
                    ->from($voteTable, 'COUNT(*)')
                    ->where('poll_id = ?', $this->getIdentity())
                    ->where('owner_id = ?', $user->getIdentity())
                    ->query()
                    ->fetchColumn(0);
  }

  /**
   * Return query for getting users vote
   *
   * @param array $user:user model
   * @return Zend_Db_Table_Select
   * */
  public function getVote(User_Model_User $user) {
    $voteTable = Engine_Api::_()->getDbtable('votes', 'sitepagepoll');
    return $voteTable
                    ->select()
                    ->from($voteTable, 'poll_option_id')
                    ->where('poll_id = ?', $this->getIdentity())
                    ->where('owner_id = ?', $user->getIdentity())
                    ->query()
                    ->fetchColumn(0);
  }

  /**
   * Return: get viewers vote
   *
   * @return get viewers vote
   * */
  public function viewerVoted() {
    $viewer = Engine_Api::_()->user()->getViewer();
    return $this->getVote($viewer);
  }

  /**
   * Make vote entry
   *
   * @param array $user:user model
   * @param array $option:options
   * @return Zend_Db_Table_Select
   * */
  public function vote(User_Model_User $user, $option) {
    $voteTable = Engine_Api::_()->getDbTable('votes', 'sitepagepoll');
    $row = $voteTable->fetchRow(array(
        'poll_id = ?' => $this->getIdentity(),
        'owner_id = ?' => $user->getIdentity(),
            ));

    if (null === $row) {
      $row = $voteTable->createRow();
      $row->setFromArray(array(
          'poll_id' => $this->getIdentity(),
          'owner_id' => $user->getIdentity(),
          'creation_date' => date("Y-m-d H:i:s"),
      ));

      $this->vote_count = new Zend_Db_Expr('vote_count + 1');
      $this->save();
    }

    $previous_option_id = $row->poll_option_id;
    $row->poll_option_id = $option;
    $row->modified_date = date("Y-m-d H:i:s");
    $row->save();

    $optionsTable = Engine_Api::_()->getDbtable('options', 'sitepagepoll');
    $optionsTable->update(array(
        'votes' => new Zend_Db_Expr('votes - 1'),
            ), array(
        'poll_id = ?' => $this->getIdentity(),
        'poll_option_id = ?' => $previous_option_id,
    ));
    $optionsTable->update(array(
        'votes' => new Zend_Db_Expr('votes + 1'),
            ), array(
        'poll_id = ?' => $this->getIdentity(),
        'poll_option_id = ?' => $option,
    ));
  }

  /**
   * Insert global search value
   *
   *
   * */
  protected function _insert() {
    if (null === $this->search) {
      $this->search = 1;
    }

    parent::_insert();
  }

  /**
   * Delete poll votes and options
   *
   *
   * */
  protected function _delete() {

    //DELETE PAGE-POLLS
    Engine_Api::_()->getDbtable('votes', 'sitepagepoll')->delete(array(
        'poll_id = ?' => $this->getIdentity(),
    ));

    //DELETE POLL OPTIONS
    Engine_Api::_()->getDbtable('options', 'sitepagepoll')->delete(array(
        'poll_id = ?' => $this->getIdentity(),
    ));

    // Delete create activity feed of poll before delete poll 
    Engine_Api::_()->getApi('subCore', 'sitepage')->deleteCreateActivityOfExtensionsItem($this, array('sitepagepoll_new', 'sitepagepoll_admin_new'));
    parent::_delete();
  }

  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   * */
  public function comments() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   * */
  public function likes() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

}
?>