<?php

namespace App\Controller;

use App\Entity\Following;
use App\Entity\Event;
use App\Form\FollowingType;
use App\Repository\FollowingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/following")
 */
class FollowingController extends Controller
{
    
    /**
     * @Route("/new", name="following_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Vous n\'avez pas accès à cette page!');
        $following = new Following();
        $form = $this->createForm(FollowingType::class, $following);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($following);
            $em->flush();

            return $this->redirectToRoute('following_index');
        }

        return $this->render('following/new.html.twig', [
            'following' => $following,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}/create", name="following_create", methods="GET")
     */
    public function create(Event $event)
    {
        $following = new Following();
        $following->setUser($this->getUser());
        $following->setEvent($event);
        if(!($this->getUser()->getFollowings()->contains($following))){
            $em = $this->getDoctrine()->getManager();
            $em->persist($following);
            $em->flush();
        }
        
        
        return $this->redirectToRoute('event_show', [
            'slug' => $event->getSlug(),
            
            ]); 
    }

     /**
     * @Route("/{slug}/remove", name="following_remove", methods="GET")
     */
    public function remove(Event $event, FollowingRepository $repo)
    {
        $following =$repo->findOneFollowing($event, $this->getUser());
       
        if($following){
            $em = $this->getDoctrine()->getManager();
            $em->remove($following);
            $em->flush();
            $this->addFlash ('success', 'Vous ne suivez plus la sortie!');
            
        }
        return $this->redirectToRoute('event_show', [
            'slug' => $event->getSlug(), 
            ]); 
        
    }


    /**
     * @Route("/{id}", name="following_show", methods="GET")
     */
    public function show(Following $following): Response
    {
        $user= $following->getUser();
        $this->denyAccessUnlessGranted('RIGHTUSER', $user, 'Vous n\'avez pas accès à cette page!');
        return $this->render('following/show.html.twig', ['following' => $following]);
    }


    /**
     * @Route("/{id}", name="following_delete", methods="DELETE")
     */
    public function delete(Request $request, Following $following): Response
    {
        $user= $following->getUser();
        $this->denyAccessUnlessGranted('RIGHTUSER', $user, 'Vous n\'avez pas accès à cette page!');
        if ($this->isCsrfTokenValid('delete'.$following->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($following);
            $em->flush();
        }

        return $this->redirectToRoute('following_index');
    }
}
