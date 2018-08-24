<?php

namespace App\Controller\moderator;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends Controller
{

    /**
     * @Route("/moderator/tag/list", name="moderator_tag_list", methods="GET|POST")
     */
    public function indexTag(Request $request,TagRepository $tagRepository)
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();
            //On met un message de succès
            $this->addFlash('success', 'Tag créé!');
            return $this->redirectToRoute('moderator_tag_list');
        }

        return $this->render('moderator/tag/index.html.twig', [
            'tag' => $tag,
            'form' => $form->createView(),
            'tags' => $tagRepository->findAll(),
        ]);
    }

    /**
     * @Route("/moderator/tag/delete/{id}", name="moderator_tag_delete", methods="DELETE")
     */
    public function deleteTag(Request $request, Tag $tag)
    {
        if ($this->isCsrfTokenValid('delete' . $tag->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($tag);
            $em->flush();
        }

        return $this->redirectToRoute('moderator_tag_list');
    }

    /**
     * @Route("/{id}/update", name="moderator_tag_update", methods="GET|POST")
     */
    public function edit(Request $request, Tag $tag): Response
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('moderator_tag_list');
        }

        return $this->render('moderator/tag/update_form.html.twig', [
            'tag' => $tag,
            'form' => $form->createView(),
        ]);
    }
}