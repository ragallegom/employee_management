<?php

namespace App\Controller\Api;

use App\Entity\Employee;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EmployeeRepository;
use App\Service\PositionService;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Dto\EmployeeInput;
use App\Dto\EmployeeOutput;

#[Route('/api/employees', name: 'api_employees_')]
final class EmployeeController extends AbstractController
{
    #[Route('/', name: 'list', methods: ['GET'])]
    public function list(EmployeeRepository $employeeRepository): JsonResponse
    {
        $employees = $employeeRepository->findAll();
        
        $output = array_map(
            fn(Employee $employee) => EmployeeOutput::fromEntity($employee),
            $employees
        );

        return new JsonResponse($output);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request, 
        SerializerInterface $serializer,
        ValidatorInterface  $validator,
        EntityManagerInterface $entityManager,
        PositionService $positionService
    ): JsonResponse {
        $input = $serializer->deserialize(
            $request->getContent(),
            EmployeeInput::class,
            'json'
        );

        $errors = $validator->validate($input);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], 400);
        }

        if (!$positionService->isValidPosition($input->position)) {
            return new JsonResponse(['error' => 'Invalid position'], 400);
        }

        $employee = new Employee();
        $employee->setName($input->name);
        $employee->setEmail($input->email);
        $employee->setPosition($input->position);

        $employee->setCreatedAt(new \DateTimeImmutable());
        $employee->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->persist($employee);
        $entityManager->flush();

        return new JsonResponse(EmployeeOutput::fromEntity($employee), 201);
    }
}
