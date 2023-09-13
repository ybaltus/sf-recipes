<?php

namespace App\Security\Voter;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    public const EDIT = 'user_edit';
    public const VIEW = 'user_view';

    public function __construct(
        private Security $security,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW])
            && (($subject instanceof Ingredient) or ($subject instanceof Recipe));
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        return match ($attribute) {
            self::EDIT, self::VIEW => $this->canEdit($subject, $user),
            default => throw new \LogicException('This code should not be reached!'),
        };
    }

    private function canEdit(Ingredient|Recipe $ingredient, User $user): bool{
        return $this->security->isGranted('ROLE_USER') && $user === $ingredient->getUser();
    }
}
