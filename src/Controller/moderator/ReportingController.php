<?php

namespace App\Controller\moderator;

use App\Entity\Event;
use App\Entity\EventReporting;
use App\Entity\User;
use App\Repository\EventReportingRepository;
use App\Repository\EventRepository;
use App\Repository\ParticipationRepository;
use App\Repository\UserReportingRepository;
use App\Repository\UserRepository;
use App\Service\SendMail;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/moderator/report")
 */
class ReportingController extends Controller
{
    /**
     * @Route("/", name="moderator_report_last", methods="GET")
     */
    public function index(
        EventReportingRepository $eventReportingRepository,
        UserReportingRepository $userReportingRepository
    ) {
        return $this->render(
            'moderator/report/index.html.twig',
            [
                'eventReports' => $eventReportingRepository->findLastEventReport(),
                'userReports' => $userReportingRepository->findLastUserReport(),
            ]
        );
    }

    /**
     * @Route("/event", name="moderator_report_event", methods="GET")
     */
    public function showEventReport(EventReportingRepository $eventReportingRepository)
    {
        return $this->render(
            'moderator/report/event_list.html.twig',
            [
                'eventReports' => $eventReportingRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/user", name="moderator_report_user", methods="GET")
     */
    public function showUserReport(UserReportingRepository $userReportingRepository)
    {
        return $this->render(
            'moderator/report/user_list.html.twig',
            [
                'userReports' => $userReportingRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/user/{slug}/manage", name="moderator_report_user_manage", methods="POST")
     */
    public function banishUser(User $user, SendMail $sendMail)
    {
        $status = $user->getIsActive();
        if ($status == true) {
            $user->setIsActive(false);

            $sendMail->mail(
                'bannissement',
                'dev.prenonslair@gmail.com',
                $user->getEmail(),
                $this->render(
                    'moderator/report/bannish_mail.html.twig'
                )
            );
        } else {
            $user->setIsActive(true);
            $sendMail->mail(
                'Réinsertion',
                'dev.prenonslair@gmail.com',
                $user->getEmail(),
                $this->render(
                    'moderator/report/bannish_mail.html.twig'
                )
            );
        }
//        dump($sendMail);exit;
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Le status banissement du membre a bien été modifié');

        return $this->redirectToRoute('moderator_user_show', ['slug' => $user->getSlug()]);
    }

    /**
     * @Route("/event/{slug}/manage", name="moderator_report_event_manage", methods="POST")
     */
    public function banishEvent(Event $event, SendMail $sendMail)
    {
        $status = $event->getIsActive();
        if ($status == true) {
            $event->setIsActive(false);

//            $sendMail->mail(
//                'bannissement',
//                'dev.prenonslair@gmail.com',
//                $user->getEmail(),
//                $this->render(
//                    'moderator/report/bannish_mail.html.twig'
//                )
//            );
        } else {
            $event->setIsActive(true);
//            $sendMail->mail(
//                'Réinsertion',
//                'dev.prenonslair@gmail.com',
//                $user->getEmail(),
//                $this->render(
//                    'moderator/report/bannish_mail.html.twig'
//                )
//            );
        }
        //        dump($sendMail);exit;
        $em = $this->getDoctrine()->getManager();
        $em->persist($event);
        $em->flush();

        $this->addFlash('success', 'Le status banissement de l\'événement a bien été modifié');

        return $this->redirectToRoute('moderator_event_show', ['slug' => $event->getSlug()]);
    }

    /**
     * @Route("/{id}/delete", name="moderator_eventReporting_delete", methods="DELETE")
     */
    public function delete(Request $request, EventReporting $eventReporting): Response
    {

        if ($this->isCsrfTokenValid('delete'.$eventReporting->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($eventReporting);
            $em->flush();
        }

        return $this->redirectToRoute('moderator_report_last');
    }
}
