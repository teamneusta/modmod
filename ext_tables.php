<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}

(static function (string $extKey): void {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        $extKey,
        'web',
        'Moderate',
        'after:info',
        [
            \Neusta\Modmod\Controller\BackendModerateCommentsController::class => 'index, toggleVisibility, delete',
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
