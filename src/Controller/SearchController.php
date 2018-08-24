<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Repository\UserRepository;
use App\Repository\DepartmentRepository;
use App\Repository\EventRepository;
use App\Entity\Region;
use App\Entity\Tag;
use Symfony\Component\HttpFoundation\Request;
use App\Form\SearchType;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @Route("/search")
 */
class SearchController extends Controller
{
    /**
     * @Route("/users/department/{int}", name="search_usersByDepartment")
     */
    public function findUsersByDepartment($int, UserRepository $userRepository, DepartmentRepository $departmentRepository)
    {
        $department = $departmentRepository->findDepartmentByName($int);

        return $this->render('search/list_users.html.twig', [
            'titleSearch' => 'Recherche par Département : '. $department[0],
            'users' => $userRepository->findUsersbyDepartement($department),
            'searchName' => 'usersByDepartment'
        ]);
    }

    /**
     * @Route("/users/region/{id}", name="search_usersByRegion")
     */
    public function findUsersByRegion(Region $region, UserRepository $userRepository)
    {
        return $this->render('search/list_users.html.twig', [
            'titleSearch' => 'Recherche par Région : ' . $region->getName(),
            'users' => $userRepository->findUsersbyRegion($region),
            'searchName' => 'usersByRegion'
        ]);
    }

    /**
     * @Route("/events/department/{int}", name="search_eventsByDepartment")
     */
    public function findEventsByDepartment($int, DepartmentRepository $departmentRepository, EventRepository $eventRepository)
    {
        $department = $departmentRepository->findDepartmentByName($int);

        return $this->render('search/list_events.html.twig', [
            'titleSearch' => 'Recherche par Départment : '. $department[0],
            'events' => $eventRepository->findEventsbyDepartment($department),
            'searchName' => 'eventsByDepartment'
        ]);
    }

    /**
     * @Route("/events/region/{id}", name="search_eventsByRegion")
     */
    public function findEventsByRegion(Region $region, EventRepository $eventRepository)
    {
        return $this->render('search/list_events.html.twig', [
            'titleSearch' => 'Recherche par Région : '. $region->getName(),
            'events' => $eventRepository->findEventsbyRegion($region),
            'searchName' => 'eventsByRegion'
        ]);
    }

    /**
     * @Route("/events/tag/{slug}", name="search_eventsByTag")
     */
    public function findEventByTag(Tag $tag, EventRepository $eventRepository)
    {
        return $this->render('search/list_events.html.twig', [
            'titleSearch' => 'Recherche par Tag : '. $tag->getName(),
            'events' => $eventRepository->findEventsByTag($tag),
        ]);
    }

    /**
     * @Route("/events/search/", name="search_bar")
     */
    public function findEventWithSearchBar(EventRepository $eventRepository, Request $request)
    {
        $search = $request->query->get('search-bar');
        
        return $this->render('search/list_events.html.twig', [
                'titleSearch' => 'Recherche du mot clé : '. $search,
                'events' => $eventRepository->findEventsWithBarSearch($search),
            ]); 
    }

    /**
     * @Route("/", name="search")
     */
    public function search(Request $request, EventRepository $eventRepository, UserInterface $userInterface, UserRepository $userRepository)
    {

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Variables list for General:
                $department = false;
                $region = false;
                
                // If choiceLocation is chosen then we look at the value of the radio button to determine the request to add
                switch ($data['choiceLocation']) {
                    
                    case 'myDepartment' : 
                        $department = ($userInterface->getDepartment());
                        break;
            
                    case 'myRegion' : 
                        $region = ($userInterface->getDepartment()->getRegion());
                        break;
            
                    case 'otherDepartment' :
                        $department = $data['selectDepartment'];
                        break;
            
                    case 'otherRegion' :
                        $region = $data['selectRegion'];
                        break;
            
                    case 'all' :
                        break;
                }   
                
                if ($data['selectRequete'] === 'events') {
                    
                    // Variables list for Events:
                    $dateStart = $data['dateStart'];
                    $dateEnd = $data['dateEnd'];
                    $selectTags = false;
                    $friend = $data['isFriend'];
                    
                    // Conditions for variables :
                    if ($data['isRatingOrganizer']) {
                        $rating = $data['ratingSelectorOrganizer'];
                    } else {
                        $rating = 0;
                    }
                    
                    if (!empty($data['selectTags'])) {
                        $selectTags = $data['selectTags'];
                    }

                    $events = $eventRepository->searchCustom($dateStart, $dateEnd, $rating, $department , $region, $selectTags, $friend, $userInterface );

                    return $this->render('search/list_events.html.twig', [
                        'titleSearch' => 'Recherche personnalisée',
                        'events' => $events,
                    ]); 
                }
                else if ($data['selectRequete'] === 'members') {

                    // Variables list for Members:
                    $new = $data['isNew'];
                    $isAge = $data['isAge'];
                    $ageStart = date('Y-m-d', strtotime('- ' . $data['ageStart'] . ' year'));
                    $ageEnd = date('Y-m-d', strtotime('- ' . $data['ageEnd'] . ' year'));
                    $genre = false;

                    // Conditions for variables :
                    if ($data['isRatingMember']) {
                        $rating = $data['ratingSelectorMember'];
                    } else {
                        $rating = 0;
                    }
                    if ($data['isAge']) {
                        $ageStart = date('Y-m-d', strtotime('- ' . $data['ageStart'] . ' year'));
                        $ageEnd = date('Y-m-d', strtotime('- ' . $data['ageEnd'] . ' year'));
                    }
                    if ($data['isGenre']) {
                        $genre = $data['genre'];
                    }

                    $users = $userRepository->searchCustom($new, $rating, $ageStart, $ageEnd, $genre, $department, $region);

                    return $this->render('search/list_users.html.twig', [
                        'titleSearch' => 'Recherche personnalisée',
                        'users' => $users,
                    ]);
                }
                else {
                    dump('ECHEC');
                }


            return $this->render('search/search.html.twig', [
                'form' => $form->createView(),
            ]);

        }

        return $this->render('search/search.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
