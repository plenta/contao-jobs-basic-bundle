<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2024, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\InsertTag;

use Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag;
use Contao\CoreBundle\InsertTag\InsertTagResult;
use Contao\CoreBundle\InsertTag\ResolvedInsertTag;
use Contao\CoreBundle\InsertTag\Resolver\InsertTagResolverNestedResolvedInterface;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsInsertTag('jobs')]
class JobsCountInsertTag implements InsertTagResolverNestedResolvedInterface
{
    public function __construct(protected RequestStack $requestStack)
    {
    }

    public function __invoke(ResolvedInsertTag $insertTag): InsertTagResult
    {
        if ('count' === $insertTag->getParameters()->get(0)) {
            $filtered = 'filtered' === $insertTag->getParameters()->get(1);

            if ($filtered) {
                $types = $this->requestStack->getCurrentRequest()->query->all('types');
                $locations = $this->requestStack->getCurrentRequest()->query->all('location');

                return new InsertTagResult((string) PlentaJobsBasicOfferModel::countAllPublishedByTypesAndLocation($types, $locations));
            }

            return new InsertTagResult((string) PlentaJobsBasicOfferModel::countAllPublishedByTypesAndLocation([], []));
        }

        return new InsertTagResult('');
    }
}
