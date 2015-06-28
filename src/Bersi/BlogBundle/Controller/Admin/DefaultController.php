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

}
