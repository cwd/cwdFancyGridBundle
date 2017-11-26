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
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Class BootgridExtension
 * @package Cwd\BootgridBundle\Twig
 * @author Ludwig Ruderstaler <lr@cwd.at>
 */
class FancyGridExtension extends \Twig_Extension
{
    protected $jsOptions = [];
    protected $router;
    protected $license;

    public function __construct(Router $router, $options = [])
    {
        if (!isset($options['js_options'])) {
            $options['js_options'] = [];
        }
        if (isset($options['license'])) {
            $this->license = $options['license'];
        }
        $this->jsOptions = $options['js_options'];
        $this->router = $router;
    }

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
        $options = array_merge($options, $this->jsOptions);
        $options['renderTo'] = $grid->getId();
        $options['paging'] = [
            'pageSize' => $grid->getOption('limit'),
            'pageSizeData' => $grid->getOption('pageSizeData')
        ];
        $options['columns'] = $grid->getColumnDefinition();
        $options['data'] = [
            'remoteSort' => true,
            'remoteFilter' => true,
            'proxy' => [
                'url' => $this->router->generate($grid->getOption('data_route'), $grid->getOption('data_route_options')),
            ],
        ];


        return $twig->render('CwdFancyGridBundle::grid.html.twig', [
            'grid' => $grid,
            'jsOptions' => $options,
            'license' => $this->license
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

