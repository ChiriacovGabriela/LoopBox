<?php

namespace App\Service;

use App\Entity\Album;
use App\Entity\Song;
use App\FormHandler\UploadFileHandler;
use Symfony\Component\String\Slugger\SluggerInterface;

class AlbumManager
{

    public function processForm(Album $album, UploadFileHandler $uploadFileHandler, SluggerInterface $slugger, $form,$directoryImage,$directorySong,$user): void
    {

            $songs = $form->get('songs')->getData();
            $artist = $form->get('artist')->getData();
            $type = $form->get('type')->getData();
            $imageFile = $form->get('pictureFileName')->getData();
            if ($imageFile) {
                $newFilename = $uploadFileHandler->upload($slugger,$imageFile,$directoryImage);
            }
            foreach ($songs as $song) {
                $songFile = $song;
                $originalFilename = pathinfo($songFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $songFile = $safeFilename . '-' . uniqid() . '.' . $songFile->guessExtension();;
                $song->move(
                    $directorySong, $songFile
                );
                $newSong = new Song();
                $newSong->setUser($user);
                $newSong->setArtist($artist);
                $newSong->setType($type);
                $newSong->setName($originalFilename);
                $newSong->setAudioFileName($songFile);
                if ($imageFile) {
                    $newSong->setPictureFileName($newFilename);
                    $album->setPictureFileName($newFilename);
                }
                $album->addSong($newSong);
            }
        }

}