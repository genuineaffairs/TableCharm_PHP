<?php

return array(
    'grid' => array(
        'label' => 'Grid View',
        'category' => 'generic',
        'helper' => 'fieldGrid',
        'multi' => false,
        'dependents' => true
    ),
    'file' => array(
        'label' => 'File',
        'category' => 'generic',
        'helper' => 'fieldFile',
        'multi' => false,
        'dependents' => false
    ),
    'fileMulti' => array(
        'label' => 'Multiple File Upload',
        'category' => 'generic',
        'helper' => 'fieldFileMulti',
        'multi' => false,
        'dependents' => false
    ),
    'subheading' => array(
      'label' => 'Sub Heading',
      'category' => 'generic',
      'multi' => false,
      'dependents' => false,
    ),
    'note' => array(
      'label' => 'Note',
      'category' => 'generic',
      'helper' => 'fieldNote',
      'multi' => false,
      'dependents' => false,
    ),
);
