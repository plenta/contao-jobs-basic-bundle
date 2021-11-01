<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\EventListener\Contao\DCA;

use Contao\DataContainer;
use Contao\StringUtil;
use Plenta\ContaoJobsBasic\Helper\EmploymentType;
use Symfony\Contracts\Translation\LocaleAwareInterface;

class TlPlentaJobsBasicOffer
{
    protected EmploymentType $employmentTypeHelper;

    protected $translator;

    public function __construct(EmploymentType $employmentTypeHelper, LocaleAwareInterface $translator)
    {
        $this->employmentTypeHelper = $employmentTypeHelper;
        $this->translator = $translator;
    }

    public function employmentTypeOptionsCallback(): array
    {
        $employmentTypes = $this->employmentTypeHelper->getEmploymentTypes();

        $return = [];
        foreach ($employmentTypes as $employmentType) {
            $employmentTypeName = $this->employmentTypeHelper->getEmploymentTypeName($employmentType);
            if (null === $employmentTypeName) {
                $employmentTypeName = $employmentType;
            }

            $return[$employmentType] = $this->translator->trans(
                'MSC.PLENTA_JOBS.'.$employmentTypeName,
                [],
                'contao_default'
            );
        }

        return $return;
    }

    public function employmentTypeSaveCallback($value, DataContainer $dc): string
    {
        $value = StringUtil::deserialize($value);

        return json_encode($value);
    }

    public function employmentTypeLoadCallback($value, DataContainer $dc): string
    {
        if (null === $value) {
            return serialize([]);
        }

        return serialize(json_decode($value));
    }
}
