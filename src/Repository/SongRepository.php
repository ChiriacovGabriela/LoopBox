<?php

namespace App\Repository;

use App\Entity\Playlist;
use App\Entity\Song;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Expr\Array_;


/**
 * @extends ServiceEntityRepository<Song>
 *
 * @method Song|null find($id, $lockMode = null, $lockVersion = null)
 * @method Song|null findOneBy(array $criteria, array $orderBy = null)
 * @method Song[]    findAll()
 * @method Song[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SongRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Song::class);
    }

    public function save(Song $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Song $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findBySearch(SearchData $searchData): array
    {
        $data = $this->createQueryBuilder('p')
            ->addOrderBy('p.created_at', 'DESC');

        if (!empty($searchData->q)) {
            $data = $data
                ->where('p.name LIKE :q')
                ->orWhere('p.artist LIKE :q')
                ->orWhere('p.type LIKE :q')
                ->setParameter('q', "%{$searchData->q}%");
        }
        $data = $data
            ->getQuery()
            ->getResult();

        return $data;

    }

    public function findSongsByType($filters)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->andWhere('s.type IN(:types)')
            ->setParameter(':types', array_values($filters));
        return $qb->getQuery()->getResult();
    }

    public function findSongsByPlaylistAndType(Playlist $playlist, array $types, int $page, int $limit): array
    {
        $limit= abs($limit);
        $result = [];

        $qb = $this->createQueryBuilder('s');
        $qb->leftJoin('s.playlists', 'p')
            ->andWhere('p.id = :playlistId')
            ->andWhere('s.type IN (:types)')
            ->setParameter('playlistId', $playlist->getId())
            ->setParameter('types', array_values($types))
            ->setMaxResults($limit)
            ->setFirstResult($page * $limit - $limit);

        $paginator = new Paginator($qb);
        $data = $paginator->getQuery()->getResult();

        if(empty($data)){
            return $result;
        }

        $pages = ceil($paginator->count() / $limit);

        $result['data'] = $data;
        $result['pages'] = $pages;
        $result['page'] = $page;
        $result['limit'] = $limit;

        return $result;
    }

    public function findSongsByPlaylistPaginated(Playlist $playlist, int $page, int $limit): array
    {
        $limit= abs($limit);
        $result = [];

        $qb = $this->createQueryBuilder('s');
        $qb->leftJoin('s.playlists', 'p')
            ->andWhere('p.id = :playlistId')
            ->setParameter('playlistId', $playlist->getId())
            ->setMaxResults($limit)
            ->setFirstResult($page * $limit - $limit);

        $paginator = new Paginator($qb);
        $data = $paginator->getQuery()->getResult();

        if(empty($data)){
            return $result;
        }

        $pages = ceil($paginator->count() / $limit);

        $result['data'] = $data;
        $result['pages'] = $pages;
        $result['page'] = $page;
        $result['limit'] = $limit;

        return $result;
    }


//    /**
//     * @return Song[] Returns an array of Song objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Song
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
