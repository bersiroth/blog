<?php

namespace Bersi\BlogBundle\Controller\Admin;

use Bersi\BlogBundle\Entity\Tag;
use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class TagController extends Controller
{

    /**
     * @Route("/admin/tag", name="admin_bersi_blog_tag")
     */
    public function tagAction()
    {
        $tag = $this->getDoctrine()->getRepository('BersiBlogBundle:Tag')->findAll();
        return $this->render('BersiBlogBundle:Admin:tag.html.twig', array(
            'liste_categories' => $tag,
        ));
    }

    /**
     * @Route("/admin/tag/edit/{id}", name="admin_bersi_blog_edit_tag")
     * @Route("/admin/tag/add", name="admin_bersi_blog_add_tag")
     */
    public function addTagAction($id = null, Request $request)
    {
        $tag = $id === null ? new Tag() : $this->getDoctrine()->getRepository('BersiBlogBundle:Tag')->find($id);
        $form = $this->get('form.factory')->createBuilder('form', $tag)
            ->add('name', 'text')
            ->add('save', 'submit')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();
            $this->addFlash(
                'success',
                'Creation / modification OK !'
            );

            return $this->redirect($this->generateUrl('admin_bersi_blog_tag'));
        }
        return $this->render('BersiBlogBundle:Admin/Forms:tag.html.twig', array(
            'form' => $form->createView()
        ));
    }

}
