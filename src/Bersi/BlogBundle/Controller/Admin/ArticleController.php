<?php

namespace Bersi\BlogBundle\Controller\Admin;

use Bersi\BlogBundle\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ArticleController extends Controller
{

    /**
     * @Route("/admin/article", name="admin_bersi_blog_article")
     */
    public function articleAction()
    {
        $articles = $this->getDoctrine()->getRepository('BersiBlogBundle:Article')->findAll();
        return $this->render('BersiBlogBundle:Admin:article.html.twig', array(
            'liste_articles' => $articles,
        ));
    }

    /**
     * @Route("/admin/article/edit/{id}", name="admin_bersi_blog_edit_article")
     * @Route("/admin/article/add", name="admin_bersi_blog_add_article")
     */
    public function addArticleAction($id = null, Request $request)
    {
        $article = $id === null ? new Article() : $this->getDoctrine()->getRepository('BersiBlogBundle:Article')->find($id);
        $form = $this->get('form.factory')->createBuilder('form', $article)
            ->add('date', 'date', [
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy HH:mm',
            ])
            ->add('title', 'text')
            ->add('introduction', 'text')
            ->add('image', 'file', array('mapped' => false, 'required' => false))
            ->add('content', 'textarea')
            ->add('published', 'checkbox', array('required' => false))
            ->add('save', 'submit')
            ->add('category', 'entity', array(
                'class' => 'BersiBlogBundle:Category',
                'choice_label' => 'name'))
            ->add('tags', 'entity', array(
                'class' => 'BersiBlogBundle:Tag',
                'choice_label' => 'name',
                'multiple' => true))
            ->add('author', 'entity', array(
                'class' => 'BersiBlogBundle:Author',
                'choice_label' => 'pseudo'))
            ->getForm();
        $form->handleRequest($request);
        $image = false;
        if ($article->getId() !== null && file_exists(__DIR__ . '/../../../../../web/images/' . $article->getSlug() . '.jpeg')) {
            $image = '/images/' . $article->getSlug() . '.jpeg';
        }
        if ($form->isValid()) {
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
                    $DOCUMENT_ROOT = $this->get('request')->server->get('DOCUMENT_ROOT');
                    $path = $DOCUMENT_ROOT . '/images/';
                    if (!file_exists($path)) mkdir($path, 0777, true);
                    $file->move($path, $article->getSlug() . '.jpeg');
                }
            }
            return $this->redirect($this->generateUrl('admin_bersi_blog_article'));
        }
        return $this->render('BersiBlogBundle:Admin:addArticle.html.twig', array(
            'form' => $form->createView(),
            'image' => $image,
        ));
    }

    /**
     * @Route("/admin/article/publish/{id}", name="admin_bersi_blog_publish_article")
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
        };
    }

    /**
     * @Route("/admin/upload", name="admin_bersi_blog_upload")
     */
    public function uploadAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $file = $request->files->get('file');
            if ($file !== null) {
                $extension = $file->guessExtension();
                if (!$extension) $extension = 'bin';
                $goodExtension = ['jpeg', 'png', 'gif'];
                if (in_array($extension, $goodExtension)) {
                    $DOCUMENT_ROOT = $this->get('request')->server->get('DOCUMENT_ROOT');
                    $path = $DOCUMENT_ROOT . '/images/articles/';
                    if (!file_exists($path)) mkdir($path, 0777, true);
                    $file->move($path, $file->getClientOriginalName());
                }
            }
            $array = array(
                'filelink' => '/images/articles/' . $file->getClientOriginalName()
            );
            return new JsonResponse($array);
        };
    }

    /**
     * @Route("/admin/image", name="admin_bersi_blog_image")
     */
    public function imagedAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $DOCUMENT_ROOT = $this->get('request')->server->get('DOCUMENT_ROOT');
            $path = $DOCUMENT_ROOT . '/images';
            $result = [];
            if (file_exists($path)) {
                $finder = new Finder();
                $finder->files()->in($path)->files();
                foreach ($finder as $file) {
                    $imagesdfsdf['image'] = '/images/' . $file->getRelativePathname();
                    $image['thumb'] = '/images/' . $file->getRelativePathname();
                    $image['title'] = $file->getRelativePath();
                    $result[] = $image;
                }
            }
            return new JsonResponse($result);
        };
    }
}
