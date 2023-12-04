<?php
declare(strict_types=1);

use Neusta\Modmod\Controller\BackendModerateCommentsController;

return [
    'web_modmod' => [
        'parent'                => 'web',
        'extensionName'         => 'modmod',
        'position'              => ['after' => 'info'],
        'controllerActions'     => [
            BackendModerateCommentsController::class => ['index', 'toggleVisibility', 'delete'],
        ],
        'access'                => 'user',
        'icon'                  => 'EXT:modmod/Resources/Public/Icons/module-modmod.svg',
        'labels'                => 'LLL:EXT:modmod/Resources/Private/Language/locallang.xlf',
        'navigationComponentId' => 'TYPO3/CMS/Backend/PageTree/PageTreeElement',
    ]
];
