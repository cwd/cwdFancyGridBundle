<?php
/*
 * This file is part of cwdFancyGridBundle
 *
 * (c)2017 cwd.at GmbH <office@cwd.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);
namespace Cwd\FancyGridBundle\Grid;

use Cwd\FancyGridBundle\Column\ColumnInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Interface GridInterface
 * @package Cwd\FancyGridBundle\Grid
 */
interface GridInterface
{
    /**
     * Builds the Grid.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the grid.
     *
     *
     * @param GridBuilderInterface $builder The form builder
     * @param array                $options The options
     * @return void
     */
    public function buildGrid(GridBuilderInterface $builder, array $options);

    /**
     * @param OptionsResolver $resolver
     * @return $this
     */
    public function configureOptions(OptionsResolver $resolver);

    /**
     * @param ObjectManager $objectManager
     * @param array         $params
     * @return QueryBuilder
     */
    public function getQueryBuilder(ObjectManager $objectManager, array $params = []);

    /**
     * @return array
     */
    public function getData() : array;

    /**
     * @param string $name
     * @return ColumnInterface
     */
    public function get(string $name) : ColumnInterface;

    public function getOption(string $name, $default = null);
    public function getOptions() : array;

    public function getColumnDefinition();
}
