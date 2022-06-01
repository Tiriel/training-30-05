<?php

namespace App\Command;

use App\Consumer\OmdbApiConsumer;
use App\Repository\MovieRepository;
use App\Transformer\MovieTransformer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MovieFindCommand extends Command
{
    protected static $defaultName = 'app:movie:find';
    protected static $defaultDescription = 'Find a movie by its IMDd ID or title';

    private OmdbApiConsumer $consumer;
    private MovieTransformer $transformer;
    private MovieRepository $repository;

    public function __construct(OmdbApiConsumer $consumer, MovieTransformer $transformer, MovieRepository $repository, string $name = null)
    {
        $this->consumer = $consumer;
        $this->transformer = $transformer;
        $this->repository = $repository;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('value', InputArgument::OPTIONAL, 'The title or id of the movie')
            ->addOption('type', 't', InputOption::VALUE_REQUIRED, 'The type of the searched value (id or title)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        if (!$value = $input->getArgument('value')) {
            $value = $io->ask('What is the if or title of the movie you are searching?');
        }

        $type = $input->getOption('type');
        while (!$type || (!array_key_exists($type, OmdbApiConsumer::SEARCH_TYPES))) {
            $type = $io->ask('What is the type of the given value? ("id" or "title")');
        }

        $io->note(sprintf("Searching for a movie with %s %s", $type, $value));
        $io->text("Searching database...");
        $movie = $this->repository->findOneBy([$type => $value]);

        if (!$movie) {
            $io->text("Movie not found in database, searching on OMDb API.");
            $method = 'getMovieBy' . ucfirst($type);
            $movie = $this->transformer->arrayToMovie($this->consumer->$method($value));

            if (!$movie) {
                $io->error("No movie found!");
                return Command::FAILURE;
            }

            $this->repository->add($movie, true);
            $io->note("Saved in database.");
        }

        $io->success("Movie found!");
        $io->table(
            ['Id', 'OMDb Id', 'Title', 'Rated'],
            [[$movie->getId(), $movie->getImdbId(), $movie->getTitle(), $movie->getRated()]]
        );

        return Command::SUCCESS;
    }
}
