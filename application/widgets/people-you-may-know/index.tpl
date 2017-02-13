<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7481 2010-09-27 08:41:01Z john $
 * @author     John
 */
?>

<style type="text/css">
 .layout_people_you_may_know {
    clear: both;
    margin-bottom: 15px;
    overflow: auto;
    width: 100%;
}
.layout_people_you_may_know> ul {
    padding: 5px;
}
.layout_people_you_may_know > ul > li {
    clear: both;
    overflow: hidden;
    padding: 3px 5px;
}
.layout_people_you_may_know a.popularmembers_thumb {
    display: block;
    float: left;
    height: 48px;
    width: 48px;
}
.layout_people_you_may_know a.popularmembers_thumb > span {
    display: block;
}
.layout_people_you_may_know .popularmembers_info {
    overflow: hidden;
    padding: 0 0 0 6px;
}
.layout_people_you_may_know .popularmembers_name {
    font-weight: 700;
}
.layout_people_you_may_know .popularmembers_friends {
    color: #999999;
    font-size: 0.8em;
}
</style>


<ul>
  <?php foreach( $this->user as $k => $user ): ?>
    <li>
      <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class' => 'popularmembers_thumb')) ?>
      <div class='popularmembers_info'>
        <div class='popularmembers_name'>
          <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
        </div>
        <div class='popularmembers_friends'>
          <?php echo $this->translate(array('%s mutual friend', '%s mutual friends', $this->mutual[$k]),$this->locale()->toNumber($this->mutual[$k])) ?>
          <br/><?php echo $this->htmlLink(array('route'=>'user_extended', 'controller'=>'friends', 'action'=>'add', 'user_id'=>$user->getIdentity()), $this->translate('Add to My Friends'), array('class' => 'smoothbox')) ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
