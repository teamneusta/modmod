<?php
declare(strict_types = 1);

namespace Neusta\Modmod\Widgets\Provider;

use Neusta\Modmod\Domain\Repository\CommentRepository;
use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;

final class LatestCommentsDataProvider implements ListDataProviderInterface
{
    public const DEFAULT_LIST_ITEM_SIZE = 15;

    private CommentRepository $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    /**
     * @return array|array[]
     */
    public function getItems(): array
    {
        return $this->commentRepository->findLatestUnmoderated(self::DEFAULT_LIST_ITEM_SIZE);
    }
}
