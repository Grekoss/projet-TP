<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\RelationShip;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\RoleRepository;
use App\Repository\LinkRepository;
use App\Repository\RelationShipRepository;
use App\Repository\EventRepository;
use App\Repository\ParticipationRepository;
use App\Repository\FollowingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Service\FileUploaderUser;
use App\Service\SendMail;
use App\Service\Slugger;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;




/**
 * @Route("/user")
 */
class UserController extends Controller
{
    private $friendLink;
    private $blacklistLink;
    private $relRepo;

    public function __construct(LinkRepository $lRepo, RelationShipRepository $relRepo)
    {
        $this->friendLink = $lRepo->getFriendLink();
        $this->blacklistLink = $lRepo->getBlackListLink();
        $this->relRepo = $relRepo;
        //parent::__construct();
    }
    
    /**
     * @Route("/", name="user_index", methods="GET")
     */
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $users = $userRepository->findAllUsersbyUsername();

        $paginator = $this->get('knp_paginator');

        $result = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10)
        );

        return $this->render('user/index.html.twig', [
            'users' => $result,
            ]);
    }

    /**
     * @Route("/new", name="user_new", methods="GET|POST")
     */
    public function new(Request $request, RoleRepository $repo, UserPasswordEncoderInterface $encoder, FileUploaderUser $fileUploaderUser, SendMail $sendMail, Slugger $slugger): Response
    {
        $user = new User();
        // On récupère le (ROLE_USER) dans la BDD
        $roleUser= $repo->findUserRole();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if ($form->isValid() && $form->get('validConditions')->getData() == true) {

                $file = $form->get('avatar')->getData();
                // Upload + strores image name only if file is present
                if ($file !== null) {
                    // upload and we get the name of the file
                    $fileName = $fileUploaderUser->upload($file);
                    // updates the avatar property to store the JPEG file name
                    // instead of its contents
                    $user->setAvatar($fileName);
                }
                
                $user->setRole($roleUser);
                // On encode le MDP
                $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
                $user->setSlug($slugger->slugify($user->getUsername()));
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
        
                // Send Mail if the new user check (isMailing)
                if( $user->getIsMailing() == true) {
                    $sendMail->mail(
                        'Inscription sur le site "Prenons l\'air"',
                        'dev.prenonslair@gmail.com',
                        $user->getEmail(),
                        $this->renderView('user/new_mail.html.twig', [
                            'username' => $user->getUsername(),
                        ])
                    );
                }

                $this->addFlash('success', 'Inscription réussite, vous pouvez vous connecter!');

                return $this->redirectToRoute('login');
            }
            else {
                $this->addFlash('warning', 'Conditions pas validées');
            }
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="user_show", methods="GET")
     */
    public function show(User $user, AuthorizationCheckerInterface $authChecker, EventRepository $eventRepository): Response
    {
        $participationList = $eventRepository->findAllEventsbyUser($user);
        $organizerList = $eventRepository->findAllEventsOrganizebyUser($user);
        // Look if is a member
        if (true === $authChecker->isGranted('ROLE_USER')) {

            $isFriend= $this->isFriend($user);
            $isBlacklisted = $this->isBlacklisted($user);
            

            return $this->render('user/show.html.twig', [
                'user' => $user,
                'isFriend'=> $isFriend,
                'isBlacklisted' => $isBlacklisted,
                'participationList' => $participationList,
                'organizerList' => $organizerList,
                'method'=> 'show',
                ]);
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'participationList' => $participationList,
            'organizerList' => $organizerList,
            'method' =>'show'
        ]);
    }

    /**
     * @Route("/{id}/friends", name="user_friends", methods="GET")
     */
    public function showFriends( LinkRepository $repoL )   //display current user's contacts list
    {
        $friends =  $this->relRepo-> findFriendsByUser ($this->getUser(), $repoL);

        return $this->render('user/friends.html.twig', [
            'users'=> $friends,
            ]);
    }

    /**
     * @Route("/{id}/notifications", name="user_notifications", methods="GET")
     */
    public function showNotifs(User $user)   //display current user's notifications list
    {
        $notifications =  $user->getNotifications();

        return $this->render('notification/index.html.twig', [
            'notifications'=> $notifications,
            'relation' => 'friend'
            ]);
    }

    /**
     * @Route("/{slug}/edit", name="user_edit", methods="GET|POST")
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $encoder, FileUploaderUser $fileUploaderUser, Slugger $slugger): Response
    {   
        //We store the current password
        $currentPassword = $user->getPassword();
        //and we done empty
        $user->setPassword('');

        // Like the password for the avatar
        $currentAvatar = $user->getAvatar();
        // and we done empty
        $user->setAvatar('');

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            if (empty($user->getPassword())) {
                // if empty then the user no change the password
                $user->setPassword($currentPassword);
            } else {
                // Encode new password
                $passwordEncoded = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($passwordEncoded);
            }
    
            if (empty($user->getAvatar())) {
                $user->setAvatar($currentAvatar);
            } else {
                $file = $form->get('avatar')->getData();
                // Upload + strores image name only if file is present
                if ($file !== null) {
                    // upload and we get the name of the file
                    $fileName = $fileUploaderUser->upload($file);
                    // updates the avatar property to store the JPEG file name
                    // instead of its contents
                    $user->setAvatar($fileName);
                }
            }
    
            $user->setSlug($slugger->slugify($user->getUsername()));
            
            $this->getDoctrine()->getManager()->flush();
            
            $this->addFlash('success', 'Les modifications sont bien enregistrées!');

       //     return $this->redirectToRoute('homepage');    
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods="DELETE")
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/{id}/add", name="add_friend")
     */
    public function addFriend (User $addedUser) //allows the connected user to add  as a friend (contact) the current user (button on the current user 's profile)
    {   
        if (!$this->isFriend($addedUser)) {
            $relationship = new RelationShip();
            $relationship->setUserMain($this->getUser());
            $relationship->setLink($this->friendLink);
            $relationship->setUserConcerned($addedUser);
            $em = $this->getDoctrine()->getManager();
            $em->persist($relationship);
            $em->flush();
            $this->addFlash('success', 'Cette personne été ajoutée à vos contacts');

            return $this->redirectToRoute('user_show', ['slug' => $addedUser->getSlug()]);

        } else {
            $this->addFlash('danger', 'Cette personne est déjà dans vos contacts');

            return $this->redirectToRoute('user_show', ['slug' => $addedUser->getSlug()]);
        }
    }

    /**
     * @Route("/{id}/friend/remove", name="remove_friend")
     *
     */
    public function removeRelation(User $userConcerned ) // function to remove a user from current user's friendslist
    {
        $userMain = $this->getUser();
        $relationship = $this->relRepo->findRelation($userMain, $userConcerned);
        $em = $this->getDoctrine()->getManager();
        $em->remove($relationship);
        $em->flush();
        
        $this->addFlash('warning', 'Cette personne été retirée de votre liste noire');

        return $this->redirectToRoute('user_show', ['slug' => $userConcerned->getSlug()]);
    }

     /**
     * @Route("/{id}/blacklist/add", name="blacklist_add")
     */
     public function blacklist (User $userConcerned)
     {
        $userMain = $this->getUser(); 
        $relationship = $this->relRepo->findRelation($userMain, $userConcerned);
        
        if(false == $relationship) {
            $relation = new RelationShip();
            $relation->setUserMain($this->getUser());
            $relation->setLink($this->blacklistLink);
            $relation->setUserConcerned($userConcerned);

            $em = $this->getDoctrine()->getManager();
            $em->persist($relation);
            $em->flush();
            
            $this->addFlash('warning', 'Cette personne a été ajoutée à votre liste noire');

            return $this->redirectToRoute('user_show', [
                'slug' => $userConcerned->getSlug(),
            ]);
        } else {
            $relationship->setLink($this->blacklistLink);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('warning', 'Cette personne a été ajoutée à votre liste noire');

            return $this->redirectToRoute('user_show', [
                'slug' => $userConcerned->getSlug(),
            ]);
        }
     }

     /**
      * @Route("/{id}/blacklist", name="user_blacklist")
      */
     public function showBlacklist( LinkRepository $repoL )
     {
        $blackList =  $this->relRepo-> findBlacklistByUser ($this->getUser(), $repoL);

        return $this->render('user/blacklist.html.twig', [
            'users'=> $blackList,
            'relation' => 'blacklist'
            ]);
     }

     private function isFriend ( User $userConcerned) //function to check if th e visited user is friend with the current User
     {
        $userMain = $this->getUser(); 
        $relationship = $this->relRepo->findRelation($userMain, $userConcerned);
        if ($relationship == true && $relationship->getLink() == 'friend' ) {

            return true;
        } else {

            return false;
        }
     }

     private function isBlacklisted( User $userConcerned) // function to check if the visited user is already blacklisted by the current user
     {
        $userMain = $this->getUser(); 
        $relationship = $this->relRepo->findRelation($userMain, $userConcerned);
        if ($relationship == true && $relationship->getLink() == 'blackListed' ) {

            return true;
        } else {

            return false;
        }
     }
    
    public function HasUserRated($event, ParticipationRepository $repo)
    {
        $participation = $repo->findOneParticipation ($event, $this->getUser());
        $hasRated = $participation->getHasrated();
        if (!$hasRated)
            return false;

    }
    /**
     * @Route("/{id}/followed", name="user_followed")
     */

    public function showFollowedEvents(User $user, FollowingRepository $repo)
    {   
        $events= $repo->getFollowedEvents($user);
        return $this->render('user/followed.html.twig', [
            'user'=> $user,
            'followedEvents' => $events, 
            'method' =>'follow',
        ]);
        
    }
}
