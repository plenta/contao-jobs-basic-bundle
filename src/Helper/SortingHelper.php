<?php

namespace Plenta\ContaoJobsBasic\Helper;

class SortingHelper
{
    public function sort($options, $model): array
    {
        $return = [];

        if ($sorting = $model->plentaJobsBasic_filterSort) {
            usort($options, function ($a, $b) use ($sorting) {
                return match ($sorting) {
                    'az' => strnatcasecmp($a['name'], $b['name']),
                    'za' => strnatcasecmp($b['name'], $a['name']),
                    '09' => $a['count'] <=> $b['count'],
                    '90' => $b['count'] <=> $a['count'],
                };
            });
        }

        foreach ($options as $option) {
            $return[$option['id']] = $option['name'];
        }

        return $return;
    }
}