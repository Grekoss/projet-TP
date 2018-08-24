<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ExceptionController extends Controller
{
    public function showException()
    {
        return $this->render('exception/404.html.twig');
    }
}
