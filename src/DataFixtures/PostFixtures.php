<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PostFixtures extends AppFixtures implements DependentFixtureInterface
{

    public function loadData(): void
    {
        $this->makeMany(50, Post::class, function (Post $post) {
            $post->setTitle($this->faker->sentence)
                ->setUser($this->getRandomReference(User::class))
                ->setUrl('https://picsum.photos/' . random_int(250, 500) . '/' . random_int(250, 500))
                ->setCreatedAt($this->faker->dateTimeThisYear( 'now'));
        });
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }
}
