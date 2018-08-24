<?php

namespace App\Controller\moderator;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Repository\ParticipationRepository;
use App\Repository\UserRepository;
use App\Service\SendMail;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/moderator/event")
 */
class EventController extends Controller
{
    /**
     * @Route("/list", name="moderator_event_list", methods="GET")
     */
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('moderator/event/index.html.twig', ['events' => $eventRepository->findAll()]);
    }

    /**
     * @Route("/{slug}", name="moderator_event_show", methods="GET")
     */
    public function show(Event $event, ParticipationRepository $participants): Response
    {
        return $this->render('moderator/event/show.html.twig', [
            'event' => $event,
            'participants' => $participants->findParticipationsByEvent($event),
        ]);
    }

    /**
     * @Route("/{slug}/update", name="moderator_event_update", methods="GET|POST")
     */
    public function edit(Request $request, Event $event): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('moderator_event_update', ['slug' => $event->getSlug()]);
        }

        return $this->render('moderator/event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}/delete", name="moderator_event_delete", methods="DELETE")
     */
    public function delete(Request $request, Event $event, SendMail $sendMail, ParticipationRepository $participations): Response
    {
        $users = $participations->getParticipants($event);

        if ($this->isCsrfTokenValid('delete'.$event->getSlug(), $request->request->get('_token'))) {
            if  ($users != null ) {
                foreach ($users as $user) {
                    $sendMail->mail(
                        'Événement annulé',
                        'dev.prenonslair@gmail.com',
                            $user->getEmail(),
                        'Votre événement a été supprimé'
                    );
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($event);
            $em->flush();
        }

        return $this->redirectToRoute('moderator_event_list');
    }
}
