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
use Cwd\FancyGridBundle\Grid\Exception;
use Cwd\FancyGridBundle\Grid\GridBuilderInterface;
use Cwd\FancyGridBundle\Grid\GridInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class GridBuilder
 * @package Cwd\FancyGridBundle\Grid\Exception
 * @author Ludwig Ruderstaler <lr@cwd.at>
 */
class GridBuilder implements GridBuilderInterface, \IteratorAggregate
{
    protected $dispatcher;
    public $options;

    /**
     * The children of the grid builder.
     *
     * @var ColumnInterface[]
     */
    public $children = array();

    /**
     * @var ObjectManager
     */
    public $doctrine;

    /**
     * @var array
     */
    protected $data;

    /**
     * Creates an empty form configuration.
     *
     * @param ObjectManager            $doctrine   The EntityManager
     * @param EventDispatcherInterface $dispatcher The event dispatcher
     * @param array                    $options    The form options
     *
     * @throws InvalidArgumentException If the data class is not a valid class or if
     *                                  the name contains invalid characters.
     */
    public function __construct(ObjectManager $doctrine, EventDispatcherInterface $dispatcher, array $options = array())
    {
        $this->dispatcher = $dispatcher;
        $this->options = $options;
        $this->doctrine = $doctrine;
    }

    /**
     * @param ColumnInterface $child
     * @param null            $type
     * @param array           $options
     * @return $this
     */
    public function add(ColumnInterface $child, $type = null, array $options = array())
    {
        $this->children[$child->getName()] = $child;

        return $this;
    }

    /**
     * @param string $name
     * @return ColumnInterface
     */
    public function get($name)
    {
        if (isset($this->children[$name])) {
            return $this->children[$name];
        }

        throw new InvalidArgumentException(sprintf('The child with the name "%s" does not exist.', $name));
    }

    /**
     * @param string $name
     * @return $this
     */
    public function remove($name)
    {
        unset($this->children[$name]);

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->children[$name]);
    }

    /**
     * @return \Cwd\FancyGridBundle\Column\ColumnInterface[]
     */
    public function all()
    {
        return $this->children;
    }

    /**
     * @return GridBuilder
     */
    protected function getGridConfig()
    {
        $config = clone $this;

        return $config;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->children);
    }

    /**
     * {@inheritdoc}
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->all());
    }


    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * @param string      $name
     * @param string|null $default
     * @return misc
     */
    public function getOption($name, $default = null)
    {
        return array_key_exists($name, $this->options) ? $this->options[$name] : $default;
    }

}
