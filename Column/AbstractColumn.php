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

use bar\baz\source_with_namespace;
use Cwd\FancyGridBundle\Grid\Exception\InvalidArgumentException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AbstractColumn
 * @package Cwd\FancyGridBundle\Column
 * @author Ludwig Ruderstaler <lr@cwd.at>
 */
abstract class AbstractColumn implements ColumnInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */

    protected $field;
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * AbstractColumn constructor.
     * @param string $name
     * @param string $field
     * @param array  $options
     */
    public function __construct($name, $field, array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
        $this->name    = $name;
        $this->field   = $field;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
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
            ]
        );
    }

    /**
     * @param \Twig_Environment $twig
     * @param string            $template
     * @param array             $options
     *
     * @return string
     */
    protected function renderTemplate(\Twig_Environment $twig, $template, $options)
    {
        $options = array_merge($options, $this->getOptions());

        return $twig->render($template, $options);
    }

    /**
     * set defaults options
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'hidden' => false,
            'cellAlign' => 'left',
            'draggable' => false,
            'editable' => false,
            'flex' => 1,
            'index' => '',
            'lockable' => false,
            'locked' => false,
            'maxWidth' => null,
            'width' => null,
            'title' => null,
            'sortable' => true,
            'searchable' => true,
            'type' => 'string',
            'ellipsis' => true,
            'filter' => [
                'header' => true,
                'headerNote' => true,
            ],

            'translation_domain' => null,
            'translatable' => false,
            'attr' => array(),
            'template' => null,

            // Legacy
            'identifier' => null,
            'label' => null,
            'visible' => null,
        ));

        $resolver->setAllowedTypes('attr', 'array');
    }

    /**
     * render options as data-* string
     * @return string
     */
    public function renderOptions()
    {
        $printOptions = [
            'index' => $this->getName(),
        ];

        if ($this->getOption('visible')) {
            $printOptions['hidden'] = $this->getOption('visible');
        }

        if ($this->getOption('label')) {
            $printOptions['title'] = $this->translator->trans($this->getOption('label'), $this->getOptions('translation_domain'));
        }

        $options = $this->options;

        foreach ($options as $key => $value) {
            // Ignore this options they are used differently
            if (in_array($key, ['attr', 'template', 'header_align', 'format','label', 'translation_domain', 'translatable', 'visible', 'identifier', 'index'])) {
                continue;
            }

            // if null we dont need to print the option
            if (null === $value) {
                continue;
            }

            //if (is_bool($value)) {
                //   $value = ($value) ? 'true' : 'false';
            //}

            //$printOptions['data-'.str_replace('_', '-', $key)] = $value;
            $printOptions[$key] = $value;
        }

        //$dataAttributes = array_map(function ($value, $key) {
        //    return sprintf('%s="%s"', $key, $value);
        //}, array_values($printOptions), array_keys($printOptions));

        return $printOptions;
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
        /** Special case handling for e.g. count() */
        if (is_array($object) && isset($object[$field])) {
            return $object[$field];
        } elseif (is_array($object)) {
            $object = $object[0];
        }

        if (!$accessor->isReadable($object, $field)) {
            // if not found, try to strip alias.
            if (strstr($field, '.')) {
                $field = substr($field, strpos($field, '.')+1);
            }
        }

        if (!$accessor->isReadable($object, $field)) {
            /*
            throw new InvalidArgumentException(
                sprintf(
                    'The Field "%s" could not be found in Object of type "%s"',
                    $field,
                    get_class($object)
                )
            );
            */
            return null;
        }

        return $accessor->getValue($object, $field);
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
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }


}
