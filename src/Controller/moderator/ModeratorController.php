<?php

namespace App\Controller\moderator;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/moderator")
 */
class ModeratorController extends Controller
{
    /**
     * @Route("/", name="moderator_home", methods="GET")
     */
    public function home()
    {
        return $this->render('moderator/home/index.html.twig');
    }
}