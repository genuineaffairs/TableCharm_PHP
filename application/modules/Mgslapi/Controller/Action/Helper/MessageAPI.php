<?php

class Mgslapi_Controller_Action_Helper_MessageAPI extends Zend_Controller_Action_Helper_Abstract {

  public function getAllMessagesPaginator() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $queryStr = $this->getRequest()->getParam('query');

    $table = Engine_Api::_()->getDbtable('conversations', 'messages');
    $query = $table->select()
            ->setIntegrityCheck(false)
            ->from('engine4_messages_conversations')
            ->joinRight('engine4_messages_recipients', 'engine4_messages_recipients.conversation_id = engine4_messages_conversations.conversation_id')
            ->joinRight('engine4_messages_messages', 'engine4_messages_messages.conversation_id=engine4_messages_recipients.conversation_id')
            ->where('engine4_messages_recipients.user_id = ?', $viewer->user_id)
            ->where('(engine4_messages_messages.title LIKE ? || engine4_messages_messages.body LIKE ?)', '%' . $queryStr . '%')
            ->where('engine4_messages_recipients.inbox_deleted != 1 OR engine4_messages_recipients.outbox_deleted != 1')
//            ->where('engine4_messages_recipients.outbox_deleted != 1')
            ->order('inbox_updated DESC')
            ->group('engine4_messages_conversations.conversation_id')
    ;

    // Get messages from source
    $source_guid = $this->getRequest()->getParam('source');
    if ($source_guid) {
      $underscoreLastPos = strrpos($source_guid, '_');

      if ($underscoreLastPos !== false) {
        $source_type = substr($source_guid, 0, $underscoreLastPos);
        $source_id = substr($source_guid, $underscoreLastPos + 1);

        if (!is_numeric($source_id)) {
          $source_type = $source_type . '_' . $source_id;
        }
      } else {
        $source_type = $source_guid;
      }

      $query->where('engine4_messages_conversations.source_type = ?', $source_type);

      if ($source_id && is_numeric($source_id)) {
        $query->where('engine4_messages_conversations.source_id = ?', $source_id);
      }
    }

    $paginatorAdapter = new Zend_Paginator_Adapter_DbTableSelect($query);
    $paginator = new Zend_Paginator($paginatorAdapter);

    return $paginator;
  }

}
