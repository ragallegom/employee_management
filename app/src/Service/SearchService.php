<?php

namespace App\Service;

use App\Dto\EmployeeOutput;
use App\Repository\EmployeeRepository;

class SearchService
{
    public function __construct(
        private EmployeeRepository $employeeRepository
    ){}

    public function searchByName(string $name): array
    {
        $employees = $this->employeeRepository->createQueryBuilder('e')
            ->where('LOWER(e.name) LIKE :name')
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->getQuery()
            ->getResult();

        return array_map(
            fn($employee) => EmployeeOutput::fromEntity($employee),
            $employees
        );
    }

    public function searchPaginated(string $search, int $page, int $perPage): array
    {
        $query = $this->employeeRepository->createQueryBuilder('e');

        if (!empty($search)) {
            $query->where('LOWER(e.name) LIKE :search OR LOWER(e.lastName) LIKE :search')
            ->setParameter('search', '%' . strtolower($search) . '%');
        }

        $total = (clone $query)->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();

        $employees = $query
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();

        return [
            'employees' => $employees,
            'total' => (int) $total,
        ];
    }

}