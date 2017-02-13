<script type="text/javascript">
        en4.core.runonce.add(function(){
            addEventForButtonAddTo();
            <?php if (!$this->renderOne): ?>
                var anchor = $('ynvideo_recent_videos').getParent();
                $('ynvideo_videos_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
                $('ynvideo_videos_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

                $('ynvideo_videos_previous').removeEvents('click').addEvent('click', function(){
                    en4.core.request.send(new Request.HTML({
                        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                        data : {
                            format : 'html',
                            subject : en4.core.subject.guid,
                            page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
                        }
                    }), {
                        'element' : anchor
                    })
                });

                $('ynvideo_videos_next').removeEvents('click').addEvent('click', function(){
                    en4.core.request.send(new Request.HTML({
                        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                        data : {
                            format : 'html',
                            subject : en4.core.subject.guid,
                            page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
                        }
                    }), {
                        'element' : anchor
                    })
                });
            <?php endif; ?>
        });
    </script>

    <ul class="generic_list_widget ynvideo_widget videos_browse ynvideo_frame ynvideo_list" id="ynvideo_recent_videos" style="padding-bottom:0px;">
        <?php foreach ($this->paginator as $item): ?>
            <?php
            if (!$item->authorization()->isAllowed( $this->viewer, 'view' ))
            {
                continue;
            }
            ?>
            <li <?php echo isset($this->marginLeft)?'style="margin-left:' . $this->marginLeft . 'px"':''?>>
                <?php
                echo $this->partial('_video_listing.tpl', 'ynvideo', array(
                    'video' => $item,
                    'recentCol' => $this->recentCol
                ));
                ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <div>
        <div id="ynvideo_videos_previous" class="paginator_previous">
            <?php
            echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                'onclick' => '',
                'class' => 'buttonlink icon_previous'
            ));
            ?>
        </div>
        <div id="ynvideo_videos_next" class="paginator_next">
            <?php
            echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                'onclick' => '',
                'class' => 'buttonlink_right icon_next'
            ));
            ?>
        </div>
        <div class="clear"></div>
    </div>