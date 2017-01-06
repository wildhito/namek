<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Player;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Security controller.
 *
 */
class SecurityController extends Controller
{
    /**
     * Login action
     *
     * @Route("/login/", name="login")
     */
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error,
        ));
    }

    /**
     * Logout action
     *
     * @Route("/logout/", name="logout")
     */
     public function logoutAction(Request $request)
     {
         $this->get('security.token_storage')->setToken(null);
         $request->getSession()->invalidate();
         return $this->redirectToRoute('homepage');
     }

    /**
     * Creates a new player entity.
     *
     * @Route("/register", name="register")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $player = new Player();
        $form = $this->createForm('AppBundle\Form\PlayerType', $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $player->setCreatedAt(new \Datetime());
            $password = $this->get('security.password_encoder')
                ->encodePassword($player, $player->getPlainPassword());
            $player->setPassword($password);
            $player->setRole('ROLE_NONE');
            $em = $this->getDoctrine()->getManager();
            $em->persist($player);
            $em->flush($player);

            return $this->redirectToRoute('homepage');
        }

        return $this->render('player/new.html.twig', array(
            'player' => $player,
            'form' => $form->createView(),
        ));
    }

}

