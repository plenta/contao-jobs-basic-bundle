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
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOfferTranslation;

class TlPlentaJobsBasicOfferTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TlPlentaJobsBasicOfferTranslation::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function doesAliasExist(string $alias, int $id, string $lang): bool
    {
        $jobOfferTrans = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.alias=:alias')
            ->andWhere('(a.offer!=:id OR a.language!=:lang)')
            ->setParameter('alias', $alias)
            ->setParameter('id', $id)
            ->setParameter('lang', $lang)
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);

        if ($jobOfferTrans > 0) {
            return true;
        }

        return false;
    }

    public function findByAliasAndLanguage($alias, $language)
    {
        return $this->findOneBy(['language' => $language, 'alias' => $alias]);
    }
}
