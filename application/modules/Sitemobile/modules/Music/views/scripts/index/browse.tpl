<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: browse.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */ 
?>

<?php if( 0 == count($this->paginator) ): ?>

  <div class="tip">
    <span>
      <?php echo $this->translate('There is no music uploaded yet.') ?>
      <?php if( $this->canCreate ): ?>
        <?php echo $this->htmlLink(array(
          'route' => 'music_general',
          'action' => 'create'
        ), $this->translate('Why don\'t you add some?')) ?>
      <?php endif; ?>
    </span>
  </div>
<?php else: ?>

  <?php if ($this->paginator->getTotalItemCount()): ?>

  <div class="sm-content-list">
    <ul data-role="listview" data-inset="false" >
      <?php foreach ($this->paginator as $playlist): ?>
        <li data-icon="arrow-r">
          <a href="<?php echo $playlist->getHref(); ?>">
            <p class="ui-li-aside">
              <b><?php echo $this->translate(array('%s play', '%s plays', $playlist->play_count), $this->locale()->toNumber($playlist->play_count)) ?></b>
            </p> 
          <?php
            if ($playlist->photo_id) :
              echo $this->itemPhoto($playlist, 'thumb.icon');
            else :?>
             <img   class="thumb.icon" alt="" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Music/externals/images/nophoto_playlist_main.png" />
            <?php endif; ?>
            <h3><?php echo $playlist->getTitle() ?></h3>
            <p>
              <?php echo $this->translate('Created by '); ?><b><?php echo $playlist->getOwner()->getTitle() ?></b>
            </p>
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

    <?php if ($this->paginator->count() > 1): ?>
      <?php 
      echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->formValues,
    )); ?>
  <?php endif; ?>
  </div>
<?php else: ?>
  <div class="tip">
    <span>
  <?php echo $this->translate('There are no search results to display.'); ?>
    </span>
  </div>
<?php endif; ?>


<?php endif; ?>

