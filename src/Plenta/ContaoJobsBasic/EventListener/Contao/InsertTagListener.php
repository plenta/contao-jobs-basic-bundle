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

use Composer\InstalledVersions;
use Contao\Config;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Input;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Symfony\Component\HttpFoundation\RequestStack;

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

            if ('title' === $chunks[1]) {
                return $translation['title'] ?? (string) $jobOfferData->title;
            }

            if ('alias' === $chunks[1]) {
                return $translation['alias'] ?? (string) $jobOfferData->alias;
            }
        }

        return false;
    }

    public function handleAutoItem(): void
    {
        if (null === $this->autoItem) {
            if (!isset($_GET['items']) && isset($_GET['auto_item']) && Config::get('useAutoItem')) {
                Input::setGet('items', Input::get('auto_item', false, true));
            }

            $this->autoItem = Input::get('items');
        }
    }
}
