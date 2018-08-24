<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;

use App\Entity\Participation;



class UserRatingVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['ORGANIZER', 'PARTICIPANT'])
            && $subject instanceof Participation;
    }

    protected function voteOnAttribute($attribute, $participation, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'PARTICIPANT':
            return $this->userEdit($participation);
                break;
            case 'ORGANIZER':
            return $this->organizeView($participation, $user);
                break;
       
           }   //return false;
        
    }
     // Only the organizer can access the form  if not already submitted
      private function organizeView(Participation $participation, User $user)
        {
            $organizer= $participation->getEvent()->getOrganize();

                if( !($participation->getHasRated() && $user == $organizer)  ) {
                    return true;
                }
      
         }
            
    // Only a user whos has truly participated to an event and has not already submitted the form can access
         private function userEdit(Participation $participation)
         {
             if ($participation && $participation->getIsReal() && !$participation->getHasRated()){
                    return true ;
             }
        
            }

}
