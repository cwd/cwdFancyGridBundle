<?php
/*
 * This file is part of cwdBootgridBundle
 *
 * (c)2016 cwd.at GmbH <office@cwd.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cwd\FancyGridBundle\Twig;
use Cwd\FancyGridBundle\Grid\GridInterface;

/**
 * Class BootgridExtension
 * @package Cwd\BootgridBundle\Twig
 * @author Ludwig Ruderstaler <lr@cwd.at>
 */
class FancyGridExtension extends \Twig_Extension
{
    /**
     * @return \Twig_SimpleFunction[]
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('fancygrid', [$this, 'fancygrid'], [
                'needs_environment' => true,
                'is_safe' => ['html'],
            ]),
        );
    }

    /**
     * @param \Twig_Environment $twig
     * @param GridInterface     $grid
     * @param array             $options
     *
     * @return string
     */
    public function fancygrid($twig, $grid, array $options = [])
    {
        return $twig->render('CwdFancyGridBundle::grid.html.twig', [
            'grid' => $grid,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cwd_fancygrid.twig_extension.fancygrid';
    }
}

