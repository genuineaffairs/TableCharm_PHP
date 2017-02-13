<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _sitepagepoll.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
	$this->headScript()
	->appendFile($this->layout()->staticBaseUrl.'application/modules/Sitepagepoll/externals/scripts/core.js');
	$this->headTranslate(array(
	'Show Question', 'Show Result', '%1$s%%', '%1$s vote',
	));
?>

<?php if(($this->sitepagepoll->end_settings == 1 && ($this->sitepagepoll->end_time > ($today = date("Y-m-d H:i:s")))&&($this->can_vote == 1)) || $this->sitepagepoll->end_settings == 0 &&($this->can_vote == 1)) {
				$valid = 1;
			} else { 
					$valid = 0;
			}
?>

<script type="text/javascript">
		//<![CDAT
	var enddate_setting = <?php echo sprintf('%d', $this->sitepagepoll->end_settings) ?>; 
	var enddate_valid = <?php echo sprintf('%d', $valid) ?>;
	en4.core.runonce.add(function() {
		var initializeSitepagepoll = function() {
			en4.sitepagepoll.urls.vote = '<?php echo $this->url(array('module' => 'sitepagepoll', 'controller' => 'index', 'action' => 'vote'), 'default') ?>';
			en4.sitepagepoll.urls.login = '<?php echo $this->url(array(), 'user_login') ?>';
			en4.sitepagepoll.addSitepagepollData(<?php echo $this->sitepagepoll->getIdentity() ?>, {
				canVote : <?php echo $this->canVote ? 'true' : 'false' ?>,
				canChangeVote : <?php echo $this->canChangeVote ? 'true' : 'false' ?>,
				hasVoted : <?php echo $this->hasVoted ? 'true' : 'false' ?>
			});

			$$('#sitepagepoll_form_<?php echo $this->sitepagepoll->getIdentity() ?> .sitepagepoll_radio input').removeEvents('click').addEvent('click', function(event) {
				en4.sitepagepoll.vote(<?php echo $this->sitepagepoll->getIdentity() ?>, event.target);
			});
		}

		// Dynamic loading for feed
		if( $type(en4) == 'object' && 'sitepagepoll' in en4 ) {
			initializeSitepagepoll();
		} else {
			new Asset.javascript('application/modules/Sitepagepoll/externals/scripts/core.js', {
				onload: function() {
					initializeSitepagepoll();
				}
			});
		}
	});
	//]]>
</script>

<span class="sitepagepoll_view_single">
	<form id="sitepagepoll_form_<?php echo $this->sitepagepoll->getIdentity() ?>" action="<?php echo $this->url() ?>" method="POST" onsubmit="return false;">
		<ul id="sitepagepoll_options_<?php echo $this->sitepagepoll->getIdentity() ?>" class="sitepagepoll_options">
			<?php foreach( $this->sitepagepollOptions as $i => $option ): ?>
				<li id="sitepagepoll_item_option_<?php echo $option->poll_option_id ?>">
					<div class="sitepagepoll_has_voted" <?php if ($valid == 1) { echo ( $this->hasVoted ? '' : 'style="display:none;"' );}  else { echo ( $this->hasVoted ? '' : 'style="display:block;"' ); } ?>>
            <?php $show_option = 0;?>
						<?php if ($valid == 1): ?>
							<div class="sitepagepoll_option">
                <?php if (($valid == 1)||($this->sitepagepoll->end_settings == 1 )): ?>
									<?php echo $option->sitepagepoll_option ?>
									<?php $show_option = 1;?>
                <?php endif;?>
							</div>
						<?php endif; ?>
						<div class="sitepagepoll_option">
							<?php if($show_option == 0 && ($this->sitepagepoll->end_settings == 1 )||$this->can_vote == 0): ?>
								<?php echo $option->sitepagepoll_option ?>
							<?php endif; ?>
						</div>
						<?php $pct = $this->sitepagepoll->vote_count
									? floor(100*($option->votes/$this->sitepagepoll->vote_count))
									: 0;
							if (!$pct)
								$pct = 1;
						?>
						<?php if (($valid == 1)||($this->sitepagepoll->end_settings == 1 )||$this->can_vote == 0): ?>
							<div id="sitepagepoll-answer-<?php echo $option->poll_option_id ?>" class='sitepagepoll_answer sitepagepoll-answer-<?php echo (($i%8)+1) ?>' style='width:                         <?php echo .7*$pct;?>%;'>
							&nbsp;
							</div>
							<div class="sitepagepoll_answer_total">
								<?php echo $this->translate(array('%1$s vote', '%1$s votes', $option->votes), $this->locale()->toNumber($option->votes)) ?>
								(<?php echo $this->translate('%1$s%%', $this->locale()->toNumber($option->votes ? $pct : 0)) ?>)
							</div>
						<?php endif; ?>
					</div>
					<div class="sitepagepoll_not_voted" <?php echo ($this->hasVoted?'style="display:none;"':'') ?> >
						<?php if ($valid == 1): ?>
							<div class="sitepagepoll_radio" id="sitepagepoll_radio_<?php echo $option->poll_option_id ?>">
								<input id="sitepagepoll_option_<?php echo $option->poll_option_id ?>"
												type="radio" name="sitepagepoll_options" value="<?php echo $option->poll_option_id ?>"
										<?php if ($this->hasVoted == $option->poll_option_id): ?>checked="true"<?php endif; ?>
										<?php if (($this->hasVoted && !$this->canChangeVote) || $this->sitepagepoll->closed): ?>disabled="true"<?php endif; ?>
								/>
							</div>
							<label for="sitepagepoll_option_<?php echo $option->poll_option_id ?>">
								<?php echo $option->sitepagepoll_option ?>
							</label>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php if( empty($this->hideStats) ): ?>
			<div class="sitepagepoll_stats">
				<!--  Start: Suggest to Friend link show work -->
				<?php if( !empty($this->pollSuggLink) && !empty($this->sitepagepoll->search) ): ?>				
					<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'suggestion', 'controller' => 'index', 'action' => 'popups', 'sugg_id' => $this->sitepagepoll->poll_id, 'sugg_type' => 'page_poll'), $this->translate('Suggest to Friends'), array(
						'class'=>'smoothbox')) ?>
					&nbsp;|&nbsp;
				<?php endif; ?>					
				<!--  End: Suggest to Friend link show work -->
				<a href='javascript:void(0);' onClick='en4.sitepagepoll.toggleResults(<?php echo $this->sitepagepoll->getIdentity() ?>); this.blur();'    class="sitepagepoll_toggleResultsLink">
				<?php if ($valid) echo $this->translate($this->hasVoted ? 'Show Question' : 'Show Result' ) ?>
				</a> 
        <?php $show = 0; ?>
				<?php if( empty($this->hideLinks) ): ?>
          <?php if($valid == 1):?>
				    &nbsp;|&nbsp;
          <?php endif;?>
					<?php echo $this->htmlLink(array(
					'module'=>'activity',
					'controller'=>'index',
					'action'=>'share',
					'route'=>'default',
					'type'=>'sitepagepoll_poll',
					'id' => $this->sitepagepoll->getIdentity(),
					'format' => 'smoothbox'
					), $this->translate("Share"), array('class' => 'smoothbox')); ?>
						&nbsp;|&nbsp;
					<?php echo $this->htmlLink(array(
					'module'=>'core',
					'controller'=>'report',
					'action'=>'create',
					'route'=>'default',
					'subject'=>$this->sitepagepoll->getGuid(),
					'format' => 'smoothbox'
					), $this->translate("Report"), array('class' => 'smoothbox')); ?>

             <?php if( $this->can_create == 1): ?>
             &nbsp;|&nbsp;
            <a href='<?php echo $this->url(array('page_id' => $this->page_id,'tab'=>$this->tab_selected_id), 'sitepagepoll_create', true) ?>'><?php echo $this->translate('Create Poll');?></a>
           
            <?php endif; ?>
          <?php if($this->sitepagepoll->owner_id == $this->viewer_id || $this->can_edit == 1): ?>
          &nbsp;|&nbsp;
				    <?php echo $this->htmlLink(array('route' => 'sitepagepoll_delete', 'poll_id' => $this->sitepagepoll->poll_id, 'page_id' => $this->sitepagepoll->page_id,'tab' => $this->tab_selected_id), $this->translate('Delete Poll')) ?>
			    <?php endif; ?>
          <?php $show = 1; ?>
				<?php endif; ?>
        <?php if($valid == 1 || $show == 1): ?>
					&nbsp;|&nbsp;
        <?php endif; ?>
				<span class="sitepagepoll_vote_total">
					<?php echo $this->translate(array('%s vote', '%s votes', $this->sitepagepoll->vote_count), $this->locale()->toNumber($this->sitepagepoll->vote_count)) ?>
				</span>
				&nbsp;|&nbsp;
        	<span class="sitepagepoll_vote_total">
					<?php echo $this->translate(array('%s like', '%s likes', $this->sitepagepoll->like_count), $this->locale()->toNumber($this->sitepagepoll->like_count)) ?>
				</span>
				&nbsp;|&nbsp;
				<?php echo $this->translate(array('%s view', '%s views', $this->sitepagepoll->views), $this->locale()->toNumber($this->sitepagepoll->views)) ?>
			</div>
		<?php endif; ?>
	</form>
</span>