<?php
$this->headScript()
->appendFile($this->baseUrl() . '/application/modules/Advgroup/externals/scripts/advgroup_function.js')
->appendFile($this->baseUrl() . '/application/modules/Advgroup/externals/scripts/slideshow/Navigation.js')
->appendFile($this->baseUrl() . '/application/modules/Advgroup/externals/scripts/slideshow/Loop.js')
->appendFile($this->baseUrl() . '/application/modules/Advgroup/externals/scripts/slideshow/SlideShow.js');
?>

<?php if( count($this->groups) >  0 ): ?>
<section id="advgroup_navigation" class="demo">
	<div id="advgroup_navigation-slideshow" class="slideshowgroup">
		<?php
		$i = 0;
		foreach ($this->groups as $item):
		$owner = $item->getOwner();
		if($i < $this->limit):
		$i ++;
		?>
		<span id="lp<?php echo $i?>">


			<div class="featured_groups">
				<div class="featured_groups_img_wrapper">
					<div class="featured_groups_img">
						<a href="<?php echo $item->getHref()?>"> <?php if($item->getPhotoUrl("thumb.feature")!= null):?>
							<img src="<?php echo $item->getPhotoUrl("thumb.feature");?>" /> <?php else:?>
							<img
							src="./application/modules/Advgroup/externals/images/nophoto_group_thumb_feature.png" />
							<?php endif;?>
						</a>
					</div>
				</div>
				<div class="group_info">
					<div class="group_title" style="font-size: 15px; color: #3BA3D0">
						<b><?php echo $item ?> </b>
					</div>
					<div class="group_owner" style="font-size: 11px; color: #7E7E7E;">
						<?php echo $this->translate("led by ");?>

						<?php echo $this->htmlLink($owner->getHref(),$owner->getTitle());?>
						-
						<?php echo $this->translate('%d member(s)',$item->member_count);?>
					</div>
					<p class="group_description"
						style="margin-top: 6px; word-wrap: break-word; text-align: justify;">
						<?php if(strlen(strip_tags($item->description))>450) echo Engine_Api::_()->advgroup()->subPhrase(strip_tags($item->description),450);
                                               else echo strip_tags($item->description);?>
					</p>
					<p class="advgroup_viewmore" style="margin-top: 6px;">
						<?php echo $this->htmlLink($item->getHref(),
     						$this->translate("View more"), array('class'=>'group_viewmore')); ?>
					</p>

				</div>
			</div>

		</span>

		<?php endif;  endforeach; ?>
		<ul class="advgroup_pagination" id="advgroup_pagination">
			<li><a class="current" href="#lp1"></a></li>
			<?php for ($j = 2; $j <= $i; $j ++):?>
			<li><a href="#lp<?php echo $j?>"></a></li>
			<?php endfor;?>
		</ul>
	</div>
</section>



<?php else: ?>
<div class="tip">
	<span> <?php echo $this->translate('There is no featured group yet.');?>
	</span>
</div>
<?php endif;?>