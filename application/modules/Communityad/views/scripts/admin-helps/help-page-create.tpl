<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: help-page-create.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
  <?php echo $this->translate('Community Ads Plugin') ?>
</h2>
<?php ?>
<script type="text/javascript">

function multiDelete()
{
  return confirm("<?php echo $this->translate("Are you sure you want to delete the selected FAQ?") ?>");
}

function selectAll()
{
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}
</script>

<?php if( count($this->navigation) ): ?>
<div class='communityad_admin_tabs'>
  <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>

<p>
	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'helps', 'action' => 'help-and-learnmore'), $this->translate("Back to Manage Advertising Help Pages"), array('class'=>'cmad_icon_back buttonlink')) ?>
</p>
<br />
<?php if( empty($this->faqCheck) ){ ?>
<div class='clear'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
<style type="text/css">
.defaultSkin iframe
{
	width:600px !important;
	height:350px !important;
}  
</style>
<?php } else { ?>

<h3><?php echo $this->translate("Editing: "). $this->page_title ?></h3>
<p style="display:block;">
	<?php
  	echo $this->translate("Here, you can manage the FAQ entries of this page.");
  ?>
	<br />
	<br />
	<?php
	// Show link for "Create Featured Content".
		echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'faqs', 'action' => 'faqcreate', 'page_id' => $this->page_id), $this->translate("Add an FAQ to this page"), array('class'=>'cmad_icon_create buttonlink'));
	?>
	<br style="clear:both;" />
</p><br />

<?php
	if( count($this->paginator) ):
?>
<form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="multiDelete()">
  <table class='admin_table'>
    <thead>
      <tr>
        <th class='admin_table_short'>
        	<input onclick='selectAll();' type='checkbox' class='checkbox' />
        </th>
        <th class='admin_table_short' align="left">
        	<?php echo $this->translate("ID"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Questions"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Answers"); ?>
        </th>
        <th align="left">
        	<?php echo $this->translate("Options"); ?>
        </th>
      </tr>
    </thead>
    <tbody>
    	<?php foreach ($this->paginator as $item): ?>
        <tr>
          <td><input type='checkbox' name='delete_<?php echo $item->faq_id;?>' value='<?php echo $item->faq_id ?>' class='checkbox' value="<?php echo $item->faq_id ?>" <?php if( !empty($item->faq_default) ){ echo 'DISABLED'; } ?>/></td>
          <td><?php echo $item->faq_id; ?></td>
          <?php
						$qus = $this->translate($item->question);
						$tmpQuestion = strip_tags($qus);
						$faqQuestion =  ( Engine_String::strlen($tmpQuestion) > 50 ? Engine_String::substr($tmpQuestion, 0, 50) . '..' : $tmpQuestion );	
          ?>
          <td><?php echo $faqQuestion; ?></td>
          <?php
						$ans = $this->translate($item->answer);
						$tmpAnswers = strip_tags($ans);
						$faqAnswers =  ( Engine_String::strlen($tmpAnswers) > 50 ? Engine_String::substr($tmpAnswers, 0, 50) . '..' : $tmpAnswers );	
          ?>
          <td><?php echo $faqAnswers; ?></td>          
          <td>
          <?php
						if( empty($item->faq_default) ) {
							echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'faqs', 'action' => 'faqcreate', 'faq_id' => $item->faq_id, 'page_id' => $this->page_id), $this->translate("edit")) ;
							echo ' | ' . $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'faqs', 'action' => 'delete', 'faq_id' => $item->faq_id), $this->translate("delete"), array('class' => 'smoothbox'));
						}else {
							echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'faqs', 'action' => 'faq-default-msg', 'faq_id' => $item->faq_id), $this->translate("edit"), array('class' => 'smoothbox'));
						}
					?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <br />
  <div class='buttons'>
  	<button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
  </div>
</form>
<br />
<div>
	<?php echo $this->paginationControl($this->paginator); ?>
</div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no FAQ available.") ?>
    </span>
  </div>
<?php endif; } ?>

<style type="text/css">
.settings .form-element .description{max-width:600px;}
</style>