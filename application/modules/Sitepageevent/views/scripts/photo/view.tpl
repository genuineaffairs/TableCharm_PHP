<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';

$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepageevent/externals/styles/style_sitepageevent.css');

$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.Crop.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/tagger/tagger.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepageevent/externals/scripts/core.js');
$this->headTranslate(array(
    'Save', 'Cancel', 'delete',
));
?>

<?php if (empty($this->isajax)): ?>
  <div class="sitepage_viewpages_head">
    <?php echo $this->htmlLink($this->sitepage->getHref(), $this->itemPhoto($this->sitepage, 'thumb.icon', '', array('align' => 'left'))) ?>
    <h2>	
      <?php $title = $this->sitepage->__toString(); ?>
      <?php $event_title = $this->htmlLink($this->sitepageevent->getHref(array('tab' => $this->tab_selected_id)), $this->translate($this->sitepageevent->getTitle())) ?>
      <?php $events = $this->htmlLink(array('route' => 'sitepage_entry_view', 'page_url' => Engine_Api::_()->sitepage()->getPageUrl($this->sitepage->page_id), 'tab' => $this->tab_selected_id), $this->translate('Events')) ?>
      <?php echo $this->translate('%1$s  &raquo; %2$s  &raquo; %3$s', $title, $events, $this->sitepageevent->__toString()); ?>
    </h2>
  </div>	
  <div class='sitepageevent_viewmedia'>
    <div class="sitepageevent_viewmedia_nav">
      <div id= 'photo_navigation1'>
        <?php
        echo $this->translate('Photo %1$s of %2$s in %3$s', $this->locale()->toNumber($this->photo->getCollectionIndex() + 1), $this->locale()->toNumber($this->sitepageeventPhoto->count()), $this->sitepageevent->getTitle())
        ?>
      </div>
      <?php if ($this->sitepageeventPhoto->count() > 1): ?>
        <div id='image_next_div1' style="display:block;">	   
          <?php $link1 = $this->photo->getPrevCollectible()->getHref() . '/tab/' . $this->tab_selected_id; ?>          <a href="javascript:void(0);" onclick="photopagination('<?php echo $link1; ?>')">
          <?php echo $this->translate('Prev'); ?>
          </a>
          <?php $link2 = $this->photo->getNextCollectible()->getHref() . '/tab/' . $this->tab_selected_id; ?>          <a href="javascript:void(0);" onclick="photopagination('<?php echo $link2; ?>')">
          <?php echo $this->translate('Next'); ?>
          </a>
        </div>
      <?php endif; ?>
    </div>
    <div id='image_div'>      
    <?php endif; ?>
    <div class='sitepageevent_viewmedia_info'> 
      <div id= 'photo_navigation2' style="display:none;">
        <?php
        echo $this->translate('Photo %1$s of %2$s in %3$s', $this->locale()->toNumber($this->photo->getCollectionIndex() + 1), $this->locale()->toNumber($this->sitepageeventPhoto->count()), $this->sitepageevent->getTitle())
        ?>
      </div>
      <?php if ($this->sitepageeventPhoto->count() > 1): ?>
        <div id='image_next_div2' style="display:none;">	   
          <?php $link1 = $this->photo->getPrevCollectible()->getHref() . '/tab/' . $this->tab_selected_id; ?>
          <a href="javascript:void(0);" onclick="photopagination('<?php echo $link1; ?>')">
            <?php echo $this->translate('Prev'); ?>
          </a>
          <?php $link2 = $this->photo->getNextCollectible()->getHref() . '/tab/' . $this->tab_selected_id; ?>
          <a href="javascript:void(0);" onclick="photopagination('<?php echo $link2; ?>')">
            <?php echo $this->translate('Next'); ?>
          </a>
        </div>
      <?php endif; ?>
      <div class='sitepageevent_viewmedia_container' id='media_image_div'>
        <a id='media_image_next'   <?php if ($this->sitepageeventPhoto->count() > 1): ?> onclick="photopagination('<?php echo $this->escape($this->photo->getNextCollectible()->getHref() . '/tab/' . $this->tab_selected_id) ?>')" <?php endif; ?> title="<?php echo $this->photo->getTitle(); ?>">
          <?php
          echo $this->htmlImage($this->photo->getPhotoUrl(), $this->photo->getTitle(), array(
              'id' => 'media_image'
          ));
          ?>
        </a>
      </div>
      <br />
      <?php if ($this->can_edit): ?>
        <div class="sitepageevent_photo_right_options">
          <a class=" icon_sitepage_photos_rotate_ccw" href="javascript:void(0)" onclick="$(this).set('class', 'page_ccw icon_loading');en4.sitepageevent.rotate(<?php echo $this->photo->getIdentity() ?>, 90).addEvent('complete', function(){ this.set('class', 'icon_sitepage_photos_rotate_ccw') }.bind(this)); loadingImage();" title="<?php echo $this->translate("Rotate Left"); ?>" >&nbsp;</a>
          <a class="icon_sitepage_photos_rotate_cw" href="javascript:void(0)" onclick="$(this).set('class', 'page_cw icon_loading');en4.sitepageevent.rotate(<?php echo $this->photo->getIdentity() ?>, 270).addEvent('complete', function(){ this.set('class', 'icon_sitepage_photos_rotate_cw') }.bind(this)); loadingImage();" title="<?php echo $this->translate("Rotate Right"); ?>" >&nbsp;</a>
          <a class="icon_sitepage_photos_flip_horizontal" href="javascript:void(0)" onclick="$(this).set('class', 'page_horizontal icon_loading');en4.sitepageevent.flip(<?php echo $this->photo->getIdentity() ?>, 'horizontal').addEvent('complete', function(){ this.set('class', 'icon_sitepage_photos_flip_horizontal') }.bind(this));loadingImage();" title="<?php echo $this->translate("Flip Vertical"); ?>" >&nbsp;</a>
          <a class="icon_sitepage_photos_flip_vertical" href="javascript:void(0)" onclick="$(this).set('class', 'page_vertical icon_loading');en4.sitepageevent.flip(<?php echo $this->photo->getIdentity() ?>, 'vertical').addEvent('complete', function(){ this.set('class', 'icon_sitepage_photos_flip_vertical') }.bind(this));loadingImage();" title="<?php echo $this->translate("Flip Horizontal"); ?>" >&nbsp;</a>
          <input type="hidden" id='canReload' />
        </div>
      <?php endif ?>

      <?php if ($this->photo->getTitle()): ?>
        <div class="sitepageevent_photo_title">
          <?php echo $this->photo->getTitle(); ?>
        </div>
      <?php endif; ?>
      <?php if ($this->photo->getDescription()): ?>
        <div class="sitepageevent_photo_description">
          <?php echo $this->photo->getDescription() ?>
        </div>
      <?php endif; ?>
      <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')) : ?>
        <div class="seaotagcheckinshowlocation">
          <?php
          // Render LOCATION WIDGET
          echo $this->content()->renderWidget("sitetagcheckin.location-sitetagcheckin");
          ?>
        </div>
      <?php endif; ?>
      <div class="sitepage_photo_tags" id="media_tags" style="display: none;">
        <?php echo $this->translate('In this photo:'); ?>
      </div>
      <?php $editurl = $this->url(array('action' => 'photo-edit', 'photo_id' => $this->photo->photo_id, 'event_id' => $this->sitepageevent->event_id, 'page_id' => $this->sitepage->page_id, 'tab' => $this->tab_selected_id), 'sitepageevent_photo_extended', true); ?>
      <?php $deleteurl = $this->url(array('action' => 'remove', 'photo_id' => $this->photo->photo_id, 'event_id' => $this->sitepageevent->event_id, 'page_id' => $this->sitepageevent->page_id, 'owner_id' => $this->photo->user_id, 'tab' => $this->tab_selected_id), 'sitepageevent_photo_extended', true); ?>
      <div class="sitepageevent_photo_date">
        <?php echo $this->translate('Added'); ?> <?php echo $this->timestamp($this->photo->creation_date) ?>
        <?php if ($this->viewer_id == $this->sitepageevent->user_id || $this->can_edit == 1): ?>			
          - <a href='javascript:void(0);' onclick='taggerInstance.begin();'><?php echo $this->translate('Tag This Photo'); ?></a>
          - <a href="javascript:void(0);" onclick="showSmoothbox('<?php echo $this->photo->photo_id; ?>', 'edit', 'null', 'null', '<?php echo $this->sitepage->page_id ?>',  '<?php echo $this->sitepageevent->event_id; ?>', '<?php echo $this->photo->user_id ?>', '<?php echo $editurl; ?>');">
            <?php echo $this->translate('Edit'); ?>
          </a>
          - <a href="javascript:void(0);" onclick="showSmoothbox('<?php echo $this->photo->photo_id; ?>', 'delete', 'null', 'null','<?php echo $this->sitepage->page_id ?>', '<?php echo $this->sitepageevent->event_id; ?>', '<?php echo $this->photo->user_id ?>', '<?php echo $deleteurl; ?>');">
            <?php echo $this->translate('Delete'); ?>
          </a>
        <?php endif; ?>
        <?php if ($this->viewer_id): ?>
          <?php if (SEA_PHOTOLIGHTBOX_SHARE): ?>
            - <a href="javascript:void(0);" onclick="showSmoothbox('<?php echo $this->photo->photo_id; ?>', 'share', '<?php echo $this->photo->getType() ?>', '<?php echo $this->photo->getIdentity() ?>','<?php echo $this->sitepage->page_id ?>', '<?php echo $this->sitepageevent->event_id; ?>', '<?php echo $this->photo->user_id ?>');">
              <?php echo $this->translate('Share'); ?>
            </a>
          <?php endif; ?>
          <?php if (SEA_PHOTOLIGHTBOX_REPORT): ?>		
            -
            <a href="javascript:void(0);" onclick="showSmoothbox('<?php echo $this->photo->getGuid(); ?>', 'report','<?php echo $this->photo->getType() ?>', '<?php echo $this->photo->getIdentity() ?>','<?php echo $this->sitepage->page_id ?>', '<?php echo $this->sitepageevent->event_id; ?>', '<?php echo $this->photo->user_id ?>');">
              <?php echo $this->translate('Report'); ?>
            </a>	
          <?php endif; ?>
        <?php endif; ?>
        <?php if (SEA_PHOTOLIGHTBOX_DOWNLOAD): ?>  
          - <iframe src="about:blank" style="display:none" name="downloadframe"></iframe>
          <a href="<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'core', 'action' => 'download'), 'default', true); ?><?php echo '?path=' . urlencode($this->photo->getPhotoUrl()) . '&file_id=' . $this->photo->file_id ?>" target='downloadframe'><?php echo $this->translate('Download') ?></a>
        <?php endif; ?>

      </div>
    </div>
    <br />

    <?php echo $this->action("list", "comment", "seaocore", array("type" => "sitepageevent_photo", "id" => $this->photo->getIdentity())) ?>

    <?php if (empty($this->isajax)): ?>
    </div>
  </div>
<?php endif; ?>

<script type="text/javascript">
 
  function getPrevPhoto(){
    return '<?php echo $this->photo->getPrevCollectible()->getHref() ?>';
  }

  function getNextPhoto(){
    return '<?php echo $this->photo->getNextCollectible()->getHref() ?>';
  }

<?php if ($this->viewer()->getIdentity()): ?>
    var taggerInstance;
    en4.core.runonce.add(function() {          
      taggerInstance = new Tagger('media_image_next', {
        'title' : '<?php echo $this->translate('Tag This Photo'); ?>',
        'description' : '<?php echo $this->translate('Type a tag or select a name from the list.'); ?>',
        'createRequestOptions' : {
          'url' : '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'add'), 'default', true) ?>',
          'data' : {
            'subject' : '<?php echo $this->subject()->getGuid() ?>'
          }
        },
        'deleteRequestOptions' : {
          'url' : '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'remove'), 'default', true) ?>',
          'data' : {
            'subject' : '<?php echo $this->subject()->getGuid() ?>'
          }
        },
        'cropOptions' : {
          'container' : $('media_image_next')
        },
        'tagListElement' : 'media_tags',
        'existingTags' : <?php echo $this->action('retrieve', 'tag', 'core', array('sendNow' => false)) ?>,
        'suggestParam' : <?php echo $this->action('suggest', 'friends', 'user', array('sendNow' => false, 'includeSelf' => true)) ?>,
        'guid' : <?php echo ( $this->viewer()->getIdentity() ? "'" . $this->viewer()->getGuid() . "'" : 'false' ) ?>,
        'enableCreate' : <?php echo ( $this->can_edit ? 'true' : 'false') ?>,
        'enableDelete' : <?php echo ( $this->can_edit ? 'true' : 'false') ?>
      });
      // Remove the onclick attrib while tagging
      var onclickNext = $('media_image_next').getProperty('onclick');
      taggerInstance.addEvents({
        'onBegin' : function() {
          $('media_image_next').setProperty('onclick','');
        },
        'onEnd' : function() {
          $('media_image_next').setProperty('onclick',onclickNext);
        }
      });
    });
<?php endif; ?>

  window.addEvent('keyup', function(e) {
    if( e.target.get('tag') == 'html' ||e.target.get('tag') == 'a'||
      e.target.get('tag') == 'body' ) {
      if( e.key == 'right' ) {
        photopagination(getNextPhoto());
      } else if( e.key == 'left' ) {
        photopagination(getPrevPhoto());
      }
    }
  });

  var loadingImage = function(){
    if(document.getElementById('media_image_div'))
      $('media_image').src = "<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/loader.gif";
  };

  var photopagination = function(url)
  {

    if(document.getElementById('media_image_div'))
      document.getElementById('media_image_div').innerHTML="<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/loader.gif' class='' />";
  
    if(document.getElementById('page_lightbox_text'))
      document.getElementById('page_lightbox_text').style.display="none";
    if(document.getElementById('page_lightbox_user_options'))
      document.getElementById('page_lightbox_user_options').style.display="none";
    if(document.getElementById('page_lightbox_user_right_options'))
      document.getElementById('page_lightbox_user_right_options').style.display="none";
    
    en4.core.request.send(new Request.HTML({
      url : url,
      data : {
        format : 'html',
        isajax : 1
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {      
        $('image_div').innerHTML = responseHTML;
        if($('photo_navigation1'))
          $('photo_navigation1').innerHTML = $('photo_navigation2').innerHTML;

        if($('image_next_div2')) {
          $('image_next_div1').innerHTML = $('image_next_div2').innerHTML;
        }
      }
    }));
  };
	
  function showSmoothbox(photo_id, action, type, id, page_id, event_id, owner_id, url) 
  {
    var tab = "<?php echo $this->tab_selected_id; ?>";
    if(action == 'report') {
      Smoothbox.open(en4.core.baseUrl + 'core/report/create/subject/' + photo_id + '/tab/' + tab + '/format/smoothbox');
      parent.Smoothbox.close;
    }
    else if(action == 'share') {  
      Smoothbox.open(en4.core.baseUrl + 'activity/index/share/type/' + type + '/id/' + id + '/tab/' + tab + '/format/smoothbox');
      parent.Smoothbox.close;			
    }
    else if(action == 'edit') {
      Smoothbox.open(url);
      parent.Smoothbox.close;
    }		
    else if(action == 'delete') {
      Smoothbox.open(url);
      parent.Smoothbox.close;
    }
  }

</script>