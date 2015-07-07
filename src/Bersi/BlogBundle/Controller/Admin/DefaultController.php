<?php

namespace Bersi\BlogBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{

    /**
     * @Route("/admin", name="admin_bersi_blog")
     */
    public function indexAction()
    {
        return $this->render(':Admin:layout.html.twig');
    }

    /**
     * @Route("/admin/menu", name="admin_bersi_blog_menu")
     */
    public function menuAction()
    {
        $entities = array();
        $meta = $this->getDoctrine()->getManager()->getMetadataFactory()->getAllMetadata();
        foreach ($meta as $m) {
            $entities[] = substr(strrchr($m->name, '\\'), 1);
        }
        sort($entities);
        return $this->render('BersiBlogBundle:Admin:menu.html.twig', array(
            'menus' => $entities
        ));
    }

}
