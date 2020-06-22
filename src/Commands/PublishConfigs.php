<?php


namespace Adeliom\WP\Extensions\Commands;

use Adeliom\WP\Commands\MakeFromStubCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PublishConfigs
 *
 * Publish base config files into the configuration directory
 *
 * @package Adeliom\WP\Extensions\Commands
 */
class PublishConfigs extends MakeFromStubCommand
{
    /**
     * The signature of the command.
     *
     * @example php console adeliom:config:publish
     * @var string
     */
    protected $signature = 'adeliom:config:publish {--force : Override current configs}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Publish Adeliom Extensions Config';

    /**
     * Execute the console command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $force = (bool) $input->getOption('force');
        $configFiles = glob(__DIR__ . '/../../config/*.php');
        foreach ($configFiles as $file) {
            $stub = file_get_contents($file);
            $file = basename($file);
            if ($this->createFile('config/' . $file, $stub, $force)) {
                $output->writeln('File created - config/' . $file);
            }
        }

        return 1;
    }
}
