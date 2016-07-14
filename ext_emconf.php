<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Mailing list',
    'description' => 'Define and assign list of recipients in the plugin settings. Users can send bunch of messages to them on the FE.',
    'category' => 'plugin',
    'author' => 'Fabien Udriot',
    'author_email' => 'fabien@ecodev',
    'module' => '',
    'state' => 'beta',
    'version' => '0.1.0',
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
