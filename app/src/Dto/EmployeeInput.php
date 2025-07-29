<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class EmployeeInput
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50)]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50)]
    public string $lastName;

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50)]
    public string $position;

    #[Assert\NotBlank]
    #[Assert\Date]
    public string $birthDate;

    public function __construct(
        string $name,
        string $lastName,
        string $email,
        string $position,
        string $birthDate
    ) {
        $this->name = $name;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->position = $position;
        $this->birthDate = $birthDate;
    }
}
