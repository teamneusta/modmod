<?php
$EM_CONF[$_EXTKEY] = [
    'title'            => 'modmod',
    'description'      => 'Add-on to pw_comments to moderate comments across pages. This extension provides a dashboard widget and a web module.',
    'category'         => 'extension',
    'constraints'      => [
        'depends'   => [
            'typo3' => '10.4.0-11.99.99',
        ],
        'conflicts' => [
        ],
    ],
    'autoload'         => [
        'psr-4' => [
            'Neusta\\Modmod\\' => 'Classes',
        ],
    ],
    'autoload-dev'     => [
        'psr-4' => [
            'Neusta\\Modmod\\Tests\\' => 'Tests',
        ],
    ],
    'state'            => 'stable',
    'author'           => 'Tobias Kretschmann',
    'author_email'     => 't.kretschmann@neusta.de',
    'author_company'   => 'team neusta GmbH',
    'version'          => '2.0.1',
];
