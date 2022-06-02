<?php

namespace App\Security\Voter;

use App\Entity\Movie;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class MovieRatedVoter extends Voter
{
    public const RATED = 'RATED';

    protected function supports(string $attribute, $subject): bool
    {
        return self::RATED === $attribute
            && $subject instanceof Movie;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        assert($subject instanceof Movie);
        if ('G' === $subject->getRated()) {
            return true;
        }

        $user = $token->getUser();
        if (!$user instanceof User || !$user->getBirthday()) {
            return false;
        }
        $age = (new \DateTime())->diff($user->getBirthday())->y;

        return $this->voteOnAge($age, $subject);
    }

    private function voteOnAge(int $age, Movie $movie): bool
    {
        switch ($movie->getRated()) {
            case 'PG':
            case 'PG-13':
                if ($age >= 13) {
                    return true;
                }
                break;
            case 'R':
            case 'NC-17':
                if ($age >= 17) {
                    return true;
                }
                break;
        }

        return false;
    }
}
