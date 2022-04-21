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
    public static function canAccessBackendRoute($name): bool
    {
        $user = BackendUser::getInstance();

        if ($user->isAdmin) {
            return true;
        }
        if (!empty($user->groups)) {
            foreach ($user->groups as $groupId) {
                $group = UserGroupModel::findByIdOrAlias($groupId);
                $jobsBasicSettings = StringUtil::deserialize($group->plenta_jobs_basic_settings);
                if (\is_array($jobsBasicSettings) && \in_array($name, $jobsBasicSettings, true)) {
                    return true;
                }
            }
        }

        return false;
    }
}
