<?php
return array(
  array(
      'title' => 'Document Browse Menu',
      'description' => 'Displays a menu in the document browse page.',
      'category' => 'Documents',
      'type' => 'widget',
      'name' => 'document.browse-menu',
      'requirements' => array(
          'no-subject',
      ),
  ),
  array(
    'title' => 'Group Profile Documents',
    'description' => 'Displays a group\'s profile documents.',
    'category' => 'Documents',
    'type' => 'widget',
    'name' => 'document.group-profile-documents',
    'requirements' => array(
      'subject' => 'group',
    ),
    'defaultParams' => array(
        'title' => 'Documents',
        'titleCount' => true,
    ),
  ),
) ?>
