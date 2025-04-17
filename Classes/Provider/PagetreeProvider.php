<?php
declare(strict_types = 1);

namespace Neusta\Modmod\Provider;

use TYPO3\CMS\Backend\Tree\Repository\PageTreeRepository;
use TYPO3\CMS\Backend\Utility\BackendUtility;

final class PagetreeProvider
{
    private PageTreeRepository $pageTreeRepository;

    public function __construct(PageTreeRepository $pageTreeRepository)
    {
        $this->pageTreeRepository = $pageTreeRepository;
    }

    /**
     * @param int $startingUid
     * @param int $depth
     *
     * @return array<string, mixed>
     */
    public function getPageTree(int $startingUid, int $depth): array
    {
        $startingPage = BackendUtility::getRecord('pages', $startingUid) ?? [];

        return empty($startingPage)
            ? []
            : $this->pageTreeRepository->getTreeLevels($startingPage, $depth);
    }
}
