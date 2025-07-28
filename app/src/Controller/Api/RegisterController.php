<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Dto\UserRegistrationDto;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

class RegisterController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserService $userService
    ): JsonResponse {

        $userDto = $serializer->deserialize(
            $request->getContent(),
            UserRegistrationDto::class,
            'json'
        );

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