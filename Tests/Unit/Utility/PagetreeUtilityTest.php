<?php
declare(strict_types = 1);

namespace Neusta\Modmod\Tests\Unit\Utility;

use Neusta\Modmod\Utility\PagetreeUtility;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PagetreeUtilityTest extends TestCase
{
    #[Test]
    public function getPageIdArrayShouldReturnListOfPageUidsFromGivenPagetreeWithChildren(): void
    {
        $pageTree = [
            'uid'       => 316,
            'pid'       => 1,
            'title'     => 'entrypoint',
            '_children' => [
                ['uid' => 412],
                [
                    'uid'       => 1129,
                    '_children' => [
                        ['uid' => 1226],
                        [
                            'uid'      => 1755,
                            'childern' => [
                                'uid'   => 987,
                                'title' => 'no to be included. wrong key',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        self::assertSame([316, 412, 1129, 1226, 1755], PagetreeUtility::getPageIdArray($pageTree));
    }
}
