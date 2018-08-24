<?php

namespace App\EventListener;

use App\Repository\UserRepository;
use Twig\Environment;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class LastSixUsers
{
    private $userRepository;
    private $twig;

    public function __construct(UserRepository $userRepository, Environment $twig)
    {
        $this->userRepository = $userRepository;
        $this->twig = $twig;       
    }

    public function OnKernelController(FilterControllerEvent $event)
    {
        // Recovery of the Controller that will be called, since$event
        $controller = $event->getController()[0];

        // Recoverys by custom repository for last six :
        $lastUsersCreated = $this->userRepository->findSixLastUsersCreated();
        $lastUsersConnected = $this->userRepository->findSixLastUsersConnected();

        // Transmit to Twig in global variables
        $this->twig->addGlobal('lastUsersCreated', $lastUsersCreated);
        $this->twig->addGlobal('lastUsersConnected', $lastUsersConnected);
    }
}