<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Player;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Player controller.
 *
 * @Route("player")
 */
class PlayerController extends Controller
{
    /**
     * Lists all player entities.
     *
     * @Route("/", name="player_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $players = $em->getRepository('AppBundle:Player')->findAll();

        return $this->render('player/index.html.twig', array(
            'players' => $players,
        ));
    }

    /**
     * Finds and displays a player entity.
     *
     * @Route("/{login}", name="player_show")
     * @Method("GET")
     */
    public function showAction(Player $player)
    {
        $deleteForm = $this->createDeleteForm($player);

        return $this->render('player/show.html.twig', array(
            'player' => $player,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing player entity.
     *
     * @Route("/{id}/edit", name="player_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Player $player)
    {
        $deleteForm = $this->createDeleteForm($player);
        $editForm = $this->createForm('AppBundle\Form\PlayerType', $player);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $password = $this->get('security.password_encoder')
                ->encodePassword($player, $player->getPlainPassword());
            $player->setPassword($password);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('player_edit', array('id' => $player->getId()));
        }

        return $this->render('player/edit.html.twig', array(
            'player' => $player,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Display a form to update a player's role
     *
     * @Route("/{id}/role", name="player_role")
     * @Method({"GET", "POST"})
     */
    public function roleAction(Request $request, Player $player)
    {
        $roleForm = $this->createForm('AppBundle\Form\PlayerRoleType', $player);
        $roleForm->handleRequest($request);
        if ($roleForm->isSubmitted() && $roleForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('player_index');
        }
        return $this->render('player/role.html.twig', array(
            'player' => $player,
            'role_form' => $roleForm->createView(),
        ));
    }

    /**
     * Displays a form to upload player logo
     *
     * @Route("/{id}/logo", name="player_logo")
     * @Method({"GET", "POST"})
     */
    public function logoAction(Request $request, Player $player)
    {
        $logoForm = $this->createForm('AppBundle\Form\UploadType', null,
             array('label' => 'Player logo'));
        $logoForm->handleRequest($request);
        if ($logoForm->isSubmitted() && $logoForm->isValid()) {
            $data = $logoForm->getData();
            $file = $data['uploaded_file'];
            $targetDir = $player->getLogoDir();
            if ($this->get('app.file_uploader')->upload($file, $targetDir, $player->getLogoName(),
                array($this->getLogoMimeType()))) {
                return $this->redirectToRoute('player_show', array('login' => $player->getLogin()));
            }
        }

        return $this->render('common/upload.html.twig', array(
            'upload_form' => $logoForm->createView(),
        ));
    }



    /**
     * Deletes a player entity.
     *
     * @Route("/{id}", name="player_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Player $player)
    {
        $form = $this->createDeleteForm($player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($player);
            $em->flush($player);
        }

        return $this->redirectToRoute('player_index');
    }

    /**
     * Creates a form to delete a player entity.
     *
     * @param Player $player The player entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Player $player)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('player_delete', array('id' => $player->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

}

