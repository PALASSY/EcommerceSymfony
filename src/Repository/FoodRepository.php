<?php

namespace App\Repository;

use App\Data\searchData;
use App\Entity\Food;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Food|null find($id, $lockMode = null, $lockVersion = null)
 * @method Food|null findOneBy(array $criteria, array $orderBy = null)
 * @method Food[]    findAll()
 * @method Food[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FoodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Food::class);
    }


    /**
     * Récupère les menus en lien avec la recherche
     * @return Food[]
     */
    public function findSearch(searchData $search): array
    {
        //Récupère tous les produits de ce repository actuel avec un alias(f)
        $query = $this->createQueryBuilder('f');
        //dd($query->getDql());

        //faire une recherche une des 3 category( entrée, plat,dessert)
        if (!empty($search->q)) {
            $query = $query->andWhere('f.category LIKE :q')
                           ->setParameter('q',"%{$search->q}%")
            ;
        }

        //faire un tri prix minimum et plus
        if (!empty($search->min)) {
            $query = $query->andWhere('f.price >= :min')
            ->setParameter('min', $search->min);
        }

        //faire un tri prix maximum et moins
        if (!empty($search->max)) {
            $query = $query->andWhere('f.price <= :max')
            ->setParameter('max', $search->max);
        }

        //dd($query->getQuery()); c'est un tableau d'Objet
        return $query->getQuery()->getResult();
    }


    /**
     * Rechercher les meilleurs menus
     *
     * @param [type] $limit
     * @return Response
     */
    public function findBestFoods($limit)
    {
        return $this->createQueryBuilder('f')
                    ->select('f as menu, AVG(c.rating) as note')
                    ->join('f.comments','c')
                    ->groupby('f')
                    ->orderBy('note','DESC')
                    ->setMaxResults($limit)
                    ->getQuery()
                    ->getResult()
                    ;
    }






    // /**
    //  * @return Food[] Returns an array of Food objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Food
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

   
}
