<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'komm.ONE jobs',
    'description' => 'Use komm.ONE jobs API to show jobs',
    'category' => 'plugin',
    'author' => 'Stefan Froemken',
    'author_email' => 'sfroemken@jweiland.net',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '1.1.1',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
