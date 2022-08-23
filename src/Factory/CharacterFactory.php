<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Character;
use App\Repository\CharacterRepository;

class CharacterFactory
{
    public function __construct(
        private readonly CharacterRepository $characterRepository
    )
    {
    }

    public function create(array $data, array $moviesUrls)
    {
        foreach ($data as $item)
        {
            $character = new Character();
            $character
                ->setName($item['name'])
                ->setMass((int) $item['mass'])
                ->setHeight((int)$item['height'])
                ->setGender($item['gender']);
            foreach($item['films'] as $movie){
                $character->addMovie($moviesUrls[$movie]);
            }
            $this->characterRepository->add($character, true);
        }
    }
}
