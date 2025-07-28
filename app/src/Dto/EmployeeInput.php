<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class EmployeeInput
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50)]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50)]
    public string $position;

    public function __construct(string $name, string $email, string $position)
    {
        $this->name = $name;
        $this->email = $email;
        $this->position = $position;
    }
}