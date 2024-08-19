<?php

namespace Module\IndexBoxes\Grid\Filters;

use Module\IndexBoxes\Grid\Definition\Factory\IndexBoxGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

class IndexBoxFilters extends Filters
{
    protected $filterId = IndexBoxGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 10,
            'offset' => 0,
            'orderBy' => 'position',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
