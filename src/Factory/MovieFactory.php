<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Movie;
use App\Repository\MovieRepository;

class MovieFactory
{
    public function __construct(
        private readonly MovieRepository $movieRepository
    ) {
    }

    public function create(array $data): array
    {
        $result = [];
        foreach ($data as $film)
        {
            $movie = $this->movieRepository->findOneBy(['name' => $film['title']]);
            if(!$movie){
                $movie = new Movie();
                $movie->setName($film['title']);
                $this->movieRepository->add($movie, true);
            }

            $result[$film['url']] = $movie;
        }

        return $result;
    }
}
