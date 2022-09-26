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

use Contao\Input;
use Contao\Config;
use Doctrine\ORM\EntityManagerInterface;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOffer;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOfferTranslation;

/**
 * @Hook("replaceInsertTags")
 */
class InsertTagListener
{
    public const TAG = 'job';

    /**
     * @var EntityManagerInterface
     */
    protected $registry;

    /**
    * @var string|null
     */
    protected $autoItem;

    public function __construct(EntityManagerInterface $registry)
    {
        $this->registry = $registry;
    }

    public function __invoke(string $tag)
    {
        $chunks = explode('::', $tag);

        if (self::TAG !== $chunks[0]) {
            return false;
        }

        $this->handelAutoItem();

        if (!$this->autoItem) {
            return false;
        }

        $jobOfferRepo = $this->registry->getRepository(TlPlentaJobsBasicOffer::class);

        $jobOfferData = $jobOfferRepo->findOneBy(
            ['alias' => $this->autoItem]
        );

        $isTranslation = false;
        if (null === $jobOfferData) {
            $jobOfferTransRepo = $this->registry->getRepository(TlPlentaJobsBasicOfferTranslation::class);
            $jobOfferData = $jobOfferTransRepo->findOneBy(
                ['alias' => $this->autoItem]
            );
            $isTranslation = true;
        }

        if (null !== $jobOfferData) {
            if ('id' === $chunks[1]) {
                if (true === $isTranslation) {
                    return (string) $jobOfferData->getOffer()->getId();
                }
                return (string)$jobOfferData->getId();
            }

            if ('title' === $chunks[1]) {
                return (string)$jobOfferData->getTitle();
            }

            if ('alias' === $chunks[1]) {
                return (string)$jobOfferData->getAlias();
            }
        }

        return false;
    }

    public function handelAutoItem(): void
    {
        if (null === $this->autoItem) {
            if (!isset($_GET['items']) && isset($_GET['auto_item']) && Config::get('useAutoItem')) {
                Input::setGet('items', Input::get('auto_item'));
            }

            $this->autoItem = Input::get('items');
        }
    }
}