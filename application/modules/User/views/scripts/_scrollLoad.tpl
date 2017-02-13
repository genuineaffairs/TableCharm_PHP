<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _browseUsers.tpl 9979 2013-03-19 22:07:33Z john $
 * @author     John
 */
?>
<?php $viewer = Engine_Api::_()->user()->getViewer();?>

<?php foreach( $this->users as $user ): ?>
  <li>
    <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')) ?>
    <?php 
    $table = Engine_Api::_()->getDbtable('block', 'user');
    $select = $table->select()
      ->where('user_id = ?', $user->getIdentity())
      ->where('blocked_user_id = ?', $viewer->getIdentity())
      ->limit(1);
    $row = $table->fetchRow($select);
    ?>
    <?php if( $row == NULL ): ?>
      <?php if( $this->viewer()->getIdentity() ): ?>
      <div class='browsemembers_results_links'>
        <?php echo $this->userFriendship($user) ?>
      </div>
    <?php endif; ?>
    <?php endif; ?>

      <div class='browsemembers_results_info'>
        <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
      </div>
      <?php if(Engine_Api::_()->getDbTable('accessLevel', 'zulu')->isAllowed($user, $this->viewer(), 'view_clinical') && !$user->isSelf($this->viewer())) : ?>
      <div class='browsemembers_results_info'>
        <?php 
          // EMR tab id
          $tab_id = Engine_Api::_()->user()->getMedicalRecordProfileTabId();
          // User's href
          $href = $user->getHref();
        ?>
        <a href='<?php echo $href . '/tab/' . $tab_id ?>'>
          <img title="This person has shared the medical record with you" alt="This person has shared the medical record with you" class="zulu_small_icon" src="<?php echo $this->baseUrl() ?>application/modules/Zulu/externals/images/zulu_05.png" />
          <?php if($accessLevel = Engine_Api::_()->getDbTable('profileshare', 'zulu')->getAccessLevel($user, $this->viewer())) : ?>
            <div class='medical_icon_access_text'><?php echo $this->translate(Zulu_Model_DbTable_AccessLevel::$accessTypeString[$accessLevel]); ?></div>
          <?php endif; ?>
        </a>
      </div>
      <?php endif; ?>

      <?php $zulu = Engine_Api::_()->getItemTable('zulu')->getZuluByUserId($user->getIdentity()); ?>
      <?php if($zulu && $zulu->hasConcussionTest()) : ?>
      <div class='browsemembers_results_info'>
        <a href='<?php echo $href . '/tab/' . $tab_id ?>'>
          <img title="Concussion Test" class="zulu_small_icon" src="<?php echo $this->baseUrl() ?>application/modules/Zulu/externals/images/concussion.png" />
          <div class='medical_icon_access_text concussion_text'>Concussion Test</div>
        </a>
      </div>
      <?php endif; ?>
  </li>
<?php endforeach; ?>