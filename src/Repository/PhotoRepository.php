<?php

namespace OHMedia\PhotoBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use OHMedia\PhotoBundle\Entity\Photo;

/**
 * @method Photo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Photo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Photo[]    findAll()
 * @method Photo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Photo::class);
    }

    public function save(Photo $photo, bool $flush = false): void
    {
        $this->getEntityManager()->persist($photo);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Photo $photo, bool $flush = false): void
    {
        $this->getEntityManager()->remove($photo);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
