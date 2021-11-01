<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Controller\Contao\BackendModule;

use Contao\BackendModule;
use Contao\Email;
use Contao\Environment;
use Contao\Input;
use Contao\System;

/**
 * Class JobsSupport.
 */
class Support extends BackendModule
{
    /**
     * Template.
     */
    protected $strTemplate = 'be_jobs_support';

    /**
     * Generate the module.
     */
    protected function compile(): void
    {
        System::loadLanguageFile('jobs_support');

        $GLOBALS['TL_CSS'][] = 'bundles/brkwskyjobs/backend.css';

        $this->Template->href = $this->Environment->request;

        if ('jobs_support' === Input::get('do')) {
            $this->Template->href = $this->getReferer(true);
            $this->Template->title = specialchars($GLOBALS['TL_LANG']['MSC']['backBT']);
            $this->Template->button = $GLOBALS['TL_LANG']['MSC']['backBT'];

            $strFormId = 'job_support';

            $this->Template->action = ampersand(Environment::get('request'));
            $this->Template->formId = $strFormId;

            if (Input::post('FORM_SUBMIT') === $strFormId) {
                if (Input::post('message')) {
                    $this->import('BackendUser', 'User');

                    $objEmail = new Email();
                    $objEmail->from = $this->User->email;
                    $objEmail->fromName = $this->User->name;
                    $objEmail->subject = sprintf(
                        $GLOBALS['TL_LANG']['JOBS_SUPPORT_MAIL']['subject'],
                        $this->User->name
                    );

                    $request = (Input::post('subject')) ? $GLOBALS['TL_LANG']['JOBS_SUPPORT'][Input::post('subject')] : '-';

                    $objEmail->text = sprintf(
                        $GLOBALS['TL_LANG']['JOBS_SUPPORT_MAIL']['text'],
                        $request,
                        Input::post('message'),
                        $this->User->name,
                        $this->User->email,
                        Environment::get('host')
                    );

                    $objEmail->sendTo(['support@jobboerse-software.de']);

                    $this->Template->content = $GLOBALS['TL_LANG']['JOBS_SUPPORT']['sended'];
                }
            }
        }
    }
}
