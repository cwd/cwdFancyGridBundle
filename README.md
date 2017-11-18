cwdBootgridBundle
=================

[![Dependency Status](https://gemnasium.com/badges/2b767ec07fb4a790dfd95c58bce7cc97.svg)](https://gemnasium.com/dcbd96e51f8a1445237e7fa436b9c410)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/gp/cwdBootgridBundle/badges/quality-score.png?b=master&s=9cf74ab462ac37c55557b30aa809e92217809233)](https://scrutinizer-ci.com/gp/cwdBootgridBundle/?branch=master)

This bundle integrates the JQuery Bootgrid into Symfony. see http://www.jquery-bootgrid.com/ for details.

![screenshot](Resources/public/screenshot.png "Screenshot")

Installation
------------

`composer require cwd/bootgrid-bundle`

Add to AppKernel.php:
```
[...]
new Cwd\FancyGridBundle\CwdFancyGridBundle(),
[...]
```

Or bundels.php:
```
[...]
'Cwd\FancyGridBundle\CwdFancyGridBundle' => ['all' => true],
[...]
```

Add to config.yml (you can use all javascript options see https://fancygrid.com/api/config):
```
cwd_fancy_grid:
  js_options:
    theme: 'bootstrap'
    height: 'fit'
    trackOver: true
    i18n: '%locale%'
    menu: true
    selModel: 'row'
    striped: true
    columnLines: false
    searching: true
    textSelection: true
    refreshButton: true
    defaults:
      resizable: true
      sortable: true
      menu: true
```

Create your first Grid:
```
<?php
namespace AppBundle\Grid;

use Cwd\FancyGridBundle\Column\NumberType;
use Cwd\FancyGridBundle\Column\TextType;
use Cwd\FancyGridBundle\Grid\AbstractGrid;
use Cwd\FancyGridBundle\Grid\GridBuilderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserGrid extends AbstractGrid
{
    /**
     * @param GridBuilderInterface $builder
     * @param array                $options
     */
    public function buildGrid(GridBuilderInterface $builder, array $options)
    {
        $builder->add(new NumberType('id', 'u.id', ['label' => 'ID', 'identifier' => true]))
                ->add(new TextType('firstname', 'u.firstname', ['label' => 'Firstname']))
                ->add(new TextType('lastname', 'u.lastname', ['label' => 'Lastname']))
                ->add(new TextType('email', 'u.email', ['label' => 'Email']));

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'default_sorts' => array('u.id' => false),
            'data_route' => 'app_user_ajaxdata',
        ));
    }


    /**
     * @param ObjectManager $objectManager
     * @param array         $params
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder(ObjectManager $objectManager, array $params = [])
    {
        $qb = $objectManager
            ->getRepository('AppBundle\Model\User')
            ->createQueryBuilder('u')
            ->orderBy('u.lastname', 'ASC');

        return $qb;
    }
}
```

Add the controller actions:
```
<?php
class UserController {
    [...]
      
    /**
    * @param Request $request
    *
    * @Route("/list/data")
    * @Method({"GET", "POST"})
    * @return JsonResponse
    */
    public function ajaxDataAction(Request $request)
    {
       $grid = $this->getGrid($request->request->all());
       $data = $grid->getData();
    
       return new JsonResponse($data);
    }
       
    /**
     * @Route("/list")
     * @Route("/")
     * @Method({"GET"})
     * @Template("AppBundle:Grid:list.html.twig")
     * Security("has_role('ROLE_ADMIN')")
     *
     * @return array
     */
    public function listAction()
    {
        $grid = $this->getGrid();

        return array(
            'grid'        => $grid,
        );
    }  
         
    /**
     * @param array $options
     *
     * @return GridBuilderInterface
     */
    protected function getGrid(array $options = [])
    {
        return $this->get('cwd_fancygrid.grid.factory')->create(UserGrid::class), $options);
    }     
```

And the view:
```
<div id="{{ grid.id }}" style="width:99%"></div>
<script type="text/javascript">
    {{ fancygrid(grid) }}
</script>

{# Add the Javascript and CSS files (after jQuery is loaded) #}
<script type="text/javascript" src="https://cdn.fancygrid.com/fancy.full.min.js"></script>
<link rel="stylesheet" href="https://code.fancygrid.com/fancy.min.css" />    
```
