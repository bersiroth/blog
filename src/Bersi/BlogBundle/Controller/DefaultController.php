<?php

namespace Bersi\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use GuzzleHttp\Client;

class DefaultController extends Controller
{

    /**
     * @Route("/twitch", name="bersi_blog_twitch")
     */
    public function twitchAction()
    {
        $channels = ['bersiroth', 'akosy', 'nikorasu'];
        $client = new Client();
        foreach($channels as $channel) {
            $channelName = $channel;
            $res = $client->get('https://api.twitch.tv/kraken/streams/' . $channel, ['verify' => false]);
            $res = json_decode($res->getBody());
            $live = $res->stream === null ? false : true;
            if ($live)break;
        }
        return $this->render('BersiBlogBundle:Default:twitch.html.twig',
            [
                'is_live' => $live,
                'channel' => $channel,
                'channelName' => $channelName,
            ]
        );
    }

    /**
     * @Route("/menu", name="bersi_blog_menu")
     */
    public function menuAction($category)
    {
        $categories = $this->getDoctrine()->getRepository('BersiBlogBundle:Category')->findAll();
        return $this->render('BersiBlogBundle:Default:menu.html.twig', array(
            'menus' => $categories,
            'category' => $category
        ));
    }

}
