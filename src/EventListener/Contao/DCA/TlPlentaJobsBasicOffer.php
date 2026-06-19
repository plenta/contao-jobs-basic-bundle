<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\EventListener\Contao\DCA;

use Composer\InstalledVersions;
use Contao\CoreBundle\DataContainer\DataContainerOperation;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Slug\Slug;
use Contao\DataContainer;
use Contao\Input;
use Contao\Message;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicJobLocationModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Plenta\ContaoJobsBasic\Helper\EmploymentType;
use Plenta\ContaoJobsBasic\Helper\NumberHelper;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment as TwigEnvironment;

class TlPlentaJobsBasicOffer
{
    public function __construct(
        protected EmploymentType $employmentTypeHelper,
        protected Slug $slugGenerator,
        protected RequestStack $requestStack,
        protected TwigEnvironment $twig,
        private readonly RouterInterface $router,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function aliasSaveCallback(mixed $varValue, DataContainer $dc): string
    {
        $lang = null;

        if ('alias' === $dc->inputName) {
            $title = $dc->activeRecord->title;
            $aliasExists = static fn (string $alias): bool => PlentaJobsBasicOfferModel::doesAliasExist($alias, (int) $dc->activeRecord->id);
        } else {
            $index = str_replace('translations__alias__', '', $dc->inputName);
            $title = Input::post('translations__title__'.$index);
            $lang = Input::post('translations__language__'.$index);
            $aliasExists = static fn (string $alias): bool => PlentaJobsBasicOfferModel::doesAliasExist($alias) || PlentaJobsBasicOfferModel::doesAliasExist($alias, (int) $dc->activeRecord->id, $lang);
        }

        if (empty($varValue)) {
            $rootPages = PageModel::findPublishedRootPages();
            $fallback = null;
            $rootPage = null;

            foreach ($rootPages as $rootP) {
                if ($rootP->fallback) {
                    $fallback = $rootP->id;
                }

                if ('alias' === $dc->inputName && $rootP->fallback) {
                    $rootPage = $fallback;
                    break;
                }

                if ($rootP->language === $lang) {
                    $rootPage = $rootP->id;
                    break;
                }
            }

            if (empty($rootPage)) {
                $rootPage = $fallback;
            }

            $varValue = $this->slugGenerator->generate(
                $title,
                $rootPage,
                $aliasExists,
            );
        } elseif (preg_match('/^[1-9]\d*$/', (string) $varValue)) {
            throw new \Exception(\sprintf($GLOBALS['TL_LANG']['ERR']['aliasNumeric'], $varValue));
        } elseif ($aliasExists($varValue)) {
            throw new \Exception(\sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
        }

        return $varValue;
    }

    /**
     * @return array<int, string>
     */
    public function jobLocationOptionsCallback(): array
    {
        $jobLocations = PlentaJobsBasicJobLocationModel::findAll();

        $return = [];

        foreach ($jobLocations as $jobLocation) {
            $return[$jobLocation->id] = $jobLocation->getRelated('pid')->name.': ';

            if ($jobLocation->title) {
                $return[$jobLocation->id] .= $jobLocation->title;
            } elseif ('onPremise' === $jobLocation->jobTypeLocation) {
                $return[$jobLocation->id] .= $jobLocation->streetAddress;

                if ('' !== $jobLocation->addressLocality) {
                    $return[$jobLocation->id] .= ($jobLocation->streetAddress ? ', ' : '').$jobLocation->addressLocality;
                }
            } else {
                $return[$jobLocation->id] .= $GLOBALS['TL_LANG']['MSC']['PLENTA_JOBS']['remote'].' ['.$jobLocation->requirementValue.']';
            }
        }

        return $return;
    }

    /**
     * @return array<string, string>
     */
    public function employmentTypeOptionsCallback(): array
    {
        $employmentTypes = $this->employmentTypeHelper->getEmploymentTypes();

        $return = [];

        foreach ($employmentTypes as $employmentType) {
            $return[$employmentType] = $this->employmentTypeHelper->getEmploymentTypeName($employmentType);
        }

        return $return;
    }

    public function employmentTypeSaveCallback(mixed $value, DataContainer $dc): string
    {
        $value = StringUtil::deserialize($value);

        return json_encode($value);
    }

    public function employmentTypeLoadCallback(mixed $value, DataContainer $dc): string
    {
        if (null === $value) {
            return serialize([]);
        }

        return serialize(json_decode((string) $value));
    }

    public function saveCallbackGlobal(DataContainer $dc): void
    {
        if (!$dc->activeRecord) {
            return;
        }

        if (null === $dc->activeRecord->datePosted && !empty(Input::post('published'))) {
            $offer = PlentaJobsBasicOfferModel::findById($dc->activeRecord->id);
            $offer->datePosted = time();
            $offer->save();
        }
    }

    public function salaryOnLoad(mixed $value, DataContainer $dc): string
    {
        $numberHelper = new NumberHelper($dc->activeRecord->salaryCurrency, $this->requestStack->getCurrentRequest()->getLocale());

        return $numberHelper->formatNumberFromDbForDCAField((string) $value);
    }

    public function salaryOnSave(mixed $value, DataContainer $dc): int
    {
        $numberHelper = new NumberHelper($dc->activeRecord->salaryCurrency, $this->requestStack->getCurrentRequest()->getLocale());

        return $numberHelper->reformatDecimalForDb($value);
    }

    public function onShowInfoCallback(DataContainer|null $dc = null): void
    {
        $GLOBALS['TL_CSS'][] = 'bundles/plentacontaojobsbasic/dashboard.css';
        $info = $this->twig->render('@PlentaContaoJobsBasic/be_plenta_info.html.twig', [
            'version' => InstalledVersions::getVersion('plenta/contao-jobs-basic-bundle'),
        ]);

        Message::addRaw($info);
    }

    /**
     * @return array<string, string>
     */
    public function getLanguages(): array
    {
        return System::getContainer()->get('contao.intl.locales')->getLanguages();
    }

    /**
     * @param array<string, mixed> $row
     * @param array<string>        $labels
     */
    public function labelCallback(array $row, string $label, DataContainer $dc, array $labels): string
    {
        $jobLocations = [];
        $locations = $this->jobLocationOptionsCallback();
        $locationsArr = StringUtil::deserialize($row['jobLocation']);

        foreach ($locationsArr as $location) {
            $jobLocations[] = $locations[$location];
        }

        $jobEmploymentTypes = [];
        $employmentTypes = $this->employmentTypeOptionsCallback();
        $typesArr = StringUtil::deserialize($this->employmentTypeLoadCallback($row['employmentType'], $dc));

        foreach ($typesArr as $type) {
            $jobEmploymentTypes[] = $employmentTypes[$type];
        }

        $label = '<h2>'.$row['title'].'</h2>';
        $label .= implode(' | ', $jobLocations);

        return $label.' | '.implode(', ', $jobEmploymentTypes);
    }

    public function getSerpUrl(PlentaJobsBasicOfferModel $offer): string
    {
        $strSuffix = '/';

        return \sprintf(preg_replace('/%(?!s)/', '%%', $strSuffix), $offer->alias ?: $offer->id);
    }

    #[AsCallback(table: 'tl_plenta_jobs_basic_offer', target: 'list.operations.preview.button')]
    public function onPreviewButton(DataContainerOperation $operation): void
    {
        $operation->setUrl($this->router->generate('contao_backend_preview', ['jobsBasicOffer' => $operation->getRecord()['id']]));
    }
}
