<div class='layout_middle'>
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

    <ul class="documents_browse">
      <?php foreach( $this->paginator as $document ): ?>
        <li>
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

            <div class="document_owner">
              <?php echo $this->translate('Posted by') ?>
              <?php
                  $poster = $document->getOwner();
                  if ($poster) {
                      echo $this->htmlLink($poster, $poster->getTitle());
                  }
              ?>
            </div>

          </div>
        </li>
      <?php endforeach; ?>
    </ul>

    <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
      )); ?>

  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('Nobody has uploaded a document yet.');?>
      </span>
    </div>
  <?php endif; ?>
</div>