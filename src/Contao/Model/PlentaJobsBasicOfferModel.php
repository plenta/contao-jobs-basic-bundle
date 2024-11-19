<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2023, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Contao\Model;

use Composer\InstalledVersions;
use Contao\Config;
use Contao\Controller;
use Contao\Model;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Plenta\ContaoJobsBasic\Events\JobOfferModelKeywordFieldsEvent;
use Plenta\ContaoJobsBasic\Events\Model\FindAllPublishedByTypesAndLocationEvent;

class PlentaJobsBasicOfferModel extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_plenta_jobs_basic_offer';

    protected $readerPage = [];

    public static function findAllPublishedByTypesAndLocation(array $types, array $locations, int $limit = 0, int $offset = 0, string $sortBy = null, string $order = null, $onlyTranslated = false, $model = null, $applyFilterRequests = true)
    {
        $columns = [];
        $values = [];
        $arrOptions = [];

        self::buildSearchQuery($columns, $arrOptions, $values, $types, $locations, $sortBy, $order, $onlyTranslated, $model, $applyFilterRequests);

        $arrOptions['limit'] = $limit;
        $arrOptions['offset'] = $offset;

        return self::findBy($columns, $values, $arrOptions);
    }

    public static function countAllPublishedByTypesAndLocation(array $types, array $locations, $onlyTranslated = false, $model = null, $applyFilterRequests = true)
    {
        $columns = [];
        $values = [];
        $arrOptions = [];

        self::buildSearchQuery($columns, $arrOptions, $values, $types, $locations, null, null, $onlyTranslated, $model);

        return self::countBy($columns, $values, $arrOptions);
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
        $requestStack = System::getContainer()->get('request_stack');

        $language = $requestStack->getCurrentRequest()->getLocale();

        if ($jobOffer && $jobOffer->getTranslation($language)) {
            $jobOffer = null;
        }

        if (!$jobOffer) {
            $offers = self::findBy(['translations LIKE ?', 'published = ?'], ['%"'.$alias.'"%', 1]);
            if ($offers) {
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
        }

        return $jobOffer;
    }

    public function getReaderPage($language): ?PageModel
    {
        if (empty($this->readerPage[$language])) {
            $modules = ModuleModel::findByType('plenta_jobs_basic_offer_list');
            if ($modules) {
                foreach ($modules as $module) {
                    $jobLocations = StringUtil::deserialize($this->jobLocation);
                    $locations = StringUtil::deserialize($module->plentaJobsBasicLocations);
                    $isCorrectModule = false;
                    if (\is_array($locations) && \is_array($jobLocations)) {
                        foreach ($locations as $location) {
                            if (\in_array($location, $jobLocations, true)) {
                                $isCorrectModule = true;
                                break;
                            }
                        }
                    } else {
                        $isCorrectModule = true;
                    }
                    if ($isCorrectModule) {
                        $page = PageModel::findWithDetails($module->jumpTo);
                        if ($page->rootLanguage === $language) {
                            $this->readerPage[$language] = $page;
                            break;
                        }
                    }
                }
            }
        }

        return $this->readerPage[$language] ?? null;
    }

    public function getAbsoluteUrl($language)
    {
        $objPage = $this->getReaderPage($language);
        if (!$objPage) {
            return null;
        }
        $params = $this->getParams($language);

        return StringUtil::ampersand($objPage->getAbsoluteUrl($params));
    }

    public function getFrontendUrl($language)
    {
        $objPage = $this->getReaderPage($language);
        if (!$objPage) {
            return null;
        }
        $params = $this->getParams($language);

        return StringUtil::ampersand($objPage->getAbsoluteUrl($params));
    }

    protected static function buildSearchQuery(array &$columns, array &$arrOptions, array &$values, array $types, array $locations, string $sortBy = null, string $order = null, $onlyTranslated = false, ?ModuleModel $model = null, $applyFilterRequests = true): void
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

        $requestStack = System::getContainer()->get('request_stack');
        if ($onlyTranslated) {
            $language = $requestStack->getCurrentRequest()->getLocale();
            $page = PageModel::findBy(['type = ?', 'language = ?', '(dns = ? OR dns = ?)'], ['root', $language, '', $requestStack->getCurrentRequest()->getHost()]);
            if ($page && !$page->fallback) {
                $str = 's:8:"language";s:'.\strlen($language).':"'.$language.'"';
                $columns[] = 'translations LIKE ?';
                $values[] = '%'.$str.'%';
            }
        }

        $dispatcher = System::getContainer()->get('event_dispatcher');

        if ($applyFilterRequests && $keyword = $requestStack->getCurrentRequest()->get('keyword')) {
            $fields = ['title', 'description', 'translations'];

            $fieldEvent = new JobOfferModelKeywordFieldsEvent($fields);
            $dispatcher->dispatch($fieldEvent, $fieldEvent::NAME);
            $fields = $fieldEvent->getFields();

            $keywords = array_filter(StringUtil::trimsplit(' ', $keyword));

            if (!empty($keywords)) {
                $criteria = [];

                foreach ($fields as $searchField) {
                    foreach ($keywords as $keyword) {
                        $criteria[] = $searchField.' LIKE ?';
                        $values[] = '%'.$keyword.'%';
                    }
                }

                if (!empty($criteria)) {
                    $columns[] = '('.implode(' OR ', $criteria).')';
                }
            }
        }

        $sortingFields = [];
        Controller::loadDataContainer('tl_plenta_jobs_basic_offer');
        foreach ($GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer']['fields'] as $name => $field) {
            if (!empty($field['sorting'])) {
                $sortingFields[] = $name;
            }
        }

        if (!empty($sortBy) && \in_array($sortBy, $sortingFields, true)) {
            if (!empty($order) && \in_array($order, ['ASC', 'DESC'], true)) {
                $sortDirection = $order;
            } else {
                $sortDirection = 'ASC';
            }
            $arrOptions = ['order' => $sortBy.' '.$sortDirection];
        } else {
            $arrOptions = [];
        }

        $findAllPublishedByTypesAndLocationEvent = new FindAllPublishedByTypesAndLocationEvent();
        $findAllPublishedByTypesAndLocationEvent
            ->setColumns($columns)
            ->setValues($values)
            ->setOptions($arrOptions)
            ->setModel($model)
            ->setApplyRequestFilters($applyFilterRequests)
        ;
        $dispatcher->dispatch($findAllPublishedByTypesAndLocationEvent, FindAllPublishedByTypesAndLocationEvent::NAME);

        $columns = $findAllPublishedByTypesAndLocationEvent->getColumns();
        $values = $findAllPublishedByTypesAndLocationEvent->getValues();
        $arrOptions = $findAllPublishedByTypesAndLocationEvent->getOptions();
    }

    protected function getParams($language)
    {
        $alias = $this->alias;

        if ($translation = $this->getTranslation($language)) {
            $alias = $translation['alias'];
        }

        return '/'.($alias ?: $this->id);
    }

    public static function findAllPublished()
    {
        $t = static::$strTable;
        $time = time();

        $arrColumns[] = "$t.published='1' AND ($t.start='' OR $t.start<=$time) AND ($t.stop='' OR $t.stop>$time)";

        return static::findBy($arrColumns, null);
    }
}
