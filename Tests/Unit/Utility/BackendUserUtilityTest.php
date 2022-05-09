<?php
declare(strict_types = 1);

namespace Neusta\Modmod\Tests\Unit\Utility;

use Neusta\Modmod\Utility\BackendUserUtility;
use Prophecy\PhpUnit\ProphecyTrait;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class BackendUserUtilityTest extends UnitTestCase
{
    use ProphecyTrait;

    protected string $pluginNamespace = 'modmod';

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($GLOBALS['BE_USER']);
    }

    /**
     * @test
     */
    public function getValueFromBackendUserConfigShouldReturnValueForGivenKeyInGivenPluginNamespace(): void
    {
        $GLOBALS['BE_USER'] = new BackendUserAuthentication();
        $GLOBALS['BE_USER']->uc[$this->pluginNamespace]['foo'] = 'bar';

        self::assertSame('bar', BackendUserUtility::getValueFromBackendUserConfig($this->pluginNamespace, 'foo'));
    }

    /**
     * @test
     */
    public function storeUcValueShouldWriteGivenValueInUserConfigWithGivenPluginNamespace(): void
    {
        $valueToStore = ['user' => 'setting'];

        $beUser = $this->prophesize(BackendUserAuthentication::class);
        $beUser->writeUC([$this->pluginNamespace => $valueToStore])->shouldBeCalled();
        $GLOBALS['BE_USER'] = $beUser->reveal();

        BackendUserUtility::storeUcValue($this->pluginNamespace, $valueToStore);
    }
}
