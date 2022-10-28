<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Contao\Model;

use Composer\InstalledVersions;
use Contao\Model;
use Contao\StringUtil;
use Contao\System;

class PlentaJobsBasicOfferModel extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_plenta_jobs_basic_offer';

    public static function findAllPublishedByTypesAndLocation(array $types, array $locations)
    {
        $time = time();

        $columns = [
            'published = ?',
            '(start < ? OR start = ?)',
            '(stop > ? OR stop = ?)',
        ];
        $values = [1, $time, '', $time, ''];

        if (!empty($types)) {
            $criteria = [];
            foreach ($types as $type) {
                $criteria[] = 'employmentType LIKE ?';
                $values[] = '%"'.$type.'"%';
            }

            $columns[] = '('.implode(' OR ', $criteria).')';
        }

        if (!empty($locations)) {
            $criteria = [];
            foreach ($locations as $location) {
                foreach (explode('|', $location) as $l) {
                    $criteria[] = 'jobLocation LIKE ?';
                    $values[] = '%"'.$l.'"%';
                }
            }

            $columns[] = '('.implode(' OR ', $criteria).')';
        }

        return self::findBy($columns, $values);
    }

    public static function doesAliasExist($alias, $id = null, $language = null): bool
    {
        $columns = ['alias = ?'];
        $values = [$alias];

        if ($id) {
            $columns[] = 'id != ?';
            $values[] = $id;
        }

        if (self::findBy($columns, $values)) {
            return true;
        }

        if (null === $id) {
            return false;
        }

        $offers = self::findBy(['translations LIKE ?'], ['%"'.$alias.'"%']);
        if ($offers) {
            foreach ($offers as $offer) {
                $translations = StringUtil::deserialize($offer->translations);
                foreach ($translations as $translation) {
                    if ($translation['alias'] === $alias && (null === $language || $language === $translation['language']) && $offer->id !== $id) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function getTranslation($locale): ?array
    {
        $translations = StringUtil::deserialize($this->translations);

        if ($translations) {
            foreach ($translations as $translation) {
                if ($translation['language'] === $locale) {
                    return $translation;
                }
            }
        }

        return null;
    }

    public static function findPublishedByIdOrAlias($alias)
    {
        $jobOffer = self::findOneBy(['(id = ? OR alias = ?)', 'published = ?'], [$alias, $alias, 1]);

        if (!$jobOffer) {
            $requestStack = System::getContainer()->get('request_stack');
            if (version_compare(InstalledVersions::getVersion('contao/core-bundle'), '4.13', '>=')) {
                $language = $requestStack->getMainRequest()->getLocale();
            } else {
                $language = $requestStack->getMasterRequest()->getLocale();
            }

            $offers = self::findBy(['translations LIKE ?', 'published = ?'], ['%"'.$alias.'"%', 1]);
            foreach ($offers as $offer) {
                $translations = StringUtil::deserialize($offer->translations);
                foreach ($translations as $translation) {
                    if ($translation['language'] === $language && $translation['alias'] === $alias) {
                        $jobOffer = $offer;
                        break 2;
                    }
                }
            }
        }

        return $jobOffer;
    }
}
