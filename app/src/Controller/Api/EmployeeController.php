<?php

namespace App\Controller\Api;

use App\Entity\Employee;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EmployeeRepository;

#[Route('/api/employees', name: 'api_employees_')]
final class EmployeeController extends AbstractController
{
    #[Route('/', name: 'list', methods: ['GET'])]
    public function list(EmployeeRepository $employeeRepository): JsonResponse
    {
        $employees = $employeeRepository->findAll();
        $data = [];

        foreach ($employees as $employee) {
            $data[] = [
                'id' => $employee->getId(),
                'name' => $employee->getName(),
                'email' => $employee->getEmail(),
                'position' => $employee->getPosition(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['name']) || empty($data['email']) || empty($data['position'])) {
            return new JsonResponse(['error' => 'Invalid input'], 400);
        }

        $employee = new Employee();
        $employee->setName($data['name']);
        $employee->setEmail($data['email']);
        $employee->setPosition($data['position']);

        $employee->setCreatedAt(new \DateTimeImmutable());
        $employee->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->persist($employee);
        $entityManager->flush();

        return new JsonResponse(['id' => $employee->getId()], 201);
    }
}
