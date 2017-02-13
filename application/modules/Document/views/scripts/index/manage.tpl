<div class='layout_middle'>
  <?php if( count($this->paginator) > 0 ): ?>

    <ul class='documents_browse'>
      <?php foreach( $this->paginator as $document ): ?>
        <li>
          <div class="document_options">
            <?php if( $document->isOwner($this->viewer()) ): ?>

              <?php echo $this->htmlLink(
                array(
                  'route' => 'document_general',
                  'action' => 'edit',
                  'document_id' => $document->getIdentity()
                ),
                $this->translate('Edit Document'),
                array(
                  'class' => 'buttonlink icon_document_edit'
                )) ?>

              <?php echo $this->htmlLink(
                array(
                  'route' => 'document_general',
                  'action' => 'delete',
                  'document_id' => $document->getIdentity(),
                  'format' => 'smoothbox'),
                $this->translate('Delete Document'),
                array(
                  'class' => 'buttonlink smoothbox icon_document_delete'
                )) ?>

            <?php endif; ?>
          </div>

          <div class="document_info">

            <div class="document_title">
              <?php echo $this->htmlLink(
                $document->getHref(),
                Engine_Api::_()->document()->subPhrase($document->getTitle(), 150)
              ) ?>
            </div>

            <div class="document_description">
              <?php echo Engine_Api::_()->document()->subPhrase($document->getDescription(), 255) ?>
            </div>

          </div>
        </li>
      <?php endforeach; ?>
    </ul>

    <?php if( count($this->paginator) > 1 ): ?>
      <?php echo $this->paginationControl($this->paginator, null, null, array(
          'pageAsQuery' => true
        )); ?>
    <?php endif; ?>

  <?php else: ?>
    <div class="tip">
      <span>
      <?php echo $this->translate('You have not uploaded any documents yet.') ?>
      </span>
    </div>
  <?php endif; ?>
</div>