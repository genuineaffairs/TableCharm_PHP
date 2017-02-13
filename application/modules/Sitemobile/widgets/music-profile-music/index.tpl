<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */ 
?>
<div class="sm-content-list" id="profile_music">
  <ul data-role="listview" data-icon="arrow-r" >
    <?php foreach ($this->paginator as $playlist): ?>
      <li>
        <a href="<?php echo $playlist->getHref(); ?>">
          <?php
          if ($playlist->photo_id) :
            echo $this->itemPhoto($playlist, 'thumb.icon');
          else :
            ?>
            <img   class="thumb.icon" alt="" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Music/externals/images/nophoto_playlist_main.png" />
          <?php endif; ?>
          <h3><?php echo $playlist->getTitle() ?></h3>
          <p>
            <?php echo $this->timestamp($playlist->creation_date) ?>
            -    
            <?php
            //count no. of tracks in a playlist
            $songs = (isset($this->songs) && !empty($this->songs)) ? $this->songs : $playlist->getSongs();

            $songCount = count($songs);
            ?>
            <?php echo $this->translate(array("%s track", "%s tracks", $songCount), $this->locale()->toNumber($songCount)) ?>
          </p>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
<?php if ($this->paginator->count() > 1): ?>
    <?php
    echo $this->paginationAjaxControl(
            $this->paginator, $this->identity, 'profile_music');
    ?>
<?php endif; ?>
