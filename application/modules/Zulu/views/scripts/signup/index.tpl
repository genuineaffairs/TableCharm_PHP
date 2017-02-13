<?php
/**
* SocialEngine
*
* @category   Application_Core
* @package    User
* @copyright  Copyright 2006-2010 Webligo Developments
* @license    http://www.socialengine.com/license/
* @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
* @author     John
*/
?>

<script type="text/javascript">
    <?php if(!Engine_Api::_()->zulu()->isMobileMode()) : ?>
    
        function skipForm() {
            document.getElementById("skip").value = "skipForm";
            $('SignupForm').submit();
        }
        function finishForm() {
            document.getElementById("nextStep").value = "finish";
        }
    
    <?php else : ?>

        function skipForm() {
            $('#skip').attr('value', 'skipForm');
            $.mobile.activePage.find('#SignupForm').submit();
        }
        function finishForm() {
            $.mobile.activePage.find('#nextStep').attr('value', 'finish');
        }
    
    <?php endif; ?>
</script>

<?php echo $this->content()->renderWidget('zulu.progress-bar', array('steps' => $this->steps)); ?>

<?php echo $this->partial($this->script[0], $this->script[1], array(
  'form' => $this->form,
  'sa_participation_list' => $this->sa_participation_list
)) ?>
