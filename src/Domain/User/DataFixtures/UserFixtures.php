<?php

namespace App\Domain\User\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Domain\User\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

  private $passwordHasher;

  public function __construct(UserPasswordHasherInterface $passwordHasher)
  {
    $this->passwordHasher = $passwordHasher;
  }

  public function load(ObjectManager $manager): void
  {
    $user = new User();

    $user->setPassword($this->passwordHasher->hashPassword(
      $user,
      'test'
    ));
    $user->setEmail('test@email.com');

    $manager->persist($user);
    $manager->flush();
  }
}
