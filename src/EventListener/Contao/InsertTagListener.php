<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2023-2025, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\EventListener\Contao;

use Composer\InstalledVersions;
use Contao\Config;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Date;
use Contao\Input;
use Contao\UserModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Plenta\ContaoJobsBasic\Helper\MetaFieldsHelper;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @Hook("replaceInsertTags")
 */
class InsertTagListener
{
    public const TAG = 'job';

    protected RequestStack $requestStack;

    protected MetaFieldsHelper $metaFieldsHelper;

    /**
     * @var string|null
     */
    protected ?string $autoItem = null;

    public function __construct(RequestStack $requestStack, MetaFieldsHelper $metaFieldsHelper)
    {
        $this->requestStack = $requestStack;
        $this->metaFieldsHelper = $metaFieldsHelper;
    }

    public function __invoke(string $tag)
    {
        $chunks = explode('::', $tag);

        if (self::TAG !== $chunks[0]) {
            return false;
        }

        $this->handleAutoItem();

        if (!$this->autoItem) {
            return false;
        }

        $jobOfferData = PlentaJobsBasicOfferModel::findPublishedByIdOrAlias($this->autoItem);
        $language = $this->requestStack->getCurrentRequest()->getLocale();

        if (null !== $jobOfferData) {
            $translation = $jobOfferData->getTranslation($language);

            if ('id' === $chunks[1]) {
                return (string) $jobOfferData->id;
            }
            
           if ('teasertext' === $chunks[1]) {
                return $translation['teaser'] ?? (string) $jobOfferData->teaser;
            }
            if ('description' === $chunks[1]) {
                return $translation['description'] ?? (string) $jobOfferData->description;
            }

            if ('title' === $chunks[1]) {
                return $translation['title'] ?? (string) $jobOfferData->title;
            }

            if ('alias' === $chunks[1]) {
                return $translation['alias'] ?? (string) $jobOfferData->alias;
            }

            if ('datePosted' === $chunks[1]) {
                $objPage = $GLOBALS['objPage'] ?? null;

                return Date::parse($objPage->dateFormat ?? Config::get('dateFormat'), $jobOfferData->datePosted);
            }

            if ('author' === $chunks[1] && isset($chunks[2])) {
                $author = UserModel::findById((int) $jobOfferData->author);

                if (null !== $author) {
                    if ('name' === $chunks[2]) {
                        return (string) $author->name;
                    }

                    if ('email' === $chunks[2]) {
                        return (string) $author->email;
                    }

                    if ('username' === $chunks[2]) {
                        return (string) $author->username;
                    }
                }
            }

            if ('location' === $chunks[1]) {
                return $this->metaFieldsHelper->formatAddressLocality($jobOfferData) ?? '';
            }

            if ('locationTitle' === $chunks[1]) {
                return $this->metaFieldsHelper->formatAddressLocalityTitle($jobOfferData) ?? '';
            }
        }

        return false;
    }

    public function handleAutoItem(): void
    {
        if (null === $this->autoItem) {
            $this->autoItem = Input::get('auto_item', false, true);
        }
    }
}
