<?php declare(strict_types = 1);

namespace Neusta\Modmod\Domain\Repository;

use PDO;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;

class CommentRepository
{
    public const TABLE = 'tx_pwcomments_domain_model_comment';

    private ConnectionPool $connectionPool;

    public function __construct(
        ConnectionPool $connectionPool
    ) {
        $this->connectionPool = $connectionPool;
    }

    /**
     * @param int[] $pageIds
     *
     * @return array<int, array>
     */
    public function findByPageIds(array $pageIds): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE);

        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);

        $comments = $queryBuilder->select('*')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->in(
                    'pid',
                    $queryBuilder->createNamedParameter($pageIds, Connection::PARAM_INT_ARRAY),
                ),
            )
            ->orderBy('hidden', 'DESC')->addOrderBy('crdate', 'DESC')->executeQuery()
            ->fetchAllAssociative();

        $result = [];
        foreach ($comments as $comment) {
            $result[(int)$comment['pid']][] = $comment;
        }

        return $result;
    }

    /**
     * @param int $amount
     *
     * @return array|array[]
     */
    public function findLatestUnmoderated(int $amount): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE);

        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);

        return $queryBuilder->select('*')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq('hidden', 1),
            )
            ->orderBy('crdate', 'DESC')->setMaxResults($amount)->executeQuery()
            ->fetchAllAssociative();
    }

    public function publishComment(int $commentUid): void
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE);

        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);

        $queryBuilder->update(self::TABLE)
            ->set('hidden', '0')->where($queryBuilder->expr()->eq(
            'uid',
            $queryBuilder->createNamedParameter($commentUid, PDO::PARAM_INT),
        ))->executeStatement();
    }

    public function deleteComment(int $commentUid): void
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE);

        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);

        $queryBuilder->update(self::TABLE)
            ->set('deleted', '1')->where($queryBuilder->expr()->eq(
            'uid',
            $queryBuilder->createNamedParameter($commentUid, PDO::PARAM_INT),
        ))->executeStatement();
    }

    public function unpublishComment(int $commentUid): void
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE);

        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);

        $queryBuilder->update(self::TABLE)
            ->set('hidden', '1')->where($queryBuilder->expr()->eq(
            'uid',
            $queryBuilder->createNamedParameter($commentUid, PDO::PARAM_INT),
        ))->executeStatement();
    }
}
