<?php echo $this->doctype()->__toString() ?>
<?php $locale = $this->locale()->getLocale()->__toString(); $orientation = ( $this->layout()->orientation == 'right-to-left' ? 'rtl' : 'ltr' ); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $locale ?>" lang="<?php echo $locale ?>" dir="<?php echo $orientation ?>">
  <head>
    <base href="<?php echo rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->baseUrl(), '/'). '/' ?>" />

    <?php // ALLOW HOOKS INTO META ?>
    <?php echo $this->hooks('onRenderLayoutDefault', $this) ?>


    <?php // TITLE/META ?>
    <?php
    $counter = (int) $this->layout()->counter;
    $staticBaseUrl = $this->layout()->staticBaseUrl;
    $headIncludes = $this->layout()->headIncludes;

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->headTitle()
      ->setSeparator(' - ');
    $pageTitleKey = 'pagetitle-' . $request->getModuleName() . '-' . $request->getActionName()
      . '-' . $request->getControllerName();
    $pageTitle = $this->translate($pageTitleKey);
    if( $pageTitle && $pageTitle != $pageTitleKey ) {
      $this
        ->headTitle($pageTitle, Zend_View_Helper_Placeholder_Container_Abstract::PREPEND);
    }
    $this
      ->headTitle($this->translate($this->layout()->siteinfo['title']), Zend_View_Helper_Placeholder_Container_Abstract::PREPEND)
    ;
    $this->headMeta()
      ->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8')
      ->appendHttpEquiv('Content-Language', $this->locale()->getLocale()->__toString());

    // Make description and keywords
    $description = '';
    $keywords = '';

    $description .= ' ' .$this->layout()->siteinfo['description'];
    $keywords = $this->layout()->siteinfo['keywords'];

    if( $this->subject() && $this->subject()->getIdentity() ) {
      $this->headTitle($this->subject()->getTitle());

      $description .= ' ' .$this->subject()->getDescription();
      if (!empty($keywords)) $keywords .= ',';
        $keywords .= $this->subject()->getKeywords(',');
      }

      $this->headMeta()->appendName('description', trim($description));
      $this->headMeta()->appendName('keywords', trim($keywords));

      // Get body identity
      if( isset($this->layout()->siteinfo['identity']) ) {
        $identity = $this->layout()->siteinfo['identity'];
    } else {
      $identity = $request->getModuleName() . '-' .
      $request->getControllerName() . '-' .
      $request->getActionName();
    }
    ?>
    <?php echo $this->headTitle()->toString()."\n" ?>
    <?php echo $this->headMeta()->toString()."\n" ?>


    <?php // LINK/STYLES ?>
    <?php
    $this->headLink(array(
      'rel' => 'favicon',
      'href' => ( isset($this->layout()->favicon)
      ? $staticBaseUrl . $this->layout()->favicon
      : '/favicon.ico' ),
      'type' => 'image/x-icon'),
      'PREPEND'
    );
    $themes = array();

    $this->headLink()->prependStylesheet($staticBaseUrl . 'application/modules/Mgsl/externals/styles/print.css', 'screen, print');

    // Process
    foreach( $this->headLink()->getContainer() as $dat ) {
      if( !empty($dat->href) ) {
        if( false === strpos($dat->href, '?') ) {
          $dat->href .= '?c=' . $counter;
        } else {
          $dat->href .= '&c=' . $counter;
        }
      }
    }
    ?>
    <?php echo $this->headLink()->toString()."\n" ?>
    <?php echo $this->headStyle()->toString()."\n" ?>


  </head>

  <body>
    <div id="printButtons">
      <a href="javascript:window.print();" class="link_button">Print</a>
      <?php echo $this->htmlLink($this->baseUrl(), 'Go Back', array(
        'class' => 'link_button',
      )); ?>
    </div>
    
    <h3>
      <?php echo $this->translate(array('%s member found.', '%s members found.', $this->totalUsers),$this->locale()->toNumber($this->totalUsers)) ?>
    </h3>
    <?php $viewer = Engine_Api::_()->user()->getViewer();?>

    <?php if( count($this->users) ): ?>
    <ul id="browsemembers_ul">
      <?php foreach( $this->users as $user ): ?>
      <li>
        <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')) ?>

        <div class='browsemembers_results_info'>
          <?php echo $user->getTitle() ?>
        </div>
        <?php if(Engine_Api::_()->getDbTable('accessLevel', 'zulu')->isAllowed($user, $this->viewer(), 'view_clinical')) : ?>
        <div class='browsemembers_results_info'>
          <img title="This person has shared the medical record with you" alt="This person has shared the medical record with you" class="zulu_small_icon" src="/application/modules/Zulu/externals/images/zulu_05.png" />
          <?php if($accessLevel = Engine_Api::_()->getDbTable('profileshare', 'zulu')->getAccessLevel($user, $this->viewer())) : ?>
            <div class='medical_icon_access_text'><?php echo $this->translate(Zulu_Model_DbTable_AccessLevel::$accessTypeString[$accessLevel]); ?></div>
          <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php $zulu = Engine_Api::_()->getItemTable('zulu')->getZuluByUserId($user->getIdentity()); ?>
        <?php if($zulu && $zulu->hasConcussionTest()) : ?>
        <div class='browsemembers_results_info'>
          <img title="Concussion Test" class="zulu_small_icon" src="<?php echo $this->baseUrl() ?>application/modules/Zulu/externals/images/concussion.png" />
          <div class='medical_icon_access_text concussion_text'>Concussion Test</div>
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
    <div class='browsemembers_viewmore' id="browsemembers_viewmore">
      <?php echo $pagination ?>
    </div>
    <?php endif ?>
    <?php endif; ?>

  </body>
</html>
  