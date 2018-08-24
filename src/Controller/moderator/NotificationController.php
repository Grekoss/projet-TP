<?php

namespace App\Controller\moderator;

use App\Entity\Notification;
use App\Form\NotificationType;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("moderator/notification")
 */
class NotificationController extends Controller
{
    /**
     * @Route("/", name="moderator_notification_index", methods="GET")
     */
    public function index(NotificationRepository $notificationRepository): Response
    {
        return $this->render('notification/index.html.twig', ['notifications' => $notificationRepository->findAll()]);
    }

    /**
     * @Route("/new", name="moderator_notification_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $notification = new Notification();
        $form = $this->createForm(NotificationType::class, $notification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($notification);
            $em->flush();

            return $this->redirectToRoute('notification_index');
        }

        return $this->render('notification/new.html.twig', [
            'notification' => $notification,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="moderator_notification_show", methods="GET")
     */
    public function show(Notification $notification): Response
    {
        return $this->render('notification/show.html.twig', ['notification' => $notification]);
    }

    /**
     * @Route("/{id}/edit", name="moderator_notification_edit", methods="GET|POST")
     */
    public function edit(Request $request, Notification $notification): Response
    {
        $form = $this->createForm(NotificationType::class, $notification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('notification_edit', ['id' => $notification->getId()]);
        }

        return $this->render('notification/edit.html.twig', [
            'notification' => $notification,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="moderator_notification_delete", methods="DELETE")
     */
    public function delete(Request $request, Notification $notification): Response
    {
        if ($this->isCsrfTokenValid('delete'.$notification->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($notification);
            $em->flush();
        }

        return $this->redirectToRoute('notification_index');
    }
}
