<?php
return array(
  array(
    'title' => 'Marketing Carousel',
    'description' => 'Displays a marketing carousel.',
    'category' => 'Mgsl',
    'type' => 'widget',
    'name' => 'mgsl.carousel',
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  array(
    'title' => 'Group Profile Videos',
    'description' => 'Displays a group\'s profile videos.',
    'category' => 'Mgsl',
    'type' => 'widget',
    'name' => 'mgsl.group-profile-videos',
    'requirements' => array(
      'subject' => 'group',
    ),
    'defaultParams' => array(
        'title' => 'Videos',
        'titleCount' => true,
    ),
  ),
  array(
      'title' => 'Signup Form',
      'description' => 'Signup Form.',
      'category' => 'Mgsl',
      'type' => 'widget',
      'name' => 'mgsl.signup',
      'requirements' => array(
        'header-footer',
      ),
  ),
  array(
      'title' => 'Mini Menu',
      'description' => 'Shows the site-wide mini menu. You can edit its contents in your menu editor.',
      'category' => 'Mgsl',
      'type' => 'widget',
      'name' => 'mgsl.menu-mini',
      'requirements' => array(
        'no-subject',
      ),
    ),
  array(
      'title' => 'Home User',
      'description' => 'Home user widget.',
      'category' => 'Mgsl',
      'type' => 'widget',
      'name' => 'mgsl.home-user',
      'adminForm' => 'Mgsl_Form_Admin_Widget_HomeUser',
      'requirements' => array(
        'no-subject',
      ),
    ),
  array(
      'title' => 'User Profile Summary',
      'description' => 'User profile summary.',
      'category' => 'Mgsl',
      'type' => 'widget',
      'name' => 'mgsl.user-profile-summary',
      'requirements' => array(
        'subject' => 'user',
      ),
    ),
  array(
      'title' => 'User Circles',
      'description' => 'User circles list.',
      'category' => 'Mgsl',
      'type' => 'widget',
      'name' => 'mgsl.user-circles',
      'adminForm' => 'Mgsl_Form_Admin_Widget_UserCircles',
      'requirements' => array(
        'subject' => 'user',
      ),
    ),
  array(
      'title' => 'Home Links',
      'description' => 'Home user links.',
      'category' => 'Mgsl',
      'type' => 'widget',
      'name' => 'mgsl.home-links',
      'adminForm' => 'Mgsl_Form_Admin_Widget_HomeLinks',
      'requirements' => array(
        'no-subject',
      ),
    ),
  array(
      'title' => 'Circle Summary',
      'description' => 'Circle summary.',
      'category' => 'Mgsl',
      'type' => 'widget',
      'name' => 'mgsl.circle-summary',
      'requirements' => array(
        'subject' => 'group',
      ),
    ),
  array(
      'title' => 'Circles Grid',
      'description' => 'Circles grid.',
      'category' => 'Mgsl',
      'type' => 'widget',
      'name' => 'mgsl.circles-grid',
      'requirements' => array(
        'subject' => 'user',
      ),
    ),
  )
?>
