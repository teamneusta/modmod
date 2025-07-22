<?php
declare(strict_types = 1);

namespace Neusta\Modmod\Tests\Unit\Utility;

use Generator;
use Neusta\Modmod\Utility\BackendUserUtility;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

final class BackendUserUtilityTest extends TestCase
{
    protected string $pluginNamespace = 'modmod';

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($GLOBALS['BE_USER']);
    }

    #[Test]
    public function getValueFromBackendUserConfigShouldReturnValueForGivenKeyInGivenPluginNamespace(): void
    {
        $GLOBALS['BE_USER'] = new BackendUserAuthentication();
        $GLOBALS['BE_USER']->uc['moduleData'][$this->pluginNamespace]['foo'] = 'bar';

        self::assertSame('bar', BackendUserUtility::getValueFromBackendUserConfig($this->pluginNamespace, 'foo'));
    }

    #[Test]
    public function storeUcValueShouldWriteGivenValueInUserConfigWithGivenPluginNamespace(): void
    {
        $valueToStore = ['user' => 'setting'];

        $beUser = $this->createMock(BackendUserAuthentication::class);
        $beUser
            ->expects($this->once())
            ->method('pushModuleData')
            ->with($this->pluginNamespace, $valueToStore);
        $GLOBALS['BE_USER'] = $beUser;

        BackendUserUtility::storeUcValue($this->pluginNamespace, $valueToStore);
    }
    
    #[Test]
    #[DataProvider('getShowPageIdWithTitleOptionDataProvider')]
    public function getShowPageIdWithTitleOptionShouldReturnIfShowPageIdWithTitleOptionIsSetInTsConfig(array $tsConfig, bool $expectedShowPageIdWithTitleOption): void
    {
        $beUser = $this->createMock(BackendUserAuthentication::class);
        $beUser
            ->expects($this->once())
            ->method('getTSConfig')
            ->willReturn($tsConfig);
        $GLOBALS['BE_USER'] = $beUser;

        self::assertSame($expectedShowPageIdWithTitleOption, BackendUserUtility::getShowPageIdWithTitleOption());
    }

    public static function getShowPageIdWithTitleOptionDataProvider(): Generator
    {
        yield 'valid showPageIdWithTitle with true value' => [
            [
                'options.' => [
                    'pageTree.' => [
                        'showPageIdWithTitle' => true,
                    ],
                ],
            ],
            true,
        ];
        yield 'valid showPageIdWithTitle with false value' => [
            [
                'options.' => [
                    'pageTree.' => [
                        'showPageIdWithTitle' => false,
                    ],
                ],
            ],
            false
        ];
        yield 'valid showPageIdWithTitle with empty value' => [
            [
                'options.' => [
                    'pageTree.' => [
                        'showPageIdWithTitle' => '',
                    ],
                ],
            ],
            false
        ];
        yield 'showPageIdWithTitle option missing' => [
            [
                'options.' => [
                    'pageTree.' => [
                    ],
                ],
            ],
            false
        ];
        yield 'empty options' => [[], false];
    }
}
