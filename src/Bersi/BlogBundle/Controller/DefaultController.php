<?php

namespace Bersi\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('::layout.html.twig');
    }

    public function newsAction($titre,$type)
    {
        $news = [[
                'mois' => 'test',
                'jour' => '25',
                'titre' => '[test] qsddqsd',
                'type' => 'jeux-video',
                'auteur' => 'Bersiqsdqsdroth',
                'image' => 'http://static.eclypsia.com/public/upload/cke/Events/E3%202014/sony/Bloodbourne_Ban_E3.jpg',
                'contenu' => 'qsdqsborn'
            ],
        ];

        return $this->render('BersiBlogBundle::layout.html.twig',
            ['list_news' => $news]
            );
    }
    public function AllNewsAction()
    {
        $news = [
            [
                'mois' => 'Juin',
                'jour' => '25',
                'titre' => '[test] Bloodborn',
                'type' => 'jeux-video',
                'auteur' => 'Bersiroth',
                'image' => 'http://static.eclypsia.com/public/upload/cke/Events/E3%202014/sony/Bloodbourne_Ban_E3.jpg',
                'contenu' => 'Voici le test sur le jeux bloodborn'
            ],[
                'mois' => 'Mai',
                'jour' => '12',
                'titre' => '[avis] mangas FullMetal Alchemist',
                'type' => 'mangas',
                'auteur' => 'Bersi',
                'image' => 'http://waines.e-monsite.com/medias/images/full-metal-alchemist.jpg',
                'contenu' => 'Voici mon avis sur le mangas fullmetal alchemist'
            ],
        ];
        return $this->render('BersiBlogBundle:default:news.html.twig',
            ['list_news' => $news]
            );
    }
}
