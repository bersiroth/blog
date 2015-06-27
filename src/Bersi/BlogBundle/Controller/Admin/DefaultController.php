<?php

namespace Bersi\BlogBundle\Controller\Admin;

use Bersi\BlogBundle\Entity\Article;
use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{

    /**
     * @Route("/admin", name="admin_bersi_blog")
     */
    public function indexAction()
    {
        return $this->render(':admin:layout.html.twig');
    }

    /**
     * @Route("/admin/article", name="admin_bersi_blog_articles")
     */
    public function articleAction()
    {
        $articles = $this->getDoctrine()->getRepository('BersiBlogBundle:Article')->findAll();
        return $this->render('BersiBlogBundle:admin:article.html.twig', array(
            'liste_articles' => $articles,
        ));
    }

    /**
     * @Route("/admin/article/edit/{id}", name="admin_bersi_blog_edit_articles")
     * @Route("/admin/article/add", name="admin_bersi_blog_add_articles")
     */
    public function addArticleAction($id = null, Request $request)
    {
        $article = $id === null ? new Article() : $this->getDoctrine()->getRepository('BersiBlogBundle:Article')->find($id);
        $form = $this->get('form.factory')->createBuilder('form', $article)
            ->add('date', 'date')
            ->add('title', 'text')
            ->add('image', 'file', array('mapped' => false, 'required' => false))
            ->add('content', 'textarea')
            ->add('published', 'checkbox', array('required' => false))
            ->add('save', 'submit')
            ->add('category', 'entity', array(
                'class' => 'BersiBlogBundle:Category',
                'property' => 'name'))
            ->add('tags', 'entity', array(
                'class' => 'BersiBlogBundle:Tag',
                'property' => 'name',
                'multiple' => true))
            ->add('author', 'entity', array(
                'class' => 'BersiBlogBundle:Author',
                'property' => 'pseudo'))
            ->getForm();
        $form->handleRequest($request);
        if (file_exists(realpath(__DIR__ . '/../../../../../web/images/' . $article->getCategory()->getName() . '/' .$article->getSlug() . '.jpeg'))){
            $image = '/images/' . $article->getCategory()->getName() . '/' .$article->getSlug() . '.jpeg';
        }else{
            $image = false;
        }
        if ($form->isValid()) {
//            var_dump($form['image']);die;
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            $this->addFlash(
                'success',
                'Creation / modification OK !'
            );
            $file = $form['image']->getData();
            if ($file !== null) {
                $extension = $file->guessExtension();
                if (!$extension) $extension = 'bin';
                $goodExtension = ['jpeg', 'png', 'gif'];
                if (in_array($extension, $goodExtension)) {
                    $path = realpath(__DIR__ . '/../../../../../web/images/' . $article->getCategory()->getName() . '/');
                    $file->move($path, $article->getSlug() . '.' . $extension);
                }
            }
            return $this->redirect($this->generateUrl('admin_bersi_blog_articles'));
        }
        return $this->render('BersiBlogBundle:admin:addArticle.html.twig', array(
            'form' => $form->createView(),
            'image' => $image,
        ));
    }

    /**
     * @Route("/admin/article/publish/{id}", name="admin_bersi_blog_publish_articles")
     */
    public function publishAction($id, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $article = $this->getDoctrine()->getRepository('BersiBlogBundle:Article')->find($id);
            $state = $article->getPublished() == true ? false : true;
            $article->setPublished($state);
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            return new JsonResponse(array('id' => $id));
//            return new Response();
        };
    }
}
