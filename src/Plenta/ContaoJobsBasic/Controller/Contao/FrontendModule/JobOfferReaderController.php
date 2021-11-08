<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Controller\Contao\FrontendModule;

use Contao\ContentModel;
use Contao\Controller;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\Input;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\Template;
use Doctrine\Persistence\ManagerRegistry;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOffer;
use Plenta\ContaoJobsBasic\Helper\MetaFieldsHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @FrontendModule("plenta_jobs_basic_offer_reader",
 *   category="plentaJobsBasic",
 *   template="mod_plenta_jobs_basic_offer_reader",
 *   renderer="forward"
 * )
 */
class JobOfferReaderController extends AbstractFrontendModuleController
{
    protected ManagerRegistry $registry;

    protected MetaFieldsHelper $metaFieldsHelper;

    public function __construct(
        ManagerRegistry $registry,
        MetaFieldsHelper $metaFieldsHelper
    ) {
        $this->registry = $registry;
        $this->metaFieldsHelper = $metaFieldsHelper;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        /* @var PageModel $objPage */
        global $objPage;

        $template->referer = 'javascript:history.go(-1)';
        $template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];

        $jobOfferRepository = $this->registry->getRepository(TlPlentaJobsBasicOffer::class);

        $alias = Input::get('auto_item');

        if (!preg_match('/^[1-9]\d*$/', $alias)) {
            $jobOffer = $jobOfferRepository->findOneBy(['alias' => $alias]);
        } else {
            $jobOffer = $jobOfferRepository->find($alias);
        }

        if (null === $jobOffer) {
            return new Response();
        }

        $parentId = $jobOffer->getId();

        // Fill the template with data from the parent record
        $template->jobOffer = $jobOffer;
        $template->jobOfferMeta = $this->metaFieldsHelper->getMetaFields($jobOffer);

        $template->content = function () use ($request, $parentId): ?string {
            // Get all the content elements belonging to this parent ID and parent table
            $elements = ContentModel::findPublishedByPidAndTable($parentId, 'tl_plenta_jobs_basic_offer');

            if (null === $elements) {
                return null;
            }

            // The layout section is stored in a request attribute
            $section = $request->attributes->get('section', 'main');

            // Get the rendered content elements
            $content = '';

            foreach ($elements as $element) {
                $content .= Controller::getContentElement($element->id, $section);
            }

            return $content;
        };

        $template->headline = StringUtil::stripInsertTags($jobOffer->getTitle());
        $template->hl = $model->plentaJobsBasicHeadlineTag;
        $objPage->pageTitle = strip_tags(StringUtil::stripInsertTags($jobOffer->getTitle()));

        return $template->getResponse();
    }
}
