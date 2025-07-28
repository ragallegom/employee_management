<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UserRegistrationDto
{
    #[Assert\NotBlank(message: "Username cannot be blank.")]
    #[Assert\Length(min: 3, max: 50)]
    #[Assert\Email(message: "Username must be a valid email address.")]
    public ?string $email = null;

    #[Assert\NotBlank(message: "Password cannot be blank.")]
    #[Assert\Length(min: 6, max: 100)]
    public ?string $password = null;
}
