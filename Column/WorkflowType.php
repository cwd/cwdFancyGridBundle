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
 * Class WorkflowType.
 */
class WorkflowType extends ActionType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'transition' => [],
            'template' => '@CwdFancyGrid/Column/workflow_actions.html.twig',
        ]);

        $resolver->setAllowedTypes('transition', 'array');
    }
}
