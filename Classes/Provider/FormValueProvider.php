<?php
declare(strict_types = 1);

namespace Neusta\Modmod\Provider;

use Neusta\Modmod\Utility\BackendUserUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ServerRequestFactory;

final class FormValueProvider implements FormValueProviderInterface
{
    /**
     *
     * @return mixed
     */
    public function getStoredValue(string $pluginName, string $name)
    {
        $ucValue = BackendUserUtility::getValueFromBackendUserConfig($pluginName, $name) ?? '';
        $value = $this->getValueFromPostRequest($name, $ucValue);
        if ($value !== $ucValue) {
            BackendUserUtility::storeUcValue($pluginName, [$name => $value]);
        }

        return $value;
    }

    /**
     * @param mixed  $default
     * @return mixed
     */
    private function getValueFromPostRequest(string $formFieldName, $default = '')
    {
        $postData = (array)$this->getRequest()->getParsedBody();

        return $postData[$formFieldName] ?? $default;
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'] ?? ServerRequestFactory::fromGlobals();
    }
}
