<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UserRegistrationDto
{
    /**
     * @Assert\NotBlank(message="Username cannot be blank.")
     * @Assert\Length(min=3, max=50, minMessage="Username must be at least {{ limit }} characters long.", maxMessage="Username cannot exceed {{ limit }} characters.")
     * @Assert\Email(message="Username must be a valid email address.")
     */
    public string $email;

    /**
     * @Assert\NotBlank(message="Password cannot be blank.")
     * @Assert\Length(min=6, max=100, minMessage="Password must be at least {{ limit }} characters long.", maxMessage="Password cannot exceed {{ limit }} characters.")
     */
    public string $password;
}