<?php
declare(strict_types = 1);

namespace Neusta\Modmod\Tests\Unit\Provider;

use Neusta\Modmod\Provider\FormValueProvider;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class FormValueProviderTest extends UnitTestCase
{
    use ProphecyTrait;

    protected string $pluginNamespace = 'modmod';

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($GLOBALS['BE_USER'], $GLOBALS['TYPO3_REQUEST']);
    }

    /**
     * @test
     */
    public function getStoredValueShouldReturnEmptyStringIfNeitherPostNorStoredValueFound(): void
    {
        $ucValueName = 'setting';
        $ucStoredValue = '';

        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withParsedBody(null);

        $beUser = $this->prepareBeUserWithConfig($ucValueName, $ucStoredValue);
        $beUser->writeUC(Argument::any())->shouldNotBeCalled();

        $formValueProvider = new FormValueProvider();

        self::assertSame('', $formValueProvider->getStoredValue($this->pluginNamespace, $ucValueName));
    }

    /**
     * @test
     */
    public function getStoredValueShouldReturnUserConfigIfNoPostValueFound(): void
    {
        $ucValueName = 'setting';
        $ucStoredValue = 'ucValue';

        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withParsedBody(null);

        $beUser = $this->prepareBeUserWithConfig($ucValueName, $ucStoredValue);
        $beUser->writeUC(Argument::any())->shouldNotBeCalled();

        $formValueProvider = new FormValueProvider();

        self::assertSame($ucStoredValue, $formValueProvider->getStoredValue($this->pluginNamespace, $ucValueName));
    }

    /**
     * @test
     */
    public function getStoredValueShouldReturnPostRequestValieAndStoreItInUserConfig(): void
    {
        $ucValueName = 'setting';
        $ucStoredValue = 'ucValue';
        $postRequestValue = 'postValue';

        $this->prepareServerRequestWithParsedBody($ucValueName, $postRequestValue);
        $beUser = $this->prepareBeUserWithConfig($ucValueName, $ucStoredValue);
        $beUser->writeUC([$this->pluginNamespace => [$ucValueName => $postRequestValue]])->shouldBeCalled();

        $formValueProvider = new FormValueProvider();

        self::assertSame($postRequestValue, $formValueProvider->getStoredValue($this->pluginNamespace, $ucValueName));
    }

    /**
     * @test
     */
    public function getStoredValueShouldNotStoreValueIfUserConfigAndPostRequestAreSame(): void
    {
        $ucValueName = 'setting';
        $ucStoredValue = 'sameValue';
        $postRequestValue = 'sameValue';

        $this->prepareServerRequestWithParsedBody($ucValueName, $postRequestValue);
        $beUser = $this->prepareBeUserWithConfig($ucValueName, $ucStoredValue);
        $beUser->writeUC(Argument::any())->shouldNotBeCalled();
        $formValueProvider = new FormValueProvider();

        self::assertSame('sameValue', $formValueProvider->getStoredValue($this->pluginNamespace, $ucValueName));
    }

    private function prepareServerRequestWithParsedBody(string $ucValueName, string $postRequestValue): void
    {
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withParsedBody([$ucValueName => $postRequestValue]);
    }

    private function prepareBeUserWithConfig(string $key, string $readValue)
    {
        $beUser = $this->prophesize(BackendUserAuthentication::class);
        $GLOBALS['BE_USER'] = $beUser->reveal();
        $GLOBALS['BE_USER']->uc[$this->pluginNamespace][$key] = $readValue;

        return $beUser;
    }
}
