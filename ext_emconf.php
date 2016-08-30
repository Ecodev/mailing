<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Mailing list',
    'description' => 'Authenticated FE users can send bunch of messages to a list of recipients. This list is defined as a dynamic selection in a FE module (Vidi).',
    'category' => 'plugin',
    'author' => 'Fabien Udriot',
    'author_email' => 'fabien@ecodev.ch',
    'module' => '',
    'state' => 'beta',
    'version' => '0.1.0',
    'autoload' => [
        'psr-4' => ['Fab\\Mailing\\' => 'Classes']
    ],
    'constraints' =>
        [
            'depends' =>
                [
                    'typo3' => '7.6.0-7.99.99',
                    'vidi' => '2-0.0-0.0.0',
                    'messenger' => '0.9.0-0.0.0',
                ],
            'conflicts' =>
                [
                ],
            'suggests' =>
                [
                ],
        ]
];