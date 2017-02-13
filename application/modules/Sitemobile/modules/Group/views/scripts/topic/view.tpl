<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: view.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php echo $this->placeholder('grouptopicnavi') ?>
<?php
echo $this->paginationControl(null, null, null, array(
    'params' => array(
        'post_id' => null // Remove post id
    )
))
?>
<ul data-inset="true" data-role="listview" class="ui-listview ui-listview-inset ui-corner-all ui-shadow sm-ui-topic-view">
  <?php
  foreach ($this->paginator as $post):
    $user = $this->item('user', $post->user_id);
    $isOwner = false;
    $isOfficer = false;
    $isMember = false;
    $liClass = 'group_discussions_thread_author_none';
    if ($this->group->isOwner($user)) {
      $isOwner = true;
      $isMember = true;
      $liClass = 'group_discussions_thread_author_isowner';
    } else if (($officerInfo = $this->officerList->get($user))) {
      $isOfficer = true;
      $isMember = true;
      $liClass = 'group_discussions_thread_author_isofficer';
    } else if ($this->group->membership()->isMember($user)) {
      $isMember = true;
      $liClass = 'group_discussions_thread_author_ismember';
    }
    ?>
    <li class="<?php echo $liClass ?> ui-li-has-count">
      <div class="sm-ui-topic-view-head">
        <div class="author_photo">
          <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')) ?>
        </div>
        <div class="thread_options" data-role="controlgroup" data-type="horizontal" data-mini="true">
          <?php
          if ($post->user_id == $this->viewer()->getIdentity() ||
                  $this->group->getOwner()->getIdentity() == $this->viewer()->getIdentity() ||
                  $this->canAdminEdit):
            ?>
            <?php
            echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'post', 'action' => 'edit', 'post_id' => $post->getIdentity(), 'topic_id' => $this->topic->getIdentity()), $this->translate('Edit'), array(
                //'class' => 'buttonlink smoothbox icon_group_post_edit'
                'data-role' => "button", 'data-icon' => "edit", 'data-iconpos' => 'notext', 'data-ajax' => 'false'
            ))
            ?>
    <?php
    echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'post', 'action' => 'delete', 'post_id' => $post->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete'), array(
        //  'class' => 'buttonlink smoothbox icon_group_post_delete'
        'data-role' => "button", 'data-icon' => "delete", 'data-iconpos' => 'notext'
    ))
    ?>
            <?php endif; ?>
        </div>
        <div class="thread_details">
          <h3 class="ui-li-heading"><?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?></h3>
            <?php if ($isOwner || $isOfficer || $isMember) : ?>
            <span class="ui-li-count ui-btn-up-e ui-btn-corner-all">
              <?php
              if ($isOwner) {
                echo $this->translate('Leader');
              } else if ($isOfficer) {
                //if( empty($officerInfo->title) ) {
                echo $this->translate('Officer');
                //} else {
                //  echo $officerInfo->title;
                //}
              } else if ($isMember) {
                echo $this->translate('Member');
              }
              ?>
            </span>
        <?php endif; ?>
          <p class="ui-li-desc"><?php echo $this->timestamp(strtotime($post->creation_date)) ?></p>
        </div>
      </div>  	
      <div class="thread_body">
  <?php echo nl2br($this->BBCode($post->body, array('link_no_preparse' => true))) ?>
      </div> 
      <span class="group_discussions_thread_body_raw" style="display: none;">
  <?php echo $post->body; ?>
      </span>
    </li>
<?php endforeach; ?>
</ul>

<?php if ($this->paginator->getCurrentItemCount() > 4): ?>

  <?php
  echo $this->paginationControl(null, null, null, array(
      'params' => array(
          'post_id' => null // Remove post id
      )
  ))
  ?>
  <?php echo $this->placeholder('grouptopicnavi') ?>

<?php endif; ?>

<?php if ($this->form): ?>
  <a name="reply"></a>
  <?php echo $this->form->setAttrib('id', 'group_topic_reply')->setAttrib('data-ajax', 'false')->render($this) ?>
<?php endif; ?>

<?php
$settings = Engine_Api::_()->getApi('settings', 'core');
$allowHtml = (bool) $settings->getSetting('group_html', 0);
$allowBbcode = (bool) $settings->getSetting('group_bbcode', 0);
?>

<?php if ($allowHtml || $allowBbcode): ?>
  <script type="text/javascript">
    $(document).ready(function() {
  <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.tinymceditor', 0)): ?>
                        setTimeout(function() {
                          sm4.core.tinymce.showTinymce();
                        }, 100);
  <?php endif; ?>
                    });
  </script>
<?php endif; ?>