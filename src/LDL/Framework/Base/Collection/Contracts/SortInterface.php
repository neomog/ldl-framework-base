<?php declare(strict_types=1);

namespace LDL\Framework\Base\Collection\Contracts;

use LDL\Framework\Base\Constants;

interface SortInterface
{
    /**
     * Returns a new instance, sorted by value
     *
     * @param string $order
     * @param bool $sortByKey
     * @return CollectionInterface
     */
    public function sort(string $sort, string $order): CollectionInterface;

    /**
     * Returns a new instance, sorted by value through an anonymous comparison function
     *
     * @param callable $fn
     * @return CollectionInterface
     */
    public function sortByCallback(callable $fn) : CollectionInterface;
}