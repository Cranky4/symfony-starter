<?php

declare(strict_types=1);

namespace App\News\Infrastructure\Console;

use App\News\Infrastructure\Service\NewsParser\NewsParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:news:parse',
)]
final class ParseConsoleCommand extends Command
{
    private const SOURCE_NAME_OPTION = 'sourceName';

    public function __construct(
        private readonly NewsParser $parser,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();
        $this
            ->addOption(self::SOURCE_NAME_OPTION, 's', InputOption::VALUE_REQUIRED, 'Specific source name')
            ->setDescription('Start parsing');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = $input->getOption(self::SOURCE_NAME_OPTION);
        $links  = $this->parser->getLinks($source);

        foreach ($links as $link) {
            try {
                $output->write(sprintf('start parsing %s ...', $link));
                $this->parser->parseAndSave($source, $link);
                $output->writeln('saved!');
            } catch (\Throwable $exception) {
                $output->writeln($exception->getPrevious()?->getMessage() ?? $exception->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}
