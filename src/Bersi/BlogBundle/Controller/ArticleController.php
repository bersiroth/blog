<?php

namespace Bersi\BlogBundle\Controller;

use Bersi\BlogBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GuzzleHttp\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends Controller
{
    private $nbPerPage = 8;

    /**
     * @Route("/search", name="bersi_blog_search")
     */
    public function searchAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('BersiBlogBundle:Article');
        $search = $request->get('q', 1);
        $articles = $repository->search($search);
        $nbTotalArticles = count($articles);
        if ($nbTotalArticles > 1) {
            $page = 1;
            $nbPerPage = $this->nbPerPage;
            $pagination = [
                'nbTotalArticles' => $nbTotalArticles,
                'page' => $page,
                'nbPerPage' => $nbPerPage,
                'nbPage' => ceil((int)$nbTotalArticles / (int)$nbPerPage)
            ];
        } else {
            $pagination = null;
        }
        return $this->afficherArticle($articles, $pagination);
    }

    private function afficherArticle($articles, $pagination = null)
    {
        $moisFR = array(1 => 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
        foreach ($articles as $key => $value) {
            $articles[$key]->mois = $moisFR[$value->getDate()->format('n')];
        }
        $option = [];
        $option['list_articles'] = $articles;
        if ($pagination != null) $option['pagination'] = $pagination;
        return $this->render('BersiBlogBundle::layout.html.twig', $option);
    }

    /**
     * @Route("/tag/{tag}", name="bersi_blog_tag")
     */
    public function tagAction(Request $request, $tag)
    {
        $result = [];
        if ($tag !== null) {
            $repository = $this->getDoctrine()->getRepository('BersiBlogBundle:Tag');
            $tags = $repository->findOneBy(array('name' => $tag));
            $articles = $tags->getArticles();
            foreach ($articles->getValues() as $article) {
                if ($article->getPublished()) $result[] = $article;
            }
            $page = $request->get('page', 1);
            $nbPerPage = $this->nbPerPage;
            $nbTotalArticles = count($result);
            $pagination = [
                'nbTotalArticles' => $nbTotalArticles,
                'page' => $page,
                'nbPerPage' => $nbPerPage,
                'nbPage' => ceil((int)$nbTotalArticles / (int)$nbPerPage)
            ];
            $result = array_slice($result, ($page - 1) * $nbPerPage, $nbPerPage);
        }
        return $this->afficherArticle($result, $pagination);
    }

    private function getCommentForm($comment = null, $articleId = null)
    {
        $form = $this->get('form.factory')->createBuilder('form', $comment)
            ->add('pseudo', 'text')
            ->add('content', 'textarea', [
                'max_length' => 1000,])
            ->add('article_id', 'hidden', [
                'mapped' => false,
                'data' => $articleId])
            ->add('Envoyer', 'submit')
            ->add('parent_id', 'hidden', [
                'mapped' => false])
            ->getForm();
        return $form;
    }

    /**
     * @Route("/comment", name="bersi_blog_comment")
     */
    public function commentAction($articleId = null, Request $request)
    {
        $comment = new Comment();
        $form = $this->getCommentForm($comment, $articleId);
        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);
            $parent_id = $request->get('form')['parent_id'];
            if (!empty($parent_id)) {
                $repository = $this->getDoctrine()->getRepository('BersiBlogBundle:Comment');
                $parentComment = $repository->find($parent_id);
                $comment->setComment($parentComment);
            }
            $comment->setContent(substr($comment->getContent(), 0, 1000));
            $articleId = $request->get('form')['article_id'];
            $date = new \DateTime();
            $date->format('Y-m-d H:i:s');
            $comment->setDate($date);
            $repository = $this->getDoctrine()->getRepository('BersiBlogBundle:Article');
            $article = $repository->find($articleId);
            $comment->setArticle($article);
            $comment->setPublished(true);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($comment);
                $em->flush();
                $comment = new Comment();
                $form = $this->getCommentForm($comment, $articleId);
                return $this->render('BersiBlogBundle:Default:comment.html.twig', [
                    'comments' => $this->getAllComment($articleId),
                    'form' => $form->createView()
                ]);
            }
        }
        return $this->render('BersiBlogBundle:Default:comment.html.twig', [
            'comments' => $this->getAllComment($articleId),
            'form' => $form->createView()
        ]);
    }

    private function getAllComment($articleId)
    {
        $repository = $this->getDoctrine()->getRepository('BersiBlogBundle:Comment');
        $comments = $repository->findBy(
            array('published' => true, 'article' => $articleId, 'comment' => null));
        return $comments;
    }

    /**
     * @Route("/{category}/{slug}", name="bersi_blog_article")
     * @Route("/", name="home_page")
     * @param string $slug
     * @param string $category
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function articleAction(Request $request, $slug = null, $category = null)
    {
        $page = $request->get('page', 1);
        $nbPerPage = $this->nbPerPage;
        $repository = $this->getDoctrine()->getRepository('BersiBlogBundle:Article');
        if ($slug !== null) {
            $articles = $repository->findBy(
                array('published' => true, 'slug' => $slug));
        } elseif ($category !== null) {
            $nbTotalArticles = $repository->getAllArticlesPublish([$category]);
            $articles = $repository->getArticlesByCategory([$category], ($page - 1) * $nbPerPage, $nbPerPage);
        } else {
            $nbTotalArticles = $repository->getAllArticlesPublish();
            $articles = $repository->findBy(
                array('published' => true),
                array('date' => 'desc'),
                $nbPerPage,
                ($page - 1) * $nbPerPage);
        }
        if (isset($nbTotalArticles)) {
            $pagination = [
                'nbTotalArticles' => $nbTotalArticles,
                'page' => $page,
                'nbPerPage' => $nbPerPage,
                'nbPage' => ceil((int)$nbTotalArticles / (int)$nbPerPage)
            ];
        } else {
            $pagination = null;
        }
        return $this->afficherArticle($articles, $pagination);
    }
}
