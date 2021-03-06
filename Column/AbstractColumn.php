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
    public function getName() : ?string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getField() : ?string
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
            'align' => 'left',
            'cellAlign' => 'left',
            'cellTip' => null,
            'cls' => null,
            'draggable' => null,
            'editable' => null,
            'ellipsis' => true,
            'flex' => 1,
            'hidden' => false,
            'index' => null,
            'lockable' => null,
            'locked' => null,
            'maxWidth' => null,
            'menu' => null,
            'minWidth' => null,
            'render' => null,
            'resizable' => null,
            'rightLocked' => null,
            'sortable' => true,
            'title' => null,
            'type' => null,
            'vtype' => null,
            'width' => null,
            'searchable' => true,
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
     * @return array
     */
    public function buildColumnOptions() : array
    {
        $printOptions = [
            // Fancy grid doesnt like . in index name
            'index' => str_replace('.', '_', $this->getName()),
        ];

        // Legacy Mapping
        if ($this->getOption('visible')) {
            $printOptions['hidden'] = $this->getOption('visible');
        }

        if ($this->getOption('label')) {
            $printOptions['title'] = $this->translator->trans($this->getOption('label'), [], $this->getOption('translation_domain'));
        }

        $options = $this->options;

        foreach ($options as $key => $value) {
            // Ignore this options they are used differently
            if (in_array($key, ['attr', 'template', 'header_align','label', 'translation_domain', 'translatable', 'visible', 'identifier', 'index', 'class', 'em', 'query_builder', 'choice_loader'])) {
                continue;
            }

            // if null we dont need to print the option
            if (null === $value) {
                continue;
            }

            $printOptions[$key] = $value;
        }

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
            return null;
        }

        return $accessor->getValue($object, $field);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasOption($name) : bool
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * @param string      $name
     * @param string|null $default
     * @return misc
     */
    public function getOption(string $name, $default = null)
    {
        return array_key_exists($name, $this->options) ? $this->options[$name] : $default;
    }

    /**
     * @return array
     */
    public function getOptions() : array
    {
        return $this->options;
    }

    /**
     * @return TranslatorInterface
     */
    public function getTranslator() : TranslatorInterface
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
