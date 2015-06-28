<?php

namespace Bersi\BlogBundle\Controller\Admin;

use Bersi\BlogBundle\Entity\Author;
use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class AuthorController extends Controller
{

    /**
     * @Route("/admin/author", name="admin_bersi_blog_author")
     */
    public function authorAction()
    {
        $authors = $this->getDoctrine()->getRepository('BersiBlogBundle:Author')->findAll();
        return $this->render('BersiBlogBundle:Admin:author.html.twig', array(
            'liste_authors' => $authors,
        ));
    }

    /**
     * @Route("/admin/author/edit/{id}", name="admin_bersi_blog_edit_author")
     * @Route("/admin/author/add", name="admin_bersi_blog_add_author")
     */
    public function addAuthorAction($id = null, Request $request)
    {
        $author = $id === null ? new Author() : $this->getDoctrine()->getRepository('BersiBlogBundle:Author')->find($id);
        $form = $this->get('form.factory')->createBuilder('form', $author)
            ->add('pseudo', 'text')
            ->add('save', 'submit')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($author);
            $em->flush();
            $this->addFlash(
                'success',
                'Creation / modification OK !'
            );

            return $this->redirect($this->generateUrl('admin_bersi_blog_author'));
        }
        return $this->render('BersiBlogBundle:Admin/Forms:author.html.twig', array(
            'form' => $form->createView()
        ));
    }

}
