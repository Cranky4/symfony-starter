<?php

declare(strict_types=1);

namespace App\News\Infrastructure\Persistence\Doctrine\Repository;

use App\News\Domain\Entity\News;
use App\News\Domain\Repository\NewsRepositoryInterface;
use App\News\Domain\ValueObject\NewsSource;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<News>
 */
class NewsRepository extends ServiceEntityRepository implements NewsRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    public function save(News $newsItem): void
    {
        $this->getEntityManager()->persist($newsItem);
    }

    public function index(DateTimeImmutable $day, int $page = 1, int $perPage = 10, array $order = []): array
    {
        $qb = $this->createQueryBuilder('t');
        $qb->andWhere(
            $qb->expr()->eq(
                't.day',
                $qb->expr()->literal($day->format('Y-m-d')),
            ),
        );
        $qb->setMaxResults($perPage);
        $qb->setFirstResult(($page - 1) * $perPage);

        foreach ($order as $field => $sort) {
            $qb->addOrderBy(
                sprintf('t.%s', $field),
                $sort,
            );
        }

        return $qb->getQuery()->getResult();
    }

    public function existBySource(NewsSource $source): bool
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('COUNT(t.id) as cnt');
        $qb->andWhere(
            $qb->expr()->eq(
                't.source.name',
                $qb->expr()->literal($source->name),
            ),
            $qb->expr()->eq(
                't.source.id',
                $qb->expr()->literal($source->id),
            ),
        );
        $qb->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleScalarResult() > 0;
        } catch (NoResultException) {
            return false;
        }
    }

    public function countByDay(DateTimeImmutable $day): int
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('COUNT(t.id)');
        $qb->andWhere(
            $qb->expr()->eq(
                't.day',
                $qb->expr()->literal($day->format('Y-m-d')),
            ),
        );

        try {
            return $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException) {
            return 0;
        }
    }
}
