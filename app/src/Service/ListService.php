<?php

namespace App\Service;

use App\Dto\EmployeeOutput;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ListService
{
    public function __construct(
        private EntityManagerInterface $entityManagerInterface,
        private EmployeeRepository $employeeRepository
    )
    {
        
    }

    public function getPaginatedList(int $page = 1, int $perPage = 10): array
    {
        $query = $this->employeeRepository->createQueryBuilder('e')
            ->orderBy('e.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery();

        $paginator = new Paginator($query);
        $results = iterator_to_array($paginator);

        return [
            'employees' => array_map(
                fn($employee) => EmployeeOutput::fromEntity($employee),
                $results
            ),
            'total' => count($paginator),
            'currentPage' => $page,
            'perPage' => $perPage,
        ];
    }
}