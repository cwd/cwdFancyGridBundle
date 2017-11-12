<?php
/*
 * This file is part of CwdBootgridBundle
 *
 * (c)2016 cwd.at GmbH <office@cwd.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cwd\FancyGridBundle\Grid;

use Cwd\FancyGridBundle\Column\AbstractColumn;
use Cwd\FancyGridBundle\Column\ColumnInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\QueryBuilder;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AbstractGrid
 * @package Cwd\FancyGridBundle\Grid
 * @author Ludwig Ruderstaler <lr@cwd.at>
 */
abstract class AbstractGrid implements GridInterface, \IteratorAggregate
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected $children = [];

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessor
     */
    protected $accessor;

    /**
     * @var null|string
     */
    protected $primary = null;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * AbstractGrid constructor.
     * @param array $options
     */
    public function __construct(TranslatorInterface $translator, array $options = array())
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
        $this->translator = $translator;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @param \Twig_Environment $twig
     */
    public function setTwig(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param ObjectManager $objectManager
     * @return $this
     */
    public function setObjectManager($objectManager)
    {
        $this->objectManager = $objectManager;

        return $this;
    }

    /**
     * generate gridid
     * @return string
     */
    public function getId()
    {
        $data = [
            $this->getOption('data_route'),
            $this->getOption('data_route_options'),
            $this->getOption('template'),
        ];

        return md5(serialize($data));
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function buildGrid(GridBuilderInterface $builder, array $options)
    {
    }

    /**
     * @return array
     */
    public function getData()
    {
        $queryBuilder = $this->getQueryBuilder($this->objectManager, $this->getOptions());

        if ($this->getOption('sortField') !== null) {
            $field = $this->getOption('sortField');
            if ($this->has($field)) {
                $column = $this->get($field);
                $queryBuilder->orderBy($column->getField(), $this->getOption('sortDir'));
            }
        }

        if ($this->getOption('filter', false)) {
            $this->addSearch($queryBuilder, $this->all());
        }

        $pager = $this->getPager($queryBuilder);

        return [
            //'current' => $pager->getCurrentPage(),
            //'rowCount' => $this->getOption('rowCount', 10),
            'totalCount' => $pager->getNbResults(),
            'data'  => $this->parseData($pager->getCurrentPageResults()),
            'success' => true,
        ];
    }

    /**
     * @param \Doctrine\DBAL\Query\QueryBuilder      $queryBuilder
     * @param ColumnInterface[] $columns
     */
    protected function addSearch($queryBuilder, $columns)
    {

        $filter = json_decode($this->getOption('filter'));
        $where = $queryBuilder->expr()->andX();

        foreach ($filter as $filterSearch) {
            if (!$this->has($filterSearch->property)) {
                continue;
            }

            $column = $this->get($filterSearch->property);

            if ($filterSearch->operator == 'like') {
                $where->add($queryBuilder->expr()->like($column->getField(), ':'.$filterSearch->property));
                $queryBuilder->setParameter(':'.$filterSearch->property, '%'.$filterSearch->value.'%');
            }

        }

        if (count($where->getParts()) > 0) {
            $queryBuilder->having($where);
        }
    }

    /**
     * @param array|\Traversable $rows
     * @return array
     */
    protected function parseData($rows)
    {
        $data = [];
        foreach ($rows as $row) {
            $rowData = [];

            foreach ($this->all() as $column) {
                /** @var ColumnInterface $column */
                $value = $column->getValue($row, $column->getName(), $this->findPrimary(), $this->accessor);
                $value = $column->render($value, $row, $this->getPrimaryValue($row), $this->twig);

                if ($column->getOption('translatable', false)) {
                    $value = $this->translator->trans($value, [], $column->getOption('translation_domain'));
                }

                $rowData[$column->getName()] = $value;
            }

            $data[] = $rowData;
        }

        return $data;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return Pagerfanta
     */
    public function getPager(QueryBuilder $queryBuilder)
    {
        $adapter = new DoctrineORMAdapter($queryBuilder, false);
        $pager = new Pagerfanta($adapter);
        $page = $this->getOption('page', 1);
        if ($page < 1) {
            $page = 1;
        }

        $pager->setCurrentPage($page)
              ->setMaxPerPage($this->getOption('limit', 10));

        return $pager;
    }

    /**
     * Get value of primary column
     *
     * @param mixed $object
     *
     * @return mixed
     */
    public function getPrimaryValue($object)
    {
        if ($this->primary === null) {
            $this->primary = $this->findPrimary();
        }

        /** special case when counting */
        if (is_array($object)) {
            $object = $object[0];
        }

        return $this->accessor->getValue($object, $this->primary);
    }

    /**
     * @return null|string
     */
    public function findPrimary()
    {
        foreach ($this->all() as $column) {
            if (true === $column->getOption('identifier')) {
                return $column->getName();
            }
        }

        return null;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'template' => 'CwdBootgridBundle:Grid:template.html.twig',
            'current' => 1,
            'pageSizeData' => [10, 25, 50, -1],
            'filter' => null,
            'sortField' => null,
            'sortDir' => null,
            'data_route_options' => [],
            'page' => 1,
            'limit' => 20,
        ]);

        $resolver->setRequired([
            'template',
            'data_route',
        ]);
    }

    public function getJsonColumns()
    {
        $columns = [];
        /** @var AbstractColumn $column */
        foreach ($this->children as $column) {
            $column->setTranslator($this->translator);
            $columns[] = $column->renderOptions();
        }

        return json_encode($columns);
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

    public function renderColumnOptions()
    {
        foreach ($this->get as $column) {

        }
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
     * @param array<ColumnInterface> $children
     * @return $this
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->all());
    }
}
