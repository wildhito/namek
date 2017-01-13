<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Game;
use AppBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
/**
 * Post controller.
 *
 * @Route("game/{game}/post")
 * @ParamConverter("game", options={"mapping": {"game": "shortname"} })
 */
class PostController extends Controller
{
    /**
     * Lists all post entities.
     *
     * @Route("/", name="post_index")
     * @Method("GET")
     */
    public function indexAction(Game $game)
    {
        $this->get('twig')->getExtension('core')->setDateFormat(
            $this->get('translator')->trans('m/d/Y \a\t h:i'));
        $em = $this->getDoctrine()->getManager();

        $posts = $em->getRepository('AppBundle:Post')->findBy(array('game' => $game));
        $deleteForms = array();
        foreach($posts as $post) {
          $deleteForms[$post->getId()] = $this->createDeleteForm($post, $game)->createView();
        }
        return $this->render('post/index.html.twig', array(
            'posts' => $posts,
            'game' => $game,
            'delete_forms' => $deleteForms,
        ));
    }

    /**
     * Creates a new post entity.
     *
     * @Route("/new", name="post_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, Game $game)
    {
        $post = new Post();
        $form = $this->createForm('AppBundle\Form\PostType', $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setCreatedAt(new \DateTime());
            $post->setModifiedAt(new \DateTime());
            $post->setGame($game);
            $player = $this->get('security.token_storage')->getToken()->getUser();
            $post->setCreatedBy($player);
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush($post);

            return $this->redirectToRoute('game_show', array('shortname' => $game->getShortname() ));
        }

        return $this->render('post/new.html.twig', array(
            'post' => $post,
            'game' => $game,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing post entity.
     *
     * @Route("/{id}/edit", name="post_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Post $post, Game $game)
    {
        $deleteForm = $this->createDeleteForm($post, $game);
        $editForm = $this->createForm('AppBundle\Form\PostType', $post);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $post->setModifiedAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('game_show', array('shortname' => $game->getShortname()));
        }

        return $this->render('post/edit.html.twig', array(
            'post' => $post,
            'game' => $game,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a post entity.
     *
     * @Route("/{id}", name="post_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Post $post, Game $game)
    {
        $form = $this->createDeleteForm($post, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush($post);
        }

	return $this->redirectToRoute('game_show', array('shortname' => $game->getShortname()));
    }

    /**
     * Creates a form to delete a post entity.
     *
     * @param Post $post The post entity
     * @param Game $game The associated game
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Post $post, Game $game)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('post_delete', array('id' => $post->getId(),
                                                                'game' => $game->getShortname())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
