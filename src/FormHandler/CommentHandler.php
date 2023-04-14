<?php

namespace App\FormHandler;

use App\Controller\PlaylistController;
use App\Entity\Playlist;
use App\Entity\Comment;
use App\Entity\Song;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;


final class CommentHandler
{
    public function __construct(
        public EntityManagerInterface $entityManager
    ){}

    public function addComment(Comment $comment, Song $song, User $user): void
    {
        $comment->setUser($user);
        $comment->setSong($song);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();

    }
}






