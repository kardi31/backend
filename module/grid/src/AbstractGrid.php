<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types = 1);

namespace Ergonode\Grid;

use Ergonode\Core\Domain\ValueObject\Language;

/**
 */
abstract class AbstractGrid
{
    public const PARAMETER_ALLOW_COLUMN_RESIZE = 'allow_column_resize';
    public const PARAMETER_ALLOW_COLUMN_EDIT = 'allow_column_edit';
    public const PARAMETER_ALLOW_COLUMN_MOVE = 'allow_column_move';
    public const CONFIGURATION_SHOW_DATA = 'DATA';
    public const CONFIGURATION_SHOW_COLUMN = 'COLUMN';
    public const CONFIGURATION_SHOW_INFO = 'INFO';
    public const CONFIGURATION_SHOW_CONFIGURATION = 'CONFIGURATION';

    public const DEFAULT = [
        self::PARAMETER_ALLOW_COLUMN_RESIZE => false,
        self::PARAMETER_ALLOW_COLUMN_EDIT => false,
        self::PARAMETER_ALLOW_COLUMN_MOVE => false,
    ];

    /**
     * @var ColumnInterface[]
     */
    protected $columns = [];

    /**
     * @var ActionInterface[]
     */
    private $actions = [];

    /**
     * @var array
     */
    private $configuration = [];

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $order = 'ASC';

    /**
     * @param GridConfigurationInterface $configuration
     * @param Language                   $language
     */
    abstract public function init(GridConfigurationInterface $configuration, Language $language): void;

    /**
     * @param string          $id
     * @param ColumnInterface $column
     */
    public function addColumn(string $id, ColumnInterface $column): void
    {
        $this->columns[$id] = $column;
    }

    /**
     * @param string $field
     * @param string $order
     */
    public function orderBy(string $field, string $order): void
    {
        $this->field = $field;
        $this->order = $order;
    }

    /**
     * @param string      $name
     * @param string|bool $value
     */
    public function setConfiguration(string $name, $value): void
    {
        $this->configuration[$name] = $value;
    }

    /**
     * @param string          $name
     * @param ActionInterface $action
     */
    public function addAction(string $name, ActionInterface $action): void
    {
        $this->actions[$name] = $action;
    }

    /**
     * @return array
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @return ColumnInterface[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getConfiguration(): array
    {
        return array_merge(self::DEFAULT, $this->configuration);
    }
}
