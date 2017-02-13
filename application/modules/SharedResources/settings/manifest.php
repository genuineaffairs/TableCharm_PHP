<?php

return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'shared-resources',
        'version' => '4.0.0',
        'path' => 'application/modules/SharedResources',
        'title' => 'Shared Resources Management',
        'description' => 'Module to manage share resources',
        'author' => 'Tristan',
        'callback' =>
        array(
            'path' => 'application/modules/SharedResources/settings/install.php',
            'class' => 'SharedResources_Installer'
        ),
        'actions' =>
        array(
            0 => 'install',
            1 => 'upgrade',
            2 => 'refresh',
            3 => 'enable',
            4 => 'disable',
        ),
        'directories' =>
        array(
            0 => 'application/modules/SharedResources',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/shared-resources.csv',
        ),
    ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onUserCreateAfter',
            'resource' => 'SharedResources_Plugin_Core',
        ),
        array(
            'event' => 'onUserLoginAfter',
            'resource' => 'SharedResources_Plugin_Core',
        ),
        array(
            'event' => 'onUserDeleteAfter',
            'resource' => 'SharedResources_Plugin_Core',
        ),
    ),
);
?>