<?php
// Create the cloud and assign static tags to it
$cloud = new Zend_Tag_Cloud(array(
    'tags' => array(
        array('title' => 'Code', 'weight' => 50,
            'params' => array('url' => '/tag/code')),
        array('title' => 'Zend Framework', 'weight' => 1,
            'params' => array('url' => '/tag/zend-framework')),
        array('title' => 'PHP', 'weight' => 5,
            'params' => array('url' => '/tag/php')),
    )
));

// Render the cloud
echo $cloud;
?>