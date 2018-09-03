<?php

namespace App\Controller;

use App\Entity\EventReporting;
use App\Form\EventReportingType;
use App\Repository\EventReportingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Event;
use App\Entity\User;
use App\Entity\UserReporting;
use App\Form\UserReportingType;

/**
 * @Route("/reporting")
 */
class ReportingController extends Controller
{
    /**
     * @Route("/event/{slug}/new", name="event_reporting_new", methods="GET|POST")
     */
    public function newEvent(Request $request, Event $event): Response
    {
        // User information
        $user = $this->getUser();

        $eventReporting = new EventReporting();
        $form = $this->createForm(EventReportingType::class, $eventReporting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $eventReporting->setUser($user);
            $eventReporting->setEvent($event);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($eventReporting);
            $em->flush();

            $this->addFlash('warning', 'Nous avons reçu votre signalement de l\'évenement ne respectant pas la charte du site.');

            return $this->redirectToRoute('event_show', ['slug' => $event->getSlug()]);
        }

        return $this->render('reporting/event_new.html.twig', [
            'event_reporting' => $eventReporting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/{slug}/new", name="user_reporting_new", methods="GET|POST")
     */
    public function newUser(Request $request, User $accusedUser): Response
    {
        // User main information
        $userMain = $this->getUser();

        $userReporting = new UserReporting();
        $form = $this->createForm(UserReportingType::class, $userReporting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userReporting->setUser($userMain);
            $userReporting->setAccusedUser($accusedUser);

            $em = $this->getDoctrine()->getManager();
            $em->persist($userReporting);
            $em->flush();

            $this->addFlash('Warning', 'Nous avons reçu votre signalement du membre ne respectant pas la charte du site.');

            return $this->redirectToRoute('user_show', ['slug' => $accusedUser->getSlug()]);
        }

        return $this->render('reporting/user_new.html.twig', [
            'user_reporting' => $userReporting,
            'form' => $form->createView(),
        ]);
    }
}
