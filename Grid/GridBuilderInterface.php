<?php
/*
 * This file is part of cwdBootgridBundle
 *
 * (c)2016 cwd.at GmbH <office@cwd.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cwd\FancyGridBundle\Grid;
use Cwd\FancyGridBundle\Column\ColumnInterface;

/**
 * Interface GridBuilderInterface
 * @package Cwd\FancyGridBundle\Grid
 */
interface GridBuilderInterface extends \Countable
{
    /**
     * Adds a new field to this group. A field must have a unique name within
     * the group. Otherwise the existing field is overwritten.
     *
     * @param ColumnInterface $child
     * @param null            $type
     * @param array           $options
     * @return $this
     */
    public function add(ColumnInterface $child, $type = null, array $options = array());

    /**
     * Returns a child by name.
     *
     * @param string $name The name of the child
     *
     * @return ColumnInterface
     *
     * @throws Cwd\FancyGridBundle\Grid\Exception\InvalidArgumentException if the given child does not exist
     */
    public function get($name);

    /**
     * Removes the field with the given name.
     *
     * @param string $name
     *
     * @return GridBuilderInterface The builder object
     */
    public function remove($name);

    /**
     * Returns whether a field with the given name exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name);

    /**
     * Returns the children.
     *
     * @return array
     */
    public function all();
}
