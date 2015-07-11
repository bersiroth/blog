<?php

namespace Bersi\BlogBundle\Controller\Admin;

use Bersi\BlogBundle\Entity\Category;
use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends Controller
{

    /**
     * @Route("/admin/category", name="admin_bersi_blog_category")
     */
    public function categoryAction()
    {
        $category = $this->getDoctrine()->getRepository('BersiBlogBundle:Category')->findAll();
        return $this->render('BersiBlogBundle:Admin:category.html.twig', array(
            'liste_categories' => $category,
        ));
    }

    /**
     * @Route("/admin/category/edit/{id}", name="admin_bersi_blog_edit_category")
     * @Route("/admin/category/add", name="admin_bersi_blog_add_category")
     */
    public function addCategoryAction($id = null, Request $request)
    {
        $category = $id === null ? new Category() : $this->getDoctrine()->getRepository('BersiBlogBundle:Category')->find($id);
        $form = $this->get('form.factory')->createBuilder('form', $category)
            ->add('name', 'text')
            ->add('save', 'submit')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            $this->addFlash(
                'success',
                'Creation / modification OK !'
            );
            return $this->redirect($this->generateUrl('admin_bersi_blog_category'));
        }
        return $this->render('BersiBlogBundle:Admin/Forms:category.html.twig', array(
            'form' => $form->createView()
        ));
    }

}
