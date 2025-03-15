<?php

namespace App\Application\Security\DTO\Login;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;


class EmailPasswordLoginDTO
{
    #[Assert\NotBlank(message: 'Email cannot be blank')]
    #[Assert\Email(message: 'Invalid email format')]
    public string $email;

    #[Assert\NotBlank(message: 'Password cannot be blank')]
    public string $password;

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->request->get('_username', ''),
            $request->request->get('_password', '')
        );
    }
}
