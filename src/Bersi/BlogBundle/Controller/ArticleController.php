<?php

namespace Bersi\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GuzzleHttp\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends Controller
{
    private $nbPerPage = 8;

    private function afficherArticle($articles, $pagination = null)
    {
        $moisFR = array(1 => 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
        foreach ($articles as $key => $value) {
            $articles[$key]->mois = $moisFR[$value->getDate()->format('n')];
        }
        $option = [];
        $option['list_articles'] = $articles;
        $comments = ['test','test','test','test'];
        $option['list_comments'] = $comments;
        if ($pagination != null) $option['pagination'] = $pagination;
        return $this->render('BersiBlogBundle::layout.html.twig', $option);
    }

    /**
     * @Route("/tag/{tag}", name="bersi_blog_tag")
     */
    public function tagAction(Request $request,$tag)
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
            $result = array_slice($result ,($page - 1) * $nbPerPage ,$nbPerPage);
        }
        return $this->afficherArticle($result, $pagination);
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

    public function commentAction(){
        return $this->render('BersiBlogBundle:Default:comment.html.twig');
    }

}
