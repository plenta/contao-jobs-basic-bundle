<?php

declare(strict_types=1);

/*
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2026, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Helper;

use Contao\ModuleModel;

class SortingHelper
{
    /**
     * @param  array<array<string, mixed>> $options
     * @return array<array<string, mixed>>
     */
    public function sort(array $options, ModuleModel $model): array
    {
        $return = [];

        if ($sorting = $model->plentaJobsBasic_filterSort) {
            usort(
                $options,
                static fn ($a, $b) => match ($sorting) {
                    'az' => strnatcasecmp((string) $a['name'], (string) $b['name']),
                    'za' => strnatcasecmp((string) $b['name'], (string) $a['name']),
                    '09' => $a['count'] <=> $b['count'],
                    '90' => $b['count'] <=> $a['count'],
                    default => 0,
                },
            );
        }

        foreach ($options as $option) {
            $return[$option['id']] = $option['name'];
        }

        return $return;
    }
}
