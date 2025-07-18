<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'komm.ONE jobs',
    'description' => 'Use komm.ONE jobs API to show jobs',
    'category' => 'plugin',
    'author' => 'Stefan Froemken',
    'author_email' => 'sfroemken@jweiland.net',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.31-13.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
