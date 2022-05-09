<?php
declare(strict_types = 1);

namespace Neusta\Modmod\Utility;

use function array_unique;

final class PagetreeUtility
{
    /**
     * @param array<string, mixed> $pageTree
     *
     * @return int[]
     */
    public static function getPageIdArray(array $pageTree): array
    {
        $uids = [];
        self::getPageTreeUids($pageTree, $uids);

        return array_unique($uids);
    }

    /**
     * @param array<string, mixed> $pages
     * @param int[]                $uids
     */
    private static function getPageTreeUids(array $pages, array &$uids): void
    {
        $uids[] = $pages['uid'] ?? 0;
        foreach ($pages['_children'] ?? [] as $children) {
            self::getPageTreeUids($children, $uids);
        }
    }
}
