<?php

namespace App\FormHandler;


use Doctrine\ORM\EntityManagerInterface;

final class FigureFormHandler
{
    public function __construct(
        public EntityManagerInterface $entityManager
    )
    {}


}


