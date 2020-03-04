<?php

namespace App\DataFixtures;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends AppFixtures
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function loadData(): void
    {

        $this->makeMany(10, User::class, function (User $user) {
            $user->setEmail($this->faker->email)
                ->setPassword(
                    $this->encoder->encodePassword($user, 'a_strong_password')
                );
        });
    }

}
