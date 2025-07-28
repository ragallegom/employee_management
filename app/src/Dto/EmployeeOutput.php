<?php

namespace App\Dto;

use App\Entity\Employee;

class EmployeeOutput
{
    public int $id;
    public string $name;
    public string $email;
    public string $position;
    public \DateTimeImmutable $createdAt;
    public \DateTimeImmutable $birthDate;

    public function __construct(
        int $id, 
        string $name, 
        string $email, 
        string $position, 
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $birthDate
    ){
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->position = $position;
        $this->createdAt = $createdAt;
        $this->birthDate = $birthDate;
    }

    public static function fromEntity(Employee $employee): self
    {
        return new self(
            $employee->getId(),
            $employee->getName(),
            $employee->getEmail(),
            $employee->getPosition(),
            $employee->getCreatedAt(),
            $employee->getBirthDate()
        );
    }
}