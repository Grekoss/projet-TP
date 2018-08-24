<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Form\NotificationType;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/notification")
 */
class NotificationController extends Controller
{
    

    
    // public function new(Request $request): Response
    // {
    //     $notification = new Notification();
    //     $form = $this->createForm(NotificationType::class, $notification);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $em = $this->getDoctrine()->getManager();
    //         $em->persist($notification);
    //         $em->flush();

    //         return $this->redirectToRoute('notification_index');
    //     }

    //     return $this->render('notification/new.html.twig', [
    //         'notification' => $notification,
    //         'form' => $form->createView(),
    //     ]);
    // }

    /**
     * @Route("/{id}", name="notification_show", methods="GET")
     */
    public function show(Notification $notification): Response
    {
        $user= $notification->getSendee();
        $this->denyAccessUnlessGranted('RIGHTUSER', $user, 'Vous n\'avez pas accès à cette page!');
        return $this->render('notification/show.html.twig', ['notification' => $notification]);
    }

    

    /**
     * @Route("/{id}", name="notification_delete", methods="DELETE")
     */
    public function delete(Request $request, Notification $notification): Response
    {
        $user= $notification->getSendee();
        $this->denyAccessUnlessGranted('RIGHTUSER', $user, 'Vous n\'avez pas accès à cette page!');
        if ($this->isCsrfTokenValid('delete'.$notification->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($notification);
            $em->flush();
        }

        return $this->redirectToRoute('user_notifications', ['id'=> $notification->getSendee()->getId()]);
    }
}
