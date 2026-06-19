<?php

declare(strict_types=1);

/*
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2026, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Contao\Model;

use Contao\Model;
use Contao\Model\Collection;

class PlentaJobsBasicJobLocationModel extends Model
{
    protected static $strTable = 'tl_plenta_jobs_basic_job_location';

    /**
     * @param  array<int>            $pids
     * @return Collection<self>|null
     */
    public static function findByMultiplePids(array $pids): Collection|null
    {
        $criteria = array_fill(0, \count($pids), 'tl_plenta_jobs_basic_job_location.pid = ?');

        $cols = ['('.implode(' OR ', $criteria).')'];

        return self::findBy($cols, $pids);
    }
}
