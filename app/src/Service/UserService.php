<?php

namespace App\Service;

use App\Dto\UserRegistrationDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ){}

    public function registerUser(UserRegistrationDto $userDto): User
    {
        // Create a new User entity
        $user = new User();
        $user->setEmail($userDto->email);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $userDto->password);
        $user->setPassword($hashedPassword);

        // Persist the user entity to the database
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}