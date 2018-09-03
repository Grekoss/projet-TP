<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Comment;
use App\Entity\EventReporting;
use App\Form\EventType;
use App\Form\CommentType;
use App\Form\OrganizerType;
use App\Repository\EventRepository;
use App\Repository\RelationShipRepository;
use App\Repository\ParticipationRepository;
use App\Repository\FollowingRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Slugger;
use App\Entity\User;
use App\Entity\Participation;
use App\Entity\Following;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Service\FileUploaderEvent;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use App\Entity\Notification;
use App\Service\SendMail;
use Symfony\Component\Form\FormError;


/**
 * @Route("/event")
 */
class EventController extends Controller
{

    private $relRepo;
    private $userRepo;
    private $sendMail;
    private $partRepo;
    
    public function __construct (RelationShipRepository $relRepo, UserRepository $userRepo, SendMail $sendMail, ParticipationRepository $partRepo)
    {
        $this->relRepo = $relRepo;
        $this->userRepo = $userRepo;
        $this->sendMail= $sendMail;
        $this->partRepo= $partRepo;
        
    }
    
    /**
     * @Route("/list/", name="event_list", methods="GET")
     */
    public function index(EventRepository $eventRepository, Request $request): Response
    {
        $now= new \DateTime("now");
        
        $events = $eventRepository->findAllEvents();

        $paginator = $this->get('knp_paginator');

        $result = $paginator->paginate(
            $events,                                /* Query */
            $request->query->getInt('page', 1),     /* page number */
            $request->query->getInt('limit', 8)     /* limit per page */
        );
       
        if($this->isGranted('IS_AUTHENTICATED_FULLY')){
            // if connected, a user only sees events he's allowed to see  (not blacklisted by organizer or in the event's age limits) 
            $age = date_diff($this->getUser()->getBirthDate(),$now)->format('%y');

            $connectedEvents = $eventRepository->findAllEventsConnected($age, $this->getUser());
            $forbiddenEvents = $eventRepository->findForbiddenEvents($this->getUser());

            $result = $paginator->paginate(
                $connectedEvents,
                $request->query->getInt('page', 1),
                $request->query->getInt('limit', 8)
            );

            
            return $this->render('event/index.html.twig',[
                'events' => $result,
                'forbidden' => $forbiddenEvents,
                'searchName' => 'eventsByDepartment'
                ]);
        } else {

            return $this->render('event/index.html.twig',[
                'events' => $result,
                'searchName' => 'eventsByDepartment'
                ]);
        }
    }

    /**
     * @Route("/new", name="event_new", methods="GET|POST")
     */
    public function new(Request $request,Slugger $slugger, FileUploaderEvent $fileUploaderEvent): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Impossible d\'accéder à cette page!');
        $event = new Event();
        $participation =new Participation();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        $url = $request->server->get('HTTP_HOST');
        $url .= '/event/';
        dump($url);

        if ($form->isSubmitted()) {

            $file = $form->get('photo')->getData();
            // Upload + stores image name only if file is present
            if ($file !== null) {
                // upload and we get the name of the file
                $fileName = $fileUploaderEvent->upload($file);
                // updates the photo property to store the JPEG file name
                // instead of its contents
                $event->setPhoto($fileName);
            }
            
            if ($form->isValid()) {
                $interval= $form->get('timeInterval')->getData()['interval'];
                $now= new \DateTime();
                //As time is composed of 2 parts, they must be reunited in order to have correct timers and dates 
                //Never forget that time modificators only apply to the object in place so you can't reassign the value. You have to clone the variable instead 
                $date =  clone $event->getDateAt();
                $time= date_parse($event->getTimeAt()->format('Y-m-d H:i:s'));
                $dateTime = $date->setTime($time['hour'],$time['minute']);
                //checks if joinTimeLimit is not in the past
                $testTime = clone $dateTime;

                if ($testTime->modify($interval) > $now && $form->isValid()) {
                    
                    
                    $event->setSlug($slugger->slugify($event->getName()));
                    $event->setOrganize($this->getUser());
                    $event->setJoinTimeLimit($dateTime->modify($interval));
                    $participation->setParticipant($event->getOrganize());
                    $participation->setEvent($event);
                    $em = $this->getDoctrine()->getManager();
                
                    if($event->getVisibility()->getTitle() == 'Amis') {
                    $event->setMinAge(18);
                    $event->setMaxAge(85);
                    $friends = $this->userRepo->findFriends($this->getUser());
                    foreach($friends as $friend){
                        if(!$this->isCurrentUserBlacklisted($friend)){
                            $notification = new Notification();
                            $notification->setTitle('Vous avez reçu une invitation!');
                            $notification->setBody($event->getOrganize().' vous a invité à sa sortie '. $event->getName().' <a href="www.prenonslair.com/event/'.$event->getSlug().'">Voir</a>');
                            $notification->setSendee($friend);
                            $em->persist($notification);
                            
                            if ($friend->getIsMailing()){
                                $subject=  'Prenons l\'air - '.$notification->getTitle();
                                $admin = 'dev.prenonslair@gmail.com';
                                $member = $friend->getEmail();
                                $message = $notification->getBody().'<br> <br> Cordialement, l\'équipe de Prenons l\'air.';
                                $this->sendMail->mail($subject,$admin,$member,$message);
                            }
                        }                        
                    } 
                }
                $em->persist($event);
                $em->persist($participation);
                $em->flush();
                
                return $this->redirectToRoute('event_list');

            } else  {
                $this->addFlash ('danger', 'Erreur! Vous ne pouvez pas mettre une date limité dans le passé!');
            }
        }
    }
  
        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="event_show", methods="GET|POST")
     */
    public function show(Event $event, Request $request, AuthorizationCheckerInterface $authChecker,ParticipationRepository $repo, FollowingRepository $fRepo): Response
    {
        $participants =$repo->getParticipants($event);
        $followedEvents = $fRepo->getFollowedEvents($this->getUser());
        
        // Look if is not a visiter
        if (true === $authChecker->isGranted('ROLE_USER')) {

            // if is not in the blacklist
            if(!$this->isCurrentUserBlacklisted($event->getOrganize())) {
                $timedOut = $this->IsTimedOut($event);
                $cantJoin = $this->cantJoin($event);
                $isFollowed= $event->isFollowedBy($this->getUser());
    
                $comment = new Comment();
                $form = $this->createForm(CommentType::class, $comment);
                $form->handleRequest($request);
    
                if ($form->isSubmitted() && $form->isValid()) {
    
                    $comment->setUser($this->getUser());
                    $comment->setEvent($event);

                    $notif= new Notification();
                    $notif->setTitle('Vous avez un nouveau commentaire');
                    $notif->setBody($comment->getUser().' a commenté vorte sortie '.$event->getName().' <a href="/event/'.$event->getSlug().'">Voir</a>');
                    $notif->setSendee($event->getOrganize());
    
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($comment);
                    $em->flush();
    
                    return $this->redirectToRoute('event_show', [
                        'slug' => $event->getSlug(),
                    ]);
                }
               
                return $this->render('event/show.html.twig', [
                    'event' => $event,
                    'participants' => $participants,
                    'form' => $form->createView(),
                    'timedOut' => $timedOut,
                    'isFollowed'=> $isFollowed,
                    'cantJoin' => $cantJoin,
                ]);
            }
        } else {

            return $this->render('event/show.html.twig', [
                'event' => $event,
                'participants' => $participants,
            ]);
        }
    }
        
    /**
     * @Route("/{slug}/update", name="event_update", methods="GET|POST")
     */
    public function update(Request $request, Event $event, FileUploaderEvent $fileUploaderEvent): Response
    {
        $participation = $this->partRepo->findOneParticipation($event,$this->getUser());
        //Only organizer can update his own event
        $this->denyAccessUnlessGranted('ORGANIZER', $participation,'Vous n\'avez pas accès à cette page!');
        // We memorize our image to preserve it if we do not modify it
        $fileUploaderEvent->setCurrentFile($event->getPhoto());

        // If the string that stores our file is not empty
        if (!empty($event->getPhoto())) {
            // You must recover the existing file on the server, via the Symfony File type
            $event->setPhoto(
                new File($this->getParameter('photos_directory') . '/' . $event->getPhoto())
            );
        }

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('photo')->getData();
            // Upload + stores image name only if file is present
            if ($file !== null) {
                // upload and we get the name of the file
                $fileName = $fileUploaderEvent->upload($file);
                // updates the photo property to store the JPEG file name
                // instead of its contents
                $event->setPhoto($fileName);
                
            }
            $interval= $form->get('timeInterval')->getData()['interval'];
            $testTime = clone $event->getDateAt();
            $time= date_parse($event->getTimeAt()->format('Y-m-d H:i:s'));
            $dateTime = $testTime->setTime($time['hour'],$time['minute']);
            $event->setJoinTimeLimit($dateTime);
            $event->getJoinTimeLimit()->modify($interval);
            
            $this->getDoctrine()->getManager()->flush();


            return $this->redirectToRoute('event_show', ['slug' => $event->getSlug()]);

        }

        return $this->render('event/update.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}/delete", name="event_delete", methods="DELETE")
     */
    public function delete(Request $request, Event $event, EventReporting $eventRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getSlug(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($event);
            $em->flush();
        }

        return $this->redirectToRoute('event_list');
    }

     /**
     * @Route("/{slug}/join", name="event_join", methods="GET")
     */
        //function that allows a user to join an event #antho
     public function join(Event $event, UserInterface $user)
    {
        if (count($event->getParticipations()) <= 20 && false == $this->isTimedOut($event)){
            $participation = new Participation();
            $participation->setParticipant($user);
            $participation->setEvent($event);
            $notif = new Notification();
            $notif->setTitle('Vous avez une inscription!');
            $notif->setBody('<a class="btn btn-link" href="/user/'.$this->getUser()->getSlug().'">'.$this->getUser()->getUsername().'</a> s\'est inscrit à votre sortie' .$event->getName().' <a class="btn btn-link" href="/event/'.$event->getSlug().'">Voir</a>');
            $notif->setSendee($event->getOrganize());
            $em = $this->getDoctrine()->getManager();
            $em->persist($participation);
            $em->persist($notif);
            $em->flush();
            if ($event->getOrganize()->getIsMailing()){
                $subject=  'Prenons l\'air - '.$notif->getTitle();
                $admin = 'dev.prenonslair@gmail.com';
                $member = $event->getOrganize()->getEmail();
                $message = 'Bonjour, <br><br>'.$notif->getBody().'<br> <br> Cordialement, l\'équipe de Prenons l\'air.';
                $this->sendMail->mail($subject,$admin,$member,$message);
                }
        }else {
            $this->addFlash ('danger', 'La sortie est déjà complète !');
        }

        if(count($event->getParticipations()) >= ($event->getParticipantsLimit())){
            $this->addFlash('warning', 'Vous avez été inscrit en liste d\'attente. Vous passerez en liste principale si une place se libère');
        } else {
            $this->addFlash ('success', 'Vous êtes bien inscrit à la sortie!');
        }
        
        return $this->redirectToRoute('event_show', ['slug' => $event->getSlug()]);
    }

     /**
      * @Route("/{slug}/unjoin", name="event_unjoin", methods="GET|POST")
      */
    public function unjoin(Event $event, UserInterface $user, ParticipationRepository $repo)  //function that allows a user to unjoin an event
    {
        $participation = $repo->findOneParticipation($event, $user);
        if ($participation)
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($participation);

            $notif = new Notification();
            $notif->setTitle('Vous avez une désinscription!');
            $notif->setBody($this->getUser()->getUsername().' s\'est désinscrit de votre sortie' .$event->getName().' <a href="/event/'.$event->getSlug().'">Voir</a>');
            $notif->setSendee($event->getOrganize());
            $em = $this->getDoctrine()->getManager();
        
            $em->persist($notif);
            $em->flush();
            $this->addFlash ('success', 'Vous êtes bien désinscrit de la sortie!');
            if ($event->getOrganize()->getIsMailing()){
                $subject=  'Prenons l\'air - '.$notif->getTitle();
                $admin = 'dev.prenonslair@gmail.com';
                $member = $event->getOrganize()->getEmail();
                $message = 'Bonjour, <br><br>'.$notif->getBody().'<br> <br> Cordialement, l\'équipe de Prenons l\'air.';
                $this->sendMail->mail($subject,$admin,$member,$message);
                }

            return $this->redirectToRoute('event_show', ['slug' => $event->getSlug()]);
        }

        return $this->redirectToRoute('event_show', ['slug' => $event->getSlug()]);
    }

    private function isCurrentUserBlacklisted($userMain)
    {
        $userConcerned = $this->getUser(); 
        $relationship = $this->relRepo->findRelation($userMain, $userConcerned);
        if ($relationship == true && $relationship->getLink() == 'blackListed' ) {

            return true;
        }
    }

    private function isTimedOut(Event $event) {

        $date = new \DateTime();
        $time= date_parse($event->getTimeAt()->format('Y-m-d H:i:s'));
        $dateTime = $event->getDateAt()->setTime($time['hour'],$time['minute']);
        
        if ($dateTime < $date) {
            return true;
        }  
    }
    
    private function cantJoin(Event $event) {

        $time= new \DateTime();

        if ($event->getJoinTimeLimit() > $time) {
            return true;
        }
    }

    /**
     * @Route("/{slug}/rating", name="event_rating", methods = "GET")
     */ 
    public function showRatingForm(Event $event, ParticipationRepository $repo) 
    { //function that bring to the event's rating page
        
        $participation = $repo->findOneParticipation($event,$this->getUser());
       
        $participants =$repo->getParticipants($event);
        $slug = $event->getSlug();

        if ($this->getUser() == $event->getOrganize()) {
            $this->denyAccessUnlessGranted('ORGANIZER', $participation, 'Vous avez déjà noté cet évènement!');

            return $this->render('rating/_organizer.html.twig', [
                'participants' => $participants,
                'slug' => $slug,
           ]);
        } else {
            $this->denyAccessUnlessGranted('PARTICIPANT', $participation, 'Vous n\'avez pas accès à cette page!');

            return $this->render('rating/_user.html.twig', [
                'participants' => $participants,
                'slug' => $slug,
                ]);
        }
    }

    /**
     * @Route("/{slug}/rating", name="organizer_rating", methods = "POST")
     */

     public function organizerRating(Event $event, Request $request, ParticipationRepository $repo)
     {
        $participants =$repo->getParticipants($event);
        //The organizer rates his own presence  but can't rate his own behaviour and the mark is neither added in the number of evaluation nor in its rating
        $orgParticipation = $repo->findOneParticipation($event, $participants[0]);

        $presenceOrga =$request->request->get('presence'.'0');
        if  ($presenceOrga == 5) {
            
            $orgParticipation->setIsReal(true);
        
            for($i=1; $i< count($participants); $i ++){
                $presence = $request->request->get('presence'.$i);
                $behavior= $request->request->get('comportement'.$i);

                if  ($presence == 5){
                    ($participants[$i]);
                    $mark = ($presence +$behavior+$participants[$i]->getRating())/3;

                    $participants[$i]->setRating($mark);
                    $participants[$i]->setEvalCount($participants[$i]->getEvalCount()+1);
                        
                    //Get the partipant's participation and indicates that the person was really present, that allow him to rate others participants
                    $participation = $repo->findOneParticipation($event, $participants[$i]);
                    $participation->setIsReal(true);
                    //A notification is sent to real participants to inivite them to rate each other
                    $notification = new Notification();
                    $notification->setTitle('Vous avez une évaluation à faire :'.$event->getName());
                    
                    $temp = '<p>Pour que le site reste un espace agréable, veuillez évaluer les autres participants de la sortie  en allant sur ce lien:</p>';
                    $temp .= '<a href="/event/'.$event->getSlug().'/rating ">évaluation</a>';
                
                    $notification->setBody(($temp));
                    $notification->setSendee($participants[$i]);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($notification);
                    $em->flush();

                    if ($participants[$i]->getIsMailing()){
                        $subject=  'Prenons l\'air - '.$notification->getTitle();
                        $admin = 'dev.prenonslair@gmail.com';
                        $member = $participants[$i]->getEmail();
                        $message = 'Bonjour, <br><br>'.$notification->getBody().'<br> <br> Cordialement, l\'équipe de Prenons l\'air.';
                        $this->sendMail->mail($subject,$admin,$member,$message);
                        }

                } else {
                    $mark = ($presence +$participants[$i]->getRating())/2;
                    $participants[$i]->setRating($mark);
                    $participants[$i]->setEvalCount($participants[$i]->getEvalCount()+1);
                    $em = $this->getDoctrine()->getManager();
                $em->flush();
                }
            }
        } 
        //indicates the organizer has rated and then can no longer do it
        $orgParticipation->setHasRated(true);
        $em = $this->getDoctrine()->getManager();
        $em->flush();
                    
        return $this->redirectToRoute('event_show', ['slug' => $event->getSlug()]); 
     }
   

     /**
     * @Route("/{slug}/user-rating", name="user_rating", methods = "POST")
     */
    public function UserRating(Event $event, Request $request, ParticipationRepository $repo)
    {
        $participants =$repo->getParticipants($event);
        dump($request);

        for($i=0; $i< count($participants)-1; $i ++)
        {
            $behavior= $request->request->get('comportement'.$i);
            if(isset($behavior)){
                $mark = ($behavior+$participants[$i]->getRating())/2;
                $participants[$i]->setRating($mark);
                $participants[$i]->setEvalCount($participants[$i]->getEvalCount()+1);
                $em = $this->getDoctrine()->getManager();
                $em->flush();
            }
        }

        $participation = $repo->findOneParticipation($event, $this->getUser());
        $participation->setHasRated(true);
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        $this->addFlash('success', 'Votre notation a bien été prise en compte');

        return $this->redirectToRoute('event_show', ['slug' => $event->getSlug()]); 
    }

    /**
     * @Route("/{slug}/follow", name="event_follow")
     * 
     */
    public function followEvent(Event $event, FollowingRepository $repo)
    {
        $following = new Following();
        $following->setUser($this->getUser());
        $following->setEvent($event);
        if(!($this->getUser()->getFollowings()->contains($following))){
            $em = $this->getDoctrine()->getManager();
            $em->persist($following);
            $em->flush();
        }
        
        return $this->redirectToRoute('event_show', [
            'slug' => $event->getSlug(),
            ]); 
    }
}



/*$form = $this->createForm(OrganizerType::class, $event);
//$form->handleRequest($request);
dump($request);
if ($request->isMethod('POST')) {
    $form->submit($request->request->get($form->getName()));

    if ($form->isSubmitted() && $form->isValid()) 
    {   
        $participants = $form->get('participants')->getData();
    dump($participants);

    /* foreach($participants as $participant){
            if ('5'== $form->get('présence')->getData()) {
                $note = ($participant->get('présence')->getData()+ $form->get('participants')->get('comportement')->getData()) /2 ;    
            } else {
                $note = $form->get('présence')->getData();
            }
            $participant->setRating($note);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            }
    
        //return $this->redirectToRoute('event_show', ['slug' => $event->getSlug()]); 
        }
    } */