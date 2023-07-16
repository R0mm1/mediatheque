<?php

namespace App\Command\MeiliSearch;

use App\Service\MeiliSearch\DatabaseIndexer\DatabaseIndexerInterface;
use App\Service\MeiliSearch\DatabaseIndexer\Event\ErrorEvent;
use App\Service\MeiliSearch\DatabaseIndexer\Event\ProgressEvent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class IndexDatabaseCommand extends Command
{
    const OPT_BATCH_SIZE = 'batch-size';
    protected static $defaultName = 'app:meilisearch:index';

    public function __construct(
        private readonly DatabaseIndexerInterface $databaseIndexer,
        private readonly EventDispatcherInterface $eventDispatcher
    )
    {
        parent::__construct(null);
    }

    protected function configure()
    {
        $this->setDescription('Index all the database content into Meilisearch');

        $this->addOption(
            name: self::OPT_BATCH_SIZE,
            mode: InputOption::VALUE_REQUIRED,
            description: 'The number of items to index in one call to Meilisearch. Meilisearch uses a lot of RAM at indexation, use this param to adapt to the server\'s capabilities',
            default: 5
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $withErrors = false;

        $batchSize = $input->getOption(self::OPT_BATCH_SIZE);
        if (!is_int($batchSize)) {
            $io->error('Batch size must be a int');
            return Command::INVALID;
        }

        $currentlyIndexedType = null;
        $progressBar = null;
        $this->eventDispatcher->addListener(
            ProgressEvent::NAME,
            function (ProgressEvent $databaseIndexerEvent) use (&$currentlyIndexedType, &$progressBar, $io) {
                if ($currentlyIndexedType !== $databaseIndexerEvent->getIndexedElementType()) {
                    $currentlyIndexedType = $databaseIndexerEvent->getIndexedElementType();

                    if ($progressBar instanceof ProgressBar) {
                        $progressBar->finish();
                    }

                    $io->write(
                        [
                            '',
                            sprintf('Index %s...', $databaseIndexerEvent->getIndexedElementType())
                        ],
                        true
                    );

                    $progressBar = $io->createProgressBar($databaseIndexerEvent->getOver());
                    $progressBar->start();
                }
                $progressBar->setProgress($databaseIndexerEvent->getCount());
            }
        );

        $this->eventDispatcher->addListener(
            ErrorEvent::NAME,
            function (ErrorEvent $errorEvent) use ($io, &$withErrors) {
                $io->error($errorEvent->getMessage());
                $withErrors = true;
            }
        );

        $this->databaseIndexer->index($batchSize);

        $io->write('');

        if ($withErrors) {
            $io->error('Errors occured, Meilisearch indexes are not up to date.');
            return Command::FAILURE;
        }

        $io->success('Meilisearch indexes are up to date.');
        return Command::SUCCESS;
    }
}
