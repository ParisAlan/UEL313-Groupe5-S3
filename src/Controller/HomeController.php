<?php

namespace Watson\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController {

    /**
     * Home page controller.
     *
     * @param Application $app Silex application
     */
    public function indexAction(Application $app) {
        $links = $app['dao.link']->findAll();
        return $app['twig']->render('index.html.twig', array('links' => $links));
    }

    /**
     * Link details controller.
     *
     * @param integer $id Link id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function linkAction($id, Request $request, Application $app) {
        $link = $app['dao.link']->find($id);
        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            // A user is fully authenticated : he can add comments
            // Check if he's author for manage link

        }
        return $app['twig']->render('link.html.twig', array(
            'link' => $link
        ));
    }

    /**
     * Links by tag controller.
     *
     * @param integer $id Tag id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function tagAction($id, Request $request, Application $app) {
        $links = $app['dao.link']->findAllByTag($id);
        $tag   = $app['dao.tag']->findTagName($id);

        return $app['twig']->render('result_tag.html.twig', array(
            'links' => $links,
            'tag'   => $tag
        ));
    }

    /**
     * User login controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function loginAction(Request $request, Application $app) {
        return $app['twig']->render('login.html.twig', array(
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
            )
        );
    }

    /**
     * rss page controller.
     *
     * @param Application $app Silex application
     */
    public function indexActionRss(Application $app) {
        $links = $app['dao.link']->findAll();

        // On définit d'avance le nombre max d'articles dans le RSS
        $objectif = 15;

        // On compte le nombre d'éléments présents dans notre tableau $links
        $count = count($links); // = 16

        // On va venir compter la différence entre le nombre d'éléments et l'objectif final
        $difference = $count - $objectif;

        // Tant que nous avons une différence entre le nombre total d'éléments et l'objectif, on en retire pour
        // arriver à 15 valeurs.
        while ($difference > 0) {
            $effaceur = array_pop($links);
            $difference--;
        }

        // Penser à transformer avec array_slice ?

        return new Response(
            $app['twig']->render('rss.xml.twig', array(
                'links' => $links,
            )),
            200,
            array('Content-Type' => 'application/rss+xml; charset=UTF-8')
            // On indique que c'est du XML et pas du HTML sous peine que ça cause des erreurs
        );
    }
}
