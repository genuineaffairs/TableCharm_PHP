<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: pageNoteHeader.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if(!empty($include_file)) :?> 
	<?php   
		$this->headLink()
	        ->appendStylesheet($this->layout()->staticBaseUrl
	                . 'application/css.php?request=/application/modules/Sitepagenote/externals/styles/style_sitepagenote.css')
	?>
	<?php
	$this->headScript()
	        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
	        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
	        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
	        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
	?>
	
	<script type="text/javascript">
	  en4.core.runonce.add(function()
	  {
	    new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>', {
	      'postVar' : 'text',
	      'customChoices' : true,
	      'minLength': 1,
	      'selectMode': 'pick',
	      'autocompleteType': 'tag',
	      'className': 'tag-autosuggest',
	      'filterSubset' : true,
	      'multiple' : true,
	      'injectChoice': function(token){
	        var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token.label, 'id':token.id});
	        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
	        choice.inputValue = token;
	        this.addChoiceEvents(choice).inject(this.choices);
	        choice.store('autocompleteChoice', token);
	      }
	    });
	  });
	</script>
<?php endif;?>

<?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
<div class="sitepage_viewpages_head">
  <?php echo $this->htmlLink($this->sitepage->getHref(), $this->itemPhoto($this->sitepage, 'thumb.icon', '', array('align' => 'left'))) ?>
  <?php if(!empty($this->can_edit)):?>
		<div class="fright">
			<a href='<?php echo $this->url(array('page_id' => $this->sitepage->page_id), 'sitepage_edit', true) ?>' class='buttonlink icon_sitepages_dashboard'><?php echo $this->translate('Dashboard');?></a>
		</div>
	<?php endif;?>
  <h2>	
    <?php echo $this->sitepage->__toString(); ?>	
    <?php echo $this->translate('&raquo; '); ?>
    <?php //echo $this->htmlLink(array('route' => 'sitepage_entry_view', 'page_url' => Engine_Api::_()->sitepage()->getPageUrl($this->sitepage->page_id), 'tab' => $this->tab_selected_id), $this->translate('Notes')) ?>
    <?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Notes')) ?>
  </h2>
</div>