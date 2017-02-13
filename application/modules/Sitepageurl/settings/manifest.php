<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'sitepageurl',
    'version' => '4.7.1p1',
    'path' => 'application/modules/Sitepageurl',
    'title' => 'Directory / Pages - Short Page URL Extension',
    'description' => 'Directory / Pages - Short Page URL Extension',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
     'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Sitepageurl/settings/install.php',
            'class' => 'Sitepageurl_Installer'
        ),
        'directories' => array(
            'application/modules/Sitepageurl'
        ),
        'files' => array(
            'application/languages/en/sitepageurl.csv'
        )
    )
); ?>