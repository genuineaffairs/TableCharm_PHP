<div class="document_view">
    <div class="document_title">
        <?php echo htmlspecialchars($this->document->getTitle()) ?>
    </div>

    <div class="addthis_toolbox addthis_default_style">
        <a class="addthis_button_facebook"></a>
        <a class="addthis_button_twitter"></a>
        <a class="addthis_button_email"></a>
        <a class="addthis_button_print"></a>
        <a class="addthis_button_google"></a>
        <a class="addthis_button_compact"></a>
        <a class="addthis_counter addthis_bubble_style"></a>
    </div>
    <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4f33860960a3e172"></script>

    <div class="document_clear"></div>

    <div class="document_description">
        <?php echo $this->document->getDescription() ?>
    </div>

    <div class="document_owner">
        <?php echo $this->translate('Posted by') ?>
        <?php
            $poster = $this->document->getOwner();
            if ($poster) {
                echo $this->htmlLink($poster, $poster->getTitle());
            }
        ?>
    </div>

    <?php if (Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
    <ul>
        <?php $document_file_path = $this->document->getFilePath() ?>
        <?php if ($document_file_path): ?>
        <li>
            <?php echo $this->htmlLink($document_file_path,  $this->translate('Download Document'), array('target'=>'_blank')) ?>
        </li>
        <?php endif; ?>

        <?php if ($this->can_edit): ?>
        <li>
            <?php
            echo $this->htmlLink(array(
                'route' => 'document_general',
                'action' => 'edit',
                'document_id' => $this->document->document_id
                    ), $this->translate('Edit Document'));
            ?>
        </li>
        <?php endif; ?>

        <?php if ($this->can_delete): ?>
        <li>
            <?php
            echo $this->htmlLink(array(
                    'route' => 'document_general',
                    'action' => 'delete',
                    'document_id' => $this->document->document_id,
                    'format' => 'smoothbox'
                ),
                $this->translate('Delete Document'),
                array(
                    'class' => 'smoothbox'
                ));
            ?>
        </li>
        <?php endif; ?>
    </ul>
    <?php endif ?>
</div>