<?php

namespace App\Controller\moderator;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EventReportingRepository;
use App\Repository\UserReportingRepository;
use App\Repository\EventRepository;
use App\Repository\UserRepository;

/**
 * @Route("/moderator")
 */
class ModeratorController extends Controller
{
    /**
     * @Route("/", name="moderator_home", methods="GET")
     */
    public function home(EventReportingRepository $eventReportingRepository, EventRepository $eventRepository, UserReportingRepository $userReportingRepository, UserRepository $userRepository)
    {
        $eventsReporting = $eventReportingRepository->adminAllEventsReporting();
        $events = $eventRepository->adminLastEvents();
        $usersReporting = $userReportingRepository->adminAllUsersReporting();
        $users = $userRepository->adminLastUsers();

        return $this->render('moderator/home/index.html.twig', [
            'eventsReporting' => $eventsReporting,
            'events' => $events,
            'usersReporting' => $usersReporting,
            'users' => $users,
        ]);
    }
}