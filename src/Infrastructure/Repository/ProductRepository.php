<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Product;
use App\Domain\Repository\ProductRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository implements ProductRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findWithFilters(?string $category = null, ?int $priceLessThan = null, int $limit = 5, int $offset = 0): array
    {
        $paginator = $this->createPaginator($category, $priceLessThan, $limit, $offset);
        return iterator_to_array($paginator->getIterator());
    }

    public function countWithFilters(?string $category = null, ?int $priceLessThan = null): int
    {
        $paginator = $this->createPaginator($category, $priceLessThan);
        return $paginator->count();
    }

    public function createPaginator(?string $category = null, ?int $priceLessThan = null, ?int $limit = null, ?int $offset = null): Paginator
    {
        $qb = $this->createQueryBuilder('p');
        
        $this->applyFilters($qb, $category, $priceLessThan);
        
        $qb->orderBy('p.id', 'ASC');
        
        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }
        
        if ($offset !== null) {
            $qb->setFirstResult($offset);
        }

        return new Paginator($qb->getQuery(), true);
    }

    private function applyFilters(QueryBuilder $qb, ?string $category, ?int $priceLessThan): void
    {
        if ($category !== null) {
            $qb->andWhere('p.category = :category')
               ->setParameter('category', $category);
        }

        // priceLessThan filter applies before discounts are applied
        if ($priceLessThan !== null) {
            $qb->andWhere('p.price <= :priceLessThan')
               ->setParameter('priceLessThan', $priceLessThan);
        }
    }

    public function save(Product $product, bool $flush = false): void
    {
        $this->getEntityManager()->persist($product);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $product, bool $flush = false): void
    {
        $this->getEntityManager()->remove($product);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}