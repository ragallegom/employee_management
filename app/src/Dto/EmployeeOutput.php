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

    public function __construct(int $id, string $name, string $email, string $position, \DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->position = $position;
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function fromEntity(Employee $employee): self
    {
        return new self(
            $employee->getId(),
            $employee->getName(),
            $employee->getEmail(),
            $employee->getPosition(),
            $employee->getCreatedAt()
        );
    }
}