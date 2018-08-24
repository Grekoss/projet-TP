<?php
namespace App\EventListener;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use App\Entity\User;
use App\Entity\Notification;
use App\Repository\EventRepository;
use App\Repository\ParticipationRepository;


/* EventListener that is supposed to  send notifications at conncetion to  an event organizer who has not rated his own events (does not work yet)*/
class UserListener
{
    private $erepo;
    private $partrepo;

    public function __construct(EventRepository $erepo, ParticipationRepository $partrepo)
    {
        $this->erepo= $erepo;
        $this->partrepo = $partrepo;
    }
        /** @PostLoad */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $entityManager = $args->getObjectManager();
        

        
        if ($entity instanceof User) {
            $events = $this->erepo->findFiveDaysEventsOrganizebyUser($entity);
            if ($events) {
                foreach ($events as $event) {
                    $participation =$this->partrepo->findOneParticipation($event, $entity);

                        if($participation->getIsReal() &&  false == $participation->getHasRated()){
                        $notification = new Notification();
                        $notification->setTitle('Vous avez un évènement à noter!');
                        $notification->setBody(' Pour maintenir la qualité du site vous devez noter votre évènement et le comportement des participants '. $event->getName().' <a href="/event/'.$event->getSlug().'/rating">Voir</a>');
                        $notification->setSendee($entity);
                        $entityManager->persist($notification);
                        $entityManager->flush();
                            if ($entity->getIsMailing()){
                                $subject=  'Prenons l\'air - '.$notification->getTitle();
                                $admin = 'dev.prenonslair@gmail.com';
                                $member = $entity->getEmail();
                                $message = 'Bonjour,<br><br>'.$notification->getBody().'<br> <br> Cordialement, l\'équipe de Prenons l\'air.';
                                $this->sendMail->mail($subject,$admin,$member,$message);
                                }
                        }
                    }
                }


        }
    }
}