<?php

namespace App\DataFixtures;

use App\Entity\Playlist;
use App\Entity\Song;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Faker;
class SongFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($s = 1; $s<=5; $s++){
            $song = new Song();
            $song->setName($faker->text(10));
            $user = $this->getReference('user'.rand(1,4));
            $song->setUser($user);
            $song->setAudioFileName($faker->text(10));


            $manager->persist($song);
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
