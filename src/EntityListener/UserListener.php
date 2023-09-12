<?php

namespace App\EntityListener;

use App\Entity\User;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserListener
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    #[PrePersist]
    public function prePersistHandler(User $user, PrePersistEventArgs $event): void{
        $this->encodePassword($user);
    }
    #[PreUpdate]
    public function preUpdateHandler(User $user, PreUpdateEventArgs $event): void{
        // Do not triggered for edit password
//        $this->encodePassword($user);
    }

    private function encodePassword(User $user){
        if(!$user->getPlainPassword()) {
            return;
        }

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $user->getPlainPassword()
        );

        $user->setPassword($hashedPassword);

    }
}