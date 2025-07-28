<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Dto\UserRegistrationDto;
use App\Entity\User;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

class RegisterController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        #[MapRequestPayload] UserRegistrationDto $userDto,
        ValidatorInterface $validator,
        UserService $userService
    ): JsonResponse {
        $errors = $validator->validate($userDto);
        if (count($errors) > 0) {
            return $this->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => (string) $errors,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $userService->registerUser($userDto);

        return $this->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'data' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
            ],
        ], JsonResponse::HTTP_CREATED);
    }  
}