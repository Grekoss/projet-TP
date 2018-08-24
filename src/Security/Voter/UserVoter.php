<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;

class UserVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['RIGHTUSER'])
            && $subject instanceof User;
    }

    protected function voteOnAttribute($attribute, $rightUser, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'RIGHTUSER':
               return $this->canEdit($rightUser, $user);
                break;
            // case 'VIEW':
                // logic to determine if the user can VIEW
                // return true or false
                // break;
        }

       // return false;
    }

    private function canEdit(User $rightUser, User $user)
    {
       
            return $rightUser == $user;
        
    }
}
