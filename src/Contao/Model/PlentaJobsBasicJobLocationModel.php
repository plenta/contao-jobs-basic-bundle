<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Contao\Model;

use Contao\Model;

class PlentaJobsBasicJobLocationModel extends Model
{
    protected static $strTable = 'tl_plenta_jobs_basic_job_location';

    public static function findByMultiplePids(array $pids)
    {
        $criteria = array_fill(0, count($pids), 'tl_plenta_jobs_basic_job_location.pid = ?');

        $cols = ['(' . implode(' OR ', $criteria) . ')'];

        return self::findBy($cols, $pids);
    }
}
