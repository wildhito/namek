<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Game;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $games = $em->getRepository('AppBundle:Game')->findBy(
            array(),  // no condition
            array('modifiedAt' => 'DESC'),  // most recent first
            9,  // limit
            0   // offset
        );
        return $this->render('default/index.html.twig', [
            'games' => $games,
            'logo_dir' => '/games/',
            'logo_name' => 'logo.png',
        ]);
    }
}
