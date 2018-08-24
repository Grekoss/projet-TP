<?php

namespace App\Controller\moderator;

use App\Entity\Genre;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\EventReportingRepository;
use App\Repository\GenreRepository;
use App\Repository\UserReportingRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @Route("/moderator/user/list", name="moderator_user_list", methods="GET")
     */
    public function showUserList(UserRepository $userRepository, GenreRepository $genre)
    {
        return $this->render('moderator/user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'genre' => $genre
        ]);
    }

    /**
     * @Route("/moderator/user/{slug}", name="moderator_user_show", methods="GET")
     */
    public function showUser(User $user, UserReportingRepository $userReportRepository, EventReportingRepository $eventReportRepository)
    {
        return $this->render('moderator/user/show.html.twig', [
            'user' => $user,
            'userReports' => $userReportRepository->findBy(['user' => $user]),
            'eventReports' => $eventReportRepository->findBy(['user' => $user]),
        ]);
    }

    /**
     * @Route("/moderator/user/{slug}/update", name="moderator_user_update", methods="GET|POST")
     */
    public function updateUser(Request $request, User $user) : Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('moderator_user_update', ['id' => $user->getId()]);
        }

        return $this->render('moderator/user/update.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/moderator/user/{genre}/list", name="moderator_user_genre_list", methods="GET")
     */
    public function showGenreList(UserRepository $userRepository, $genre)
    {
        return $this->render('moderator/user/index.html.twig', [
            'users' => $userRepository->findUserByGenreName($genre)
        ]);
    }

    /**
     * @Route("/moderator/user/delete/{slug}", name="moderator_user_delete", methods="DELETE")
     */
    public function deleteUser(Request $request, User $user) : Response
    {

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('moderator_user_list');

    }
}