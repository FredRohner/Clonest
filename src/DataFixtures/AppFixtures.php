<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    /** @var Generator */
    protected $faker;

    /** @var ObjectManager */
    protected $manager;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $this->faker = $faker;
        $this->manager = $manager;

        $this->loadData();
        $manager->flush();
    }

    protected function loadData(): void
    {
    }

    protected function makeMany(int $limit, string $entityClassName, callable $factory): void
    {
        for ($i = 0; $i < $limit; $i++) {
            $entity = new $entityClassName();

            $factory($entity, $i);

            $this->manager->persist($entity);
            $this->setReference($entityClassName . '_' . $i, $entity);
        }
    }

    protected function getRandomReference(string $entityClassName)
    {
        $entities = [];
        $iterator = 0;

        while ($this->hasReference($entityClassName . '_' . $iterator)) {
            $entities[] = $this->getReference($entityClassName . '_' . $iterator);
            $iterator++;
        }

        return $this->faker->randomElement($entities);
    }
}
