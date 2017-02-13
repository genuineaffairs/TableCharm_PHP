<?php return array (
  'package' => 
  array (
    'type' => 'widget',
    'name' => 'people-you-may-know',
    'version' => '4.0.1',
    'path' => 'application/widgets/people-you-may-know',
    'meta' => 
    array (
      'title' => 'People You May Know',
      'description' => 'Displays a list of Friends of your Friends',
      'author' => 'SocialEngineMarket',
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'remove',
    ),
    'directories' => 
    array (
      0 => 'application/widgets/people-you-may-know',
    ),
  ),
  'type' => 'widget',
  'name' => 'people-you-may-know',
  'version' => '4.0.1',
  'title' => 'People You May Know',
  'description' => 'Displays a list of Friends of your Friends',
  'category' => 'Widgets',
  'adminForm' => array(
    'elements' => array(
      array(
        'Text',
        'title',
        array(
          'label' => 'Title'
        )
      ),
      array(
        'Text',
        'itemPerPage',
        array(
          'label' => 'Count (number of users to show)'
        )
      ),
    ),
  ),

); ?>
