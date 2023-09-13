<?php

namespace App\Security\Voter;

use App\Entity\Recipe;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class RecipeVoter extends Voter
{
    public const EDIT = 'recipe_edit';
    public const VIEW = 'recipe_view';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof Recipe;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute){
            self::EDIT, self::VIEW => $this->checkUser($subject, $user) || $this->checkIfPublic($subject),
            default => throw new \LogicException('This code should not be reached!'),
        };
    }

    private function checkIfPublic(Recipe $recipe): bool{
        return $recipe->isIsPublic();
    }

    private function checkUser(Recipe $recipe, UserInterface $user): bool{
        return $recipe->getUser() === $user;
    }
}
