<?php

namespace App\FormHandler;


use App\Entity\Playlist;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Scalar\String_;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

final class UploadFileHandler

{

    public function __construct(
        public SluggerInterface $slugger
    )
    {}
    public function upload( SluggerInterface $slugger, mixed $uploadPathFile, String $directory):string
    {
        $originalFilename = pathinfo($uploadPathFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadPathFile->guessExtension();
        try {
            $uploadPathFile->move(
                $directory,
                $newFilename
            );
        } catch (FileException $e) {
            die ('File did not upload: ' . $e->getMessage());
        }
        return $newFilename;
    }
}


