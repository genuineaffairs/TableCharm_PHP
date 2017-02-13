<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: pagination.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if ($this->pageCount > 1): ?>
<div class="seaocore_pagination">
	<div class="pages">
	  <ul class="paginationControl">
	    <?php /* Previous page link */ ?>
	    <?php if (isset($this->previous)): ?>
	    	<li>
	      	<a href="javascript:void(0)" onclick="javascript:pageAction('<?php echo $this->previous; ?>')"><?php echo $this->translate("&#171; Previous") ?></a>
	      </li>
	    <?php endif; ?>

	    <?php foreach ($this->pagesInRange as $page): ?>
	      <?php if ($page != $this->current): ?>
	      	<li>
	        	<a href="javascript:void(0)" onclick="javascript:pageAction('<?php echo $page; ?>')"><?php echo $page; ?></a>
	        </li>
	      <?php else: ?>
	      	<li class="selected">
	        	<a href="javascript:void(0)"><?php echo $page; ?></a>
	        </li>
	      <?php endif; ?>
	    <?php endforeach; ?>

	    <?php /* Next page link */ ?>
	    <?php if (isset($this->next)): ?>
      	<li>
      		<a href="javascript:void(0)" onclick="javascript:pageAction('<?php echo $this->next; ?>')"><?php echo $this->translate("Next &#187;") ?></a>
      	</li>	
	    <?php endif; ?>
		</ul>
	</div> 
</div> 
<?php endif; ?>