<?php

namespace OHMedia\PhotoBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use OHMedia\PhotoBundle\Entity\Gallery;

/**
 * @method Gallery|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gallery|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gallery[]    findAll()
 * @method Gallery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GalleryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gallery::class);
    }

    public function save(Gallery $gallery, bool $flush = false): void
    {
        $this->getEntityManager()->persist($gallery);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Gallery $gallery, bool $flush = false): void
    {
        $this->getEntityManager()->remove($gallery);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
