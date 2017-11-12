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

use Cwd\FancyGridBundle\Grid\Exception\InvalidArgumentException;
use Cwd\FancyGridBundle\Grid\Exception\UnexpectedTypeException;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class GridFactory
 * @package Cwd\FancyGridBundle\Grid
 * @author Ludwig Ruderstaler <lr@cwd.at>
 */
class GridFactory
{
    /**
     * @var ManagerRegistry
     */
    protected $doctrineRegistry;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * GridFactory constructor.
     * @param TranslatorInterface $translator
     * @param ManagerRegistry     $doctrineRegistry
     * @param \Twig_environment   $twig
     */
    public function __construct(
        TranslatorInterface $translator,
        ManagerRegistry $doctrineRegistry,
        \Twig_Environment $twig
    ) {
        $this->doctrineRegistry = $doctrineRegistry;
        $this->translator = $translator;
        $this->twig = $twig;
    }

    /**
     * @param string $type
     * @param array  $options
     * @return GridInterface
     */
    public function create($type = 'Cwd\FancyGridBundle\Grid\AbstractGrid', array $options = array())
    {
        if (!is_string($type)) {
            throw new UnexpectedTypeException($type, 'string');
        }

        $type = $this->getType($type, $options);
        $builder = new GridBuilder($this->doctrineRegistry->getManager(), new EventDispatcher(), $options);

        $type->buildGrid($builder, array_merge($type->getOptions(), $options));
        $type->setChildren($builder->children);


        return $type;
    }

    /**
     * @param string $name
     * @param array  $options
     * @return GridInterface
     */
    public function getType($name, $options)
    {
        if (class_exists($name) && in_array('Cwd\FancyGridBundle\Grid\GridInterface', class_implements($name))) {
            $type = new $name($this->translator, $options);
            $type->setObjectManager($this->doctrineRegistry->getManager());
            $type->setTwig($this->twig);
        } else {
            throw new InvalidArgumentException(sprintf('Could not load type "%s"', $name));
        }

        return $type;
    }


}
