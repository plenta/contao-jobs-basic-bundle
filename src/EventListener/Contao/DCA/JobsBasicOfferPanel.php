<?php

namespace Plenta\ContaoJobsBasic\EventListener\Contao\DCA;

use Symfony\Component\HttpFoundation\RequestStack;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;

class JobsBasicOfferPanel
{
    public function __construct(private readonly RequestStack $request)
    {
    }

    #[AsCallback(table: 'tl_plenta_jobs_basic_offer', target: 'list.sorting.panel_callback.job_filter')]
    public function filterPanel(): string
    {
        if ($this->request->getCurrentRequest()->query->get('id') > 0) {
            return '';
        }

        $strBuffer = '
<div class="tl_filter tl_subpanel">
<strong>Filter: </strong>' . "\n";

        // Generate filters

            $strOptions = '
  <option value="Stellenarten">Stellenarten</option>
  <option value="Stellenarten">---</option>
                <option value="1">111</option>
    <option value="1">111</option>
                '."\n";

            // Alle Typen aus Jobs holen

        //$strOptions .'<option value="1">111</option>'."\n";
        //$strOptions .'<option value="2">222</option>'."\n";

        $strBuffer .= '<select name="name" id="name" class="tl_select">'.$strOptions.'</select>'."\n";

        return $strBuffer . '</div>';
    }
}