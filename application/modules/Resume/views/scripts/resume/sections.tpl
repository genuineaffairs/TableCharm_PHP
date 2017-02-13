<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
?>

<?php 
$sections = $this->resume->getSections();

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Resume/externals/styles/resume.css');
?>

<div class="resume_add_section_link">
<?php echo $this->htmlLink($this->resume->getActionHref('add-section'), $this->translate('Add New Section'), array('class'=>'resume_create_button smoothbox')); ?>
</div>

<div id="resume_sections_manage_list">
<?php foreach ($sections as $section):  $child_type = strtolower($section->child_type); ?>
  <div id="resume_section_<?php echo $section->getIdentity(); ?>" class="resume_sections_manage_block">
    <div class="resume_sections_manage_section_title">
      <?php echo $this->translate($section->getTitle()); ?>
    </div>
    <ul class="resume_sections_actions">
      <?php if (!$section->isChildTypeText()): ?>
      <li class="resume_sections_actions_add resume_sections_actions_add_<?php echo $child_type; ?>">
        <?php echo $this->htmlLink($section->getHref(array('action'=>'add-child')), $this->translate('Add ' . $section->child_type), array('class'=>'smoothbox'))?>
      </li>
      <?php endif; ?>
      <li class="resume_sections_actions_edit">
        <?php echo $this->htmlLink($section->getHref(array('action'=>'edit')), $this->translate('Edit'), array('class'=>'smoothbox'))?>
      </li>
      <li class="resume_sections_actions_delete">
        <?php echo $this->htmlLink($section->getHref(array('action'=>'delete')), $this->translate('Delete'), array('class'=>'smoothbox'))?>
      </li>
    </ul>
    <?php if ($description = trim($section->getDescription())): ?>
      <div class="resume_body_description resume_sections_manage_section_description">
        <?php echo $description; ?>
      </div>
    <?php endif; ?>
    
    <?php if (!$section->isChildTypeText()): $children = $section->getChildItems(); ?>
      <?php if (!empty($children)) :?>
        <ul id="resume_sections_children_<?php echo $section->getIdentity(); ?>" class="resume_sections_children resume_sections_children_<?php echo $child_type; ?>">
          <?php foreach ($children as $child): ?>
            <li id="resume_section_child_<?php echo $child->getIdentity(); ?>">
              <img src="application/modules/Core/externals/images/admin/sortable.png" width="16" height="16" class='move-me' /> 
              <?php 
                echo $this->partial('section/_manage_child_'.$child_type.'.tpl', 'resume', array('child' => $child));
              ?>
              <ul class="resume_sections_children_actions">
                <li class="resume_sections_children_actions_edit">
                  <?php echo $this->htmlLink($section->getHref(array('action'=>'edit-child', 'child_id'=>$child->getIdentity())), $this->translate('Edit'), array('class'=>'smoothbox'))?>
                </li>
                <li class="resume_sections_children_actions_delete">
                  <?php echo $this->htmlLink($section->getHref(array('action'=>'delete-child', 'child_id'=>$child->getIdentity())), $this->translate('Delete'), array('class'=>'smoothbox'))?>
                </li>
              </ul>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
</div>

<script type="text/javascript">

  var SortablesInstance;

  var SortablesInstance2;
  
  window.addEvent('load', function() {
    SortablesInstance = new Sortables('resume_sections_manage_list', {
      clone: true,
      constrain: true,
      handle: 'div.resume_sections_manage_section_title',
      opacity: 0.3, //default is 1
      
      onComplete: function(e) {
        reorder(e);
      }
    });

    SortablesInstance2 = new Sortables('ul.resume_sections_children', {
        clone: true,
        constrain: true,
        handle: 'img.move-me',
        opacity: 0.3, //default is 1
        
        onComplete: function(e) {
          reorder2(e);
        }
      });
  });

  var reorder = function(e) {
    
	     var sectionitems = e.parentNode.childNodes;
	     var ordering = {};
       
	     var i = 1;
	     for (var sectionitem in sectionitems)
	     {
	       var child_id = sectionitems[sectionitem].id;

	       if ((child_id != undefined) && (child_id.substr(0, 15) == 'resume_section_'))
	       {
	         ordering[child_id] = i;
	         i++;
	       }
	     }
	    ordering['format'] = 'json';
	    
	    // Send request
	    var url = '<?php echo $this->url(array('action' => 'order-sections')) ?>';
	    var request = new Request.JSON({
	      'url' : url,
	      'method' : 'POST',
	      'data' : ordering,
	      onSuccess : function(responseJSON) {
	      }
	    });

	    request.send();
	    
	  }

  var reorder2 = function(e) {
	    
	     var sectionitems = e.parentNode.childNodes;
	     var ordering = {};
 
	     var i = 1;
	     for (var sectionitem in sectionitems)
	     {
	       var child_id = sectionitems[sectionitem].id;

	       if ((child_id != undefined) && (child_id.substr(0, 21) == 'resume_section_child_'))
	       {
	         ordering[child_id] = i;
	         i++;
	       }
	     }
	    ordering['format'] = 'json';
	    ordering['section_id'] = e.parentNode.id.replace("resume_sections_children_","");
	    
	    // Send request
	    var url = '<?php echo $this->url(array('action' => 'order-children', 'controller'=>'section'), 'resume_extended', true) ?>';
	    var request = new Request.JSON({
	      'url' : url,
	      'method' : 'POST',
	      'data' : ordering,
	      onSuccess : function(responseJSON) {
	      }
	    });

	    request.send();
	    
	  }
  
  function ignoreDrag()
  {
    event.stopPropagation();
    return false;
  }

</script>
