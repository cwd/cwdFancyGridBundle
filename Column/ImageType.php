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

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ImageType
 * @package Cwd\FancyGridBundle\Column
 * @author Ludwig Ruderstaler <lr@cwd.at>
 */
class ImageType extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'type' => 'image',
            'minListWidth' => null,
            'filter' => [],
            'prefix' => null
        ));

        $resolver->setAllowedTypes('attr', 'array');
    }

    /**
     * @param mixed             $value
     * @param mixed             $object
     * @param mixed             $primary
     * @param \Twig_Environment $twig
     *
     * @return mixed
     */
    public function render($value, $object, $primary, \Twig_Environment $twig)
    {
        /** dont use twig if no template is provided */
        if (null === $this->getOption('template')) {
            return $this->getOption('prefix').$value;
        }

        return $this->renderTemplate(
            $twig,
            $this->getOption('template'),
            [
                'value'   => $this->getOption('prefux').$value,
                'object'  => $object,
                'primary' => $primary,
            ]
        );
    }
}
