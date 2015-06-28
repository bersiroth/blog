<?php

namespace Bersi\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GuzzleHttp\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ArticleController extends Controller
{

    private function afficherArticle($articles){
        $moisFR = array(1 => 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
        foreach ($articles as $key => $value) {
            $articles[$key]->mois = $moisFR[$value->getDate()->format('n')];
        }
        return $this->render('BersiBlogBundle::layout.html.twig',
            ['list_articles' => $articles]
        );
    }

    /**
     * @Route("/tag/{tag}", name="bersi_blog_tag")
     */
    public function tagAction($tag){
        $articles = [];
        $repository = $this->getDoctrine()->getRepository('BersiBlogBundle:Tag');
        if($tag !== null){
            $tags = $repository->findoneBy(array('name' => $tag));
            $articles = $tags->getArticles();
        }
        return $this->afficherArticle($articles->getValues());
    }

    /**
     * @Route("/twitch", name="bersi_blog_twitch")
     */
    public function twitchAction()
    {
        $channel = "bersiroth";
        $client = new Client();
        $res = $client->get('https://api.twitch.tv/kraken/streams/' . $channel, ['verify' => false]);
        $res = json_decode($res->getBody());
        $live = $res->stream === null ? false : true;
        return $this->render('BersiBlogBundle:Default:twitch.html.twig',
            [
                'is_live' => $live,
                'channel' => $channel,
            ]
        );
    }


    /**
     * @Route("/{category}/{slug}", name="bersi_blog_article")
     * @Route("/", name="home_page")
     * @param string $slug
     * @param string $category
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function articleAction($slug = null, $category = null)
    {
        $repository = $this->getDoctrine()->getRepository('BersiBlogBundle:Article');
        if ($slug !== null) {
            $articles = $repository->getArticlesBy('slug', $slug);
        } elseif($category !== null){
            $articles = $repository->getArticlesByCategory([$category]);
        } else {
            $articles = $repository->findBy(array('published' => true),array('date' => 'desc'));
        }
        return $this->afficherArticle($articles);
    }

}
