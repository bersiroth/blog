<?php

namespace Bersi\BlogBundle\Controller;

use Bersi\BlogBundle\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GuzzleHttp\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{

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
            $articles = $repository->findBy(array('slug' => $slug),array('published' => true));
        } elseif($category !== null){
            $articles = $repository->getArticlesByCategory([$category]);
        } else {
            $articles = $repository->findBy(array('published' => true));
        }
        $moisFR = array(1 => 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
        foreach ($articles as $key => $value) {
            $articles[$key]->mois = $moisFR[$value->getDate()->format('n')];
        }
        return $this->render('BersiBlogBundle::layout.html.twig',
            ['list_articles' => $articles]
        );
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
        return $this->render('BersiBlogBundle:default:twitch.html.twig',
            [
                'is_live' => $live,
                'channel' => $channel,
            ]
        );
    }
}
