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
use App\Service\ListService;
use App\Service\NotificationService;
use App\Service\SearchService;

#[Route('/api/employees', name: 'api_employees_')]
final class EmployeeController extends AbstractController
{

    #[Route('/', name: 'search_list', methods: ['GET'])]
    public function searchList(Request $request, SearchService $searchService): JsonResponse
    {
        $search = $request->query->get('search', '');
        $page = (int) $request->query->get('page', 1);
        $perPage = (int) $request->query->get('perPage', 10);

        $results = $searchService->searchPaginated($search, $page, $perPage);

        return new JsonResponse([
            'employees' => array_map(
                fn(Employee $employee) => EmployeeOutput::fromEntity($employee),
                $results['employees']
            ),
            'total' => $results['total'],
            'currentPage' => $page,
            'perPage' => $perPage,
        ]);
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function list(Request $request, ListService $listService): JsonResponse
    {
        $page = (int) $request->query->get('page', 1);
        $perPage = (int) $request->query->get('perPage', 10);

        $result = $listService->getPaginatedList($page, $perPage);

        return new JsonResponse($result);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request, 
        SerializerInterface $serializer,
        ValidatorInterface  $validator,
        EntityManagerInterface $entityManager,
        PositionService $positionService,
        NotificationService $notificationService
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

        $birthDate = \DateTimeImmutable::createFromFormat('Y-m-d', $input->birthDate);
        if (!$birthDate) {
            return new JsonResponse(['error' => 'Invalid birth date format'], 400);
        }

        $employee = new Employee();
        $employee->setName($input->name);
        $employee->setLastName($input->lastName);
        $employee->setEmail($input->email);
        $employee->setPosition($input->position);
        $employee->setBirthDate($birthDate);
        $employee->setUser($this->getUser()); // Assuming the user is set from the authenticated session
        if (!$employee->getUser()) {
            return new JsonResponse(['error' => 'User not found'], 400);
        }

        $employee->setCreatedAt(new \DateTimeImmutable());
        $employee->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->persist($employee);
        $entityManager->flush();

        $notificationService->notify([
            'name' => $employee->getName(),
            'email' => $employee->getEmail()
        ]);

        return new JsonResponse(EmployeeOutput::fromEntity($employee), 201);
    }

    #[Route('/{id}', name: 'show_by_id', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id, EmployeeRepository $employeeRepository): JsonResponse
    {
        $employee = $employeeRepository->find($id);
        if (!$employee) {
            return new JsonResponse(['error' => 'Employee not found'], 404);        
        }

        return new JsonResponse(EmployeeOutput::fromEntity($employee));
    }

    #[Route('/search', name: 'show_by_name', methods: ['GET'])]
    public function searchByName(Request $request, SearchService $searchService): JsonResponse
    {
        $name = $request->query->get('name');

        if(!$name)
            return new JsonResponse(['error' => 'Name is required'], 400);

        
        $results = $searchService->searchByName($name);

        if(empty($results))
            return new JsonResponse(['error' => 'No results found']);

        return new JsonResponse($results);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(
        int $id, 
        Request $request, 
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        PositionService $positionService,
        EmployeeRepository $employeeRepository
    ): JsonResponse {

        $currentUser = $this->getUser();
        if (!$currentUser) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }
        
        $employee = $employeeRepository->find($id);
        if (!$employee) {
            return new JsonResponse(['error' => 'Employee not found'], 404);
        }

        if ($employee->getUser() !== $currentUser) {
            return new JsonResponse(['error' => 'You do not have permission to update this employee'], 403);
        }

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

        $birthDate = \DateTimeImmutable::createFromFormat('Y-m-d', $input->birthDate);
        if (!$birthDate) {
            return new JsonResponse(['error' => 'Invalid birth date format'], 400);
        }

        $employee->setName($input->name);
        $employee->setLastName($input->lastName);
        $employee->setEmail($input->email);
        $employee->setPosition($input->position);
        $employee->setBirthDate($birthDate);
        $employee->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->persist($employee);
        $entityManager->flush();

        return new JsonResponse(EmployeeOutput::fromEntity($employee));
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager, EmployeeRepository $employeeRepository): JsonResponse
    {
        $employee = $employeeRepository->find($id);
        if (!$employee) {
            return new JsonResponse(['error' => 'Employee not found'], 404);
        }

        $currentUser = $this->getUser();
        if (!$currentUser) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        if ($employee->getUser() !== $currentUser) {
            return new JsonResponse(['error' => 'You do not have permission to delete this employee'], 403);
        }
        
        $entityManager->remove($employee);
        $entityManager->flush();
        return new JsonResponse(null, 204);
    }
}
