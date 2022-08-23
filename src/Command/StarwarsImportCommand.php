<?php

namespace App\Command;

use App\Services\ApiImporterService;
use App\Factory\CharacterFactory;
use App\Factory\MovieFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'starwars:import',
    description: 'Import characters and movies from https://swapi.dev/',
)]
class StarwarsImportCommand extends Command
{
    private int $limit = 30;

    public function __construct(
        protected readonly ApiImporterService $apiImporter,
        protected readonly CharacterFactory   $characterFactory,
        protected readonly MovieFactory $movieFactory
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $movies = $this->apiImporter->getApiData("films");
        $moviesUrls = $this->movieFactory->create($movies['results']);

        $characters = $this->apiImporter->getApiData("people")['results'];
        $perPage = count($characters);

        if ($perPage < $this->limit) {
            $pages = $this->limit / $perPage;
            for ($i=2; $i <= $pages; $i++){
                array_push($characters, ...$this->apiImporter->getApiData("people/?page=".$i)['results']);
            }
        }

        $this->characterFactory->create($characters, $moviesUrls);

        $io->success('All data was imported.');

        return Command::SUCCESS;
    }
}
