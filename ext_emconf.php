<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Mailing list',
    'description' => 'Authenticated FE users can send bunch of messages to a list of recipients. This list is defined as a dynamic selection in a FE module (Vidi).',
    'category' => 'plugin',
    'author' => 'Fabien Udriot',
    'author_email' => 'fabien@ecodev.ch',
    'module' => '',
    'state' => 'beta',
    'version' => '0.4.0-dev',
    'autoload' => [
        'psr-4' => ['Fab\\Mailing\\' => 'Classes']
    ],
    'constraints' =>
        [
            'depends' =>
                [
                    'typo3' => '9.5.0-9.5.99',
                    'vidi' => '4-0.0-0.0.0',
                    'messenger' => '2.0.0-0.0.0',
                ],
            'conflicts' =>
                [
                ],
            'suggests' =>
                [
                ],
        ]
];
