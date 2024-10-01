<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Events\Model;

use Contao\ModuleModel;
use Symfony\Contracts\EventDispatcher\Event;

class FindAllPublishedByTypesAndLocationEvent extends Event
{
    public const NAME = 'plenta_jobs_basic.model.find_all_published_by_types_and_location';

    private array $columns;

    private array $values;

    private array $options;

    private bool $applyRequestFilters = false;

    private ?ModuleModel $model = null;

    public function __construct()
    {
    }

    /**
    * @return array
    */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param array $columns
     *
     * @return FindAllPublishedByTypesAndLocationEvent
     */
    public function setColumns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param array $values
     *
     * @return FindAllPublishedByTypesAndLocationEvent
     */
    public function setValues(array $values): self
    {
        $this->values = $values;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return FindAllPublishedByTypesAndLocationEvent
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function isApplyRequestFilters(): bool
    {
        return $this->applyRequestFilters;
    }

    public function setApplyRequestFilters(bool $applyRequestFilters): FindAllPublishedByTypesAndLocationEvent
    {
        $this->applyRequestFilters = $applyRequestFilters;
        return $this;
    }

    public function getModel(): ?ModuleModel
    {
        return $this->model;
    }

    public function setModel(?ModuleModel $model): FindAllPublishedByTypesAndLocationEvent
    {
        $this->model = $model;
        return $this;
    }
}
