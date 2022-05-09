<?php
declare(strict_types = 1);

namespace Neusta\Modmod\Utility;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

final class BackendUserUtility
{

    /**
     * @param string $pluginName
     * @param string $key
     *
     * @return mixed|null
     */
    public static function getValueFromBackendUserConfig(string $pluginName, string $key)
    {
        $pluginUserConfig = self::getBackendUser()->uc[$pluginName] ?? [];

        return $pluginUserConfig[$key] ?? null;
    }

    /**
     * @param string $pluginName
     * @param mixed  $value
     */
    public static function storeUcValue(string $pluginName, $value): void
    {
        self::getBackendUser()->writeUC([$pluginName => $value]);
    }

    public static function getShowPageIdWithTitleOption(): bool
    {
        $tsConfig = self::getBackendUser()->getTSConfig();

        return (bool)($tsConfig['options.']['pageTree.']['showPageIdWithTitle'] ?? false);
    }

    private static function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
