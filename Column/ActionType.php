<?php
/*
 * This file is part of cwdBootgridBundle
 *
 * (c)2016 cwd.at GmbH <office@cwd.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cwd\FancyGridBundle\Column;

use Cwd\FancyGridBundle\Grid\Exception\InvalidArgumentException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class ActionType
 * @package Cwd\FancyGridBundle\Column
 * @author Ludwig Ruderstaler <lr@cwd.at>
 */
class ActionType extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'align' => 'right',
            'searchable' => false,
            'sortable' => false,
            'actions' => [],
            'actions_params' => [],
            'filter' => false,
            'template' => 'CwdFancyGridBundle:Column:actions.html.twig',
            'width' => 200,
            'cls' => 'grid-action',
        ));

        $resolver->setAllowedTypes('actions', 'array');
        $resolver->setAllowedTypes('actions_params', 'array');
    }

    /**
     * @param mixed            $object
     * @param string           $field
     * @param string           $primary
     * @param PropertyAccessor $accessor
     *
     * @return mixed
     */
    public function getValue($object, $field, $primary, $accessor)
    {
        return $accessor->getValue($object, $primary);
    }
}
