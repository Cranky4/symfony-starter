<?php

declare(strict_types=1);

namespace App\Hello\Infrastructure\Persistence\Doctrine\Repository;

use App\Hello\Domain\Entity\HelloWorldEntity;
use App\Hello\Domain\Repository\HelloRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HelloWorldEntity>
 */
class HelloWorldEntityRepository extends ServiceEntityRepository implements HelloRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HelloWorldEntity::class);
    }

    public function total(): int
    {
        return $this->count([]);
    }

    public function getAll(): array
    {
        return $this->findAll();
    }

    public function save(HelloWorldEntity $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function existsById(string $id): bool
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('count(t.id) as cnt');
        $qb->andWhere(
            $qb->expr()->eq(
                't.id',
                $qb->expr()->literal($id),
            ),
        );
        $qb->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult()['cnt'] > 0;
        } catch (NoResultException) {
            return false;
        }
    }
}
