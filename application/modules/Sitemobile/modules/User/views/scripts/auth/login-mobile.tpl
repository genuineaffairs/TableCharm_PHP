<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: login.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<div class="ui-login-page-content-wrap">
  <div class="ui-page-content ui-login-page-content">
    <h3 class="sm-ui-cont-cont-heading">
      <?php echo $this->translate('Sign In or %1$sJoin%2$s', '<a href="' . $this->url(array(), "user_signup") . '">', '</a>'); ?>
    </h3>
    <?php echo $this->form->render($this) ?>
    <p><b><?php echo $this->translate('New to %s?', Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.site.title', Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title'))); ?></b></p>
    <a data-role="button" data-theme="d" href="<?php echo $this->url(array(), "user_signup") ?>"><?php echo $this->translate('Create New Account'); ?></a>
    <div class="ui-login-page-btm-links">
      <a href='<?php echo $this->url(array('module' => 'user', 'controller' => 'auth', 'action' => 'forgot'), 'default', true) ?>'><?php echo $this->translate('Forgot Password?') ?></a>
      <span>&#183;</span>
      <?php if (Engine_Api::_()->getDbtable('modules', 'sitemobile')->isModuleEnabled('sitefaq')): ?>
          <a href='<?php echo $this->url(array('action' => 'home'), 'sitefaq_general', true) ?>'><?php echo $this->translate('Help') ?></a>
      <?php else:?>
    <a href='<?php echo $this->url(array('module' => 'core', 'controller' => 'help', 'action' => 'contact'), 'default', true) ?>'><?php echo $this->translate('Help') ?></a>
    <?php endif; ?>
    </div>
  </div>
</div>


<script type="text/javascript">
    sm4.core.runonce.add(function() { 
       if (typeof $('#facebook-element').get(0) != 'undefined')
          $('#facebook-element').find('a').attr('data-ajax', 'false');
       if (typeof $('#twitter-element').get(0) != 'undefined') 
          $('#twitter-element').find('a').attr('data-ajax', 'false');
      
    })
    
</script> 