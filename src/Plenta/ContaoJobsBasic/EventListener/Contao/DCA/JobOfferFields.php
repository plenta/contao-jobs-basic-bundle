<?php

namespace Plenta\ContaoJobsBasic\EventListener;

class JobOfferFields
{
    public static function getFields()
    {
        return ['title', 'tstamp', 'datePosted'];
    }
}