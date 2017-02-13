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

<?php if($this->is_friend_list === true && $this->friendRequestCount > 0) : ?>
  <div class="sitepage_member_see_waiting"><?php echo $this->htmlLink(array('route' => 'user_general', 'action' => 'friend-request'), $this->translate(array('View %s friend request.', 'View %s friend requests.', $this->friendRequestCount), $this->friendRequestCount), array(' class' => 'buttonlink icon_sitepage_member smoothbox')); ?></div>
<?php endif; ?>

<h3>
  <?php if($this->is_friend_list === true) : ?>
    <?php echo $this->translate(array('%s friend found.', '%s friends found.', $this->totalUsers),$this->locale()->toNumber($this->totalUsers)) ?>
  <?php else : ?>
    <?php echo $this->translate(array('%s member found.', '%s members found.', $this->totalUsers),$this->locale()->toNumber($this->totalUsers)) ?>
  <?php endif; ?>
</h3>
<?php $viewer = Engine_Api::_()->user()->getViewer();?>

<?php if( count($this->users) ): ?>
  <ul id="browsemembers_ul">
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
  </ul>
<?php endif ?>

<?php if( $this->users ):
    $pagination = $this->paginationControl($this->users, null, null, array(
      'pageAsQuery' => true,
      'query' => $this->formValues,
    ));
  ?>
  <?php if( trim($pagination) ): ?>
    <div class='browsemembers_viewmore' id="browse_viewmore">
      <?php // echo $pagination ?>
    </div>
    <script type='text/javascript'>
      var page = 1;
      var totalPage = <?php echo $this->users->count() ?>;
      var injectedElementId = 'browsemembers_ul';
      
      en4.core.runonce.add(function() {
        window.onscroll = doLoadOnScroll;
        
        $('back_to_top_button').addEvent('click', function(event) {
          event = new Event(event).stop();
          jQuery("html, body").animate({scrollTop: 0}, "slow");
        });
        window.addEvent('scroll', function (){
          var element=$("back_to_top_button");  
          if(!element)
            return;
          if( typeof( $(injectedElementId).offsetParent ) != 'undefined' ) {
            var elementPostionY=$(injectedElementId).offsetTop;
          }else{
            var elementPostionY=$(injectedElementId).y; 
          }
          if(elementPostionY + window.getSize().y < window.getScrollTop()){
            if(element.hasClass('Offscreen'))
              element.removeClass('Offscreen');
          }else if(!element.hasClass('Offscreen')){       
            element.addClass('Offscreen');
          }
        });
      });
      var doLoadOnScroll = function() {
        if( typeof( $('browse_viewmore').offsetParent ) != 'undefined' ) {
          var botElPos = $('browse_viewmore').offsetTop;
        } else {
          var botElPos = $('browse_viewmore').y;
        }
        if(botElPos <= window.getScrollTop()+(window.getSize().y -40)){
          viewMore();    
        }
      };
      
      // Flag to indicate whether the page is loading
      var viewMoreActive = false;
      var viewMore = function() {
        if(viewMoreActive || page >= totalPage) return;
        viewMoreActive = true;
        page++;
        var url = document.URL + '<?php $query = http_build_query($this->formValues); if($query) echo "?" . $query; ?>';
        $('loading').style.display = '';
        var request = new Request.HTML({
          'url' : url,
          data : {
            format : 'html',
            'page' : page,
            'scrollLoad' : true
          },
          evalScripts : true,
          onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            viewMoreActive = false;
            Elements.from(responseHTML).inject($(injectedElementId));
            $('loading').setStyle('display', 'none');
            Smoothbox.bind();
          }
        }).send();
      };
    </script>
  <?php endif ?>
<?php endif; ?>

<script type="text/javascript">
  totalUsers = '<?php echo sprintf('%d', $this->totalUsers) ?>';
  userCount = '<?php echo sprintf('%d', $this->userCount) ?>';
</script>