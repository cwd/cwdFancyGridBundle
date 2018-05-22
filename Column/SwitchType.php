<?php

namespace Cwd\FancyGridBundle\Column;

use Symfony\Component\OptionsResolver\OptionsResolver;

class SwitchType extends TextType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'route' => null,
            'attr' => [
                'class' => 'bootstrap-switch',
                'data-on-text' => 'generic.yes',
                'data-off-text' => 'generic.no',
                'data-size' => 'mini',
            ],
            'template' => '@CwdFancyGrid/Column/switch.html.twig'
        ]);

        $resolver->setRequired('route');
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
            return $value;
        }

        return $this->renderTemplate(
            $twig,
            $this->getOption('template'),
            [
                'value'   => $value,
                'object'  => $object,
                'primary' => $primary,
                'attributes' => $this->getOption('attr'),
                'route' => $this->getOption('route'),
            ]
        );
    }
}