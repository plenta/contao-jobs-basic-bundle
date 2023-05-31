<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Helper;

use Contao\BackendUser;
use Contao\StringUtil;
use Contao\UserGroupModel;

class PermissionsHelper
{
    public static function canAccessModule($name, $module = 'plenta_jobs_basic_settings'): bool
    {
        $user = BackendUser::getInstance();

        if ($user->isAdmin) {
            return true;
        }
        if (!empty($user->groups)) {
            foreach ($user->groups as $groupId) {
                $group = UserGroupModel::findByIdOrAlias($groupId);
                $moduleArr = StringUtil::deserialize($group->{$module});
                if (\is_array($moduleArr) && \in_array($name, $moduleArr, true)) {
                    return true;
                }
            }
        }

        return false;
    }
}
