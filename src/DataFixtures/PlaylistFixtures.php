<?php

namespace App\DataFixtures;

use App\Entity\Playlist;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;
class PlaylistFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($play = 1; $play<=5; $play++){
            $playlist = new Playlist();
            $playlist->setName($faker->text(10));
            $playlist->setImageFileName($faker->image(null,640,480));
            // on va chercher une reference d'user
            $user = $this->getReference('user'.rand(1,4));
            $playlist->setUser($user);
            $manager->persist($playlist);
        }
        $manager->flush();
    }

    public function getDependencies():array
    {
        return [
            UserFixtures::class
        ];
    }
}
