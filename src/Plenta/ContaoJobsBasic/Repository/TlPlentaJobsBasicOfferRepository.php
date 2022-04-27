<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
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
        return $this->createQueryBuilder('a')
            ->andwhere('a.published=:published')
            ->andWhere('a.start<:time OR a.start=:empty')
            ->andWhere('a.stop>:time OR a.stop=:empty')
            ->setParameter('published', '1')
            ->setParameter('time', time())
            ->setParameter('empty', '')
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_OBJECT);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function doesAliasExist(string $alias, int $id): bool
    {
        $jobOffer = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.alias=:alias')
            ->andWhere('a.id!=:id')
            ->setParameter('alias', $alias)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);

        if ($jobOffer > 0) {
            return true;
        }

        return false;
    }

    public function findAllPublishedByTypesAndLocation(array $types, array $locations, ?string $sortBy = null, ?string $order = null): array
    {
        $qb = $this->createQueryBuilder('a');

        $qb
            ->andwhere('a.published=:published')
            ->andWhere('a.start<:time OR a.start=:empty')
            ->andWhere('a.stop>:time OR a.stop=:empty')
            ->setParameter('published', '1')
            ->setParameter('time', time())
            ->setParameter('empty', '')
        ;

        $criterionType = [];

        foreach ($types as $type) {
            $criterionType[] = "a.employmentType LIKE '%".$type."%'";
        }

        if (\count($criterionType)) {
            $qb->andWhere(implode(' OR ', $criterionType));
        }

        $criterionLocation = [];
        $remoteJobs = false;

        foreach ($locations as $location) {
            foreach (explode('|', $location) as $l) {
                if ('remote' === $l) {
                    $remoteJobs = true;
                    $criterionLocation[] = 'a.isRemote = 1';
                } else {
                    $criterionLocation[] = "a.jobLocation LIKE '%\"".$l."\"%'";
                }
            }
        }

        if (\count($criterionLocation)) {
            if (!$remoteJobs) {
                $qb->andWhere('a.isRemote = 0 OR a.isOnlyRemote = 0');
            }
            $qb->andWhere(implode(' OR ', $criterionLocation));
        }

        if (null !== $sortBy) {
            $qb->orderBy('a.'.$sortBy, $order);
        }

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_OBJECT);
    }
}
