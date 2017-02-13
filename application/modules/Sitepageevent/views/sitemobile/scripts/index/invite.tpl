<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: invite.tpl 9747 2012-07-26 02:08:08Z john $
 * @access	   Sami
 */
?>
<?php if( $this->count > 0 ): ?>
  <script type="text/javascript">
    sm4.core.runonce.add(function(){
      $('#selectall').bind('click', function(event) {
        if(this.checked) {
          $("input[type='checkbox']").prop("checked",true).checkboxradio("refresh"); 
        } else {
          $("input[type='checkbox']").prop("checked",false).checkboxradio("refresh"); 
        }
      });
    });
  </script>
    <div class="ui-member-invite-popup">
  <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
    </div>
<?php else: ?>
  <div class="tip">
    <span>
			<?php echo $this->translate('You have no friends you can invite.');?>
    </span>
  </div>
<?php endif; ?>