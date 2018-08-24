<?php

namespace App\Controller\moderator;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/moderator/comment")
 */
class CommentController extends Controller
{
    /**
     * @Route("/list", name="moderator_comment_list", methods="GET")
     */
    public function index(CommentRepository $commentRepository, UserRepository $userRepository): Response
    {
        return $this->render('moderator/comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="moderator_comment_show", methods="GET")
     */
    public function show(Comment $comment, UserRepository $userRepository, CommentRepository $comments): Response
    {
        return $this->render('moderator/comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="moderator_comment_edit", methods="GET|POST")
     */
    public function edit(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('moderator_comment_edit', ['id' => $comment->getId()]);
        }

        return $this->render('moderator/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="moderator_comment_delete", methods="DELETE")
     */
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush();
        }

        return $this->redirectToRoute('moderator_comment_list');
    }
}
