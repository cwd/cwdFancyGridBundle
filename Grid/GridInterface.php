<?php
/*
 * This file is part of mailowl
 *
 * (c)2016 cwd.at GmbH <office@cwd.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cwd\FancyGridBundle\Grid;

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
     * @return string
     */
    public function getData();

    /**
     * @param string $name
     * @return ColumnInterface
     */
    public function get($name);

    public function getOption($name, $default = null);

    public function getColumnDefinition();
}
