<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headLink()->appendStylesheet($this->seaddonsBaseUrl()
  	              . '/application/modules/Seaocore/externals/styles/styles.css');
?>
<div class="layout_core_container_tabs">
	<?php if (($this->list_view && $this->grid_view) || ($this->grid_view) || ($this->list_view) || count($this->tabs)>1): ?>
		<?php if(Count($this->tabs) > 1): ?>
			<div class="tabs_alt tabs_parent">
		<?php else: ?>
			<div class="document_view_select">
		<?php endif;?>
			<ul id="main_tabs">
				<?php if(Count($this->tabs) > 1): ?>
					<?php $active=true; ?> 
					<?php foreach ($this->tabs as $key => $tab): ?>
						<?php $class = $active ? 'active' : '' ?>
						<?php $active=false; ?> 
						<li class = '<?php echo $class ?>'  id = '<?php echo 'document_home_document_' . $key.'_tab' ?>'>
							<a href='javascript:void(0);'  onclick="showListDocument('<?php echo $tab['tabShow']; ?>','<?php echo $key; ?>');"><?php echo $this->translate($tab['title']) ?></a>
						</li>
					<?php endforeach; ?>
				<?php endif; ?>  
				<?php if (($this->list_view && $this->grid_view)): ?>
					<li class="seaocore_tbs_widget_filter_icon">
						<div class="seaocore_tbs_widget_tip"><?php echo $this->translate("Grid View") ?></div>
						<img src='application/modules/Document/externals/images/grid.png' onclick="rswitchviewDocument(1)" align="left" alt="" class="select_view" />
					</li> 
					<li class="seaocore_tbs_widget_filter_icon">
						<div class="seaocore_tbs_widget_tip"><?php echo $this->translate("List View") ?></div>
						<img src='application/modules/Document/externals/images/list.png' onclick="rswitchviewDocument(0)" align="left" alt="" class="select_view" />
					</li>
				<?php endif;?>
			</ul>
		</div>
	<?php endif; ?>
	<div id="dynamic_app_info_document">
		<?php include_once APPLICATION_PATH . '/application/modules/Document/views/scripts/_ajax_home_documents.tpl';?>
	</div>
</div>
<script type="text/javascript">
	function rswitchviewDocument(flage){
		if(flage==1) {
			if($('rgrid_view_document'))
				$('rgrid_view_document').style.display='none';
			if($('rimage_view_document'))
				$('rimage_view_document').style.display='block';
		}
		else{   
			if($('rgrid_view_document'))
				$('rgrid_view_document').style.display='block';
			if($('rimage_view_document'))
				$('rimage_view_document').style.display='none';
		}
	}

	/* moo style */
	window.addEvent('domready',function() {
		if($('rimage_view_document')){
		showtooltipDocument();
		}

		rswitchviewDocument(<?php echo $this->defaultView ?>);
	});

	var showtooltipDocument = function (){
		if($('rimage_view_document')){
		//opacity / display fix
		$$('.document_tooltip_show').setStyles({
			opacity: 0,
			display: 'block'
		});
		//put the effect in place
		$$('.jq-document_tooltip li').each(function(el,i) {
			el.addEvents({
				'mouseenter': function() {
					el.getElement('div').fade('in');
				},
				'mouseleave': function() {
					el.getElement('div').fade('out');
				}
			});
		});
		}
	}

  var showListDocument = function (tabshow,tabName) {  
		<?php foreach ($this->tabs as $key=> $tab): ?>
			if($('<?php echo 'document_home_document_'.$key.'_tab' ?>'))
				$('<?php echo 'document_home_document_' .$key.'_tab' ?>').erase('class');
		<?php  endforeach; ?>

		if($('document_home_document_'+tabName+'_tab'))
        $('document_home_document_'+tabName+'_tab').set('class', 'active');
      
		if($('dynamic_app_info_document') != null) {
      $('dynamic_app_info_document').innerHTML = '<center><img alt="" src="application/modules/Seaocore/externals/images/loading.gif" class="seaocore_tabs_loader_img" /></center>';
    }

    var request = new Request.HTML({
      'url' : '<?php echo $this->url(array(), 'document_ajaxhome', true) ?>',
      'data' : {
        'format' : 'html',
        'task' : 'ajax',
        'tab_show' : tabshow,
        'list_limit':<?php echo  $this->active_tab_list; ?>,
        'grid_limit':<?php echo $this->active_tab_image; ?>,
        'list_view':<?php echo $this->list_view; ?>,
        'grid_view':<?php echo $this->grid_view; ?>,
        'defaultView':<?php echo $this->defaultView; ?>,

      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $('dynamic_app_info_document').innerHTML = responseHTML;
        
        showtooltipDocument();
        rswitchviewDocument(<?php echo $this->defaultView ?>);
      }
    });

    request.send();
  }
</script>