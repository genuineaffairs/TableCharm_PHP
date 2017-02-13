<?php

class Messages_Form_Search extends Engine_Form {

  public function init() {
    $this
            ->setAttrib('style', 'float: right;')
            ->setAction('messages/search')
            ->setMethod('GET')
            ->setName('message_search')
    ;

    // init source
    $this->addElement('Select', 'source', array(
        'multiOptions' => $this->_getSourceList(),
    ));
    $this->getElement('source')->removeDecorator('Label');

    // init keywords
    $this->addElement('Text', 'query', array(
        'placeholder' => 'Keywords',
    ));
    $this->getElement('query')->removeDecorator('Label');

    // init submit
    $this->addElement('Button', 'submit', array(
        'label' => 'Search',
        'type' => 'submit',
        'ignore' => true
    ));
    $this
            ->getElement('submit')->removeDecorator('DivDivDivWrapper')
            ->addDecorator('HtmlTag2', array('tag' => 'div', 'id' => 'submit-wrapper', 'class' => 'form-wrapper'))
            ->addDecorator('HtmlTag', array('tag' => 'div', 'id' => 'submit-element', 'class' => 'form-element'));
    ;
  }

  protected function _getSourceList() {
    $viewer = Engine_Api::_()->user()->getViewer();

    $table = Engine_Api::_()->getDbtable('conversations', 'messages');
    $query = $table->select()
            ->setIntegrityCheck(false)
            ->from('engine4_messages_conversations', array('source_type', 'source_id'))
            ->joinRight('engine4_messages_recipients', 'engine4_messages_recipients.conversation_id = engine4_messages_conversations.conversation_id')
            ->group(array('source_type', 'source_id'))
            ->where('engine4_messages_recipients.user_id = ?', $viewer->user_id)
            ->where('engine4_messages_recipients.inbox_deleted != 1 OR engine4_messages_recipients.outbox_deleted != 1')
    ;
    $results = $query->query()->fetchAll();

    $list = array('All Inboxes', 1 => 'Personal Inbox', 'resume' => 'CV Profiler', 'zulu' => 'Medical Record');
    $group_type = array('sitepage_page');

    foreach ($results as $result) {
      if ($result['source_type'] && in_array($result['source_type'], $group_type)) {
        // get type translated name
        $type_text = Engine_Api::_()->messages()->getItemTypeText($result['source_type']);
        $item = Engine_Api::_()->getItem($result['source_type'], $result['source_id']);
        $group_name = $type_text . ':';

        // If it is the first member of a type
//        if (!array_key_exists($group_name, $list)) {
//          $list[$group_name] = array(
//              $result['source_type'] => '- All ' . $type_text
//          );
//        }
        // Add more member if have ?
        if ($item) {
          $list[$group_name][$result['source_type'] . '_' . $result['source_id']] = '- ' . $item->getTitle();
        }
      }
    }
    return $list;
  }

}
