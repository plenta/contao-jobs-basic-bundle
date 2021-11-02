<?php


namespace Plenta\ContaoJobsBasic\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOffer;

class TlPlentaJobsBasicOfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TlPlentaJobsBasicOffer::class);
    }

    public function findAllPublished(): array
    {
        return $this->findAll();
    }
}
