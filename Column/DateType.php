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
namespace Cwd\FancyGridBundle\Column;

use Cwd\FancyGridBundle\Grid\Exception\InvalidArgumentException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class NumberType
 * @package Cwd\FancyGridBundle\Column
 * @author Ludwig Ruderstaler <lr@cwd.at>
 */
class DateType extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'align' => 'right',
            'cellAlign' => 'right',
            'format' => [
                'read' => 'Y-m-d H:i:s',
                'write' => 'd.m.Y H:i:s',
                'edit' => 'Y-m-d'
            ],
            'width' => 150,
            'type' => 'date'
        ));

        $resolver->setAllowedTypes('attr', 'array');
    }

    /**
     * @param mixed             $value
     * @param mixed             $object
     * @param mixed             $primary
     * @param \Twig_Environment $twig
     *
     * @return string
     */
    public function render($value, $object, $primary, \Twig_Environment $twig)
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof \DateTime) {
            throw new InvalidArgumentException('%s is not of expected \DateTime', $this->getName());
        }

        return $value->format($this->getOption('format')['read']);
    }
}
