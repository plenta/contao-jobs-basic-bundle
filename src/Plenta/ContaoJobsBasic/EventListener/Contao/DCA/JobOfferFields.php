<?php

namespace Plenta\ContaoJobsBasic\EventListener\Contao\DCA;

class JobOfferFields
{
    public static function getFields()
    {
        return ['title', 'tstamp', 'datePosted'];
    }
}