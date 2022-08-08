<?php

use Neusta\Modmod\Controller\BackendModerateCommentsController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

if (!defined('TYPO3')) {
    die('Access denied.');
}

(static function (string $extKey): void {
    ExtensionUtility::registerModule(
        $extKey,
        'web',
        'Moderate',
        'after:info',
        [
            BackendModerateCommentsController::class => 'index, toggleVisibility, delete',
        ],
        [
            'access'                                   => 'user',
            'icon'                                     => 'EXT:' . $extKey . '/Resources/Public/Icons/module-modmod.svg',
            'labels'                                   => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang.xlf',
            'navigationComponentId'                    => 'TYPO3/CMS/Backend/PageTree/PageTreeElement',
            'inheritNavigationComponentFromMainModule' => false,
        ],
    );
})('modmod');
