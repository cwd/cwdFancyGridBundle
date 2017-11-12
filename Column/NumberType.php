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

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class NumberType
 * @package Cwd\FancyGridBundle\Column
 * @author Ludwig Ruderstaler <lr@cwd.at>
 */
class NumberType extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'align' => 'right',
            'header_align' => 'right',
        ));

        $resolver->setAllowedTypes('attr', 'array');
    }
}
