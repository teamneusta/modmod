<?php
declare(strict_types = 1);

namespace Neusta\Modmod\Tests\Unit\Provider;

use Neusta\Modmod\Provider\FormValueProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Http\ServerRequest;

final class FormValueProviderTest extends TestCase
{
    protected string $pluginNamespace = 'modmod';

    protected function tearDown(): void
    {
        unset($GLOBALS['BE_USER'], $GLOBALS['TYPO3_REQUEST']);
    }

    #[Test]
    public function getStoredValueShouldReturnEmptyStringIfNeitherPostNorStoredValueFound(): void
    {
        $ucValueName = 'setting';
        $ucStoredValue = '';

        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withParsedBody(null);

        $beUser = $this->prepareBeUserWithConfig($ucValueName, $ucStoredValue);
        $beUser
            ->expects($this->never())
            ->method('pushModuleData');

        $formValueProvider = new FormValueProvider();

        self::assertSame('', $formValueProvider->getStoredValue($this->pluginNamespace, $ucValueName));
    }

    #[Test]
    public function getStoredValueShouldReturnUserConfigIfNoPostValueFound(): void
    {
        $ucValueName = 'setting';
        $ucStoredValue = 'ucValue';

        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withParsedBody(null);

        $beUser = $this->prepareBeUserWithConfig($ucValueName, $ucStoredValue);
        $beUser
            ->expects($this->never())
            ->method('pushModuleData');

        $formValueProvider = new FormValueProvider();

        self::assertSame($ucStoredValue, $formValueProvider->getStoredValue($this->pluginNamespace, $ucValueName));
    }

    #[Test]
    public function getStoredValueShouldReturnPostRequestValieAndStoreItInUserConfig(): void
    {
        $ucValueName = 'setting';
        $ucStoredValue = 'ucValue';
        $postRequestValue = 'postValue';

        $this->prepareServerRequestWithParsedBody($ucValueName, $postRequestValue);
        $beUser = $this->prepareBeUserWithConfig($ucValueName, $ucStoredValue);
        $beUser
            ->expects($this->once())
            ->method('pushModuleData')
            ->with($this->pluginNamespace, [$ucValueName => $postRequestValue]);

        $formValueProvider = new FormValueProvider();

        self::assertSame($postRequestValue, $formValueProvider->getStoredValue($this->pluginNamespace, $ucValueName));
    }

    #[Test]
    public function getStoredValueShouldNotStoreValueIfUserConfigAndPostRequestAreSame(): void
    {
        $ucValueName = 'setting';
        $ucStoredValue = 'sameValue';
        $postRequestValue = 'sameValue';

        $this->prepareServerRequestWithParsedBody($ucValueName, $postRequestValue);
        $beUser = $this->prepareBeUserWithConfig($ucValueName, $ucStoredValue);
        $beUser
            ->expects($this->never())
            ->method('pushModuleData');
        $formValueProvider = new FormValueProvider();

        self::assertSame('sameValue', $formValueProvider->getStoredValue($this->pluginNamespace, $ucValueName));
    }

    private function prepareServerRequestWithParsedBody(string $ucValueName, string $postRequestValue): void
    {
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withParsedBody([$ucValueName => $postRequestValue]);
    }

    private function prepareBeUserWithConfig(string $key, string $readValue): MockObject|BackendUserAuthentication
    {
        $beUser = $this->createMock(BackendUserAuthentication::class);
        $GLOBALS['BE_USER'] = $beUser;
        $GLOBALS['BE_USER']->uc['moduleData'][$this->pluginNamespace][$key] = $readValue;

        return $beUser;
    }
}
