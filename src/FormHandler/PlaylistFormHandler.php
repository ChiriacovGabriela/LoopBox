<?php

namespace App\FormHandler;


use App\Entity\Playlist;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\String\Slugger\SluggerInterface;


final class PlaylistFormHandler
{
    public function __construct(
        public EntityManagerInterface $entityManager
    )
    {}

    public function addPlaylist(Playlist $playlist, mixed $imagePathFile,
                                UploadFileHandler $uploadFileHandler,
                                SluggerInterface $slugger,
                                String $directory): void
    {
        if ($imagePathFile) {
            $newFilename = $uploadFileHandler->upload($slugger, $imagePathFile, $directory);
            $playlist->setImageFileName($newFilename);
        }
        //On stock
        $this->entityManager->persist($playlist);
        $this->entityManager->flush();
    }

}