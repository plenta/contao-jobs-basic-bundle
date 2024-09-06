<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\EventListener\Contao;

use Contao\Date;
use Contao\Input;
use Contao\Config;
use Contao\UserModel;
use Composer\InstalledVersions;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Symfony\Component\HttpFoundation\RequestStack;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;

/**
 * @Hook("replaceInsertTags")
 */
class InsertTagListener
{
    public const TAG = 'job';

    protected RequestStack $requestStack;

    /**
     * @var string|null
     */
    protected ?string $autoItem = null;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
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
        if (version_compare(InstalledVersions::getVersion('contao/core-bundle'), '4.13', '>=')) {
            $language = $this->requestStack->getMainRequest()->getLocale();
        } else {
            $language = $this->requestStack->getMasterRequest()->getLocale();
        }

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
                return Date::parse($elements[1] ?? ($objPage->dateFormat ?? Config::get('dateFormat')));
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
