<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Game controller.
 *
 * @Route("game")
 */
class GameController extends Controller
{
    /**
     * Lists all game entities.
     *
     * @Route("/", name="game_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $games = $em->getRepository('AppBundle:Game')->findAll();

        return $this->render('game/index.html.twig', array(
            'games' => $games,
        ));
    }

    /**
     * Creates a new game entity.
     *
     * @Route("/new", name="game_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $game = new Game();
        $form = $this->createForm('AppBundle\Form\GameType', $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $player = $this->get('security.token_storage')->getToken()->getUser();
            $game->setCreatedAt(new \DateTime());
            $game->setCreatedBy($player);
            $game->setModifiedAt(new \DateTime());
            $game->setModifiedBy($player);
            $em = $this->getDoctrine()->getManager();
            $em->persist($game);
            $em->flush($game);

            return $this->redirectToRoute('game_show', array('shortname' => $game->getShortname()));
        }

        return $this->render('game/new.html.twig', array(
            'game' => $game,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a game entity.
     *
     * @Route("/{shortname}", name="game_show")
     * @Method("GET")
     */
    public function showAction(Game $game)
    {
        $em = $this->getDoctrine()->getManager();
        $players = $em->getRepository('AppBundle:Game')->getPlayersFromGame($game->getId());

        $deleteForm = $this->createDeleteForm($game);

        $finder = new Finder();
        $fs = new Filesystem();
        $pictureDeleteForms = array();
        $pictures = array();
        if ($fs->exists($game->getPictureDir())) {
            $finder->files()->in($game->getPictureDir());
            foreach ($finder as $picture) {
                $pictures[] = sprintf("%s/%s", $game->getPictureWebDir(), $picture->getFilename());
                $pictureDeleteForms[] = $this->createPictureDeleteForm(
                    $game,
                    sprintf("%s/%s", $game->getPictureDir(), $picture->getFilename()))
                  ->createView();
            }
        }
        return $this->render('game/show.html.twig', array(
            'game' => $game,
            'game_players' => $players,
            'pictures' => $pictures,
            'picture_delete_forms' => $pictureDeleteForms,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing game entity.
     *
     * @Route("/{id}/edit", name="game_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Game $game)
    {
        $deleteForm = $this->createDeleteForm($game);
        $editForm = $this->createForm('AppBundle\Form\GameType', $game);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $game->setModifiedAt(new \Datetime());
            $game->setModifiedBy($this->get('security.token_storage')->getToken()->getUser());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('game_show', array('shortname' => $game->getShortname()));
        }

        return $this->render('game/edit.html.twig', array(
            'game' => $game,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

   /**
     * Displays a form to upload a game picture
     *
     * @Route("/{id}/picture", name="game_picture")
     * @Method({"GET", "POST"})
     */
    public function pictureAction(Request $request, Game $game)
    {
        $pictureForm = $this->createForm('AppBundle\Form\UploadType', null,
             array('label' => $this->get('translator')->trans('Game picture')));
        $pictureForm->handleRequest($request);
        if ($pictureForm->isSubmitted() && $pictureForm->isValid()) {
            $data = $pictureForm->getData();
            $file = $data['uploaded_file'];
            $targetDir = $game->getPictureDir();
            if ($this->get('app.file_uploader')->upload($file, $targetDir, $file->getClientOriginalName(),
                $game->getPictureMimeTypes())) {
                return $this->redirectToRoute('game_show', array('shortname' => $game->getShortname()));
            }
        }
        return $this->render('common/upload.html.twig', array(
            'upload_form' => $pictureForm->createView(),
            'upload_title' => $this->get('translator')->trans("%name%'s pictures",
                                                              array("%name%" => $game->getFullname())),
        ));
    }

    /**
     * Delete a picture
     *
     * @Route("/{id}/picture", name="game_picture_delete")
     * @Method("DELETE")
     */
    public function pictureDeleteAction(Request $request, Game $game)
    {
        $fs = new Filesystem();
        $form = $this->createPictureDeleteForm($game, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          $fs->remove($request->request->get("form")["picture"]);
        }
        return $this->redirectToRoute('game_show', array('shortname' => $game->getShortname()));
    }

    /**
     * Displays a form to upload game logo
     *
     * @Route("/{id}/logo", name="game_logo")
     * @Method({"GET", "POST"})
     */
    public function logoAction(Request $request, Game $game)
    {
        $logoForm = $this->createForm('AppBundle\Form\UploadType', null,
             array('label' => $this->get('translator')->trans('Game logo')));
        $logoForm->handleRequest($request);
        if ($logoForm->isSubmitted() && $logoForm->isValid()) {
            $data = $logoForm->getData();
            $file = $data['uploaded_file'];
            $targetDir = $game->getLogoDir();
            if ($this->get('app.file_uploader')->upload($file, $targetDir, $game->getLogoName(),
                array($game->getLogoMimeType()))) {
                return $this->redirectToRoute('game_show', array('shortname' => $game->getShortname()));
            }
        }

        return $this->render('common/upload.html.twig', array(
            'upload_form' => $logoForm->createView(),
            'upload_title' => $this->get('translator')->trans("%name%'s logo",
                                                              array("%name%" => $game->getFullname())),
        ));
    }

    /**
     * Deletes a game entity.
     *
     * @Route("/{id}", name="game_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Game $game)
    {
        $form = $this->createDeleteForm($game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($game);
            $em->flush($game);
        }
        return $this->redirectToRoute('game_index');
    }

    /**
     * Add current player to game
     *
     * @Route("/{shortname}/add_player/", name="game_add_player")
     * @Method("GET")
     */
    public function addPlayerAction(Game $game)
    {
       $user = $this->get('security.token_storage')->getToken()->getUser();
       $user->addGame($game);
       $em = $this->getDoctrine()->getManager();
       $em->persist($user);
       $em->flush($user);
       return $this->redirectToRoute('game_show', array('shortname' => $game->getShortname()));
    }

    /**
     * Creates a form to delete a game entity.
     *
     * @param Game $game The game entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Game $game)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('game_delete', array('id' => $game->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Creates a form to delete a picture from a game entity.
     *
     * @param Game $game The game entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createPictureDeleteForm(Game $game, $picture)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('game_picture_delete', array('id' => $game->getId())))
            ->setMethod('DELETE')
            ->add('picture', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, array('data' => $picture))
            ->getForm()
        ;
    }

}

