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
use Contao\ModuleModel;
use Contao\Template;
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
    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        $template->referer = 'javascript:history.go(-1)';
        $template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];

        // Get the parent ID via a query parameter
        $parentId = $request->query->get('example_id');

        // Get the parent record
        $example = ExampleModel::findById($parentId);

        if (null === $example) {
            return new Response();
        }

        // Fill the template with data from the parent record
        $template->setData(array_merge($example->row(), $template->getData()));

        $template->content = function () use ($request, $parentId): ?string {
            // Get all the content elements belonging to this parent ID and parent table
            $elements = ContentModel::findPublishedByPidAndTable($parentId, 'tl_example');

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

        return $template->getResponse();
    }
}
