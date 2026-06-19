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

    /**
     * @var array<string>
     */
    private array $columns;

    /**
     * @var array<mixed>
     */
    private array $values;

    /**
     * @var array<mixed>
     */
    private array $options;

    private bool $applyRequestFilters = false;

    private ModuleModel|null $model = null;

    /**
     * @return array<string>
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param array<string> $columns
     */
    public function setColumns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param array<mixed> $values
     */
    public function setValues(array $values): self
    {
        $this->values = $values;

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array<mixed> $options
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

    public function setApplyRequestFilters(bool $applyRequestFilters): self
    {
        $this->applyRequestFilters = $applyRequestFilters;

        return $this;
    }

    public function getModel(): ModuleModel|null
    {
        return $this->model;
    }

    public function setModel(ModuleModel|null $model): self
    {
        $this->model = $model;

        return $this;
    }
}
