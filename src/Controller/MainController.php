<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Repository\EventRepository;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


class MainController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(EventRepository $eventReporting, AuthorizationCheckerInterface $authChecker)
    {
        // Look if is not a visiter
        if (true === $authChecker->isGranted('ROLE_USER')) {
            // Recover the connected User
            $user = $this->getUser();
            // Add Time
            $user->setConnectedAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

        }
        return $this->render('main/index.html.twig', [
            'events' => $eventReporting->showForHomepage(),
        ]);
    }

    /**
     * @Route("/cgu", name="cgu")
     */
    public function cgu()
    {
        return $this->render('main/cgu.html.twig');
    }
}
