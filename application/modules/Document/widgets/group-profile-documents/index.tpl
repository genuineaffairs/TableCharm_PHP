<script type="text/javascript">
    en4.core.runonce.add(function(){
        <?php if (!$this->renderOne): ?>
            var anchor = $('group_documents').getParent();
            $('documents_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
            $('documents_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

            // set up the previous and next button click handlers
            // note: each handler will post to this widget's controller, returning this very view!

            $('documents_previous').removeEvents('click').addEvent('click', function() {
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

            $('documents_next').removeEvents('click').addEvent('click', function() {
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

<ul class="group_documents" id="group_documents">
    <?php foreach ($this->paginator as $document): ?>
        <li>
            <div class="document_title">
                <?php echo "<a href='" . $document->getHref() . "' class='info'>" . $document->title . "</a>" ?>
            </div>
            <div class="document_description">
                <?php echo $document->description ?>
            </div>
            <div class="document_owner">
                <?php echo $this->translate('Posted by') ?>
                <?php
                    $owner = $document->getOwner();
                    if ($owner) {
                        echo $this->htmlLink($owner, $owner->getTitle());
                    }
                ?>
            </div>
        </li>
    <?php endforeach; ?>
</ul>

<div>
    <div id="documents_previous" class="paginator_previous">
        <?php
        echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
            'onclick' => '',
            'class' => 'buttonlink icon_previous'
        ));
        ?>
    </div>
    <div id="documents_next" class="paginator_next">
        <?php
        echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
            'onclick' => '',
            'class' => 'buttonlink_right icon_next'
        ));
        ?>
    </div>
    <div class="clear"></div>
</div>