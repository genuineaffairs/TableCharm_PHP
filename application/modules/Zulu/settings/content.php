<?php

return array(
    array(
        'title' => 'Zulu Profile Fields',
        'description' => 'Displays a member\'s profile field data on their profile.',
        'category' => 'Zulu',
        'type' => 'widget',
        'name' => 'zulu.profile-fields',
        'defaultParams' => array(
            'title' => 'Info',
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
    array(
        'title' => 'Zulu Clinical Fields',
        'description' => 'Displays a member\'s clinical field data on their profile.',
        'category' => 'Zulu',
        'type' => 'widget',
        'name' => 'zulu.clinical-fields',
        'defaultParams' => array(
            'title' => 'Info',
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
);
