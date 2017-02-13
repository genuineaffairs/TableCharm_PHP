<?php

?>
<?php if ($this->count > 0): ?>
   <script type="text/javascript">
      en4.core.runonce.add(function(){
          $('selectall').addEvent('click', function(){
                 
            var ids = document.getElementById('users-element').getElementsByTagName('li');        
                    
            for (var i=0; i<ids.length; i++)
            {
               var temp =ids[i].firstChild ;
                                 
               if(temp.type == 'checkbox')
               {
                   temp.checked = this.checked;
               }
            }
        // $$('input[type=checkbox]').set('checked', $(this).get('checked', false)); 
      })});
   </script>

   <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
<?php else: ?>
   <div>
      <?php echo $this->translate('You have no friends you can invite.'); ?>
      <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Close'), array('onclick' => 'parent.Smoothbox.close();')) ?>
   </div>
<?php endif; ?>